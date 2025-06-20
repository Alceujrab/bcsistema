<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Reconciliation;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }
    
    public function transactions(Request $request)
    {
        $query = Transaction::with('bankAccount');
        
        if ($request->filled('bank_account_id')) {
            $query->where('bank_account_id', $request->bank_account_id);
        }
        
        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }
        
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        $transactions = $query->get();
        
        $summary = [
            'total_credit' => $transactions->where('type', 'credit')->sum('amount'),
            'total_debit' => $transactions->where('type', 'debit')->sum('amount'),
            'balance' => $transactions->where('type', 'credit')->sum('amount') - 
                        $transactions->where('type', 'debit')->sum('amount'),
            'count' => $transactions->count(),
        ];
        
        $bankAccounts = BankAccount::where('active', true)->get();
        $categories = Transaction::distinct()->pluck('category')->filter();
        
        return view('reports.transactions', compact('transactions', 'summary', 'bankAccounts', 'categories'));
    }
    
    public function categories(Request $request)
    {
        $query = Transaction::query();
        
        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }
        
        $categorySummary = $query->select('category', 'type')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(amount) as total')
            ->groupBy('category', 'type')
            ->get()
            ->groupBy('category');
        
        return view('reports.categories', compact('categorySummary'));
    }
    
    public function reconciliations(Request $request)
    {
        $query = Reconciliation::with(['bankAccount', 'creator', 'approver']);
        
        if ($request->filled('bank_account_id')) {
            $query->where('bank_account_id', $request->bank_account_id);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }
        
        $reconciliations = $query->latest()->get();
        
        $stats = [
            'total' => $reconciliations->count(),
            'approved' => $reconciliations->where('status', 'approved')->count(),
            'pending' => $reconciliations->whereIn('status', ['draft', 'completed'])->count(),
            'balanced' => $reconciliations->filter(function ($r) { return $r->isBalanced(); })->count(),
        ];
        
        $bankAccounts = BankAccount::where('active', true)->get();
        
        return view('reports.reconciliations', compact('reconciliations', 'stats', 'bankAccounts'));
    }
    
    public function cashFlow(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        $dailyFlow = Transaction::select('transaction_date')
            ->selectRaw("SUM(CASE WHEN type = 'credit' THEN amount ELSE 0 END) as credits")
            ->selectRaw("SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END) as debits")
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->where('status', 'reconciled')
            ->groupBy('transaction_date')
            ->orderBy('transaction_date')
            ->get();
        
        $cumulativeBalance = 0;
        $dailyFlow = $dailyFlow->map(function ($day) use (&$cumulativeBalance) {
            $day->daily_balance = $day->credits - $day->debits;
            $cumulativeBalance += $day->daily_balance;
            $day->cumulative_balance = $cumulativeBalance;
            return $day;
        });
        
        return view('reports.cash-flow', compact('dailyFlow', 'startDate', 'endDate'));
    }
}