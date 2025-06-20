<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Importação automática diária às 6h
        $schedule->command('bank:import')->dailyAt('06:00');
        
        // Relatório de conciliações pendentes toda segunda-feira
        $schedule->command('reports:pending-reconciliations')->weeklyOn(1, '08:00');
        
        // Limpeza de logs antigos
        $schedule->command('log:clear')->weekly();
        
        // Backup do banco de dados (se tiver o pacote de backup instalado)
        // $schedule->command('backup:run')->daily()->at('02:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}