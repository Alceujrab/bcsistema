<?php

namespace App\Jobs;

use App\Models\Reconciliation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessReconciliation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $reconciliation;

    /**
     * Create a new job instance.
     */
    public function __construct(Reconciliation $reconciliation)
    {
        $this->reconciliation = $reconciliation;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Processar a conciliação
            $this->reconciliation->calculate();
            
            // Verificar diferenças e criar alertas se necessário
            if (!$this->reconciliation->isBalanced()) {
                Log::warning("Conciliação #{$this->reconciliation->id} possui diferença de R$ {$this->reconciliation->difference}");
                
                // Aqui você pode adicionar lógica para enviar email ou notificação
                // Mail::to($this->reconciliation->creator->email)->send(new ReconciliationUnbalanced($this->reconciliation));
            }
            
            // Atualizar saldo da conta
            $this->reconciliation->bankAccount->updateBalance();
            
            Log::info("Conciliação #{$this->reconciliation->id} processada com sucesso");
            
        } catch (\Exception $e) {
            Log::error("Erro ao processar conciliação #{$this->reconciliation->id}: " . $e->getMessage());
            throw $e;
        }
    }
}