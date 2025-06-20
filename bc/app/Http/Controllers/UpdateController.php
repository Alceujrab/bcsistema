<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UpdateService;
use App\Models\Update;
use App\Jobs\ProcessUpdateJob;
use Illuminate\Support\Facades\Log;

class UpdateController extends Controller
{
    protected $updateService;

    public function __construct(UpdateService $updateService)
    {
        $this->updateService = $updateService;
    }

    /**
     * Página principal do sistema de updates
     */
    public function index()
    {
        try {
            // Teste direto sem scopes primeiro
            $availableUpdates = Update::where('status', 'available')->latest()->get();
            $appliedUpdates = Update::where('status', 'applied')->latest()->limit(10)->get();
            $systemInfo = $this->getSystemInfo();
            $hasUpdates = $availableUpdates->count() > 0;
            
            return view('system.update.index', compact('availableUpdates', 'appliedUpdates', 'systemInfo', 'hasUpdates'));
        } catch (\Exception $e) {
            Log::error('Erro ao carregar updates: ' . $e->getMessage());
            
            // Se a tabela não existir, mostrar página de configuração
            if (str_contains($e->getMessage(), "doesn't exist") || 
                str_contains($e->getMessage(), "Base table or view not found") ||
                str_contains($e->getMessage(), 'updates')) {
                return view('system.update.setup');
            }
            
            // Para outros erros, usar collections vazias
            $availableUpdates = collect([]);
            $appliedUpdates = collect([]);
            $systemInfo = $this->getSystemInfo();
            $hasUpdates = false;
            
            return view('system.update.index', compact('availableUpdates', 'appliedUpdates', 'systemInfo', 'hasUpdates'))
                ->with('error', 'Erro ao carregar atualizações: ' . $e->getMessage());
        }
    }

