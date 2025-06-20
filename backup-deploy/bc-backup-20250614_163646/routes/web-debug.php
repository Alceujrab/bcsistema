<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReconciliationController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CategoryController;

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

// Rota de teste do dashboard
Route::get('/dashboard-test', function () {
    try {
        $controller = new DashboardController();
        return response()->json([
            'status' => 'success',
            'message' => 'DashboardController pode ser instanciado',
            'controller' => get_class($controller)
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

// Dashboard - Rota principal
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Rota alternativa para dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.alt');

// Contas Bancárias
Route::resource('bank-accounts', BankAccountController::class);

// Transações
Route::resource('transactions', TransactionController::class);
Route::post('transactions/reconcile', [TransactionController::class, 'reconcile'])->name('transactions.reconcile');
Route::post('transactions/bulk-categorize', [TransactionController::class, 'bulkCategorize'])->name('transactions.bulk-categorize');

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
