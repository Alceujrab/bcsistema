<?php

namespace App\Services;

use App\Models\Update;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class UpdateService
{
    protected $updateUrl;
    protected $currentVersion;
    protected $backupPath;
    protected $updatePath;

    public function __construct()
    {
        $this->updateUrl = config('updater.update_url');
        $this->currentVersion = config('app.version', '1.0.0');
        $this->backupPath = storage_path('app/backups');
        $this->updatePath = storage_path('app/updates');

        // Criar diretórios se não existirem
        if (!File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }
        if (!File::exists($this->updatePath)) {
            File::makeDirectory($this->updatePath, 0755, true);
        }
    }

    /**
     * Verificar atualizações disponíveis
     */
    public function checkForUpdates($force = false)
    {
        try {
            $cacheKey = 'system_updates_check';
            $cacheTime = 3600; // 1 hora

            if (!$force && cache()->has($cacheKey)) {
                return cache()->get($cacheKey);
            }

            Log::info('Verificando atualizações no servidor remoto');

            $response = Http::timeout(30)->get($this->updateUrl . '/check', [
                'current_version' => $this->currentVersion,
                'system_info' => $this->getSystemInfo()
            ]);

            if (!$response->successful()) {
                throw new \Exception('Erro ao conectar com servidor de atualizações: ' . $response->status());
            }

            $updates = $response->json('updates', []);

            // Armazenar no cache
            cache()->put($cacheKey, $updates, $cacheTime);

            // Sincronizar com banco de dados local
            $this->syncUpdatesWithDatabase($updates);

            return $updates;

        } catch (\Exception $e) {
            Log::error('Erro ao verificar atualizações: ' . $e->getMessage());
            
            // Retornar atualizações do banco local como fallback
            return Update::available()->latest()->get()->map(function($update) {
                return [
                    'version' => $update->version,
                    'name' => $update->name,
                    'description' => $update->description,
                    'release_date' => $update->release_date->format('d/m/Y H:i'),
                    'download_url' => $update->download_url,
                    'file_size' => $update->file_size,
                    'changes' => $update->changes,
                    'requirements' => $update->requirements,
                    'status' => $update->status
                ];
            })->toArray();
        }
    }

    /**
     * Baixar uma atualização
     */
    public function downloadUpdate(Update $update)
    {
        try {
            $update->update(['status' => Update::STATUS_DOWNLOADING]);

            $downloadUrl = $update->download_url;
            $filename = "update_{$update->version}.zip";
            $filePath = $this->updatePath . '/' . $filename;

            Log::info("Baixando atualização {$update->version} de {$downloadUrl}");

            $response = Http::timeout(300)->get($downloadUrl);

            if (!$response->successful()) {
                throw new \Exception('Erro ao baixar atualização: ' . $response->status());
            }

            File::put($filePath, $response->body());

            // Verificar hash do arquivo
            if ($update->file_hash && hash_file('sha256', $filePath) !== $update->file_hash) {
                File::delete($filePath);
                throw new \Exception('Hash do arquivo não confere. Download corrompido.');
            }

            $update->update(['status' => Update::STATUS_READY]);

            Log::info("Atualização {$update->version} baixada com sucesso");

            return [
                'success' => true,
                'file_path' => $filePath,
                'message' => 'Atualização baixada com sucesso'
            ];

        } catch (\Exception $e) {
            $update->update([
                'status' => Update::STATUS_FAILED,
                'error_message' => $e->getMessage()
            ]);

            Log::error("Erro ao baixar atualização {$update->version}: {$e->getMessage()}");

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Aplicar uma atualização
     */
    public function applyUpdate(Update $update, $createBackup = true)
    {
        DB::beginTransaction();
        
        try {
            Log::info("Iniciando aplicação da atualização {$update->version}");

            // Criar backup se solicitado
            if ($createBackup) {
                $backupResult = $this->createBackup();
                if (!$backupResult['success']) {
                    throw new \Exception('Falha ao criar backup: ' . $backupResult['error']);
                }
            }

            // Verificar arquivo de atualização
            $updateFile = $this->updatePath . "/update_{$update->version}.zip";
            if (!File::exists($updateFile)) {
                $downloadResult = $this->downloadUpdate($update);
                if (!$downloadResult['success']) {
                    throw new \Exception('Falha ao baixar atualização: ' . $downloadResult['error']);
                }
                $updateFile = $downloadResult['file_path'];
            }

            // Extrair atualização
            $extractPath = $this->updatePath . "/extracted_{$update->version}";
            $this->extractUpdate($updateFile, $extractPath);

            // Aplicar arquivos
            $this->applyFiles($extractPath);

            // Executar migrations se houver
            $this->runMigrations($extractPath);

            // Executar comandos customizados se houver
            $this->runCustomCommands($extractPath);

            // Limpar cache
            $this->clearCache();

            // Marcar como aplicada
            $update->update([
                'status' => Update::STATUS_APPLIED,
                'applied_at' => now()
            ]);

            DB::commit();

            Log::info("Atualização {$update->version} aplicada com sucesso");

            return [
                'success' => true,
                'message' => 'Atualização aplicada com sucesso'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            $update->update([
                'status' => Update::STATUS_FAILED,
                'error_message' => $e->getMessage()
            ]);

            Log::error("Erro ao aplicar atualização {$update->version}: {$e->getMessage()}");

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Criar backup do sistema
     */
    public function createBackup()
    {
        try {
            $timestamp = now()->format('Y-m-d_H-i-s');
            $backupName = "backup_{$this->currentVersion}_{$timestamp}";
            $backupFile = $this->backupPath . "/{$backupName}.zip";

            Log::info("Criando backup do sistema: {$backupName}");

            $zip = new ZipArchive();
            if ($zip->open($backupFile, ZipArchive::CREATE) !== TRUE) {
                throw new \Exception('Não foi possível criar arquivo de backup');
            }

            // Adicionar arquivos do projeto (exceto storage e vendor)
            $this->addDirectoryToZip($zip, base_path(), '', [
                'storage', 'vendor', 'node_modules', '.git', 'tests'
            ]);

            // Backup do banco de dados
            $this->backupDatabase($zip);

            $zip->close();

            Log::info("Backup criado com sucesso: {$backupFile}");

            return [
                'success' => true,
                'backup_file' => $backupFile,
                'backup_name' => $backupName
            ];

        } catch (\Exception $e) {
            Log::error("Erro ao criar backup: {$e->getMessage()}");
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Extrair arquivo de atualização
     */
    protected function extractUpdate($updateFile, $extractPath)
    {
        if (File::exists($extractPath)) {
            File::deleteDirectory($extractPath);
        }

        File::makeDirectory($extractPath, 0755, true);

        $zip = new ZipArchive();
        if ($zip->open($updateFile) !== TRUE) {
            throw new \Exception('Não foi possível abrir arquivo de atualização');
        }

        $zip->extractTo($extractPath);
        $zip->close();
    }

    /**
     * Aplicar arquivos da atualização
     */
    protected function applyFiles($extractPath)
    {
        $filesPath = $extractPath . '/files';
        
        if (!File::exists($filesPath)) {
            return; // Nenhum arquivo para aplicar
        }

        Log::info('Aplicando arquivos da atualização');

        // Copiar arquivos recursivamente
        $this->copyDirectory($filesPath, base_path());
    }

    /**
     * Executar migrations
     */
    protected function runMigrations($extractPath)
    {
        $migrationsPath = $extractPath . '/migrations';
        
        if (!File::exists($migrationsPath)) {
            return; // Nenhuma migration para executar
        }

        Log::info('Executando migrations da atualização');

        // Copiar migrations para o diretório correto
        $targetMigrationsPath = database_path('migrations');
        File::copyDirectory($migrationsPath, $targetMigrationsPath);

        // Executar migrations
        Artisan::call('migrate', ['--force' => true]);
    }

    /**
     * Executar comandos customizados
     */
    protected function runCustomCommands($extractPath)
    {
        $commandsFile = $extractPath . '/commands.json';
        
        if (!File::exists($commandsFile)) {
            return; // Nenhum comando para executar
        }

        Log::info('Executando comandos customizados da atualização');

        $commands = json_decode(File::get($commandsFile), true);

        foreach ($commands as $command) {
            if (isset($command['artisan'])) {
                Artisan::call($command['artisan'], $command['params'] ?? []);
            } elseif (isset($command['shell'])) {
                exec($command['shell']);
            }
        }
    }

    /**
     * Limpar cache do sistema
     */
    protected function clearCache()
    {
        Log::info('Limpando cache do sistema');

        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }

    /**
     * Sincronizar atualizações com banco de dados
     */
    protected function syncUpdatesWithDatabase(array $updates)
    {
        foreach ($updates as $updateData) {
            Update::updateOrCreate(
                ['version' => $updateData['version']],
                $updateData
            );
        }
    }

    /**
     * Obter informações do sistema
     */
    protected function getSystemInfo()
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'os' => PHP_OS,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'current_version' => $this->currentVersion
        ];
    }

    /**
     * Adicionar diretório ao ZIP (recursivo)
     */
    protected function addDirectoryToZip(ZipArchive $zip, $dir, $zipPath = '', array $exclude = [])
    {
        if (is_dir($dir)) {
            $files = scandir($dir);
            
            foreach ($files as $file) {
                if ($file != '.' && $file != '..' && !in_array($file, $exclude)) {
                    $filePath = $dir . '/' . $file;
                    $zipFilePath = $zipPath . $file;
                    
                    if (is_dir($filePath)) {
                        $zip->addEmptyDir($zipFilePath);
                        $this->addDirectoryToZip($zip, $filePath, $zipFilePath . '/', $exclude);
                    } else {
                        $zip->addFile($filePath, $zipFilePath);
                    }
                }
            }
        }
    }

    /**
     * Backup do banco de dados
     */
    protected function backupDatabase(ZipArchive $zip)
    {
        try {
            $dbConnection = config('database.default');
            $dbConfig = config("database.connections.{$dbConnection}");

            if ($dbConfig['driver'] === 'sqlite') {
                $dbPath = $dbConfig['database'];
                if (File::exists($dbPath)) {
                    $zip->addFile($dbPath, 'database/database.sqlite');
                }
            } else {
                // Para MySQL/PostgreSQL, criar dump SQL
                $dumpFile = storage_path('app/temp_db_dump.sql');
                
                if ($dbConfig['driver'] === 'mysql') {
                    $command = sprintf(
                        'mysqldump -h%s -u%s -p%s %s > %s',
                        $dbConfig['host'],
                        $dbConfig['username'],
                        $dbConfig['password'],
                        $dbConfig['database'],
                        $dumpFile
                    );
                    exec($command);
                }

                if (File::exists($dumpFile)) {
                    $zip->addFile($dumpFile, 'database/dump.sql');
                    File::delete($dumpFile);
                }
            }
        } catch (\Exception $e) {
            Log::warning("Não foi possível fazer backup do banco de dados: {$e->getMessage()}");
        }
    }

    /**
     * Copiar diretório recursivamente
     */
    protected function copyDirectory($source, $destination)
    {
        if (!File::exists($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        $files = File::allFiles($source);
        
        foreach ($files as $file) {
            $relativePath = $file->getRelativePathname();
            $destinationFile = $destination . '/' . $relativePath;
            
            // Criar diretório se necessário
            $destinationDir = dirname($destinationFile);
            if (!File::exists($destinationDir)) {
                File::makeDirectory($destinationDir, 0755, true);
            }
            
            File::copy($file->getPathname(), $destinationFile);
        }
    }

    /**
     * Obter lista de backups
     */
    public function getBackups()
    {
        $backups = [];
        $files = File::files($this->backupPath);
        
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'zip') {
                $backups[] = [
                    'name' => basename($file, '.zip'),
                    'file' => $file,
                    'size' => File::size($file),
                    'created_at' => File::lastModified($file)
                ];
            }
        }
        
        // Ordenar por data de criação (mais recente primeiro)
        usort($backups, function($a, $b) {
            return $b['created_at'] - $a['created_at'];
        });
        
        return $backups;
    }

    /**
     * Restaurar backup
     */
    public function restoreBackup($backupFile)
    {
        try {
            Log::info("Iniciando restauração do backup: {$backupFile}");

            if (!File::exists($backupFile)) {
                throw new \Exception('Arquivo de backup não encontrado');
            }

            $extractPath = $this->backupPath . '/restore_' . time();
            File::makeDirectory($extractPath, 0755, true);

            // Extrair backup
            $zip = new ZipArchive();
            if ($zip->open($backupFile) !== TRUE) {
                throw new \Exception('Não foi possível abrir arquivo de backup');
            }

            $zip->extractTo($extractPath);
            $zip->close();

            // Restaurar arquivos
            $this->copyDirectory($extractPath, base_path());

            // Restaurar banco de dados se houver
            $this->restoreDatabase($extractPath);

            // Limpar diretório temporário
            File::deleteDirectory($extractPath);

            Log::info("Backup restaurado com sucesso");

            return [
                'success' => true,
                'message' => 'Backup restaurado com sucesso'
            ];

        } catch (\Exception $e) {
            Log::error("Erro ao restaurar backup: {$e->getMessage()}");
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Restaurar banco de dados
     */
    protected function restoreDatabase($extractPath)
    {
        $dbConnection = config('database.default');
        $dbConfig = config("database.connections.{$dbConnection}");

        if ($dbConfig['driver'] === 'sqlite') {
            $backupDbFile = $extractPath . '/database/database.sqlite';
            if (File::exists($backupDbFile)) {
                File::copy($backupDbFile, $dbConfig['database']);
            }
        } else {
            $dumpFile = $extractPath . '/database/dump.sql';
            if (File::exists($dumpFile)) {
                if ($dbConfig['driver'] === 'mysql') {
                    $command = sprintf(
                        'mysql -h%s -u%s -p%s %s < %s',
                        $dbConfig['host'],
                        $dbConfig['username'],
                        $dbConfig['password'],
                        $dbConfig['database'],
                        $dumpFile
                    );
                    exec($command);
                }
            }
        }
    }
}
