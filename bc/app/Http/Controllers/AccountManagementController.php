<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Transaction;
use App\Models\Reconciliation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountManagementController extends Controller
{
    /**
     * Dashboard principal de gestão de contas
     */
    public function index()
    {
        try {
            // Buscar todas as contas com estatísticas
            $accounts = BankAccount::with(['transactions' => function($query) {
                    $query->latest('transaction_date')->limit(5);
                }])
                ->withCount(['transactions'])
                ->withSum(['transactions as total_credit' => function($query) {
                    $query->where('type', 'credit');
                }], 'amount')
                ->withSum(['transactions as total_debit' => function($query) {
                    $query->where('type', 'debit');
                }], 'amount')
                ->orderBy('name')
                ->get();

            // Calcular estatísticas gerais
            $totalBalance = $accounts->sum('balance');
            $totalCredit = $accounts->sum('total_credit') ?? 0;
            $totalDebit = abs($accounts->sum('total_debit') ?? 0);
            
            // Estatísticas por tipo de conta
            $accountTypes = $accounts->groupBy('type')->map(function($group, $type) {
                return [
                    'type' => $type,
                    'type_name' => $this->getTypeName($type),
                    'count' => $group->count(),
                    'total_balance' => $group->sum('balance'),
                    'accounts' => $group
                ];
            });

            // Transações recentes de todas as contas
            $recentTransactions = Transaction::with(['bankAccount'])
                ->latest('transaction_date')
                ->limit(10)
                ->get();

            // Contas com maior movimentação
            $topAccounts = $accounts->sortByDesc('transactions_count')->take(5);

            return view('account-management.index', compact(
                'accounts',
                'accountTypes', 
                'totalBalance',
                'totalCredit',
                'totalDebit',
                'recentTransactions',
                'topAccounts'
            ));

        } catch (\Exception $e) {
            return view('account-management.index', [
                'accounts' => collect([]),
                'accountTypes' => collect([]),
                'totalBalance' => 0,
                'totalCredit' => 0,
                'totalDebit' => 0,
                'recentTransactions' => collect([]),
                'topAccounts' => collect([])
            ])->with('error', 'Erro ao carregar dados: ' . $e->getMessage());
        }
    }

    /**
     * Ficha detalhada de uma conta específica
     */
    public function show(BankAccount $account, Request $request)
    {
        try {
            // Query base para transações
            $transactionsQuery = $account->transactions()
                ->with(['category' => function ($query) {
                    $query->select('id', 'name', 'color')
                          ->where('active', true);
                }]);

            // Aplicar filtros
            if ($request->filled('category_id')) {
                $transactionsQuery->where('category_id', $request->category_id);
            }

            if ($request->filled('type')) {
                $transactionsQuery->where('type', $request->type);
            }

            if ($request->filled('status')) {
                $transactionsQuery->where('status', $request->status);
            }

            if ($request->filled('date_from')) {
                $transactionsQuery->whereDate('transaction_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $transactionsQuery->whereDate('transaction_date', '<=', $request->date_to);
            }

            if ($request->filled('search')) {
                $transactionsQuery->where('description', 'LIKE', '%' . $request->search . '%');
            }

            // Carregar transações com paginação
            $transactions = $transactionsQuery->latest('transaction_date')->paginate(20);

            // Verificar se há transações com categoria_id mas sem categoria carregada
            foreach ($transactions as $transaction) {
                if (!empty($transaction->category_id) && empty($transaction->category)) {
                    // Log para debug se necessário
                    Log::warning("Transaction {$transaction->id} has category_id {$transaction->category_id} but no category loaded");
                }
            }

            // Estatísticas da conta
            $stats = [
                'total_transactions' => $account->transactions()->count(),
                'total_credit' => $account->transactions()->where('type', 'credit')->sum('amount') ?? 0,
                'total_debit' => abs($account->transactions()->where('type', 'debit')->sum('amount') ?? 0),
                'average_credit' => $account->transactions()->where('type', 'credit')->avg('amount') ?? 0,
                'average_debit' => abs($account->transactions()->where('type', 'debit')->avg('amount') ?? 0),
                'pending_count' => $account->transactions()->where('status', 'pending')->count(),
                'reconciled_count' => $account->transactions()->where('status', 'reconciled')->count(),
            ];

            // Estatísticas mensais (últimos 6 meses)
            $monthlyStats = DB::table('transactions')
                ->where('bank_account_id', $account->id)
                ->where('transaction_date', '>=', now()->subMonths(6))
                ->select(
                    DB::raw('DATE_FORMAT(transaction_date, "%Y-%m") as month'),
                    DB::raw('SUM(CASE WHEN type = "credit" THEN amount ELSE 0 END) as credit'),
                    DB::raw('SUM(CASE WHEN type = "debit" THEN ABS(amount) ELSE 0 END) as debit'),
                    DB::raw('COUNT(*) as total_transactions')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            // Top categorias
            $topCategories = DB::table('transactions')
                ->join('categories', 'transactions.category_id', '=', 'categories.id')
                ->where('transactions.bank_account_id', $account->id)
                ->select(
                    'categories.name',
                    'categories.color',
                    DB::raw('COUNT(*) as transaction_count'),
                    DB::raw('SUM(ABS(amount)) as total_amount')
                )
                ->groupBy('categories.id', 'categories.name', 'categories.color')
                ->orderByDesc('total_amount')
                ->limit(5)
                ->get();

            // Conciliações da conta
            $reconciliations = $account->reconciliations()
                ->with(['creator'])
                ->latest('created_at')
                ->limit(5)
                ->get();

            // Categorias disponíveis para filtro
            $categories = \App\Models\Category::where('active', true)
                ->orderBy('name')
                ->get();

            return view('account-management.show', compact(
                'account',
                'transactions',
                'stats',
                'monthlyStats',
                'topCategories',
                'reconciliations',
                'categories'
            ));

        } catch (\Exception $e) {
            return redirect()->route('account-management.index')
                ->with('error', 'Erro ao carregar dados da conta: ' . $e->getMessage());
        }
    }

    /**
     * Transferir transação entre contas
     */
    public function transferTransaction(Request $request)
    {
        $validated = $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'target_account_id' => 'required|exists:bank_accounts,id',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $transaction = Transaction::findOrFail($validated['transaction_id']);
            $oldAccountId = $transaction->bank_account_id;
            
            // Atualizar a transação
            $transaction->update([
                'bank_account_id' => $validated['target_account_id'],
                'notes' => $validated['notes'] ? 
                    ($transaction->notes ? $transaction->notes . ' | ' . $validated['notes'] : $validated['notes']) : 
                    $transaction->notes
            ]);

            // Registrar a transferência no log
            Log::info('Transaction transferred', [
                'transaction_id' => $transaction->id,
                'old_account_id' => $oldAccountId,
                'new_account_id' => $validated['target_account_id'],
                'notes' => $validated['notes']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transação transferida com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao transferir transação: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exibir formulário de transferência entre contas
     */
    public function showTransferForm()
    {
        try {
            $accounts = BankAccount::where('active', true)
                ->orderBy('name')
                ->get();

            if ($accounts->count() < 2) {
                return redirect()->route('account-management.index')
                    ->with('error', 'É necessário ter pelo menos 2 contas ativas para realizar transferências.');
            }

            return view('account-management.transfer', compact('accounts'));

        } catch (\Exception $e) {
            return redirect()->route('account-management.index')
                ->with('error', 'Erro ao carregar formulário de transferência: ' . $e->getMessage());
        }
    }

    /**
     * Processar transferência entre contas
     */
    public function processTransfer(Request $request)
    {
        $request->validate([
            'from_account_id' => 'required|exists:bank_accounts,id',
            'to_account_id' => 'required|exists:bank_accounts,id|different:from_account_id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'transfer_date' => 'required|date|before_or_equal:today'
        ]);

        try {
            DB::beginTransaction();

            $fromAccount = BankAccount::findOrFail($request->from_account_id);
            $toAccount = BankAccount::findOrFail($request->to_account_id);
            $amount = floatval($request->amount);

            // Verificar se a conta de origem tem saldo suficiente
            if ($fromAccount->balance < $amount) {
                return back()->withErrors([
                    'amount' => 'Saldo insuficiente na conta de origem. Saldo disponível: R$ ' . number_format($fromAccount->balance, 2, ',', '.')
                ])->withInput();
            }

            // Gerar hash único para a transferência
            $transferHash = 'TRF_' . time() . '_' . uniqid();

            // Criar transação de débito na conta de origem
            Transaction::create([
                'bank_account_id' => $fromAccount->id,
                'transaction_date' => $request->transfer_date,
                'description' => 'Transferência para ' . $toAccount->name . ' - ' . $request->description,
                'amount' => -$amount,
                'type' => 'debit',
                'status' => 'reconciled',
                'reference_number' => $transferHash,
                'import_hash' => $transferHash
            ]);

            // Criar transação de crédito na conta de destino
            Transaction::create([
                'bank_account_id' => $toAccount->id,
                'transaction_date' => $request->transfer_date,
                'description' => 'Transferência de ' . $fromAccount->name . ' - ' . $request->description,
                'amount' => $amount,
                'type' => 'credit',
                'status' => 'reconciled',
                'reference_number' => $transferHash,
                'import_hash' => $transferHash
            ]);

            // Atualizar saldos das contas
            $fromAccount->decrement('balance', $amount);
            $toAccount->increment('balance', $amount);

            DB::commit();

            return redirect()->route('account-management.index')
                ->with('success', 'Transferência realizada com sucesso! R$ ' . number_format($amount, 2, ',', '.') . ' transferidos de ' . $fromAccount->name . ' para ' . $toAccount->name);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao processar transferência: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Listar histórico de transferências
     */
    public function transferHistory(Request $request)
    {
        try {
            $query = Transaction::where('reference_number', 'like', 'TRF_%')
                ->with(['bankAccount'])
                ->orderBy('transaction_date', 'desc');

            // Filtros
            if ($request->filled('account_id')) {
                $query->where('bank_account_id', $request->account_id);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('transaction_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('transaction_date', '<=', $request->date_to);
            }

            $transfers = $query->paginate(20);

            // Agrupar transferências por hash
            $groupedTransfers = $transfers->groupBy('reference_number')->map(function($group) {
                $debit = $group->where('type', 'debit')->first();
                $credit = $group->where('type', 'credit')->first();
                
                return [
                    'hash' => $group->first()->reference_number,
                    'date' => $group->first()->transaction_date,
                    'amount' => abs($debit ? $debit->amount : $credit->amount),
                    'from_account' => $debit ? $debit->bankAccount : null,
                    'to_account' => $credit ? $credit->bankAccount : null,
                    'description' => $group->first()->description,
                    'transactions' => $group
                ];
            });

            $accounts = BankAccount::orderBy('name')->get();

            return view('account-management.transfer-history', compact('groupedTransfers', 'accounts', 'transfers'));

        } catch (\Exception $e) {
            return redirect()->route('account-management.index')
                ->with('error', 'Erro ao carregar histórico de transferências: ' . $e->getMessage());
        }
    }

    /**
     * Obter nome do tipo de conta
     */
    private function getTypeName($type)
    {
        return [
            'checking' => 'Conta Corrente',
            'savings' => 'Poupança',
            'credit_card' => 'Cartão de Crédito',
            'investment' => 'Investimento'
        ][$type] ?? $type;
    }

    /**
     * API para buscar contas para transferência
     */
    public function getAccountsForTransfer(Request $request)
    {
        $currentAccountId = $request->get('current_account_id');
        
        $accounts = BankAccount::where('active', true)
            ->where('id', '!=', $currentAccountId)
            ->select('id', 'name', 'type', 'bank_name')
            ->orderBy('name')
            ->get();

        return response()->json($accounts);
    }
}
