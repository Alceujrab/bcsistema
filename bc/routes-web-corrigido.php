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
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
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

// Conciliações
Route::resource('reconciliations', ReconciliationController::class);
Route::get('reconciliations/{reconciliation}/review', [ReconciliationController::class, 'review'])->name('reconciliations.review');
Route::post('reconciliations/{reconciliation}/finalize', [ReconciliationController::class, 'finalize'])->name('reconciliations.finalize');
Route::get('reconciliations/{reconciliation}/transactions', [ReconciliationController::class, 'getTransactions'])->name('reconciliations.transactions');
Route::get('reconciliations/{reconciliation}/download', [ReconciliationController::class, 'download'])->name('reconciliations.download');

// Importações
Route::resource('imports', ImportController::class);
Route::post('imports/{import}/reprocess', [ImportController::class, 'reprocess'])->name('imports.reprocess');
Route::get('imports/{import}/download', [ImportController::class, 'download'])->name('imports.download');
Route::get('imports/{import}/export', [ImportController::class, 'export'])->name('imports.export');

// Relatórios
Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('reports/transactions', [ReportController::class, 'transactions'])->name('reports.transactions');
Route::get('reports/reconciliations', [ReportController::class, 'reconciliations'])->name('reports.reconciliations');
Route::get('reports/banks', [ReportController::class, 'banks'])->name('reports.banks');
Route::get('reports/custom', [ReportController::class, 'custom'])->name('reports.custom');
Route::post('reports/generate', [ReportController::class, 'generate'])->name('reports.generate');

// Categorias
Route::resource('categories', CategoryController::class);

// Debug routes (apenas para desenvolvimento)
Route::get('/debug-dashboard', function () {
    return view('debug.dashboard');
});

Route::get('/test-db-connection', function () {
    try {
        $transactions = Transaction::count();
        $accounts = BankAccount::count();
        return response()->json([
            'status' => 'success',
            'message' => 'Conexão com banco de dados OK',
            'data' => [
                'transactions' => $transactions,
                'bank_accounts' => $accounts
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Erro na conexão: ' . $e->getMessage()
        ], 500);
    }
});

// Debug para conciliação
Route::get('/debug-conciliacao', [ReconciliationController::class, 'debug'])->name('debug.conciliacao');

// Rota de teste para importação
Route::get('/test-import', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Funcionalidade de importação ativa'
    ]);
});

// Clientes
Route::resource('clients', ClientController::class);

// Fornecedores
Route::resource('suppliers', SupplierController::class);

// Contas a Pagar
Route::resource('account-payables', AccountPayableController::class);
Route::patch('account-payables/{accountPayable}/pay', [AccountPayableController::class, 'markAsPaid'])->name('account-payables.pay');

// Contas a Receber
Route::resource('account-receivables', AccountReceivableController::class);
Route::patch('account-receivables/{accountReceivable}/receive', [AccountReceivableController::class, 'markAsReceived'])->name('account-receivables.receive');

// Configurações
Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
Route::post('settings', [SettingsController::class, 'store'])->name('settings.store');
Route::get('settings/export', [SettingsController::class, 'export'])->name('settings.export');
Route::post('settings/import', [SettingsController::class, 'import'])->name('settings.import');

// Importação de Extratos
Route::get('extract-import', [ExtractImportController::class, 'index'])->name('extract-import.index');
Route::post('extract-import', [ExtractImportController::class, 'store'])->name('extract-import.store');
Route::get('extract-import/create', [ExtractImportController::class, 'create'])->name('extract-import.create');
Route::get('extract-import/{import}', [ExtractImportController::class, 'show'])->name('extract-import.show');
Route::delete('extract-import/{import}', [ExtractImportController::class, 'destroy'])->name('extract-import.destroy');

// Atualizações do Sistema
Route::get('updates', [UpdateController::class, 'index'])->name('updates.index');
Route::post('updates/check', [UpdateController::class, 'check'])->name('updates.check');
Route::post('updates/{update}/install', [UpdateController::class, 'install'])->name('updates.install');

// Sistema de Notificações
Route::get('notifications/mark-read/{id}', function($id) {
    // Implementar sistema de notificações se necessário
    return response()->json(['status' => 'success']);
})->name('notifications.mark-read');

// API Routes para AJAX
Route::prefix('api')->group(function () {
    Route::get('transactions/summary', [TransactionController::class, 'getSummary'])->name('api.transactions.summary');
    Route::get('bank-accounts/balance/{id}', [BankAccountController::class, 'getBalance'])->name('api.bank-accounts.balance');
    Route::get('dashboard/stats', [DashboardController::class, 'getStats'])->name('api.dashboard.stats');
    Route::get('reconciliations/{reconciliation}/progress', [ReconciliationController::class, 'getProgress'])->name('api.reconciliations.progress');
});

// Fallback para rotas não encontradas
Route::fallback(function () {
    return redirect()->route('dashboard')->with('warning', 'Página não encontrada. Redirecionado para o dashboard.');
});
