<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReconciliationController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\AccountPayableController;
use App\Http\Controllers\AccountReceivableController;
use App\Http\Controllers\SettingsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Dashboard - Rota principal (APENAS UMA)
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Contas Bancárias
Route::resource('bank-accounts', BankAccountController::class);

// Transações
Route::resource('transactions', TransactionController::class);

// Conciliações
Route::resource('reconciliations', ReconciliationController::class);

// Importações
Route::resource('imports', ImportController::class);

// Relatórios
Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

// Categorias
Route::resource('categories', CategoryController::class);

// Clientes
Route::resource('clients', ClientController::class);

// Fornecedores
Route::resource('suppliers', SupplierController::class);

// Contas a Pagar
Route::resource('account-payables', AccountPayableController::class);

// Contas a Receber
Route::resource('account-receivables', AccountReceivableController::class);

// Configurações
Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
Route::post('settings', [SettingsController::class, 'store'])->name('settings.store');

// Teste simples para debug
Route::get('/test', function () {
    return 'Sistema funcionando!';
});
