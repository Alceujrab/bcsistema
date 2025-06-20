<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UpdateService;

class CheckUpdatesCommand extends Command
{
    protected $signature = 'system:check-updates {--force : Forçar verificação mesmo se recente}';
    protected $description = 'Verifica se há atualizações disponíveis para o sistema';

    protected $updateService;

    public function __construct(UpdateService $updateService)
    {
        parent::__construct();
        $this->updateService = $updateService;
    }

    public function handle()
    {
        $this->info('🔍 Verificando atualizações disponíveis...');
        
        try {
            $force = $this->option('force');
            $updates = $this->updateService->checkForUpdates($force);
            
            if (empty($updates)) {
                $this->info('✅ Sistema está atualizado! Nenhuma atualização disponível.');
                return Command::SUCCESS;
            }
            
            $this->warn('🚀 ' . count($updates) . ' atualização(ões) disponível(is):');
            $this->newLine();
            
            foreach ($updates as $update) {
                $this->line("📦 <info>{$update['name']}</info> - v{$update['version']}");
                $this->line("   📅 Data: {$update['release_date']}");
                $this->line("   📝 Descrição: {$update['description']}");
                
                if (!empty($update['changes'])) {
                    $this->line("   🔧 Mudanças:");
                    foreach ($update['changes'] as $change) {
                        $this->line("      • {$change}");
                    }
                }
                
                if (!empty($update['requirements'])) {
                    $this->line("   ⚠️  Requisitos:");
                    foreach ($update['requirements'] as $req) {
                        $this->line("      • {$req}");
                    }
                }
                
                $this->newLine();
            }
            
            $this->info('💡 Para aplicar as atualizações, acesse: ' . config('app.url') . '/system/update');
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('❌ Erro ao verificar atualizações: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
