<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\BankAccount;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // Query básica com relacionamentos
            $query = Transaction::with(['bankAccount', 'category']);

            // Filtros
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                      ->orWhere('reference_number', 'like', "%{$search}%");
                });
            }

            if ($request->filled('type') && $request->type !== 'all') {
                $query->where('type', $request->type);
            }

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->filled('category_id') && $request->category_id !== 'all') {
                if ($request->category_id === 'none') {
                    $query->whereNull('category_id');
                } else {
                    $query->where('category_id', $request->category_id);
                }
            }

            if ($request->filled('date_from')) {
                $query->whereDate('transaction_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('transaction_date', '<=', $request->date_to);
            }

            if ($request->filled('bank_account_id') && $request->bank_account_id !== 'all') {
                $query->where('bank_account_id', $request->bank_account_id);
            }

            // Ordenação
            $orderBy = $request->get('order_by', 'transaction_date');
            $orderDirection = $request->get('order_direction', 'desc');
            $query->orderBy($orderBy, $orderDirection);

            // Paginação
            $transactions = $query->paginate(20)->withQueryString();

            // Estatísticas
            $stats = [
                'total_transactions' => $query->count(),
                'total_credit' => $query->where('amount', '>', 0)->sum('amount'),
                'total_debit' => abs($query->where('amount', '<', 0)->sum('amount')),
                'balance' => $query->sum('amount')
            ];

            // Dados para filtros
            $bankAccounts = BankAccount::orderBy('name')->get();
            $categories = Category::orderBy('name')->get();

            // Se for requisição AJAX
            if ($request->ajax()) {
                return response()->json([
                    'transactions' => $transactions->items(),
                    'stats' => $stats,
                    'pagination' => $transactions->links()->toHtml()
                ]);
            }

            return view('transactions.index', compact(
                'transactions', 
                'stats', 
                'bankAccounts', 
                'categories'
            ));

        } catch (\Exception $e) {
            Log::error('Erro na listagem de transações: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json(['error' => 'Erro ao carregar transações'], 500);
            }

            return view('transactions.index', [
                'transactions' => collect([]),
                'stats' => ['total_transactions' => 0, 'total_credit' => 0, 'total_debit' => 0, 'balance' => 0],
                'bankAccounts' => collect([]),
                'categories' => collect([])
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            $bankAccounts = BankAccount::where('active', true)->orderBy('name')->get();
            $categories = Category::orderBy('name')->get();
            
            // Se for duplicação de transação
            $duplicateTransaction = null;
            if ($request->has('duplicate')) {
                $duplicateTransaction = Transaction::find($request->get('duplicate'));
            }
            
            return view('transactions.create', compact('bankAccounts', 'categories', 'duplicateTransaction'));
            
        } catch (\Exception $e) {
            return redirect()->route('transactions.index')
                           ->with('error', 'Erro ao carregar formulário: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'transaction_date' => 'required|date',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:credit,debit',
            'category_id' => 'nullable|exists:categories,id',
            'notes' => 'nullable|string',
            'tags' => 'nullable|string',
            'status' => 'nullable|in:pending,reconciled,cancelled'
        ]);

        try {
            DB::beginTransaction();

            // Ajustar o valor baseado no tipo
            if ($validated['type'] === 'debit') {
                $validated['amount'] = -abs($validated['amount']);
            } else {
                $validated['amount'] = abs($validated['amount']);
            }

            $validated['status'] = $validated['status'] ?? 'pending';

            // Criar transação
            $transaction = Transaction::create($validated);

            // Atualizar saldo da conta bancária
            $this->updateAccountBalance($transaction->bankAccount);

            DB::commit();

            return redirect()->route('transactions.index')
                           ->with('success', 'Transação criada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar transação: ' . $e->getMessage());
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Erro ao criar transação: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        try {
            $transaction->load(['bankAccount', 'category']);
            return view('transactions.show', compact('transaction'));
            
        } catch (\Exception $e) {
            return redirect()->route('transactions.index')
                           ->with('error', 'Transação não encontrada.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        try {
            $bankAccounts = BankAccount::where('active', true)->orderBy('name')->get();
            $categories = Category::orderBy('name')->get();
            
            return view('transactions.edit', compact('transaction', 'bankAccounts', 'categories'));
            
        } catch (\Exception $e) {
            return redirect()->route('transactions.index')
                           ->with('error', 'Erro ao carregar formulário de edição.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'transaction_date' => 'required|date',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'type' => 'required|in:credit,debit',
            'category_id' => 'nullable|exists:categories,id',
            'notes' => 'nullable|string',
            'tags' => 'nullable|string',
            'status' => 'nullable|in:pending,reconciled,cancelled'
        ]);

        try {
            DB::beginTransaction();

            // Verificar se pode editar
            if ($transaction->status === 'reconciled') {
                return back()->with('error', 'Não é possível editar transações conciliadas.');
            }

            // Conta original para atualizar saldo
            $originalAccount = $transaction->bankAccount;

            // Ajustar o valor baseado no tipo
            if ($validated['type'] === 'debit') {
                $validated['amount'] = -abs($validated['amount']);
            } else {
                $validated['amount'] = abs($validated['amount']);
            }

            // Atualizar transação
            $transaction->update($validated);

            // Atualizar saldos das contas
            $this->updateAccountBalance($originalAccount);
            
            if ($transaction->bank_account_id != $originalAccount->id) {
                $this->updateAccountBalance($transaction->bankAccount);
            }

            DB::commit();

            return redirect()->route('transactions.index')
                           ->with('success', 'Transação atualizada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar transação: ' . $e->getMessage());
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Erro ao atualizar transação: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        try {
            if ($transaction->status === 'reconciled') {
                return back()->with('error', 'Não é possível excluir transações conciliadas.');
            }

            $bankAccount = $transaction->bankAccount;
            $transaction->delete();

            // Atualizar saldo da conta
            $this->updateAccountBalance($bankAccount);

            return redirect()->route('transactions.index')
                           ->with('success', 'Transação excluída com sucesso!');
                           
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao excluir transação: ' . $e->getMessage());
        }
    }

    /**
     * Reconcile a single transaction
     */
    public function reconcileTransaction(Request $request, Transaction $transaction)
    {
        try {
            if ($transaction->status === 'reconciled') {
                return back()->with('warning', 'Transação já está conciliada.');
            }

            $transaction->update(['status' => 'reconciled']);

            return back()->with('success', 'Transação conciliada com sucesso!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao conciliar transação: ' . $e->getMessage());
        }
    }

    /**
     * Reconcile multiple transactions
     */
    public function reconcile(Request $request)
    {
        try {
            $transactionIds = $request->input('transaction_ids', []);
            
            $count = Transaction::whereIn('id', $transactionIds)
                ->where('status', 'pending')
                ->update(['status' => 'reconciled']);

            return back()->with('success', $count . ' transações conciliadas!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao conciliar transações: ' . $e->getMessage());
        }
    }

    /**
     * Quick update for inline editing
     */
    public function quickUpdate(Request $request, Transaction $transaction)
    {
        try {
            $field = $request->field;
            $value = $request->value;

            // Validar campo permitido
            $allowedFields = ['description', 'amount', 'category_id', 'transaction_date', 'status'];
            if (!in_array($field, $allowedFields)) {
                return response()->json(['message' => 'Campo não permitido'], 400);
            }

            // Atualizar campo
            $transaction->update([$field => $value]);

            return response()->json(['message' => 'Atualizado com sucesso']);
            
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao atualizar'], 500);
        }
    }

    /**
     * Get transaction summary for API
     */
    public function getSummary(Request $request)
    {
        try {
            $bankAccountId = $request->get('bank_account_id');
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');

            $query = Transaction::query();

            if ($bankAccountId) {
                $query->where('bank_account_id', $bankAccountId);
            }

            if ($dateFrom) {
                $query->whereDate('transaction_date', '>=', $dateFrom);
            }

            if ($dateTo) {
                $query->whereDate('transaction_date', '<=', $dateTo);
            }

            $summary = [
                'total_transactions' => $query->count(),
                'total_credit' => $query->where('amount', '>', 0)->sum('amount'),
                'total_debit' => abs($query->where('amount', '<', 0)->sum('amount')),
                'balance' => $query->sum('amount'),
                'pending_count' => $query->where('status', 'pending')->count(),
                'reconciled_count' => $query->where('status', 'reconciled')->count()
            ];

            return response()->json($summary);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao obter resumo'], 500);
        }
    }

    /**
     * Export transactions
     */
    public function export(Request $request)
    {
        try {
            $format = $request->get('format', 'csv');
            $query = Transaction::with(['bankAccount', 'category']);

            // Aplicar filtros se necessário
            if ($request->filled('date_from')) {
                $query->whereDate('transaction_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('transaction_date', '<=', $request->date_to);
            }

            $transactions = $query->orderBy('transaction_date', 'desc')->get();

            if ($format === 'csv') {
                return $this->exportCsv($transactions);
            }

            return back()->with('error', 'Formato não suportado');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao exportar: ' . $e->getMessage());
        }
    }

    /**
     * Update account balance based on transactions
     */
    private function updateAccountBalance(BankAccount $account)
    {
        try {
            $balance = $account->transactions()->sum('amount');
            $account->update(['balance' => $balance]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar saldo da conta: ' . $e->getMessage());
        }
    }

    /**
     * Export transactions to CSV
     */
    private function exportCsv($transactions)
    {
        $filename = 'transacoes_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // Cabeçalho
            fputcsv($file, [
                'Data', 'Descrição', 'Valor', 'Tipo', 'Conta', 'Categoria', 'Status'
            ]);
            
            // Dados
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->transaction_date,
                    $transaction->description,
                    $transaction->amount,
                    $transaction->type,
                    $transaction->bankAccount->name ?? '',
                    $transaction->category->name ?? '',
                    $transaction->status
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
