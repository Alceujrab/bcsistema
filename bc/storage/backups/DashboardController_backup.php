<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Transaction;
use App\Models\Reconciliation;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Estatísticas principais
            $stats = [
                'total_accounts' => BankAccount::count(),
                'active_accounts' => BankAccount::where('active', true)->count(),
                'total_transactions' => Transaction::count(),
                'pending_transactions' => Transaction::where('status', 'pending')->count(),
                'month_reconciliations' => Reconciliation::whereMonth('created_at', now()->month)->count(),
                'total_balance' => BankAccount::sum('balance') ?? 0,
                'total_categories' => Category::count(),
            ];
            
            // Transações recentes
            $recentTransactions = Transaction::with(['bankAccount', 'category'])
                ->latest('transaction_date')
                ->limit(10)
                ->get();
            
            // Estatísticas do mês atual
            $monthlyStats = [
                'current_month_transactions' => Transaction::whereMonth('transaction_date', now()->month)
                    ->whereYear('transaction_date', now()->year)
                    ->count(),
                'current_month_credit' => Transaction::whereMonth('transaction_date', now()->month)
                    ->whereYear('transaction_date', now()->year)
                    ->where('type', 'credit')
                    ->sum('amount') ?? 0,
                'current_month_debit' => Transaction::whereMonth('transaction_date', now()->month)
                    ->whereYear('transaction_date', now()->year)
                    ->where('type', 'debit')
                    ->sum('amount') ?? 0,
            ];

            // Alertas do sistema
            $alerts = [];

            // Verificar transações pendentes há mais de 7 dias
            $oldPendingCount = Transaction::where('status', 'pending')
                ->where('transaction_date', '<', now()->subDays(7))
                ->count();

            if ($oldPendingCount > 0) {
                $alerts[] = [
                    'type' => 'warning',
                    'title' => 'Transações Pendentes',
                    'message' => "Existem {$oldPendingCount} transações pendentes há mais de 7 dias.",
                    'action' => route('transactions.index', ['status' => 'pending'])
                ];
            }

            // Verificar contas com saldo baixo
            $lowBalanceAccounts = BankAccount::where('active', true)
                ->where('balance', '<', 1000) // Exemplo: saldo menor que R$ 1.000
                ->count();

            if ($lowBalanceAccounts > 0) {
                $alerts[] = [
                    'type' => 'danger',
                    'title' => 'Saldo Baixo',
                    'message' => "{$lowBalanceAccounts} conta(s) com saldo baixo.",
                    'action' => route('bank-accounts.index')
                ];
            }

            return view('dashboard', [
                'totalAccounts' => $stats['total_accounts'],
                'totalTransactions' => $stats['total_transactions'],
                'totalBalance' => $stats['total_balance'],
                'pendingTransactions' => $stats['pending_transactions'],
                'recentTransactions' => $recentTransactions,
                'monthlyStats' => $monthlyStats,
                'alerts' => $alerts,
                'monthlyData' => $this->getMonthlyTransactionData(),
                'categoryData' => $this->getCategoryDistribution(),
                'accounts' => $this->getAccountBalances()
            ]);

        } catch (\Exception $e) {
            // Log do erro
            \Log::error('Dashboard error: ' . $e->getMessage());
            
            // Retorna dados básicos em caso de erro
            return view('dashboard', [
                'totalAccounts' => 0,
                'totalTransactions' => 0,
                'totalBalance' => 0,
                'pendingTransactions' => 0,
                'recentTransactions' => collect(),
                'monthlyStats' => [],
                'alerts' => [],
                'monthlyData' => ['labels' => [], 'datasets' => []],
                'categoryData' => ['labels' => [], 'data' => []],
                'accounts' => ['labels' => [], 'data' => []]
            ]);
        }
    }

    /**
     * Dados para gráfico de transações mensais
     */
    private function getMonthlyTransactionData()
    {
        $months = [];
        $credits = [];
        $debits = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M/Y');
            
            $credits[] = Transaction::whereMonth('transaction_date', $date->month)
                ->whereYear('transaction_date', $date->year)
                ->where('type', 'credit')
                ->sum('amount') ?? 0;
                
            $debits[] = Transaction::whereMonth('transaction_date', $date->month)
                ->whereYear('transaction_date', $date->year)
                ->where('type', 'debit')
                ->sum('amount') ?? 0;
        }
        
        return [
            'labels' => $months,
            'datasets' => [
                [
                    'label' => 'Créditos',
                    'data' => $credits,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Débitos',
                    'data' => $debits,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1
                ]
            ]
        ];
    }

    /**
     * Distribuição por categoria
     */
    private function getCategoryDistribution()
    {
        $categories = Transaction::selectRaw('category_id, COUNT(*) as count')
            ->whereNotNull('category_id')
            ->groupBy('category_id')
            ->with('category:id,name')
            ->get();

        $labels = $categories->pluck('category.name')->filter()->toArray();
        $data = $categories->pluck('count')->toArray();

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Saldos por conta
     */
    private function getAccountBalances()
    {
        $accounts = BankAccount::select('name', 'balance')
            ->where('active', true)
            ->get();

        return [
            'labels' => $accounts->pluck('name')->toArray(),
            'data' => $accounts->pluck('balance')->toArray()
        ];
    }
}
