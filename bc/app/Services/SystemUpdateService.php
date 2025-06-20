<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class SystemUpdateService
{
    protected $updatePath;
    protected $backupPath;
    protected $currentVersion;
    
    public function __construct()
    {
        $this->updatePath = storage_path('app/updates');
        $this->backupPath = storage_path('app/backups');
        $this->currentVersion = config('app.version', '1.0.0');
        
        // Criar diretórios se não existirem
        if (!File::exists($this->updatePath)) {
            File::makeDirectory($this->updatePath, 0755, true);
        }
        
        if (!File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }
    }

    /**
     * Verificar se há atualizações disponíveis
     */
    public function hasUpdatesAvailable()
    {
        $updateFiles = File::files($this->updatePath);
        return count($updateFiles) > 0;
    }

    /**
     * Listar atualizações disponíveis
     */
    public function getAvailableUpdates()
    {
        $updates = [];
        $updateFiles = File::files($this->updatePath);
        
        foreach ($updateFiles as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'zip') {
                $updateInfo = $this->extractUpdateInfo($file);
                if ($updateInfo) {
                    $updates[] = $updateInfo;
                }
            }
        }
        
        // Ordenar por versão
        usort($updates, function($a, $b) {
            return version_compare($a['version'], $b['version']);
        });
        
        return $updates;
    }

    /**
     * Extrair informações do arquivo de atualização
     */
    protected function extractUpdateInfo($filePath)
    {
        $zip = new ZipArchive();
        
        if ($zip->open($filePath) === TRUE) {
            // Procurar arquivo update.json
            $updateJson = $zip->getFromName('update.json');
            
            if ($updateJson) {
                $updateData = json_decode($updateJson, true);
                $updateData['file_path'] = $filePath;
                $updateData['file_name'] = basename($filePath);
                $updateData['file_size'] = filesize($filePath);
                $updateData['created_at'] = date('Y-m-d H:i:s', filemtime($filePath));
                
                $zip->close();
                return $updateData;
            }
            
            $zip->close();
        }
        
        return null;
    }

    /**
     * Validar atualização antes da instalação
     */
    public function validateUpdate($updateId)
    {
        $updates = $this->getAvailableUpdates();
        $update = collect($updates)->firstWhere('id', $updateId);
        
        if (!$update) {
            return [
                'valid' => false,
                'errors' => ['Atualização não encontrada']
            ];
        }
        
        $errors = [];
        $warnings = [];
        
        // Verificar versão
        if (version_compare($update['version'], $this->currentVersion) <= 0) {
            $warnings[] = "Esta versão ({$update['version']}) não é mais recente que a atual ({$this->currentVersion})";
        }
        
        // Verificar dependências
        if (isset($update['requirements'])) {
            foreach ($update['requirements'] as $requirement => $version) {
                if (!$this->checkRequirement($requirement, $version)) {
                    $errors[] = "Requisito não atendido: {$requirement} >= {$version}";
                }
            }
        }
        
        // Verificar espaço em disco
        $requiredSpace = $update['file_size'] * 3; // 3x para arquivo + backup + descompactação
        $availableSpace = disk_free_space(base_path());
        
        if ($availableSpace < $requiredSpace) {
            $errors[] = "Espaço em disco insuficiente. Necessário: " . $this->formatBytes($requiredSpace);
        }
        
        // Verificar permissões de escrita
        $criticalPaths = [
            base_path(),
            storage_path(),
            public_path(),
            database_path()
        ];
        
        foreach ($criticalPaths as $path) {
            if (!is_writable($path)) {
                $errors[] = "Sem permissão de escrita em: {$path}";
            }
        }
        
        // Verificar conexão com banco de dados
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $errors[] = "Erro de conexão com banco de dados: " . $e->getMessage();
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'update' => $update
        ];
    }

    /**
     * Criar backup do sistema atual
     */
    public function createBackup()
    {
        $backupName = 'backup_' . date('Y-m-d_H-i-s') . '_v' . $this->currentVersion;
        $backupFile = $this->backupPath . '/' . $backupName . '.zip';
        
        Log::info("Criando backup: {$backupName}");
        
        $zip = new ZipArchive();
        
        if ($zip->open($backupFile, ZipArchive::CREATE) !== TRUE) {
            throw new \Exception("Não foi possível criar arquivo de backup");
        }
        
        // Adicionar arquivos do sistema (exceto storage e vendor)
        $this->addDirectoryToZip($zip, base_path('app'), 'app');
        $this->addDirectoryToZip($zip, base_path('config'), 'config');
        $this->addDirectoryToZip($zip, base_path('database'), 'database');
        $this->addDirectoryToZip($zip, base_path('public'), 'public');
        $this->addDirectoryToZip($zip, base_path('resources'), 'resources');
        $this->addDirectoryToZip($zip, base_path('routes'), 'routes');
        
        // Adicionar arquivos importantes da raiz
        $rootFiles = ['composer.json', 'composer.lock', '.env.example', 'artisan'];
        foreach ($rootFiles as $file) {
            $filePath = base_path($file);
            if (File::exists($filePath)) {
                $zip->addFile($filePath, $file);
            }
        }
        
        // Criar dump do banco de dados
        $this->createDatabaseBackup($zip);
        
        $zip->close();
        
        Log::info("Backup criado com sucesso: {$backupFile}");
        
        return [
            'success' => true,
            'backup_file' => $backupFile,
            'backup_name' => $backupName,
            'size' => filesize($backupFile)
        ];
    }

    /**
     * Aplicar atualização
     */
    public function applyUpdate($updateId)
    {
        Log::info("Iniciando aplicação da atualização: {$updateId}");
        
        $validation = $this->validateUpdate($updateId);
        
        if (!$validation['valid']) {
            throw new \Exception("Validação falhou: " . implode(', ', $validation['errors']));
        }
        
        $update = $validation['update'];
        
        try {
            // 1. Criar backup
            $backup = $this->createBackup();
            
            // 2. Extrair atualização
            $extractPath = $this->updatePath . '/temp_' . time();
            $this->extractUpdate($update['file_path'], $extractPath);
            
            // 3. Aplicar arquivos
            $this->applyFiles($extractPath);
            
            // 4. Executar migrations
            $this->runMigrations($extractPath);
            
            // 5. Executar scripts personalizados
            $this->runUpdateScripts($extractPath);
            
            // 6. Limpar cache
            $this->clearCache();
            
            // 7. Atualizar versão
            $this->updateVersion($update['version']);
            
            // 8. Limpar arquivos temporários
            File::deleteDirectory($extractPath);
            
            Log::info("Atualização aplicada com sucesso: {$updateId}");
            
            return [
                'success' => true,
                'message' => "Atualização para versão {$update['version']} aplicada com sucesso",
                'backup' => $backup,
                'version' => $update['version']
            ];
            
        } catch (\Exception $e) {
            Log::error("Erro ao aplicar atualização: " . $e->getMessage());
            
            // Tentar restaurar backup em caso de erro
            if (isset($backup)) {
                try {
                    $this->restoreBackup($backup['backup_file']);
                } catch (\Exception $restoreError) {
                    Log::error("Erro ao restaurar backup: " . $restoreError->getMessage());
                }
            }
            
            throw $e;
        }
    }

    /**
     * Extrair arquivo de atualização
     */
    protected function extractUpdate($zipFile, $extractPath)
    {
        $zip = new ZipArchive();
        
        if ($zip->open($zipFile) !== TRUE) {
            throw new \Exception("Não foi possível abrir arquivo de atualização");
        }
        
        if (!$zip->extractTo($extractPath)) {
            throw new \Exception("Erro ao extrair atualização");
        }
        
        $zip->close();
    }

    /**
     * Aplicar arquivos da atualização
     */
    protected function applyFiles($extractPath)
    {
        $filesPath = $extractPath . '/files';
        
        if (!File::exists($filesPath)) {
            return; // Não há arquivos para aplicar
        }
        
        // Copiar arquivos recursivamente
        $this->copyDirectory($filesPath, base_path());
    }

    /**
     * Executar migrations
     */
    protected function runMigrations($extractPath)
    {
        $migrationsPath = $extractPath . '/migrations';
        
        if (File::exists($migrationsPath)) {
            // Copiar migrations para o diretório correto
            $targetPath = database_path('migrations');
            
            $migrations = File::files($migrationsPath);
            foreach ($migrations as $migration) {
                File::copy($migration, $targetPath . '/' . basename($migration));
            }
        }
        
        // Executar migrate
        Artisan::call('migrate', ['--force' => true]);
    }

    /**
     * Executar scripts personalizados
     */
    protected function runUpdateScripts($extractPath)
    {
        $scriptsPath = $extractPath . '/scripts';
        
        if (!File::exists($scriptsPath)) {
            return;
        }
        
        $scripts = File::files($scriptsPath);
        
        foreach ($scripts as $script) {
            if (pathinfo($script, PATHINFO_EXTENSION) === 'php') {
                Log::info("Executando script: " . basename($script));
                require_once $script;
            }
        }
    }

    /**
     * Limpar cache do sistema
     */
    protected function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }

    /**
     * Atualizar versão no config
     */
    protected function updateVersion($version)
    {
        $configFile = base_path('config/app.php');
        $content = File::get($configFile);
        
        $content = preg_replace(
            "/'version'\s*=>\s*'[^']*'/",
            "'version' => '{$version}'",
            $content
        );
        
        File::put($configFile, $content);
    }

    /**
     * Restaurar backup
     */
    public function restoreBackup($backupFile)
    {
        Log::info("Restaurando backup: {$backupFile}");
        
        if (!File::exists($backupFile)) {
            throw new \Exception("Arquivo de backup não encontrado");
        }
        
        $extractPath = $this->backupPath . '/restore_' . time();
        
        $zip = new ZipArchive();
        
        if ($zip->open($backupFile) !== TRUE) {
            throw new \Exception("Não foi possível abrir arquivo de backup");
        }
        
        $zip->extractTo($extractPath);
        $zip->close();
        
        // Restaurar arquivos
        $this->copyDirectory($extractPath, base_path());
        
        // Restaurar banco de dados
        $sqlFile = $extractPath . '/database.sql';
        if (File::exists($sqlFile)) {
            $this->restoreDatabase($sqlFile);
        }
        
        File::deleteDirectory($extractPath);
        
        Log::info("Backup restaurado com sucesso");
    }

    // Métodos auxiliares...
    protected function addDirectoryToZip($zip, $path, $zipPath)
    {
        if (!File::exists($path)) return;
        
        $files = File::allFiles($path);
        
        foreach ($files as $file) {
            $relativePath = $zipPath . '/' . $file->getRelativePathname();
            $zip->addFile($file->getRealPath(), $relativePath);
        }
    }

    protected function copyDirectory($source, $destination)
    {
        if (!File::exists($source)) return;
        
        $files = File::allFiles($source);
        
        foreach ($files as $file) {
            $relativePath = $file->getRelativePathname();
            $targetPath = $destination . '/' . $relativePath;
            
            // Criar diretório se não existir
            $targetDir = dirname($targetPath);
            if (!File::exists($targetDir)) {
                File::makeDirectory($targetDir, 0755, true);
            }
            
            File::copy($file->getRealPath(), $targetPath);
        }
    }

    protected function createDatabaseBackup($zip)
    {
        $connection = config('database.default');
        $config = config("database.connections.{$connection}");
        
        if ($config['driver'] === 'mysql') {
            $command = sprintf(
                'mysqldump --host=%s --user=%s --password=%s %s > %s',
                $config['host'],
                $config['username'],
                $config['password'],
                $config['database'],
                storage_path('app/temp_db_backup.sql')
            );
            
            exec($command);
            
            if (File::exists(storage_path('app/temp_db_backup.sql'))) {
                $zip->addFile(storage_path('app/temp_db_backup.sql'), 'database.sql');
                File::delete(storage_path('app/temp_db_backup.sql'));
            }
        }
    }

    protected function restoreDatabase($sqlFile)
    {
        $connection = config('database.default');
        $config = config("database.connections.{$connection}");
        
        if ($config['driver'] === 'mysql') {
            $command = sprintf(
                'mysql --host=%s --user=%s --password=%s %s < %s',
                $config['host'],
                $config['username'],
                $config['password'],
                $config['database'],
                $sqlFile
            );
            
            exec($command);
        }
    }

    protected function checkRequirement($requirement, $version)
    {
        switch ($requirement) {
            case 'php':
                return version_compare(PHP_VERSION, $version, '>=');
            case 'laravel':
                return version_compare(app()->version(), $version, '>=');
            default:
                return true; // Assumir que está OK se não soubermos como verificar
        }
    }

    protected function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
}
