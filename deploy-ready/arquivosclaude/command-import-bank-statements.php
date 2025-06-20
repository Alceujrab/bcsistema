<?php

namespace App\Console\Commands;

use App\Models\BankAccount;
use App\Services\StatementImportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportBankStatements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bank:import {--account=all : ID da conta ou "all" para todas}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa extratos bancários automaticamente';

    protected $importService;

    /**
     * Create a new command instance.
     */
    public function __construct(StatementImportService $importService)
    {
        parent::__construct();
        $this->importService = $importService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $accountId = $this->option('account');
        
        if ($accountId === 'all') {
            $accounts = BankAccount::where('active', true)->get();
        } else {
            $accounts = BankAccount::where('id', $accountId)->get();
        }
        
        if ($accounts->isEmpty()) {
            $this->error('Nenhuma conta encontrada.');
            return 1;
        }
        
        $this->info('Iniciando importação de extratos...');
        
        foreach ($accounts as $account) {
            $this->info("Processando conta: {$account->name}");
            
            // Verificar pasta de importação
            $importPath = "imports/{$account->id}";
            
            if (!Storage::exists($importPath)) {
                Storage::makeDirectory($importPath);
                $this->warn("Pasta de importação criada para conta {$account->name}");
                continue;
            }
            
            $files = Storage::files($importPath);
            
            if (empty($files)) {
                $this->info("Nenhum arquivo encontrado para importar na conta {$account->name}");
                continue;
            }
            
            foreach ($files as $file) {
                try {
                    $this->info("Importando arquivo: " . basename($file));
                    
                    // Determinar tipo do arquivo pela extensão
                    $extension = pathinfo($file, PATHINFO_EXTENSION);
                    $fileType = strtolower($extension);
                    
                    if (!in_array($fileType, ['csv', 'ofx', 'qif'])) {
                        $this->warn("Tipo de arquivo não suportado: {$extension}");
                        continue;
                    }
                    
                    // Criar arquivo temporário para processar
                    $tempPath = sys_get_temp_dir() . '/' . basename($file);
                    file_put_contents($tempPath, Storage::get($file));
                    
                    // Criar UploadedFile
                    $uploadedFile = new \Illuminate\Http\UploadedFile(
                        $tempPath,
                        basename($file),
                        Storage::mimeType($file),
                        null,
                        true
                    );
                    
                    // Processar arquivo
                    $result = $this->importService->import($uploadedFile, $account, $fileType);
                    
                    $this->info("Importação concluída:");
                    $this->info("- Total: {$result['total']} transações");
                    $this->info("- Importadas: {$result['imported']} transações");
                    $this->info("- Duplicadas: {$result['duplicates']} transações");
                    $this->info("- Erros: {$result['errors']} transações");
                    
                    // Mover para pasta de processados
                    $processedPath = "imports/processed/" . date('Y-m-d') . "/" . basename($file);
                    Storage::move($file, $processedPath);
                    
                    // Limpar arquivo temporário
                    unlink($tempPath);
                    
                } catch (\Exception $e) {
                    $this->error("Erro ao importar {$file}: {$e->getMessage()}");
                }
            }
        }
        
        $this->info('Importação concluída!');
        return 0;
    }
}