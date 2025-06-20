<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReconciliationController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CategoryController;
use App\Models\Transaction;
use App\Models\BankAccount;

// Rota de teste básica
Route::get('/test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Laravel funcionando!',
        'timestamp' => now(),
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version()
    ]);
});

// Rota de debug para verificar se o controller está funcionando
Route::get('/debug-dashboard', function () {
    try {
        $stats = [
            'total_accounts' => \App\Models\BankAccount::count(),
            'total_transactions' => \App\Models\Transaction::count(),
            'total_categories' => \App\Models\Category::count(),
        ];
        
        return response()->json([
            'status' => 'success',
            'message' => 'Dashboard data loaded successfully',
            'stats' => $stats
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
    }
});

// Dashboard - Rota principal (GET explícito)
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/home', [DashboardController::class, 'index'])->name('home');

// Rota alternativa para dashboard (caso a principal não funcione)
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.alt');

// API para gráficos do dashboard
Route::get('dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chart-data');

// Contas Bancárias
Route::resource('bank-accounts', BankAccountController::class);

// Transações
Route::resource('transactions', TransactionController::class);
Route::post('transactions/bulk-reconcile', [TransactionController::class, 'reconcile'])->name('transactions.bulk-reconcile');
Route::post('transactions/bulk-categorize', [TransactionController::class, 'bulkCategorize'])->name('transactions.bulk-categorize');

// Rotas adicionais para transações
Route::patch('transactions/{transaction}/reconcile', [TransactionController::class, 'reconcileTransaction'])->name('transactions.reconcile');

// Rotas AJAX para transações
Route::patch('transactions/{transaction}/quick-update', [TransactionController::class, 'quickUpdate'])->name('transactions.quick-update');
Route::delete('transactions/bulk-delete', [TransactionController::class, 'bulkDelete'])->name('transactions.bulk-delete');
Route::post('transactions/bulk-status', [TransactionController::class, 'bulkUpdateStatus'])->name('transactions.bulk-status');
Route::post('transactions/auto-categorize', [TransactionController::class, 'autoCategorize'])->name('transactions.auto-categorize');
Route::get('transactions/export', [TransactionController::class, 'export'])->name('transactions.export');
Route::post('transactions/export-selected', [TransactionController::class, 'export'])->name('transactions.export-selected');

// Conciliações
Route::resource('reconciliations', ReconciliationController::class);
Route::post('reconciliations/{reconciliation}/process', [ReconciliationController::class, 'process'])->name('reconciliations.process');
Route::post('reconciliations/{reconciliation}/approve', [ReconciliationController::class, 'approve'])->name('reconciliations.approve');
Route::get('reconciliations/{reconciliation}/report', [ReconciliationController::class, 'report'])->name('reconciliations.report');

// Importações
Route::resource('imports', ImportController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
Route::get('imports/download-template/{type}', [ImportController::class, 'downloadTemplate'])->name('imports.download-template');

// Relatórios
Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('reports/transactions', [ReportController::class, 'transactions'])->name('reports.transactions');
Route::get('reports/reconciliations', [ReportController::class, 'reconciliations'])->name('reports.reconciliations');
Route::get('reports/categories', [ReportController::class, 'categories'])->name('reports.categories');
Route::get('reports/cash-flow', [ReportController::class, 'cashFlow'])->name('reports.cash-flow');

// Categorias
Route::resource('categories', CategoryController::class);
Route::post('categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');

// Rota de teste para dashboard simples
Route::get('/test', function () {
    $stats = [
        'total_transactions' => Transaction::count(),
        'total_income' => Transaction::where('amount', '>', 0)->sum('amount'),
        'total_expense' => abs(Transaction::where('amount', '<', 0)->sum('amount')),
        'balance' => Transaction::sum('amount')
    ];
    
    $recentTransactions = Transaction::with('bankAccount')
        ->orderBy('transaction_date', 'desc')
        ->limit(10)
        ->get();
    
    return view('dashboard-simple-test', compact('stats', 'recentTransactions'));
});
