<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ExtractImportService;
use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExtractImportController extends Controller
{
    protected $importService;

    public function __construct(ExtractImportService $importService)
    {
        $this->importService = $importService;
    }

    public function index()
    {
        $recentImports = $this->getRecentImports();
        $bankAccounts = BankAccount::all();
        $supportedFormats = $this->importService->getSupportedFormats();

        return view('imports.index', compact('recentImports', 'bankAccounts', 'supportedFormats'));
    }

    public function create()
    {
        $bankAccounts = BankAccount::all();
        $supportedFormats = $this->importService->getSupportedFormats();
        $bankPatterns = $this->importService->getBankPatterns();

        return view('imports.create', compact('bankAccounts', 'supportedFormats', 'bankPatterns'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'import_type' => 'required|in:bank_statement,credit_card',
            'encoding' => 'nullable|in:UTF-8,ISO-8859-1,Windows-1252',
            'delimiter' => 'nullable|in:;,|,\t',
            'date_format' => 'nullable|in:d/m/Y,m/d/Y,Y-m-d',
            'skip_duplicates' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            // Salvar arquivo temporariamente
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('imports', $filename);

            // Processar importação
            $options = [
                'encoding' => $request->encoding,
                'delimiter' => $request->delimiter,
                'date_format' => $request->date_format,
                'bank_account_id' => $request->bank_account_id,
                'import_type' => $request->import_type,
                'skip_duplicates' => $request->boolean('skip_duplicates')
            ];

            $result = $this->importService->importFile($file, $options);

            if (!$result['success']) {
                DB::rollBack();
                Storage::delete($path);
                
                return redirect()->back()
                    ->withErrors(['file' => $result['error']])
                    ->withInput();
            }

            // Salvar transações no banco
            $imported = $this->saveTransactions($result['transactions'], $request->bank_account_id, $options);

            // Salvar log da importação
            $this->logImport([
                'filename' => $file->getClientOriginalName(),
                'path' => $path,
                'bank_account_id' => $request->bank_account_id,
                'import_type' => $request->import_type,
                'total_records' => $result['count'],
                'imported_records' => $imported['created'],
                'skipped_records' => $imported['skipped'],
                'format' => $result['format'],
                'encoding' => $result['encoding'] ?? null,
                'delimiter' => $result['delimiter'] ?? null,
                'detected_bank' => $result['detected_bank'] ?? null
            ]);

            DB::commit();

            $message = "Importação concluída! {$imported['created']} transações importadas";
            if ($imported['skipped'] > 0) {
                $message .= ", {$imported['skipped']} duplicadas ignoradas";
            }

            return redirect()->route('imports.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            
            if (isset($path)) {
                Storage::delete($path);
            }

            return redirect()->back()
                ->withErrors(['file' => 'Erro ao processar arquivo: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show($id)
    {
        $import = $this->getImportById($id);
        $transactions = $this->getImportTransactions($id);

        return view('imports.show', compact('import', 'transactions'));
    }

    public function validate(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'bank_account_id' => 'required|exists:bank_accounts,id'
        ]);

        try {
            $file = $request->file('file');
            $result = $this->importService->importFile($file, [
                'validate_only' => true,
                'bank_account_id' => $request->bank_account_id
            ]);

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function downloadTemplate($type)
    {
        try {
            $templates = [
                'csv' => [
                    'filename' => 'template_extrato.csv',
                    'content' => "Data,Descrição,Valor,Saldo\n01/01/2024,\"Depósito em dinheiro\",1000.00,1000.00\n02/01/2024,\"Pagamento débito automático\",-150.00,850.00\n03/01/2024,\"Transferência recebida\",500.00,1350.00",
                    'mime' => 'text/csv'
                ],
                'ofx' => [
                    'filename' => 'exemplo_extrato.ofx',
                    'content' => $this->getOfxTemplate(),
                    'mime' => 'application/x-ofx'
                ],
                'qif' => [
                    'filename' => 'exemplo_extrato.qif',
                    'content' => $this->getQifTemplate(),
                    'mime' => 'text/plain'
                ]
            ];

            if (!isset($templates[$type])) {
                return response()->json(['error' => 'Tipo de template não encontrado'], 404);
            }

            $template = $templates[$type];
            
            return response($template['content'])
                ->header('Content-Type', $template['mime'])
                ->header('Content-Disposition', 'attachment; filename="' . $template['filename'] . '"');

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao gerar template'], 500);
        }
    }

    public function history()
    {
        $imports = $this->getRecentImports(50);
        return view('extract-imports.history', compact('imports'));
    }

    protected function saveTransactions($transactions, $bankAccountId, $options)
    {
        $created = 0;
        $skipped = 0;

        foreach ($transactions as $transactionData) {
            // Verificar duplicatas se solicitado
            if ($options['skip_duplicates']) {
                $exists = Transaction::where('bank_account_id', $bankAccountId)
                    ->where('date', $transactionData['date'])
                    ->where('amount', $transactionData['amount'])
                    ->where('description', $transactionData['description'])
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }
            }

            // Criar transação
            Transaction::create([
                'bank_account_id' => $bankAccountId,
                'date' => $transactionData['date'],
                'description' => $transactionData['description'],
                'amount' => $transactionData['amount'],
                'type' => $transactionData['type'],
                'balance' => $transactionData['balance'] ?? null,
                'memo' => $transactionData['memo'] ?? null,
                'imported' => true,
                'import_type' => $options['import_type']
            ]);

            $created++;
        }

        return ['created' => $created, 'skipped' => $skipped];
    }

    protected function logImport($data)
    {
        // Salvar log em tabela específica ou usar logs do Laravel
        \Log::info('Importação de extrato realizada', $data);
        
        // TODO: Criar tabela de logs de importação se necessário
    }

    protected function getRecentImports($limit = 10)
    {
        // TODO: Buscar importações recentes de uma tabela de logs
        return collect([]);
    }

    protected function getImportById($id)
    {
        // TODO: Buscar importação específica
        return null;
    }

    protected function getImportTransactions($id)
    {
        // TODO: Buscar transações de uma importação específica
        return collect([]);
    }

    protected function getOfxTemplate()
    {
        return <<<OFX
OFXHEADER:100
DATA:OFXSGML
VERSION:102
SECURITY:NONE
ENCODING:USASCII
CHARSET:1252
COMPRESSION:NONE
OLDFILEUID:NONE
NEWFILEUID:NONE

<OFX>
<SIGNONMSGSRSV1>
<SONRS>
<STATUS>
<CODE>0
<SEVERITY>INFO
</STATUS>
<DTSERVER>20240101000000
<LANGUAGE>POR
</SONRS>
</SIGNONMSGSRSV1>
<BANKMSGSRSV1>
<STMTTRNRS>
<TRNUID>1
<STATUS>
<CODE>0
<SEVERITY>INFO
</STATUS>
<STMTRS>
<CURDEF>BRL
<BANKACCTFROM>
<BANKID>123
<ACCTID>456789
<ACCTTYPE>CHECKING
</BANKACCTFROM>
<BANKTRANLIST>
<DTSTART>20240101000000
<DTEND>20240131000000
<STMTTRN>
<TRNTYPE>DEBIT
<DTPOSTED>20240101000000
<TRNAMT>-50.00
<FITID>202401010001
<NAME>Pagamento
</STMTTRN>
<STMTTRN>
<TRNTYPE>CREDIT
<DTPOSTED>20240102000000
<TRNAMT>1000.00
<FITID>202401020001
<NAME>Depósito
</STMTTRN>
</BANKTRANLIST>
<LEDGERBAL>
<BALAMT>950.00
<DTASOF>20240131000000
</LEDGERBAL>
</STMTRS>
</STMTTRNRS>
</BANKMSGSRSV1>
</OFX>
OFX;
    }

    protected function getQifTemplate()
    {
        return <<<QIF
!Account
NBanco do Brasil - Conta Corrente
TBank
^
!Type:Bank
D01/01/2024
T1000.00
PDepósito em dinheiro
^
D02/01/2024
T-150.00
PPagamento débito automático
^
D03/01/2024
T500.00
PTransferência recebida
^
QIF;
    }
}
