<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\ImportLog;
use App\Models\Transaction;
use App\Models\StatementImport;
use App\Services\StatementImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportController extends Controller
{
    protected $importService;

    public function __construct(StatementImportService $importService)
    {
        $this->importService = $importService;
    }

    public function index()
    {
        // Usar StatementImport como fallback se ImportLog não existir/funcionar
        try {
            // Tentar usar ImportLog se disponível
            \App\Models\ImportLog::count(); // Teste se a tabela existe
            
            $imports = ImportLog::with('bankAccount')
                ->latest()
                ->paginate(20);

            // Estatísticas para o painel
            $stats = [
                'total_imports' => ImportLog::count(),
                'successful_imports' => ImportLog::where('status', 'completed')->count(),  
                'failed_imports' => ImportLog::where('status', 'failed')->count(),
                'processing_imports' => ImportLog::where('status', 'processing')->count(),
                'total_transactions_imported' => ImportLog::sum('imported_records'),
                'total_files_processed' => ImportLog::count(),
            ];

            // Importações recentes
            $recentImports = ImportLog::with('bankAccount')
                ->latest()
                ->limit(5)
                ->get();
                
        } catch (\Exception $e) {
            // Fallback usando StatementImport se ImportLog falhar
            $imports = StatementImport::with('bankAccount')
                ->latest()
                ->paginate(20);

            // Estatísticas usando dados disponíveis
            $stats = [
                'total_imports' => StatementImport::count(),
                'successful_imports' => StatementImport::where('status', 'completed')->count(),
                'failed_imports' => StatementImport::where('status', 'failed')->count(), 
                'processing_imports' => StatementImport::where('status', 'processing')->count(),
                'total_transactions_imported' => Transaction::count(),
                'total_files_processed' => StatementImport::count(),
            ];

            // Importações recentes
            $recentImports = StatementImport::with('bankAccount')
                ->latest()
                ->limit(5)
                ->get();
        }

        // Contas bancárias para filtros
        $bankAccounts = BankAccount::where('active', true)->get();

        // Tipos de arquivo suportados
        $supportedFormats = ['csv', 'txt', 'ofx', 'qif', 'pdf', 'xls', 'xlsx'];

        return view('imports.index', compact('imports', 'stats', 'recentImports', 'bankAccounts', 'supportedFormats'));
    }

    public function create()
    {
        $bankAccounts = BankAccount::where('active', true)->get();
        
        // Estatísticas das contas para ajudar na seleção
        $accountStats = [];
        foreach ($bankAccounts as $account) {
            $accountStats[$account->id] = [
                'last_import' => StatementImport::where('bank_account_id', $account->id)
                    ->where('status', 'completed')
                    ->latest()
                    ->first(),
                'total_imports' => StatementImport::where('bank_account_id', $account->id)->count(),
                'transaction_count' => $account->transactions()->count(),
                'current_balance' => $account->balance ?? 0,
            ];
        }

        // Formatos suportados e suas descrições
        $fileFormats = [
            'csv' => [
                'name' => 'CSV',
                'description' => 'Comma Separated Values - arquivo de texto separado por vírgulas',
                'max_size' => '20MB',
                'sample_columns' => 'Data, Descrição, Valor, Tipo',
                'extensions' => ['csv', 'txt'],
            ],
            'ofx' => [
                'name' => 'OFX',
                'description' => 'Open Financial Exchange - formato padrão bancário',
                'max_size' => '20MB',
                'sample_columns' => 'Formato XML estruturado',
                'extensions' => ['ofx'],
            ],
            'qif' => [
                'name' => 'QIF',
                'description' => 'Quicken Interchange Format - formato do Quicken',
                'max_size' => '20MB',
                'sample_columns' => 'Formato de texto estruturado',
                'extensions' => ['qif'],
            ],
            'pdf' => [
                'name' => 'PDF',
                'description' => 'Extratos bancários em PDF - extração automática via OCR',
                'max_size' => '20MB',
                'sample_columns' => 'Extração via OCR e análise de padrões',
                'extensions' => ['pdf'],
            ],
            'excel' => [
                'name' => 'Excel',
                'description' => 'Planilhas Excel/LibreOffice - XLS e XLSX',
                'max_size' => '20MB',
                'sample_columns' => 'Primeira linha como cabeçalho',
                'extensions' => ['xls', 'xlsx'],
            ],
        ];

        return view('imports.create', compact('bankAccounts', 'accountStats', 'fileFormats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'file' => 'required|file|mimes:csv,txt,ofx,qif,pdf,xls,xlsx|max:20480',
            'file_type' => 'required|in:csv,ofx,qif,pdf,excel',
        ]);

        $file = $request->file('file');
        $bankAccount = BankAccount::find($validated['bank_account_id']);

        DB::transaction(function () use ($file, $bankAccount, $validated, &$import) {
            $import = StatementImport::create([
                'bank_account_id' => $bankAccount->id,
                'filename' => $file->getClientOriginalName(),
                'file_type' => $validated['file_type'],
                'status' => 'processing',
                'imported_by' => 1, // TODO: Implementar autenticação
                'total_transactions' => 0,
                'imported_transactions' => 0,
                'duplicate_transactions' => 0,
                'error_transactions' => 0,
            ]);

            try {
                $result = $this->importService->import($file, $bankAccount, $validated['file_type']);
                
                $import->update([
                    'status' => 'completed',
                    'total_transactions' => $result['total'],
                    'imported_transactions' => $result['imported'],
                    'duplicate_transactions' => $result['duplicates'],
                    'error_transactions' => $result['errors'],
                    'import_log' => $result['log'],
                ]);
            } catch (\Exception $e) {
                $import->update([
                    'status' => 'failed',
                    'import_log' => ['error' => $e->getMessage()],
                ]);
                throw $e;
            }
        });

        return redirect()->route('imports.show', $import)
            ->with('success', 'Arquivo importado com sucesso!');
    }

    public function show(StatementImport $import)
    {
        $import->load('bankAccount', 'importer');
        
        // Transações relacionadas à importação
        $transactions = $import->transactions()
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(50);

        // Estatísticas detalhadas da importação
        $detailedStats = [
            'success_rate' => $import->total_transactions > 0 
                ? round(($import->imported_transactions / $import->total_transactions) * 100, 2) 
                : 0,
            'duplicate_rate' => $import->total_transactions > 0 
                ? round(($import->duplicate_transactions / $import->total_transactions) * 100, 2) 
                : 0,
            'error_rate' => $import->total_transactions > 0 
                ? round(($import->error_transactions / $import->total_transactions) * 100, 2) 
                : 0,
            'processing_time' => $import->created_at->diffForHumans($import->updated_at),
            'file_size_formatted' => 'N/A', // Campo removido pois não existe na tabela
        ];

        // Análise por categoria das transações importadas
        $categoryAnalysis = $import->transactions()
            ->select('category')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(amount) as total')
            ->selectRaw('type')
            ->groupBy('category', 'type')
            ->get()
            ->groupBy('category');

        // Log de erros se houver
        $errorLog = [];
        if ($import->status === 'failed' && $import->import_log) {
            $errorLog = is_array($import->import_log) ? $import->import_log : json_decode($import->import_log, true);
        }

        return view('imports.show', compact(
            'import', 
            'transactions', 
            'detailedStats', 
            'categoryAnalysis', 
            'errorLog'
        ));
    }

    public function downloadTemplate($type)
    {
        $templates = [
            'csv' => 'templates/import-template.csv',
            'ofx' => 'templates/import-template.ofx',
        ];

        if (!isset($templates[$type])) {
            abort(404);
        }

        return response()->download(storage_path('app/' . $templates[$type]));
    }

    /**
     * Remove the specified import and its transactions.
     */
    public function destroy(StatementImport $import)
    {
        if (!$import->canBeDeleted()) {
            return back()->with('error', 'Não é possível excluir esta importação pois possui transações já conciliadas.');
        }

        DB::transaction(function () use ($import) {
            // Deletar transações não conciliadas relacionadas
            $deletedCount = $import->transactions()
                ->where('status', '!=', 'reconciled')
                ->delete();

            // Deletar a importação
            $import->delete();

            // Atualizar saldo da conta
            $import->bankAccount->updateBalance();

            session()->flash('success', "Importação excluída com sucesso! {$deletedCount} transações foram removidas.");
        });

        return redirect()->route('imports.index');
    }

    /**
     * Get import statistics by period
     */
    public function getImportStats($period = 'month')
    {
        $periodMap = [
            'week' => 7,
            'month' => 30,
            'quarter' => 90,
            'year' => 365
        ];

        $days = $periodMap[$period] ?? 30;
        $startDate = now()->subDays($days);

        $stats = StatementImport::where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(imported_transactions) as total_imported'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as successful'),
                DB::raw('SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($stats);
    }
}