<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Reconciliation;
use App\Models\BankAccount;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportExportService
{
    /**
     * Exporta relatório de transações para CSV
     */
    public function exportTransactionsToCsv(Request $request)
    {
        $query = Transaction::with(['bankAccount', 'category']);
        
        // Aplicar filtros
        $this->applyTransactionFilters($query, $request);
        
        $transactions = $query->orderBy('transaction_date', 'desc')->get();
        
        $filename = 'relatorio_transacoes_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // Cabeçalho CSV
            fputcsv($file, [
                'ID',
                'Data',
                'Descrição',
                'Valor',
                'Tipo',
                'Categoria',
                'Conta Bancária',
                'Status',
                'Referência Externa',
                'Observações'
            ]);
            
            // Dados
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->id,
                    $transaction->transaction_date->format('d/m/Y'),
                    $transaction->description,
                    number_format($transaction->amount, 2, ',', '.'),
                    $transaction->type == 'credit' ? 'Crédito' : 'Débito',
                    $transaction->category_id ?? 'Sem categoria',
                    $transaction->bankAccount->name ?? 'N/A',
                    $this->getStatusLabel($transaction->status),
                    $transaction->external_reference ?? '',
                    $transaction->notes ?? ''
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Exporta relatório de conciliações para CSV
     */
    public function exportReconciliationsToCsv(Request $request)
    {
        $query = Reconciliation::with(['bankAccount', 'creator', 'approver']);
        
        // Aplicar filtros
        $this->applyReconciliationFilters($query, $request);
        
        $reconciliations = $query->orderBy('created_at', 'desc')->get();
        
        $filename = 'relatorio_conciliacoes_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($reconciliations) {
            $file = fopen('php://output', 'w');
            
            // Cabeçalho CSV
            fputcsv($file, [
                'ID',
                'Conta Bancária',
                'Período',
                'Saldo Inicial',
                'Saldo Final',
                'Status',
                'Diferença',
                'Criado por',
                'Data Criação',
                'Aprovado por',
                'Data Aprovação'
            ]);
            
            // Dados
            foreach ($reconciliations as $reconciliation) {
                fputcsv($file, [
                    $reconciliation->id,
                    $reconciliation->bankAccount->name ?? 'N/A',
                    $reconciliation->period_start->format('d/m/Y') . ' - ' . $reconciliation->period_end->format('d/m/Y'),
                    number_format($reconciliation->opening_balance, 2, ',', '.'),
                    number_format($reconciliation->closing_balance, 2, ',', '.'),
                    $this->getReconciliationStatusLabel($reconciliation->status),
                    number_format($reconciliation->difference_amount, 2, ',', '.'),
                    $reconciliation->creator->name ?? 'N/A',
                    $reconciliation->created_at->format('d/m/Y H:i'),
                    $reconciliation->approver->name ?? 'Não aprovado',
                    $reconciliation->approved_at ? $reconciliation->approved_at->format('d/m/Y H:i') : 'N/A'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Exporta relatório de fluxo de caixa para CSV
     */
    public function exportCashFlowToCsv(Request $request)
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
        
        // Calcular saldo cumulativo
        $cumulativeBalance = 0;
        $dailyFlow = $dailyFlow->map(function ($day) use (&$cumulativeBalance) {
            $day->daily_balance = $day->credits - $day->debits;
            $cumulativeBalance += $day->daily_balance;
            $day->cumulative_balance = $cumulativeBalance;
            return $day;
        });
        
        $filename = 'fluxo_caixa_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($dailyFlow) {
            $file = fopen('php://output', 'w');
            
            // Cabeçalho CSV
            fputcsv($file, [
                'Data',
                'Créditos',
                'Débitos',
                'Saldo Diário',
                'Saldo Cumulativo',
                'Qtd Transações'
            ]);
            
            // Dados
            foreach ($dailyFlow as $day) {
                fputcsv($file, [
                    $day->transaction_date,
                    number_format($day->credits, 2, ',', '.'),
                    number_format($day->debits, 2, ',', '.'),
                    number_format($day->daily_balance, 2, ',', '.'),
                    number_format($day->cumulative_balance, 2, ',', '.'),
                    $day->transaction_count
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Exporta relatório de categorias para CSV
     */
    public function exportCategoriesToCsv(Request $request)
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
            ->orderByRaw('SUM(amount) DESC')
            ->get();
        
        $filename = 'relatorio_categorias_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($categorySummary) {
            $file = fopen('php://output', 'w');
            
            // Cabeçalho CSV
            fputcsv($file, [
                'Categoria',
                'Tipo',
                'Quantidade',
                'Total',
                'Média',
                'Valor Mínimo',
                'Valor Máximo'
            ]);
            
            // Dados
            foreach ($categorySummary as $category) {
                fputcsv($file, [
                    $category->category_id ?? 'Sem categoria',
                    $category->type == 'credit' ? 'Crédito' : 'Débito',
                    $category->count,
                    number_format($category->total, 2, ',', '.'),
                    number_format($category->average, 2, ',', '.'),
                    number_format($category->min_amount, 2, ',', '.'),
                    number_format($category->max_amount, 2, ',', '.')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Gera dados para PDF dos relatórios
     */
    public function generateReportData($type, Request $request)
    {
        switch ($type) {
            case 'transactions':
                return $this->getTransactionsReportData($request);
            case 'reconciliations':
                return $this->getReconciliationsReportData($request);
            case 'cash-flow':
                return $this->getCashFlowReportData($request);
            case 'categories':
                return $this->getCategoriesReportData($request);
            default:
                throw new \InvalidArgumentException('Tipo de relatório inválido');
        }
    }
    
    /**
     * Aplica filtros na query de transações
     */
    private function applyTransactionFilters($query, Request $request)
    {
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
    }
    
    /**
     * Aplica filtros na query de conciliações
     */
    private function applyReconciliationFilters($query, Request $request)
    {
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
    }
    
    /**
     * Obtém dados do relatório de transações
     */
    private function getTransactionsReportData(Request $request)
    {
        $query = Transaction::with(['bankAccount', 'category']);
        $this->applyTransactionFilters($query, $request);
        
        $transactions = $query->orderBy('transaction_date', 'desc')->get();
        
        $summary = [
            'total_credit' => $transactions->where('type', 'credit')->sum('amount'),
            'total_debit' => $transactions->where('type', 'debit')->sum('amount'),
            'count' => $transactions->count(),
            'avg_transaction' => $transactions->avg('amount'),
        ];
        $summary['balance'] = $summary['total_credit'] - $summary['total_debit'];
        
        return [
            'title' => 'Relatório de Transações',
            'transactions' => $transactions,
            'summary' => $summary,
            'filters' => $request->all()
        ];
    }
    
    /**
     * Obtém dados do relatório de conciliações
     */
    private function getReconciliationsReportData(Request $request)
    {
        $query = Reconciliation::with(['bankAccount', 'creator', 'approver']);
        $this->applyReconciliationFilters($query, $request);
        
        $reconciliations = $query->orderBy('created_at', 'desc')->get();
        
        $stats = [
            'total' => $reconciliations->count(),
            'approved' => $reconciliations->where('status', 'approved')->count(),
            'pending' => $reconciliations->whereIn('status', ['draft', 'completed'])->count(),
        ];
        
        return [
            'title' => 'Relatório de Conciliações',
            'reconciliations' => $reconciliations,
            'stats' => $stats,
            'filters' => $request->all()
        ];
    }
    
    /**
     * Obtém dados do relatório de fluxo de caixa
     */
    private function getCashFlowReportData(Request $request)
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

        $periodStats = [
            'total_credits' => $dailyFlow->sum('credits'),
            'total_debits' => $dailyFlow->sum('debits'),
            'net_flow' => $dailyFlow->sum('credits') - $dailyFlow->sum('debits'),
            'avg_daily_credits' => $dailyFlow->count() > 0 ? $dailyFlow->sum('credits') / $dailyFlow->count() : 0,
            'avg_daily_debits' => $dailyFlow->count() > 0 ? $dailyFlow->sum('debits') / $dailyFlow->count() : 0,
        ];
        
        return [
            'title' => 'Relatório de Fluxo de Caixa',
            'dailyFlow' => $dailyFlow,
            'periodStats' => $periodStats,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'filters' => $request->all()
        ];
    }
    
    /**
     * Obtém dados do relatório de categorias
     */
    private function getCategoriesReportData(Request $request)
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
            ->orderByRaw('SUM(amount) DESC')
            ->get()
            ->groupBy('category_id');

        $stats = [
            'total_categories' => $categorySummary->count(),
            'total_amount' => $query->sum('amount'),
            'avg_per_category' => $categorySummary->count() > 0 ? $query->sum('amount') / $categorySummary->count() : 0,
        ];
        
        return [
            'title' => 'Relatório de Categorias',
            'categorySummary' => $categorySummary,
            'stats' => $stats,
            'filters' => $request->all()
        ];
    }
    
    /**
     * Obtém o label do status da transação
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'pending' => 'Pendente',
            'reconciled' => 'Conciliado',
            'cancelled' => 'Cancelado',
            'draft' => 'Rascunho'
        ];
        
        return $labels[$status] ?? $status;
    }
    
    /**
     * Obtém o label do status da conciliação
     */
    private function getReconciliationStatusLabel($status)
    {
        $labels = [
            'draft' => 'Rascunho',
            'completed' => 'Concluído',
            'approved' => 'Aprovado',
            'rejected' => 'Rejeitado'
        ];
        
        return $labels[$status] ?? $status;
    }
}
