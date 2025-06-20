<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Transaction;
use App\Models\Reconciliation;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                'month_reconciliations' => $this->safeCount(Reconciliation::class, 'created_at'),
                'total_balance' => $this->safeSumBalance(),
                'total_categories' => $this->safeCount(Category::class),
            ];
            
            // Transações recentes com tratamento de erro
            $recentTransactions = $this->getRecentTransactions();
            
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
                'total_transactions' => $stats['total_transactions'],
                'daily_average' => $stats['total_transactions'] > 0 ? round($stats['total_transactions'] / 30, 1) : 0,
                'reconciliations_count' => $stats['month_reconciliations'],
                'top_categories' => $this->getTopCategories()
            ];
            // Conciliações recentes
            $recentReconciliations = $this->getRecentReconciliations();

            // Alertas do sistema
            $alerts = $this->getSystemAlerts();

            // Dados para gráficos
            $chartData = [
                'monthly' => $this->getMonthlyTransactionData(),
                'categories' => $this->getCategoryDistribution(),
                'accounts' => $this->getAccountBalances()
            ];

            return view('dashboard', compact(
                'stats', 
                'recentTransactions', 
                'recentReconciliations',
                'monthlyStats', 
                'alerts',
                'chartData'
            ));

        } catch (\Exception $e) {
            \Log::error('Dashboard Error: ' . $e->getMessage());
            
            // Em caso de erro, dados básicos seguros
            return $this->getEmptyDashboard();
        }
    }

    /**
     * Método auxiliar para contagem segura
     */
    private function safeCount($model, $dateField = null)
    {
        try {
            if ($dateField) {
                return $model::whereMonth($dateField, now()->month)->count();
            }
            return $model::count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Método auxiliar para soma segura de saldos
     */
    private function safeSumBalance()
    {
        try {
            return BankAccount::where('active', true)->sum('balance') ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Buscar transações recentes de forma segura
     */
    private function getRecentTransactions()
    {
        try {
            return Transaction::select([
                    'id', 'bank_account_id', 'transaction_date', 'description', 
                    'amount', 'type', 'status', 'category_id'
                ])
                ->with(['bankAccount:id,name,bank_name', 'category:id,name'])
                ->latest('transaction_date')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Buscar conciliações recentes
     */
    private function getRecentReconciliations()
    {
        try {
            if (!class_exists('App\Models\Reconciliation')) {
                return collect();
            }
            
            return Reconciliation::with(['bankAccount:id,name'])
                ->latest('created_at')
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Top categorias mais usadas
     */
    private function getTopCategories()
    {
        try {
            return Transaction::select('category_id', DB::raw('COUNT(*) as total'))
                ->with('category:id,name')
                ->whereNotNull('category_id')
                ->groupBy('category_id')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get()
                ->map(function($item) {
                    return [
                        'name' => $item->category->name ?? 'Sem categoria',
                        'total' => $item->total
                    ];
                });
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Gerar alertas do sistema
     */
    private function getSystemAlerts()
    {
        $alerts = [];

        try {
            // Verificar transações pendentes há mais de 7 dias
            $oldPendingCount = Transaction::where('status', 'pending')
                ->where('transaction_date', '<', now()->subDays(7))
                ->count();

            if ($oldPendingCount > 0) {
                $alerts[] = [
                    'type' => 'warning',
                    'title' => 'Transações Pendentes',
                    'message' => "Existem {$oldPendingCount} transações pendentes há mais de 7 dias.",
                    'action' => route('transactions.index', ['status' => 'pending']),
                    'icon' => 'fas fa-clock'
                ];
            }

            // Verificar contas com saldo baixo
            $lowBalanceAccounts = BankAccount::where('active', true)
                ->where('balance', '<', 1000)
                ->count();

            if ($lowBalanceAccounts > 0) {
                $alerts[] = [
                    'type' => 'danger',
                    'title' => 'Saldo Baixo',
                    'message' => "{$lowBalanceAccounts} conta(s) com saldo baixo.",
                    'action' => route('bank-accounts.index'),
                    'icon' => 'fas fa-exclamation-triangle'
                ];
            }

            // Verificar se há transações duplicadas
            $duplicateTransactions = Transaction::select('description', 'amount', 'transaction_date', DB::raw('COUNT(*) as count'))
                ->groupBy('description', 'amount', 'transaction_date')
                ->having('count', '>', 1)
                ->count();

            if ($duplicateTransactions > 0) {
                $alerts[] = [
                    'type' => 'info',
                    'title' => 'Possíveis Duplicatas',
                    'message' => "Encontradas {$duplicateTransactions} possíveis transações duplicadas.",
                    'action' => route('transactions.index', ['duplicates' => 'true']),
                    'icon' => 'fas fa-copy'
                ];
            }

        } catch (\Exception $e) {
            // Silenciosamente falha se não conseguir gerar alertas
        }

        return $alerts;
    }

    /**
     * Dashboard vazio em caso de erro
     */
    private function getEmptyDashboard()
    {
        $stats = [
            'total_accounts' => 0,
            'active_accounts' => 0,
            'total_transactions' => 0,
            'pending_transactions' => 0,
            'month_reconciliations' => 0,
            'total_balance' => 0,
            'total_categories' => 0,
        ];

        $recentTransactions = collect();
        $recentReconciliations = collect();
        $monthlyStats = [
            'current_month_transactions' => 0,
            'current_month_credit' => 0,
            'current_month_debit' => 0,
            'total_transactions' => 0,
            'daily_average' => 0,
            'reconciliations_count' => 0,
            'top_categories' => collect()
        ];
        $alerts = [];
        $chartData = [
            'monthly' => ['labels' => [], 'datasets' => []],
            'categories' => ['labels' => [], 'data' => []],
            'accounts' => ['labels' => [], 'data' => []]
        ];

        return view('dashboard', compact(
            'stats', 
            'recentTransactions', 
            'recentReconciliations',
            'monthlyStats', 
            'alerts',
            'chartData'
        ));
    }

    /**
     * Dados para gráfico de transações mensais
     */
    private function getMonthlyTransactionData()
    {
        try {
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
                        'backgroundColor' => 'rgba(40, 167, 69, 0.3)',
                        'borderColor' => 'rgba(40, 167, 69, 1)',
                        'borderWidth' => 2,
                        'fill' => true
                    ],
                    [
                        'label' => 'Débitos',
                        'data' => $debits,
                        'backgroundColor' => 'rgba(220, 53, 69, 0.3)',
                        'borderColor' => 'rgba(220, 53, 69, 1)',
                        'borderWidth' => 2,
                        'fill' => true
                    ]
                ]
            ];
        } catch (\Exception $e) {
            return [
                'labels' => [],
                'datasets' => []
            ];
        }
    }

    /**
     * Distribuição por categoria
     */
    private function getCategoryDistribution()
    {
        try {
            $categories = Transaction::select('category_id', DB::raw('COUNT(*) as count'))
                ->whereNotNull('category_id')
                ->groupBy('category_id')
                ->with('category:id,name')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get();

            $labels = [];
            $data = [];
            $colors = [
                'rgb(255, 99, 132)', 'rgb(54, 162, 235)', 'rgb(255, 205, 86)',
                'rgb(75, 192, 192)', 'rgb(153, 102, 255)', 'rgb(255, 159, 64)',
                'rgb(199, 199, 199)', 'rgb(83, 102, 147)', 'rgb(255, 99, 255)',
                'rgb(99, 255, 132)'
            ];
            
            foreach ($categories as $index => $item) {
                if ($item->category) {
                    $labels[] = $item->category->name;
                    $data[] = $item->count;
                }
            }

            return [
                'labels' => $labels,
                'datasets' => [
                    [
                        'data' => $data,
                        'backgroundColor' => array_slice($colors, 0, count($data)),
                        'borderWidth' => 2,
                        'borderColor' => '#fff'
                    ]
                ]
            ];
        } catch (\Exception $e) {
            return [
                'labels' => [],
                'datasets' => []
            ];
        }
    }

    /**
     * Saldos por conta
     */
    private function getAccountBalances()
    {
        try {
            $accounts = BankAccount::select('name', 'balance')
                ->where('active', true)
                ->orderBy('balance', 'desc')
                ->get();

            return [
                'labels' => $accounts->pluck('name')->toArray(),
                'data' => $accounts->pluck('balance')->toArray()
            ];
        } catch (\Exception $e) {
            return [
                'labels' => [],
                'data' => []
            ];
        }
    }

    /**
     * API para dados de gráfico via AJAX
     */
    public function getChartData(Request $request)
    {
        $type = $request->get('type', 'monthly');
        
        try {
            switch ($type) {
                case 'monthly':
                    return response()->json($this->getMonthlyTransactionData());
                case 'categories':
                    return response()->json($this->getCategoryDistribution());
                case 'accounts':
                    return response()->json($this->getAccountBalances());
                default:
                    return response()->json(['error' => 'Tipo inválido'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao carregar dados'], 500);
        }
    }
}