    /**
     * Verificar atualizações disponíveis
     */
    public function check(Request $request)
    {
        try {
            $force = $request->boolean('force');
            $updates = $this->updateService->checkForUpdates($force);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'updates' => $updates,
                    'count' => count($updates)
                ]);
            }
            
            return redirect()->route('system.update.index')->with('success', 
                count($updates) > 0 
                    ? count($updates) . ' atualização(ões) encontrada(s)!'
                    : 'Sistema está atualizado!'
            );
            
        } catch (\Exception $e) {
            Log::error('Erro ao verificar atualizações: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('system.update.index')->with('error', 
                'Erro ao verificar atualizações: ' . $e->getMessage()
            );
        }
    }

    /**
     * Baixar uma atualização
     */
    public function download(Request $request, Update $update)
    {
        try {
            if (!$update->canApply()) {
                throw new \Exception('Esta atualização não pode ser baixada no momento');
            }

            $result = $this->updateService->downloadUpdate($update);
            
            if ($request->expectsJson()) {
                return response()->json($result);
            }
            
            if ($result['success']) {
                return redirect()->route('system.update.index')->with('success', $result['message']);
            } else {
                return redirect()->route('system.update.index')->with('error', $result['error']);
            }
            
        } catch (\Exception $e) {
            Log::error('Erro ao baixar atualização: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('system.update.index')->with('error', 
                'Erro ao baixar atualização: ' . $e->getMessage()
            );
        }
    }

    /**
     * Aplicar uma atualização
     */
    public function apply(Request $request, Update $update)
    {
        $request->validate([
            'create_backup' => 'boolean'
        ]);

        try {
            if (!$update->canApply()) {
                throw new \Exception('Esta atualização não pode ser aplicada no momento');
            }

            $createBackup = $request->boolean('create_backup', true);
            
            // Iniciar processo em background
            ProcessUpdateJob::dispatch($update, $createBackup);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Atualização iniciada em background',
                    'update_id' => $update->id
                ]);
            }
            
            return redirect()->route('system.update.index')->with('info', 
                'Atualização iniciada! Acompanhe o progresso na tela.'
            );
            
        } catch (\Exception $e) {
            Log::error('Erro ao iniciar atualização: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('system.update.index')->with('error', 
                'Erro ao iniciar atualização: ' . $e->getMessage()
            );
        }
    }

    /**
     * Verificar status de uma atualização
     */
    public function status(Update $update)
    {
        return response()->json([
            'update_id' => $update->id,
            'version' => $update->version,
            'status' => $update->status,
            'applied_at' => $update->applied_at?->format('d/m/Y H:i:s'),
            'error_message' => $update->error_message,
            'can_apply' => $update->canApply(),
            'is_applied' => $update->isApplied()
        ]);
    }

    /**
     * Histórico de atualizações
     */
    public function history()
    {
        $updates = Update::latest()->paginate(20);
        
        return view('system.update.history', compact('updates'));
    }

    /**
     * Página de gerenciamento de backups
     */
    public function backup()
    {
        $backups = $this->updateService->getBackups();
        
        return view('system.update.backup', compact('backups'));
    }

    /**
     * Criar backup manual
     */
    public function createBackup(Request $request)
    {
        try {
            $result = $this->updateService->createBackup();
            
            if ($request->expectsJson()) {
                return response()->json($result);
            }
            
            if ($result['success']) {
                return redirect()->route('system.update.backup')->with('success', 
                    'Backup criado com sucesso!'
                );
            } else {
                return redirect()->route('system.update.backup')->with('error', $result['error']);
            }
            
        } catch (\Exception $e) {
            Log::error('Erro ao criar backup: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('system.update.backup')->with('error', 
                'Erro ao criar backup: ' . $e->getMessage()
            );
        }
    }

    /**
     * Restaurar backup
     */
    public function restoreBackup(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|string'
        ]);

        try {
            $backupFile = $request->input('backup_file');
            $result = $this->updateService->restoreBackup($backupFile);
            
            if ($request->expectsJson()) {
                return response()->json($result);
            }
            
            if ($result['success']) {
                return redirect()->route('system.update.backup')->with('success', $result['message']);
            } else {
                return redirect()->route('system.update.backup')->with('error', $result['error']);
            }
            
        } catch (\Exception $e) {
            Log::error('Erro ao restaurar backup: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('system.update.backup')->with('error', 
                'Erro ao restaurar backup: ' . $e->getMessage()
            );
        }
    }

    /**
     * Exibir detalhes de uma atualização específica
     */
    public function show(Update $update)
    {
        return view('system.update.show', compact('update'));
    }

    /**
     * Obter informações do sistema
     */
    protected function getSystemInfo()
    {
        $diskFree = disk_free_space('.');
        $diskTotal = disk_total_space('.');
        $diskUsed = $diskTotal - $diskFree;
        $diskPercentageUsed = $diskTotal > 0 ? ($diskUsed / $diskTotal) * 100 : 0;

        return [
            'current_version' => config('app.version', '1.0.0'),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'environment' => config('app.env', 'production'),
            'debug_mode' => config('app.debug', false),
            'database_driver' => config('database.default', 'mysql'),
            'os' => PHP_OS_FAMILY,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'disk_space' => [
                'free' => $diskFree,
                'total' => $diskTotal,
                'used' => $diskUsed,
                'percentage_used' => $diskPercentageUsed,
                'free_formatted' => $this->formatBytes($diskFree),
                'total_formatted' => $this->formatBytes($diskTotal),
                'used_formatted' => $this->formatBytes($diskUsed),
            ],
            'disk_free_space' => $this->formatBytes($diskFree),
            'disk_total_space' => $this->formatBytes($diskTotal),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'extensions' => [
                'zip' => extension_loaded('zip'),
                'curl' => extension_loaded('curl'),
                'openssl' => extension_loaded('openssl'),
                'pdo' => extension_loaded('pdo'),
                'mbstring' => extension_loaded('mbstring'),
                'json' => extension_loaded('json'),
            ]
        ];
    }

    /**
     * Formatar bytes em formato legível
     */
    protected function formatBytes($size, $precision = 2)
    {
        if ($size === false) return 'N/A';
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }

    /**
     * Obter lista de backups via API (para AJAX)
     */
    public function getBackups()
    {
        try {
            $backups = $this->updateService->getBackups();
            
            return response()->json([
                'success' => true,
                'backups' => $backups
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao listar backups: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download de um backup específico
     */
    public function downloadBackup($backup)
    {
        try {
            $backupPath = storage_path('app/backups/' . $backup);
            
            if (!file_exists($backupPath)) {
                abort(404, 'Backup não encontrado');
            }
            
            return response()->download($backupPath);
            
        } catch (\Exception $e) {
            Log::error('Erro ao baixar backup: ' . $e->getMessage());
            abort(500, 'Erro ao baixar backup');
        }
    }

    /**
     * Restaurar um backup específico
     */
    public function restoreSpecificBackup(Request $request, $backup)
    {
        try {
            $result = $this->updateService->restoreBackup($backup);
            
            if ($request->expectsJson()) {
                return response()->json($result);
            }
            
            if ($result['success']) {
                return redirect()->route('system.update.backup')->with('success', $result['message']);
            } else {
                return redirect()->route('system.update.backup')->with('error', $result['error']);
            }
            
        } catch (\Exception $e) {
            Log::error('Erro ao restaurar backup específico: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('system.update.backup')->with('error', 
                'Erro ao restaurar backup: ' . $e->getMessage()
            );
        }
    }
}
