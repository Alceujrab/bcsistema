<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Transaction;
use App\Models\Reconciliation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'active_accounts' => BankAccount::where('active', true)->count(),
            'pending_transactions' => Transaction::pending()->count(),
            'month_reconciliations' => Reconciliation::whereMonth('created_at', now()->month)->count(),
            'total_balance' => BankAccount::where('active', true)->sum('balance'),
        ];
        
        $recentTransactions = Transaction::with('bankAccount')
            ->latest('transaction_date')
            ->limit(10)
            ->get();
            
        $alerts = collect();
        
        // Alertas de transações pendentes antigas
        $oldPendingCount = Transaction::pending()
            ->where('transaction_date', '<', now()->subDays(30))
            ->count();
            
        if ($oldPendingCount > 0) {
            $alerts->push((object)[
                'type' => 'warning',
                'message' => "Existem {$oldPendingCount} transações pendentes há mais de 30 dias."
            ]);
        }
        
        // Alertas de contas sem conciliação recente
        $accountsWithoutReconciliation = BankAccount::where('active', true)
            ->whereDoesntHave('reconciliations', function ($query) {
                $query->where('created_at', '>', now()->subMonth());
            })
            ->count();
            
        if ($accountsWithoutReconciliation > 0) {
            $alerts->push((object)[
                'type' => 'info',
                'message' => "{$accountsWithoutReconciliation} contas não foram conciliadas no último mês."
            ]);
        }
        
        return view('dashboard', compact('stats', 'recentTransactions', 'alerts'));
    }
}