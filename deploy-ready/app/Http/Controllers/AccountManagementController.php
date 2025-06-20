<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Transaction;
use App\Models\Reconciliation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    public function show(BankAccount $account)
    {
        try {
            // Carregar transações com paginação e eager loading robusto
            $transactions = $account->transactions()
                ->with(['category' => function ($query) {
                    $query->select('id', 'name', 'color')
                          ->where('active', true);
                }])
                ->latest('transaction_date')
                ->paginate(20);

            // Verificar se há transações com categoria_id mas sem categoria carregada
            foreach ($transactions as $transaction) {
                if (!empty($transaction->category_id) && empty($transaction->category)) {
                    // Log para debug se necessário
                    \Log::warning("Transaction {$transaction->id} has category_id {$transaction->category_id} but no category loaded");
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

            return view('account-management.show', compact(
                'account',
                'transactions',
                'stats',
                'monthlyStats',
                'topCategories',
                'reconciliations'
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
            activity()
                ->performedOn($transaction)
                ->withProperties([
                    'old_account_id' => $oldAccountId,
                    'new_account_id' => $validated['target_account_id'],
                    'notes' => $validated['notes']
                ])
                ->log('transaction_transferred');

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
     * Comparar contas
     */
    public function compare(Request $request)
    {
        $accountIds = $request->get('accounts', []);
        
        if (empty($accountIds) || count($accountIds) < 2) {
            return redirect()->route('account-management.index')
                ->with('error', 'Selecione pelo menos 2 contas para comparar.');
        }

        $accounts = BankAccount::whereIn('id', $accountIds)
            ->withCount('transactions')
            ->withSum(['transactions as total_credit' => function($query) {
                $query->where('type', 'credit');
            }], 'amount')
            ->withSum(['transactions as total_debit' => function($query) {
                $query->where('type', 'debit');
            }], 'amount')
            ->get();

        // Estatísticas comparativas
        $comparison = $accounts->map(function($account) {
            return [
                'account' => $account,
                'balance' => $account->balance,
                'total_credit' => $account->total_credit ?? 0,
                'total_debit' => abs($account->total_debit ?? 0),
                'net_flow' => ($account->total_credit ?? 0) - abs($account->total_debit ?? 0),
                'transaction_count' => $account->transactions_count,
                'avg_transaction' => $account->transactions_count > 0 ? 
                    (($account->total_credit ?? 0) + abs($account->total_debit ?? 0)) / $account->transactions_count : 0
            ];
        });

        return view('account-management.compare', compact('comparison'));
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
