<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SystemUpdateService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class SystemUpdateController extends Controller
{
    protected $updateService;

    public function __construct(SystemUpdateService $updateService)
    {
        $this->updateService = $updateService;
        
        // Middleware de segurança - só admins podem acessar
        $this->middleware(function ($request, $next) {
            // Verificar se é ambiente de produção e se tem autorização
            if (app()->environment('production')) {
                // Verificar IP autorizado ou token de segurança
                $allowedIps = config('update.allowed_ips', []);
                $clientIp = $request->ip();
                
                if (!empty($allowedIps) && !in_array($clientIp, $allowedIps)) {
                    abort(403, 'Acesso não autorizado');
                }
            }
            
            return $next($request);
        });
    }

    /**
     * Mostrar página principal de atualizações
     */
    public function index()
    {
        $hasUpdates = $this->updateService->hasUpdatesAvailable();
        $availableUpdates = $this->updateService->getAvailableUpdates();
        $currentVersion = config('app.version', '1.0.0');
        
        // Informações do sistema
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'current_version' => $currentVersion,
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug'),
            'timezone' => config('app.timezone'),
            'database_driver' => config('database.default'),
            'disk_space' => $this->getDiskSpace(),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
        ];
        
        return view('system.update.index', compact(
            'hasUpdates', 
            'availableUpdates', 
            'currentVersion', 
            'systemInfo'
        ));
    }

    /**
     * Mostrar detalhes de uma atualização específica
     */
    public function show($updateId)
    {
        $validation = $this->updateService->validateUpdate($updateId);
        
        if (!$validation['update']) {
            abort(404, 'Atualização não encontrada');
        }
        
        return view('system.update.show', [
            'update' => $validation['update'],
            'validation' => $validation,
            'currentVersion' => config('app.version', '1.0.0')
        ]);
    }

    /**
     * Validar atualização via AJAX
     */
    public function validate(Request $request, $updateId)
    {
        try {
            $validation = $this->updateService->validateUpdate($updateId);
            
            return response()->json([
                'success' => true,
                'validation' => $validation
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Aplicar atualização
     */
    public function apply(Request $request, $updateId)
    {
        try {
            // Validar primeiro
            $validation = $this->updateService->validateUpdate($updateId);
            
            if (!$validation['valid']) {
                return response()->json([
                    'success' => false,
                    'error' => 'Validação falhou',
                    'errors' => $validation['errors']
                ], 400);
            }
            
            // Confirmar que o usuário quer aplicar
            if (!$request->boolean('confirmed')) {
                return response()->json([
                    'success' => false,
                    'error' => 'Confirmação necessária',
                    'requires_confirmation' => true,
                    'validation' => $validation
                ], 400);
            }
            
            Log::info("Usuário iniciou aplicação de atualização: {$updateId}", [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            // Aplicar atualização
            $result = $this->updateService->applyUpdate($updateId);
            
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'version' => $result['version'],
                'backup' => $result['backup']
            ]);
            
        } catch (\Exception $e) {
            Log::error("Erro ao aplicar atualização {$updateId}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload de arquivo de atualização
     */
    public function upload(Request $request)
    {
        $request->validate([
            'update_file' => 'required|file|mimes:zip|max:51200' // 50MB max
        ]);
        
        try {
            $file = $request->file('update_file');
            $fileName = 'update_' . time() . '_' . $file->getClientOriginalName();
            $path = storage_path('app/updates/' . $fileName);
            
            $file->move(storage_path('app/updates'), $fileName);
            
            // Validar arquivo de atualização
            $updates = $this->updateService->getAvailableUpdates();
            $newUpdate = collect($updates)->firstWhere('file_name', $fileName);
            
            if (!$newUpdate) {
                File::delete($path);
                throw new \Exception('Arquivo de atualização inválido - arquivo update.json não encontrado');
            }
            
            Log::info("Arquivo de atualização enviado: {$fileName}");
            
            return response()->json([
                'success' => true,
                'message' => 'Arquivo de atualização enviado com sucesso',
                'update' => $newUpdate
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Criar backup manual
     */
    public function backup(Request $request)
    {
        try {
            $backup = $this->updateService->createBackup();
            
            Log::info("Backup manual criado: " . $backup['backup_name']);
            
            return response()->json([
                'success' => true,
                'message' => 'Backup criado com sucesso',
                'backup' => $backup
            ]);
            
        } catch (\Exception $e) {
            Log::error("Erro ao criar backup: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar backups disponíveis
     */
    public function backups()
    {
        $backupPath = storage_path('app/backups');
        $backups = [];
        
        if (File::exists($backupPath)) {
            $files = File::files($backupPath);
            
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'zip') {
                    $backups[] = [
                        'name' => basename($file),
                        'path' => $file,
                        'size' => filesize($file),
                        'created_at' => date('Y-m-d H:i:s', filemtime($file))
                    ];
                }
            }
        }
        
        // Ordenar por data de criação (mais recente primeiro)
        usort($backups, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        return response()->json([
            'success' => true,
            'backups' => $backups
        ]);
    }

    /**
     * Download de backup
     */
    public function downloadBackup($backupName)
    {
        $backupPath = storage_path('app/backups/' . $backupName);
        
        if (!File::exists($backupPath)) {
            abort(404, 'Backup não encontrado');
        }
        
        return response()->download($backupPath);
    }

    /**
     * Restaurar backup
     */
    public function restoreBackup(Request $request, $backupName)
    {
        try {
            if (!$request->boolean('confirmed')) {
                return response()->json([
                    'success' => false,
                    'error' => 'Confirmação necessária',
                    'requires_confirmation' => true
                ], 400);
            }
            
            $backupPath = storage_path('app/backups/' . $backupName);
            
            if (!File::exists($backupPath)) {
                throw new \Exception('Backup não encontrado');
            }
            
            Log::warning("Iniciando restauração de backup: {$backupName}", [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            $this->updateService->restoreBackup($backupPath);
            
            return response()->json([
                'success' => true,
                'message' => 'Backup restaurado com sucesso'
            ]);
            
        } catch (\Exception $e) {
            Log::error("Erro ao restaurar backup {$backupName}: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remover arquivo de atualização
     */
    public function remove(Request $request, $updateId)
    {
        try {
            $updates = $this->updateService->getAvailableUpdates();
            $update = collect($updates)->firstWhere('id', $updateId);
            
            if (!$update) {
                throw new \Exception('Atualização não encontrada');
            }
            
            File::delete($update['file_path']);
            
            Log::info("Arquivo de atualização removido: {$update['file_name']}");
            
            return response()->json([
                'success' => true,
                'message' => 'Arquivo de atualização removido com sucesso'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Informações do sistema
     */
    public function systemInfo()
    {
        $info = [
            'system' => [
                'os' => PHP_OS,
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'current_version' => config('app.version', '1.0.0'),
                'environment' => app()->environment(),
                'debug_mode' => config('app.debug'),
                'timezone' => config('app.timezone'),
            ],
            'database' => [
                'driver' => config('database.default'),
                'connection' => $this->testDatabaseConnection()
            ],
            'storage' => [
                'disk_space' => $this->getDiskSpace(),
                'temp_space' => $this->getTempSpace()
            ],
            'php_config' => [
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size'),
            ],
            'extensions' => [
                'zip' => extension_loaded('zip'),
                'pdo' => extension_loaded('pdo'),
                'mbstring' => extension_loaded('mbstring'),
                'openssl' => extension_loaded('openssl'),
                'tokenizer' => extension_loaded('tokenizer'),
                'xml' => extension_loaded('xml'),
                'ctype' => extension_loaded('ctype'),
                'json' => extension_loaded('json'),
            ]
        ];
        
        return response()->json([
            'success' => true,
            'info' => $info
        ]);
    }

    /**
     * Métodos auxiliares
     */
    protected function getDiskSpace()
    {
        $total = disk_total_space(base_path());
        $free = disk_free_space(base_path());
        $used = $total - $free;
        
        return [
            'total' => $total,
            'free' => $free,
            'used' => $used,
            'percentage_used' => round(($used / $total) * 100, 2)
        ];
    }

    protected function getTempSpace()
    {
        $tempPath = sys_get_temp_dir();
        $total = disk_total_space($tempPath);
        $free = disk_free_space($tempPath);
        
        return [
            'total' => $total,
            'free' => $free,
            'path' => $tempPath
        ];
    }

    protected function testDatabaseConnection()
    {
        try {
            \DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
