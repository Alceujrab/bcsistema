<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Update;
use App\Services\UpdateService;
use Illuminate\Support\Facades\Log;

class ProcessUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $update;
    protected $backup;

    public function __construct(Update $update, $backup = true)
    {
        $this->update = $update;
        $this->backup = $backup;
    }

    public function handle(UpdateService $updateService)
    {
        try {
            Log::info("Iniciando aplicação da atualização {$this->update->version}");
            
            // Atualizar status
            $this->update->update(['status' => Update::STATUS_APPLYING]);
            
            // Aplicar atualização
            $result = $updateService->applyUpdate($this->update, $this->backup);
            
            if ($result['success']) {
                $this->update->update([
                    'status' => Update::STATUS_APPLIED,
                    'applied_at' => now(),
                    'error_message' => null
                ]);
                
                Log::info("Atualização {$this->update->version} aplicada com sucesso");
            } else {
                $this->update->update([
                    'status' => Update::STATUS_FAILED,
                    'error_message' => $result['error']
                ]);
                
                Log::error("Falha ao aplicar atualização {$this->update->version}: {$result['error']}");
            }
            
        } catch (\Exception $e) {
            $this->update->update([
                'status' => Update::STATUS_FAILED,
                'error_message' => $e->getMessage()
            ]);
            
            Log::error("Erro ao processar atualização {$this->update->version}: {$e->getMessage()}");
            
            // Re-throw para que o Laravel saiba que o job falhou
            throw $e;
        }
    }

    public function failed(\Exception $exception)
    {
        $this->update->update([
            'status' => Update::STATUS_FAILED,
            'error_message' => $exception->getMessage()
        ]);
        
        Log::error("Job de atualização falhou: {$exception->getMessage()}");
    }
}
