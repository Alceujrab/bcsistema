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
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\AccountPayableController;
use App\Http\Controllers\AccountReceivableController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ExtractImportController;
use App\Http\Controllers\UpdateController;

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
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/reports/financial-management', [DashboardController::class, 'financialManagement'])->name('reports.financial-management');

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
    
    // Transferências entre contas
    Route::get('/transferencia', [App\Http\Controllers\AccountManagementController::class, 'showTransferForm'])->name('transfer.form');
    Route::post('/transferencia', [App\Http\Controllers\AccountManagementController::class, 'processTransfer'])->name('transfer.process');
    Route::get('/transferencias', [App\Http\Controllers\AccountManagementController::class, 'transferHistory'])->name('transfer.history');
});

// NOVOS MÓDULOS - GESTÃO FINANCEIRA

/*
|--------------------------------------------------------------------------
| Clientes
|--------------------------------------------------------------------------
*/
Route::resource('clients', ClientController::class);
Route::patch('clients/{client}/toggle-status', [ClientController::class, 'toggleStatus'])->name('clients.toggle-status');

/*
|--------------------------------------------------------------------------
| Fornecedores
|--------------------------------------------------------------------------
*/
Route::resource('suppliers', SupplierController::class);
Route::patch('suppliers/{supplier}/toggle-status', [SupplierController::class, 'toggleStatus'])->name('suppliers.toggle-status');

/*
|--------------------------------------------------------------------------
| Contas a Pagar
|--------------------------------------------------------------------------
*/
Route::resource('account-payables', AccountPayableController::class);
Route::patch('account-payables/{account_payable}/pay', [AccountPayableController::class, 'markAsPaid'])->name('account-payables.pay');
Route::patch('account-payables/{account_payable}/partial-pay', [AccountPayableController::class, 'partialPayment'])->name('account-payables.partial-pay');
Route::get('account-payables/overdue/list', [AccountPayableController::class, 'overdue'])->name('account-payables.overdue');

/*
|--------------------------------------------------------------------------
| Contas a Receber
|--------------------------------------------------------------------------
*/
Route::resource('account-receivables', AccountReceivableController::class);
Route::patch('account-receivables/{account_receivable}/receive', [AccountReceivableController::class, 'markAsReceived'])->name('account-receivables.receive');
Route::patch('account-receivables/{account_receivable}/partial-receive', [AccountReceivableController::class, 'partialReceive'])->name('account-receivables.partial-receive');
Route::get('account-receivables/overdue/list', [AccountReceivableController::class, 'overdue'])->name('account-receivables.overdue');

// Sistema de Configurações
Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('/', [SettingsController::class, 'index'])->name('index');
    Route::post('/', [SettingsController::class, 'store'])->name('store');
    Route::post('/reset', [SettingsController::class, 'reset'])->name('reset');
    Route::get('/export', [SettingsController::class, 'export'])->name('export');
    Route::post('/import', [SettingsController::class, 'import'])->name('import');
    Route::post('/create-custom', [SettingsController::class, 'createCustom'])->name('create-custom');
    Route::get('/api/public', [SettingsController::class, 'getPublicSettings'])->name('api.public');
    Route::get('/dynamic.css', [SettingsController::class, 'dynamicCSS'])->name('dynamic-css');
});

// Importações Avançadas - Multi-formato
Route::prefix('extract-imports')->name('extract-imports.')->group(function() {
    Route::get('/', [ExtractImportController::class, 'index'])->name('index');
    Route::get('/create', [ExtractImportController::class, 'create'])->name('create');
    Route::post('/', [ExtractImportController::class, 'store'])->name('store');
    Route::get('/{import}', [ExtractImportController::class, 'show'])->name('show');
    Route::delete('/{import}', [ExtractImportController::class, 'destroy'])->name('destroy');
    Route::get('/template/{type}', [ExtractImportController::class, 'downloadTemplate'])->name('template');
    Route::post('/validate', [ExtractImportController::class, 'validate'])->name('validate');
    Route::get('/history', [ExtractImportController::class, 'history'])->name('history');
});

// Sistema de Atualizações Automáticas
Route::prefix('system/update')->name('system.update.')->middleware('update.security')->group(function() {
    Route::get('/', [UpdateController::class, 'index'])->name('index');
    Route::get('/check', [UpdateController::class, 'check'])->name('check');
    Route::post('/download/{update}', [UpdateController::class, 'download'])->name('download');
    Route::post('/apply/{update}', [UpdateController::class, 'apply'])->name('apply');
    Route::get('/status/{update}', [UpdateController::class, 'status'])->name('status');
    Route::get('/history', [UpdateController::class, 'history'])->name('history');
    Route::get('/backup', [UpdateController::class, 'backup'])->name('backup');
    Route::post('/backup/create', [UpdateController::class, 'createBackup'])->name('backup.create');
    Route::post('/backup/restore', [UpdateController::class, 'restoreBackup'])->name('backup.restore');
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

// Rota de teste para PDF (desenvolvimento)
Route::get('/test-pdf', function () {
    try {
        $pdfService = new \App\Services\PdfService();
        
        $data = [
            'title' => 'Teste de PDF',
            'transactions' => collect([
                (object) [
                    'date' => '2025-06-16',
                    'description' => 'Teste de transação',
                    'type' => 'credit',
                    'amount' => 100.00,
                    'bankAccount' => (object) ['name' => 'Conta Teste']
                ]
            ]),
            'filters' => [
                'date_from' => '2025-06-01',
                'date_to' => '2025-06-16'
            ],
            'summary' => [
                'total_transactions' => 1,
                'total_credit' => 100.00,
                'total_debit' => 0.00,
                'balance' => 100.00
            ]
        ];

        return $pdfService->generatePdf('reports.pdf.transactions', $data, [
            'filename' => 'teste.pdf'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
})->name('test.pdf');
