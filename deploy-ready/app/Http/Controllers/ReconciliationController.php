<?php

namespace App\Http\Controllers;

use App\Models\Reconciliation;
use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReconciliationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reconciliations = Reconciliation::with('bankAccount', 'creator')
            ->latest()
            ->paginate(20);

        // Estatísticas gerais
        $stats = [
            'total_reconciliations' => Reconciliation::count(),
            'approved_reconciliations' => Reconciliation::where('status', 'approved')->count(),
            'pending_reconciliations' => Reconciliation::whereIn('status', ['draft', 'completed'])->count(),
            'failed_reconciliations' => Reconciliation::where('status', 'failed')->count(),
            'average_difference' => Reconciliation::where('status', '!=', 'draft')->avg('difference') ?? 0,
            'total_reconciled_amount' => Reconciliation::where('status', 'approved')->sum('ending_balance'),
        ];

        // Conciliações recentes por status
        $recentByStatus = [
            'draft' => Reconciliation::with('bankAccount')->where('status', 'draft')->latest()->limit(3)->get(),
            'completed' => Reconciliation::with('bankAccount')->where('status', 'completed')->latest()->limit(3)->get(),
            'approved' => Reconciliation::with('bankAccount')->where('status', 'approved')->latest()->limit(3)->get(),
        ];

        // Contas bancárias para filtros
        $bankAccounts = BankAccount::where('active', true)->get();

        // Análise mensal
        $monthlyStats = Reconciliation::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return view('reconciliations.index', compact(
            'reconciliations', 
            'stats', 
            'recentByStatus', 
            'bankAccounts', 
            'monthlyStats'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $bankAccounts = BankAccount::where('active', true)->get();
        
        $bankAccountId = $request->input('bank_account_id');
        $lastReconciliation = null;
        $suggestedDates = null;
        $pendingTransactions = collect();
        
        if ($bankAccountId) {
            $bankAccount = BankAccount::find($bankAccountId);
            
            $lastReconciliation = Reconciliation::where('bank_account_id', $bankAccountId)
                ->where('status', 'approved')
                ->latest('end_date')
                ->first();

            // Sugerir datas baseadas na última conciliação
            if ($lastReconciliation) {
                $suggestedDates = [
                    'start_date' => $lastReconciliation->end_date->addDay()->format('Y-m-d'),
                    'end_date' => now()->format('Y-m-d'),
                ];
            } else {
                $suggestedDates = [
                    'start_date' => $bankAccount->created_at->format('Y-m-d'),
                    'end_date' => now()->format('Y-m-d'),
                ];
            }

            // Transações pendentes para conciliação
            $pendingTransactions = Transaction::where('bank_account_id', $bankAccountId)
                ->where('status', 'pending')
                ->orderBy('transaction_date', 'desc')
                ->limit(10)
                ->get();
        }

        // Estatísticas das contas para ajudar na seleção
        $accountStats = [];
        foreach ($bankAccounts as $account) {
            $accountStats[$account->id] = [
                'pending_transactions' => $account->transactions()->where('status', 'pending')->count(),
                'last_reconciliation' => Reconciliation::where('bank_account_id', $account->id)
                    ->where('status', 'approved')
                    ->latest('end_date')
                    ->first(),
                'current_balance' => $account->balance ?? 0,
                'unreconciled_amount' => $account->transactions()
                    ->where('status', 'pending')
                    ->sum(DB::raw('CASE WHEN type = "credit" THEN amount ELSE -amount END')),
            ];
        }

        return view('reconciliations.create', compact(
            'bankAccounts', 
            'lastReconciliation', 
            'suggestedDates', 
            'pendingTransactions', 
            'accountStats'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'starting_balance' => 'required|numeric',
            'ending_balance' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id() ?? 1; // Usar ID 1 se não houver autenticação

        DB::transaction(function () use ($validated, &$reconciliation) {
            $reconciliation = Reconciliation::create($validated);
            
            // Associar transações do período
            Transaction::where('bank_account_id', $validated['bank_account_id'])
                ->whereBetween('transaction_date', [$validated['start_date'], $validated['end_date']])
                ->where('status', 'pending')
                ->update(['reconciliation_id' => $reconciliation->id]);
            
            $reconciliation->calculate();
        });

        return redirect()->route('reconciliations.show', $reconciliation)
            ->with('success', 'Conciliação criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Reconciliation $reconciliation)
    {
        $reconciliation->load([
            'bankAccount',
            'transactions' => function ($query) {
                $query->orderBy('transaction_date')->orderBy('id');
            },
            'creator',
            'approver'
        ]);

        $summary = [
            'total_credits' => $reconciliation->transactions()->where('type', 'credit')->sum('amount'),
            'total_debits' => $reconciliation->transactions()->where('type', 'debit')->sum('amount'),
            'transaction_count' => $reconciliation->transactions()->count(),
            'net_movement' => $reconciliation->transactions()->where('type', 'credit')->sum('amount') - 
                             $reconciliation->transactions()->where('type', 'debit')->sum('amount'),
            'expected_balance' => $reconciliation->starting_balance + 
                                 ($reconciliation->transactions()->where('type', 'credit')->sum('amount') - 
                                  $reconciliation->transactions()->where('type', 'debit')->sum('amount')),
        ];

        // Análise das transações por categoria
        $categoryBreakdown = $reconciliation->transactions()
            ->select('category', 'type')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(amount) as total')
            ->groupBy('category', 'type')
            ->get()
            ->groupBy('category');

        // Transações por dia para gráfico
        $dailyTransactions = $reconciliation->transactions()
            ->select('transaction_date')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(CASE WHEN type = "credit" THEN amount ELSE 0 END) as credits')
            ->selectRaw('SUM(CASE WHEN type = "debit" THEN amount ELSE 0 END) as debits')
            ->groupBy('transaction_date')
            ->orderBy('transaction_date')
            ->get();

        // Histórico de status da conciliação
        $statusHistory = [
            'created' => $reconciliation->created_at,
            'completed' => $reconciliation->status === 'completed' ? $reconciliation->updated_at : null,
            'approved' => $reconciliation->approved_at,
        ];

        return view('reconciliations.show', compact(
            'reconciliation', 
            'summary', 
            'categoryBreakdown', 
            'dailyTransactions', 
            'statusHistory'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reconciliation $reconciliation)
    {
        if ($reconciliation->status !== 'draft') {
            return redirect()->route('reconciliations.show', $reconciliation)
                ->with('error', 'Apenas conciliações em rascunho podem ser editadas.');
        }

        $bankAccounts = BankAccount::where('active', true)->get();
        
        // Transações associadas à conciliação
        $associatedTransactions = $reconciliation->transactions()
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get();

        // Transações disponíveis para adicionar (dentro do período da conciliação)
        $availableTransactions = Transaction::where('bank_account_id', $reconciliation->bank_account_id)
            ->whereBetween('transaction_date', [$reconciliation->start_date, $reconciliation->end_date])
            ->where('status', 'pending')
            ->whereNull('reconciliation_id')
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get();

        // Estatísticas atuais
        $currentStats = [
            'total_credits' => $associatedTransactions->where('type', 'credit')->sum('amount'),
            'total_debits' => $associatedTransactions->where('type', 'debit')->sum('amount'),
            'transaction_count' => $associatedTransactions->count(),
            'calculated_ending_balance' => $reconciliation->starting_balance + 
                                         ($associatedTransactions->where('type', 'credit')->sum('amount') - 
                                          $associatedTransactions->where('type', 'debit')->sum('amount')),
            'difference' => $reconciliation->ending_balance - 
                          ($reconciliation->starting_balance + 
                           ($associatedTransactions->where('type', 'credit')->sum('amount') - 
                            $associatedTransactions->where('type', 'debit')->sum('amount'))),
        ];

        return view('reconciliations.edit', compact(
            'reconciliation', 
            'bankAccounts', 
            'associatedTransactions', 
            'availableTransactions', 
            'currentStats'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reconciliation $reconciliation)
    {
        if ($reconciliation->status !== 'draft') {
            return back()->with('error', 'Apenas conciliações em rascunho podem ser editadas.');
        }

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'starting_balance' => 'required|numeric',
            'ending_balance' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        $reconciliation->update($validated);
        $reconciliation->calculate();

        return redirect()->route('reconciliations.show', $reconciliation)
            ->with('success', 'Conciliação atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reconciliation $reconciliation)
    {
        if ($reconciliation->status === 'approved') {
            return back()->with('error', 'Não é possível excluir conciliações aprovadas.');
        }

        // Desassociar transações
        $reconciliation->transactions()->update(['reconciliation_id' => null]);
        
        $reconciliation->delete();

        return redirect()->route('reconciliations.index')
            ->with('success', 'Conciliação excluída com sucesso!');
    }

    /**
     * Process reconciliation
     */
    public function process(Reconciliation $reconciliation)
    {
        if ($reconciliation->status !== 'draft') {
            return back()->with('error', 'Esta conciliação já foi processada.');
        }

        DB::transaction(function () use ($reconciliation) {
            // Marcar transações como conciliadas
            $reconciliation->transactions()
                ->where('status', 'pending')
                ->update(['status' => 'reconciled']);
            
            // Recalcular e atualizar status
            $reconciliation->calculate();
            $reconciliation->update(['status' => 'completed']);
            
            // Atualizar saldo da conta
            $reconciliation->bankAccount->updateBalance();
        });

        return redirect()->route('reconciliations.show', $reconciliation)
            ->with('success', 'Conciliação processada com sucesso!');
    }

    /**
     * Approve reconciliation
     */
    public function approve(Reconciliation $reconciliation)
    {
        if ($reconciliation->status !== 'completed') {
            return back()->with('error', 'Apenas conciliações completas podem ser aprovadas.');
        }

        if (!$reconciliation->isBalanced()) {
            return back()->with('error', 'A conciliação possui diferenças e não pode ser aprovada.');
        }

        $reconciliation->update([
            'status' => 'approved',
            'approved_by' => auth()->id() ?? 1,
            'approved_at' => now(),
        ]);

        return redirect()->route('reconciliations.show', $reconciliation)
            ->with('success', 'Conciliação aprovada com sucesso!');
    }

    /**
     * Generate reconciliation report
     */
    public function report(Reconciliation $reconciliation)
    {
        $reconciliation->load(['bankAccount', 'transactions', 'creator', 'approver']);
        
        return view('reconciliations.report', compact('reconciliation'));
    }

    /**
     * Transferir transação para outra conta durante conciliação
     */
    public function transferTransactionDuringReconciliation(Request $request)
    {
        $validated = $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'target_account_id' => 'required|exists:bank_accounts,id',
            'reconciliation_id' => 'nullable|exists:reconciliations,id',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();
            
            $transaction = Transaction::findOrFail($validated['transaction_id']);
            $oldAccountId = $transaction->bank_account_id;
            
            // Remover da conciliação atual se existir
            if (isset($validated['reconciliation_id'])) {
                $reconciliation = Reconciliation::findOrFail($validated['reconciliation_id']);
                $reconciliation->transactions()->detach($transaction->id);
            }
            
            // Atualizar a transação
            $transaction->update([
                'bank_account_id' => $validated['target_account_id'],
                'notes' => $validated['notes'] ? 
                    ($transaction->notes ? $transaction->notes . ' | Transferida da conta: ' . $transaction->bankAccount->name . ' | ' . $validated['notes'] : 'Transferida da conta: ' . $transaction->bankAccount->name . ' | ' . $validated['notes']) : 
                    ($transaction->notes ? $transaction->notes . ' | Transferida da conta: ' . $transaction->bankAccount->name : 'Transferida da conta: ' . $transaction->bankAccount->name)
            ]);

            // Registrar a transferência
            activity()
                ->performedOn($transaction)
                ->withProperties([
                    'old_account_id' => $oldAccountId,
                    'new_account_id' => $validated['target_account_id'],
                    'notes' => $validated['notes'],
                    'context' => 'reconciliation_transfer'
                ])
                ->log('transaction_transferred_during_reconciliation');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transação transferida com sucesso!',
                'transaction' => $transaction->fresh(['bankAccount'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao transferir transação: ' . $e->getMessage()
            ], 500);
        }
    }
}