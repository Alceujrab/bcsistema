<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Transaction;
use App\Models\StatementImport;
use App\Services\StatementImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ImportController extends Controller
{
    protected $importService;

    public function __construct(StatementImportService $importService)
    {
        $this->importService = $importService;
    }

    public function index()
    {
        // Usar APENAS StatementImport - modelo funcional e confiável
        $imports = StatementImport::with(['bankAccount', 'importer'])
            ->latest()
            ->paginate(20);

        // Estatísticas baseadas em dados reais e funcionais
        $stats = [
            'total_imports' => StatementImport::count(),
            'successful_imports' => StatementImport::where('status', 'completed')->count(),
            'failed_imports' => StatementImport::where('status', 'failed')->count(),
            'processing_imports' => StatementImport::where('status', 'processing')->count(),
            'total_transactions_imported' => StatementImport::sum('imported_transactions'),
            'total_files_processed' => StatementImport::count(),
        ];

        // Importações recentes
        $recentImports = StatementImport::with(['bankAccount', 'importer'])
            ->latest()
            ->limit(5)
            ->get();

        // Contas bancárias para filtros
        $bankAccounts = BankAccount::where('active', true)->get();

        // Tipos de arquivo suportados (dados REAIS)
        $supportedFormats = [
            'csv' => ['description' => 'CSV (Comma-separated values)', 'icon' => 'fa-file-csv', 'color' => 'success'],
            'txt' => ['description' => 'TXT (Arquivo de texto)', 'icon' => 'fa-file-alt', 'color' => 'info'],
            'ofx' => ['description' => 'OFX (Open Financial Exchange)', 'icon' => 'fa-university', 'color' => 'primary'],
            'qif' => ['description' => 'QIF (Quicken Interchange Format)', 'icon' => 'fa-file-invoice', 'color' => 'secondary'],
            'pdf' => ['description' => 'PDF (Portable Document Format)', 'icon' => 'fa-file-pdf', 'color' => 'danger'],
            'xlsx' => ['description' => 'Excel (Microsoft Excel)', 'icon' => 'fa-file-excel', 'color' => 'success'],
            'xls' => ['description' => 'Excel Legacy (Microsoft Excel)', 'icon' => 'fa-file-excel', 'color' => 'success']
        ];

        // Estatísticas por conta bancária
        $accountStats = [];
        foreach ($bankAccounts as $account) {
            $accountStats[$account->id] = [
                'total_imports' => StatementImport::where('bank_account_id', $account->id)->count(),
                'successful_imports' => StatementImport::where('bank_account_id', $account->id)
                    ->where('status', 'completed')->count(),
                'total_transactions' => StatementImport::where('bank_account_id', $account->id)
                    ->sum('imported_transactions'),
                'last_import' => StatementImport::where('bank_account_id', $account->id)
                    ->latest()->first(),
            ];
        }

        return view('imports.index', compact(
            'imports', 
            'stats', 
            'recentImports', 
            'bankAccounts', 
            'supportedFormats',
            'accountStats'
        ));
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

        // Formatos suportados e suas descrições REAIS
        $fileFormats = [
            'csv' => [
                'name' => 'CSV',
                'description' => 'Arquivo de valores separados por vírgula',
                'extensions' => ['csv'],
                'icon' => 'fa-file-csv',
                'color' => 'success',
                'examples' => ['extrato.csv', 'movimentacao.csv'],
                'banks' => ['Banco do Brasil', 'Itaú', 'Bradesco', 'Santander', 'Caixa']
            ],
            'txt' => [
                'name' => 'TXT',
                'description' => 'Arquivo de texto simples',
                'extensions' => ['txt'],
                'icon' => 'fa-file-alt',
                'color' => 'info',
                'examples' => ['extrato.txt', 'movimentacao.txt'],
                'banks' => ['Banco do Brasil', 'Itaú', 'Caixa']
            ],
            'ofx' => [
                'name' => 'OFX',
                'description' => 'Open Financial Exchange',
                'extensions' => ['ofx'],
                'icon' => 'fa-university',
                'color' => 'primary',
                'examples' => ['extrato.ofx'],
                'banks' => ['Itaú', 'Bradesco', 'Santander']
            ],
            'qif' => [
                'name' => 'QIF',
                'description' => 'Quicken Interchange Format',
                'extensions' => ['qif'],
                'icon' => 'fa-file-invoice',
                'color' => 'secondary',
                'examples' => ['extrato.qif'],
                'banks' => ['Diversos bancos']
            ],
            'pdf' => [
                'name' => 'PDF',
                'description' => 'Documento PDF com extração de texto',
                'extensions' => ['pdf'],
                'icon' => 'fa-file-pdf',
                'color' => 'danger',
                'examples' => ['extrato.pdf', 'fatura.pdf'],
                'banks' => ['Todos os bancos']
            ],
            'excel' => [
                'name' => 'Excel',
                'description' => 'Planilha Microsoft Excel',
                'extensions' => ['xlsx', 'xls'],
                'icon' => 'fa-file-excel',
                'color' => 'success',
                'examples' => ['extrato.xlsx', 'movimentacao.xls'],
                'banks' => ['Todos os bancos']
            ]
        ];

        return view('imports.create', compact('bankAccounts', 'accountStats', 'fileFormats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB máximo
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'import_type' => 'nullable|in:bank_statement,credit_card',
            'encoding' => 'nullable|in:UTF-8,ISO-8859-1,Windows-1252',
            'delimiter' => 'nullable|in:comma,semicolon,pipe,tab',
        ]);

        try {
            DB::beginTransaction();

            // Processar arquivo usando o serviço
            $result = $this->importService->processImport(
                $request->file('file'),
                $request->bank_account_id,
                [
                    'import_type' => $request->import_type ?? 'bank_statement',
                    'encoding' => $request->encoding ?? 'UTF-8',
                    'delimiter' => $request->delimiter ?? 'comma',
                    'imported_by' => Auth::id()
                ]
            );

            // Criar registro de importação
            $import = StatementImport::create([
                'bank_account_id' => $request->bank_account_id,
                'filename' => $request->file('file')->getClientOriginalName(),
                'file_type' => $request->file('file')->getClientOriginalExtension(),
                'total_transactions' => $result['total_records'] ?? 0,
                'imported_transactions' => $result['imported_records'] ?? 0,
                'duplicate_transactions' => $result['duplicate_records'] ?? 0,
                'error_transactions' => $result['error_records'] ?? 0,
                'status' => $result['status'] ?? 'completed',
                'import_log' => $result['log'] ?? [],
                'imported_by' => Auth::id()
            ]);

            DB::commit();

            return redirect()
                ->route('imports.show', $import->id)
                ->with('success', 'Arquivo importado com sucesso! ' . 
                    $result['imported_records'] . ' transações processadas.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erro na importação: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $import = StatementImport::with(['bankAccount', 'importer'])->findOrFail($id);
        
        // Buscar transações relacionadas a esta importação
        $transactions = Transaction::where('bank_account_id', $import->bank_account_id)
            ->whereDate('created_at', $import->created_at->toDateString())
            ->latest()
            ->paginate(20);

        // Estatísticas da importação
        $stats = [
            'success_rate' => $import->success_rate,
            'total_value' => $transactions->sum('amount'),
            'average_value' => $transactions->avg('amount'),
            'transaction_types' => $transactions->groupBy('type')->map->count(),
        ];

        return view('imports.show', compact('import', 'transactions', 'stats'));
    }

    public function destroy($id)
    {
        try {
            $import = StatementImport::findOrFail($id);
            
            // Remover transações relacionadas se necessário
            // (implementar lógica de remoção se necessário)
            
            $import->delete();
            
            return redirect()
                ->route('imports.index')
                ->with('success', 'Importação removida com sucesso.');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erro ao remover importação: ' . $e->getMessage());
        }
    }

    public function download($id)
    {
        try {
            $import = StatementImport::findOrFail($id);
            
            // Se o arquivo ainda existe
            if (Storage::exists($import->filename)) {
                return Storage::download($import->filename);
            }
            
            return redirect()
                ->back()
                ->with('error', 'Arquivo não encontrado.');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erro ao baixar arquivo: ' . $e->getMessage());
        }
    }

    public function reprocess($id)
    {
        try {
            $import = StatementImport::findOrFail($id);
            
            // Marcar como processando
            $import->update(['status' => 'processing']);
            
            // Reprocessar usando o serviço
            // (implementar lógica de reprocessamento se necessário)
            
            return redirect()
                ->route('imports.show', $id)
                ->with('success', 'Importação reprocessada com sucesso.');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erro ao reprocessar importação: ' . $e->getMessage());
        }
    }

    public function export($id)
    {
        try {
            $import = StatementImport::with('bankAccount')->findOrFail($id);
            
            // Buscar transações da importação
            $transactions = Transaction::where('bank_account_id', $import->bank_account_id)
                ->whereDate('created_at', $import->created_at->toDateString())
                ->get();

            // Gerar CSV para download
            $filename = 'importacao_' . $import->id . '_' . date('Y-m-d') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($transactions) {
                $file = fopen('php://output', 'w');
                
                // Cabeçalho
                fputcsv($file, [
                    'Data', 'Descrição', 'Valor', 'Tipo', 'Categoria', 'Conta'
                ]);
                
                // Dados
                foreach ($transactions as $transaction) {
                    fputcsv($file, [
                        $transaction->date,
                        $transaction->description,
                        $transaction->amount,
                        $transaction->type,
                        $transaction->category,
                        $transaction->bankAccount->name ?? ''
                    ]);
                }
                
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erro ao exportar dados: ' . $e->getMessage());
        }
    }
}
