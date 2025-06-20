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

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Sistema Financeiro BC - Rotas Web
| Rotas para funcionalidades do sistema financeiro
|
*/

// Dashboard - Rota principal
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/home', [DashboardController::class, 'index'])->name('home');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.alt');

// API para gráficos do dashboard
Route::get('dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chart-data');

// Exportação do dashboard
Route::get('dashboard/export/{format}', [DashboardController::class, 'export'])->name('dashboard.export');

// Contas Bancárias
Route::resource('bank-accounts', BankAccountController::class);

// Transações
Route::resource('transactions', TransactionController::class);
Route::patch('transactions/{transaction}/reconcile', [TransactionController::class, 'reconcileTransaction'])->name('transactions.reconcile');
Route::patch('transactions/{transaction}/quick-update', [TransactionController::class, 'quickUpdate'])->name('transactions.quick-update');
Route::post('transactions/bulk-reconcile', [TransactionController::class, 'reconcile'])->name('transactions.bulk-reconcile');
Route::post('transactions/bulk-categorize', [TransactionController::class, 'bulkCategorize'])->name('transactions.bulk-categorize');
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

// Rotas de exportação de relatórios
Route::get('reports/export/{type}/{format}', [ReportController::class, 'export'])->name('reports.export');
// Exemplos:
// /reports/export/transactions/pdf
// /reports/export/transactions/csv
// /reports/export/transactions/excel
// /reports/export/reconciliations/pdf
// /reports/export/cash-flow/pdf
// /reports/export/categories/pdf

// Categorias
Route::resource('categories', CategoryController::class);
Route::post('categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');

// Gestão Avançada de Contas
Route::prefix('gestao')->name('account-management.')->group(function() {
    Route::get('/', [App\Http\Controllers\AccountManagementController::class, 'index'])->name('index');
    Route::get('/conta/{account}', [App\Http\Controllers\AccountManagementController::class, 'show'])->name('show');
    Route::post('/transferir-transacao', [App\Http\Controllers\AccountManagementController::class, 'transferTransaction'])->name('transfer-transaction');
    Route::get('/comparar', [App\Http\Controllers\AccountManagementController::class, 'compare'])->name('compare');
    Route::get('/api/contas-para-transferencia', [App\Http\Controllers\AccountManagementController::class, 'getAccountsForTransfer'])->name('accounts-for-transfer');
});

// Rotas de teste e debug
Route::get('/test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Laravel funcionando!',
        'timestamp' => now(),
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version()
    ]);
});

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
