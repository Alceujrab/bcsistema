<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Reconciliation;
use App\Models\BankAccount;
use App\Models\StatementImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // Estatísticas gerais para a página de relatórios
        $stats = [
            'total_transactions' => Transaction::count(),
            'total_reconciliations' => Reconciliation::count(),
            'active_accounts' => BankAccount::count(), // Removido where active
            'total_balance' => BankAccount::sum('balance') ?? 0,
            'last_import' => StatementImport::latest()->first(),
            'pending_reconciliations' => Reconciliation::count(), // Simplificado
        ];

        // Relatórios mais acessados
        $popularReports = [
            'transactions' => 'Relatório de Transações',
            'cash-flow' => 'Fluxo de Caixa',
            'categories' => 'Análise por Categorias',
            'reconciliations' => 'Conciliações',
        ];

        // Dados recentes para preview
        $recentActivity = [
            'transactions' => Transaction::with('bankAccount')
                ->latest()
                ->limit(5)
                ->get(),
            'reconciliations' => Reconciliation::with('bankAccount')
                ->latest()
                ->limit(3)
                ->get(),
        ];

        // Resumo mensal atual
        $currentMonth = [
            'credits' => Transaction::where('type', 'credit')
                ->whereMonth('transaction_date', now()->month)
                ->whereYear('transaction_date', now()->year)
                ->sum('amount'),
            'debits' => Transaction::where('type', 'debit')
                ->whereMonth('transaction_date', now()->month)
                ->whereYear('transaction_date', now()->year)
                ->sum('amount'),
        ];
        $currentMonth['balance'] = $currentMonth['credits'] - $currentMonth['debits'];

        return view('reports.index', compact('stats', 'popularReports', 'recentActivity', 'currentMonth'));
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
            $query->where('category_id', $request->category);
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('amount_min')) {
            $query->where('amount', '>=', $request->amount_min);
        }

        if ($request->filled('amount_max')) {
            $query->where('amount', '<=', $request->amount_max);
        }
        
        $transactions = $query->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(30);
        
        // Recalcular summary com os filtros aplicados
        $filteredQuery = clone $query;
        $summary = [
            'total_credit' => $filteredQuery->where('type', 'credit')->sum('amount'),
            'total_debit' => $filteredQuery->where('type', 'debit')->sum('amount'),
            'count' => $filteredQuery->count(),
            'avg_transaction' => $filteredQuery->avg('amount'),
        ];
        $summary['balance'] = $summary['total_credit'] - $summary['total_debit'];

        // Análise por mês
        $monthlyAnalysis = $filteredQuery
            ->select(DB::raw('YEAR(transaction_date) as year, MONTH(transaction_date) as month'))
            ->selectRaw('SUM(CASE WHEN type = "credit" THEN amount ELSE 0 END) as credits')
            ->selectRaw('SUM(CASE WHEN type = "debit" THEN amount ELSE 0 END) as debits')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        // Top categorias
        $topCategories = $filteredQuery
            ->select('category_id', 'type')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(amount) as total')
            ->groupBy('category_id', 'type')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();
        
        $bankAccounts = BankAccount::where('active', true)->get();
        $categories = Transaction::distinct()->pluck('category_id')->filter();
        
        return view('reports.transactions', compact(
            'transactions', 
            'summary', 
            'monthlyAnalysis', 
            'topCategories', 
            'bankAccounts', 
            'categories'
        ));
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

        if ($request->filled('bank_account_id')) {
            $query->where('bank_account_id', $request->bank_account_id);
        }
        
        $categorySummary = $query->select('category_id', 'type')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(amount) as total')
            ->selectRaw('AVG(amount) as average')
            ->selectRaw('MIN(amount) as min_amount')
            ->selectRaw('MAX(amount) as max_amount')
            ->groupBy('category_id', 'type')
            ->orderBy('total', 'desc')
            ->get()
            ->groupBy('category_id');

        // Estatísticas gerais
        $stats = [
            'total_categories' => $categorySummary->count(),
            'total_amount' => $query->sum('amount'),
            'avg_per_category' => $categorySummary->count() > 0 ? $query->sum('amount') / $categorySummary->count() : 0,
            'most_active_category' => $query->select('category_id')
                ->selectRaw('COUNT(*) as count')
                ->groupBy('category_id')
                ->orderBy('count', 'desc')
                ->first(),
        ];

        // Análise temporal das categorias
        $monthlyCategories = $query
            ->select('category_id', DB::raw('YEAR(transaction_date) as year, MONTH(transaction_date) as month'))
            ->selectRaw('SUM(amount) as total')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('category_id', 'year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(50)
            ->get()
            ->groupBy('category_id');

        // Top 10 categorias por valor
        $topCategories = $categorySummary->map(function ($items, $category) {
            $total = $items->sum('total');
            $count = $items->sum('count');
            return [
                'category' => $category,
                'total' => $total,
                'count' => $count,
                'avg' => $count > 0 ? $total / $count : 0,
                'items' => $items,
            ];
        })->sortByDesc('total')->take(10);

        $bankAccounts = BankAccount::where('active', true)->get();
        
        return view('reports.categories', compact(
            'categorySummary', 
            'stats', 
            'monthlyCategories', 
            'topCategories', 
            'bankAccounts'
        ));
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
        
        $reconciliations = $query->latest()->paginate(25);
        
        // Stats precisam ser calculadas separadamente para todas as reconciliações (não só a página atual)
        $allReconciliations = $query->get();
        $stats = [
            'total' => $allReconciliations->count(),
            'approved' => $allReconciliations->where('status', 'approved')->count(),
            'pending' => $allReconciliations->whereIn('status', ['draft', 'completed'])->count(),
            'balanced' => $allReconciliations->filter(function ($r) { return $r->isBalanced(); })->count(),
        ];
        
        $bankAccounts = BankAccount::where('active', true)->get();
        
        return view('reports.reconciliations', compact('reconciliations', 'stats', 'bankAccounts'));
    }
    
    public function cashFlow(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $bankAccountId = $request->input('bank_account_id');
        
        $query = Transaction::query()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->where('status', 'reconciled');

        if ($bankAccountId) {
            $query->where('bank_account_id', $bankAccountId);
        }

        $dailyFlow = $query
            ->select('transaction_date')
            ->selectRaw("SUM(CASE WHEN type = 'credit' THEN amount ELSE 0 END) as credits")
            ->selectRaw("SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END) as debits")
            ->selectRaw("COUNT(*) as transaction_count")
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

        // Estatísticas do período
        $periodStats = [
            'total_credits' => $dailyFlow->sum('credits'),
            'total_debits' => $dailyFlow->sum('debits'),
            'net_flow' => $dailyFlow->sum('credits') - $dailyFlow->sum('debits'),
            'avg_daily_credits' => $dailyFlow->count() > 0 ? $dailyFlow->sum('credits') / $dailyFlow->count() : 0,
            'avg_daily_debits' => $dailyFlow->count() > 0 ? $dailyFlow->sum('debits') / $dailyFlow->count() : 0,
            'days_positive' => $dailyFlow->where('daily_balance', '>', 0)->count(),
            'days_negative' => $dailyFlow->where('daily_balance', '<', 0)->count(),
            'best_day' => $dailyFlow->sortByDesc('daily_balance')->first(),
            'worst_day' => $dailyFlow->sortBy('daily_balance')->first(),
        ];

        // Análise semanal
        $weeklyFlow = $dailyFlow->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->transaction_date)->startOfWeek()->format('Y-m-d');
        })->map(function ($week, $weekStart) {
            return [
                'week_start' => $weekStart,
                'week_end' => \Carbon\Carbon::parse($weekStart)->endOfWeek()->format('Y-m-d'),
                'credits' => $week->sum('credits'),
                'debits' => $week->sum('debits'),
                'balance' => $week->sum('credits') - $week->sum('debits'),
                'transaction_count' => $week->sum('transaction_count'),
                'days_count' => $week->count(),
            ];
        });

        // Comparação com período anterior
        $previousPeriod = [
            'start' => \Carbon\Carbon::parse($startDate)->subDays(\Carbon\Carbon::parse($endDate)->diffInDays(\Carbon\Carbon::parse($startDate)))->format('Y-m-d'),
            'end' => \Carbon\Carbon::parse($startDate)->subDay()->format('Y-m-d'),
        ];

        $previousQuery = Transaction::query()
            ->whereBetween('transaction_date', [$previousPeriod['start'], $previousPeriod['end']])
            ->where('status', 'reconciled');

        if ($bankAccountId) {
            $previousQuery->where('bank_account_id', $bankAccountId);
        }

        $previousStats = [
            'credits' => $previousQuery->where('type', 'credit')->sum('amount'),
            'debits' => $previousQuery->where('type', 'debit')->sum('amount'),
        ];
        $previousStats['balance'] = $previousStats['credits'] - $previousStats['debits'];

        $bankAccounts = BankAccount::where('active', true)->get();
        
        return view('reports.cash-flow', compact(
            'dailyFlow', 
            'startDate', 
            'endDate', 
            'periodStats', 
            'weeklyFlow', 
            'previousStats', 
            'bankAccounts'
        ));
    }
}