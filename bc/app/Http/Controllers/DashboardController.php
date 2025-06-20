<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Transaction;
use App\Models\Reconciliation;
use App\Models\Category;
use App\Models\Client;
use App\Models\Supplier;
use App\Models\AccountPayable;
use App\Models\AccountReceivable;
use App\Helpers\ConfigHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
                // Novos módulos de gestão financeira
                'total_clients' => Client::count(),
                'active_clients' => Client::where('active', true)->count(),
                'total_suppliers' => Supplier::count(),
                'active_suppliers' => Supplier::where('active', true)->count(),
                'total_payables' => AccountPayable::count(),
                'pending_payables' => AccountPayable::where('status', 'pending')->count(),
                'overdue_payables' => AccountPayable::where('status', 'overdue')->count(),
                'total_receivables' => AccountReceivable::count(),
                'pending_receivables' => AccountReceivable::where('status', 'pending')->count(),
                'overdue_receivables' => AccountReceivable::where('status', 'overdue')->count(),
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
            
            // Dados dos módulos de gestão financeira
            $financialManagement = $this->getFinancialManagementData();

            // Alertas do sistema
            $alerts = $this->getSystemAlerts();

            // Dados para gráficos
            $chartData = [
                'monthly' => $this->getMonthlyTransactionData(),
                'categories' => $this->getCategoryDistribution(),
                'accounts' => $this->getAccountBalances()
            ];

            // Configurações do sistema
            $companyData = ConfigHelper::getCompanyData();
            $dashboardConfig = ConfigHelper::getDashboardConfig();

            return view('dashboard', compact(
                'stats', 
                'recentTransactions', 
                'monthlyStats', 
                'recentReconciliations',
                'financialManagement',
                'companyData',
                'dashboardConfig'
            ));

        } catch (\Exception $e) {
            Log::error('Dashboard Error: ' . $e->getMessage());
            
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
                ->orderByRaw('COUNT(*) DESC')
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
                ->orderByRaw('COUNT(*) DESC')
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
    
    /**
     * Exporta relatório do dashboard
     */
    public function export(Request $request, $format)
    {
        try {
            // Reutilizar a lógica do index para obter dados
            $stats = [
                'total_accounts' => BankAccount::count(),
                'active_accounts' => BankAccount::where('active', true)->count(),
                'total_transactions' => Transaction::count(),
                'pending_transactions' => Transaction::where('status', 'pending')->count(),
                'month_reconciliations' => $this->safeCount(Reconciliation::class, 'created_at'),
                'total_balance' => $this->safeSumBalance(),
                'total_categories' => $this->safeCount(Category::class),
            ];
            
            $recentTransactions = $this->getRecentTransactions();
            $recentReconciliations = $this->getRecentReconciliations();
            
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
            
            switch ($format) {
                case 'pdf':
                    return $this->exportDashboardToPdf($stats, $recentTransactions, $recentReconciliations, $monthlyStats);
                case 'csv':
                    return $this->exportDashboardToCsv($stats, $recentTransactions, $recentReconciliations, $monthlyStats);
                case 'excel':
                    // Por enquanto, usar CSV
                    return $this->exportDashboardToCsv($stats, $recentTransactions, $recentReconciliations, $monthlyStats);
                default:
                    return redirect()->back()->with('error', 'Formato de exportação inválido.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao exportar dashboard: ' . $e->getMessage());
        }
    }
    
    /**
     * Exporta dashboard para PDF usando template
     */
    private function exportDashboardToPdf($stats, $recentTransactions, $recentReconciliations, $monthlyStats)
    {
        $data = [
            'title' => 'Relatório do Dashboard',
            'stats' => $stats,
            'recentTransactions' => $recentTransactions,
            'recentReconciliations' => $recentReconciliations,
            'monthlyStats' => $monthlyStats,
            'generated_at' => now()->format('d/m/Y H:i:s')
        ];
        
        // Usando o mesmo serviço de PDF que criamos
        $pdfService = app(\App\Services\PdfService::class);
        $filename = 'dashboard_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        
        return $pdfService->generatePdf('reports.pdf.dashboard', $data, ['filename' => $filename]);
    }
    
    /**
     * Exporta dashboard para CSV
     */
    private function exportDashboardToCsv($stats, $recentTransactions, $recentReconciliations, $monthlyStats)
    {
        $filename = 'dashboard_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($stats, $recentTransactions, $recentReconciliations, $monthlyStats) {
            $file = fopen('php://output', 'w');
            
            // Seção de Estatísticas Gerais
            fputcsv($file, ['RELATÓRIO DO DASHBOARD - ' . now()->format('d/m/Y H:i:s')]);
            fputcsv($file, []);
            fputcsv($file, ['ESTATÍSTICAS GERAIS']);
            fputcsv($file, ['Métrica', 'Valor']);
            fputcsv($file, ['Total de Contas', $stats['total_accounts']]);
            fputcsv($file, ['Contas Ativas', $stats['active_accounts']]);
            fputcsv($file, ['Total d Transações', $stats['total_transactions']]);
            fputcsv($file, ['Transações Pendentes', $stats['pending_transactions']]);
            fputcsv($file, ['Conciliações do Mês', $stats['month_reconciliations']]);
            fputcsv($file, ['Saldo Total', 'R$ ' . number_format($stats['total_balance'], 2, ',', '.')]);
            fputcsv($file, ['Total de Categorias', $stats['total_categories']]);
            
            // Seção de Estatísticas Mensais
            fputcsv($file, []);
            fputcsv($file, ['ESTATÍSTICAS DO MÊS ATUAL']);
            fputcsv($file, ['Métrica', 'Valor']);
            fputcsv($file, ['Transações do Mês', $monthlyStats['current_month_transactions']]);
            fputcsv($file, ['Créditos do Mês', 'R$ ' . number_format($monthlyStats['current_month_credit'], 2, ',', '.')]);
            fputcsv($file, ['Débitos do Mês', 'R$ ' . number_format($monthlyStats['current_month_debit'], 2, ',', '.')]);
            fputcsv($file, ['Média Diária', $monthlyStats['daily_average']]);
            
            // Seção de Transações Recentes
            fputcsv($file, []);
            fputcsv($file, ['TRANSAÇÕES RECENTES']);
            fputcsv($file, ['Data', 'Descrição', 'Valor', 'Tipo', 'Conta', 'Status']);
            foreach ($recentTransactions as $transaction) {
                fputcsv($file, [
                    $transaction->transaction_date->format('d/m/Y'),
                    $transaction->description,
                    'R$ ' . number_format($transaction->amount, 2, ',', '.'),
                    $transaction->type == 'credit' ? 'Crédito' : 'Débito',
                    $transaction->bankAccount->name ?? 'N/A',
                    $transaction->status == 'reconciled' ? 'Conciliado' : 'Pendente'
                ]);
            }
            
            // Seção de Conciliações Recentes
            fputcsv($file, []);
            fputcsv($file, ['CONCILIAÇÕES RECENTES']);
            fputcsv($file, ['ID', 'Conta', 'Data', 'Saldo Inicial', 'Saldo Final', 'Status']);
            foreach ($recentReconciliations as $reconciliation) {
                fputcsv($file, [
                    $reconciliation->id,
                    $reconciliation->bankAccount->name ?? 'N/A',
                    $reconciliation->created_at->format('d/m/Y H:i'),
                    'R$ ' . number_format($reconciliation->opening_balance, 2, ',', '.'),
                    'R$ ' . number_format($reconciliation->closing_balance, 2, ',', '.'),
                    ucfirst($reconciliation->status)
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Exporta dados completos de gestão financeira
     */
    public function exportFinancialData(Request $request)
    {
        try {
            // Obter todos os dados necessários
            $accountsPayable = AccountPayable::with('supplier')->get();
            $accountsReceivable = AccountReceivable::with('client')->get();
            $bankAccounts = BankAccount::all();
            $transactions = Transaction::with(['bankAccount', 'category'])->get();
            $reconciliations = Reconciliation::with('bankAccount')->get();
            
            $filename = 'dados_gestao_financeira_' . now()->format('Y-m-d_H-i-s') . '.json';
            
            // Criar dados consolidados
            $exportData = [
                'contas_a_pagar' => $accountsPayable->toArray(),
                'contas_a_receber' => $accountsReceivable->toArray(),
                'contas_bancarias' => $bankAccounts->toArray(),
                'transacoes' => $transactions->toArray(),
                'conciliacoes' => $reconciliations->toArray(),
                'exported_at' => now()->toDateTimeString()
            ];
            
            // Salvar arquivo JSON
            $filePath = storage_path('app/public/' . $filename);
            file_put_contents($filePath, json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao exportar dados: ' . $e->getMessage()], 500);
        }
        
        return response()->json(['success' => true, 'message' => 'Relatório exportado com sucesso!']);
    }
    
    private function getFinancialManagementData()
    {
        // Contas a Pagar
        $accountsPayable = [
            'total' => AccountPayable::count(),
            'pending' => AccountPayable::where('status', 'pending')->count(),
            'overdue' => AccountPayable::where('status', 'overdue')->count(),
            'paid' => AccountPayable::where('status', 'paid')->count(),
            'total_amount' => AccountPayable::sum('amount'),
            'pending_amount' => AccountPayable::where('status', 'pending')->sum('amount'),
            'overdue_amount' => AccountPayable::where('status', 'overdue')->sum('amount'),
            'paid_amount' => AccountPayable::where('status', 'paid')->sum('paid_amount'),
        ];
        
        // Contas a Receber
        $accountsReceivable = [
            'total' => AccountReceivable::count(),
            'pending' => AccountReceivable::where('status', 'pending')->count(),
            'overdue' => AccountReceivable::where('status', 'overdue')->count(),
            'received' => AccountReceivable::where('status', 'received')->count(),
            'total_amount' => AccountReceivable::sum('amount'),
            'pending_amount' => AccountReceivable::where('status', 'pending')->sum('amount'),
            'overdue_amount' => AccountReceivable::where('status', 'overdue')->sum('amount'),
            'received_amount' => AccountReceivable::where('status', 'received')->sum('received_amount'),
        ];
        
        // Fluxo de Caixa
        $cashFlow = [
            'to_receive' => $accountsReceivable['pending_amount'] + $accountsReceivable['overdue_amount'],
            'to_pay' => $accountsPayable['pending_amount'] + $accountsPayable['overdue_amount'],
            'balance' => ($accountsReceivable['pending_amount'] + $accountsReceivable['overdue_amount']) - 
                        ($accountsPayable['pending_amount'] + $accountsPayable['overdue_amount'])
        ];
        
        // Contas vencendo nos próximos 7 dias
        $upcomingPayables = AccountPayable::where('status', 'pending')
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(7))
            ->with('supplier')
            ->orderBy('due_date')
            ->limit(5)
            ->get();
            
        $upcomingReceivables = AccountReceivable::where('status', 'pending')
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(7))
            ->with('client')
            ->orderBy('due_date')
            ->limit(5)
            ->get();
        
        // Contas vencidas
        $overduePayables = AccountPayable::where('status', 'overdue')
            ->orWhere(function($q) {
                $q->where('status', 'pending')
                  ->where('due_date', '<', now());
            })
            ->with('supplier')
            ->orderBy('due_date')
            ->limit(5)
            ->get();
            
        $overdueReceivables = AccountReceivable::where('status', 'overdue')
            ->orWhere(function($q) {
                $q->where('status', 'pending')
                  ->where('due_date', '<', now());
            })
            ->with('client')
            ->orderBy('due_date')
            ->limit(5)
            ->get();
        
        return compact(
            'accountsPayable',
            'accountsReceivable',
            'cashFlow',
            'upcomingPayables',
            'upcomingReceivables',
            'overduePayables',
            'overdueReceivables'
        );
    }
    
    public function financialManagement()
    {
        $financialData = $this->getFinancialManagementData();
        
        // Adicionar dados básicos
        $financialData['totalClients'] = Client::count();
        $financialData['totalSuppliers'] = Supplier::count();
        
        return view('reports.financial-management', $financialData);
    }
}
