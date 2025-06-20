<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
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
        $imports = StatementImport::with('bankAccount', 'importer')
            ->latest()
            ->paginate(20);

        return view('imports.index', compact('imports'));
    }

    public function create()
    {
        $bankAccounts = BankAccount::where('active', true)->get();
        return view('imports.create', compact('bankAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'file' => 'required|file|mimes:csv,txt,ofx,qif|max:10240',
            'file_type' => 'required|in:csv,ofx,qif',
        ]);

        $file = $request->file('file');
        $bankAccount = BankAccount::find($validated['bank_account_id']);

        DB::transaction(function () use ($file, $bankAccount, $validated, &$import) {
            $import = StatementImport::create([
                'bank_account_id' => $bankAccount->id,
                'filename' => $file->getClientOriginalName(),
                'file_type' => $validated['file_type'],
                'status' => 'processing',
                'imported_by' => auth()->id() ?? 1,
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
        return view('imports.show', compact('import'));
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
}