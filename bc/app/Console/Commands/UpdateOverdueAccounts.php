<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AccountPayable;
use App\Models\AccountReceivable;
use Carbon\Carbon;

class UpdateOverdueAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounts:update-overdue {--report : Show detailed report}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update overdue accounts status and generate financial reports';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Atualizando status de contas vencidas...');
        $this->newLine();

        // Atualizar contas a pagar vencidas
        $payablesUpdated = AccountPayable::where('status', 'pending')
            ->where('due_date', '<', now())
            ->update(['status' => 'overdue']);

        // Atualizar contas a receber vencidas
        $receivablesUpdated = AccountReceivable::where('status', 'pending')
            ->where('due_date', '<', now())
            ->update(['status' => 'overdue']);

        $this->info("✅ Contas a pagar atualizadas: {$payablesUpdated}");
        $this->info("✅ Contas a receber atualizadas: {$receivablesUpdated}");
        $this->newLine();

        if ($this->option('report')) {
            $this->showDetailedReport();
        } else {
            $this->showSummaryReport();
        }

        $this->info('🎉 Atualização concluída com sucesso!');
        return 0;
    }

    private function showSummaryReport()
    {
        $this->info('📊 RESUMO FINANCEIRO');
        $this->line('═══════════════════');

        // Estatísticas de contas a pagar
        $payablesStats = [
            'total' => AccountPayable::count(),
            'pending' => AccountPayable::where('status', 'pending')->count(),
            'overdue' => AccountPayable::where('status', 'overdue')->count(),
            'paid' => AccountPayable::where('status', 'paid')->count(),
            'pending_amount' => AccountPayable::where('status', 'pending')->sum('amount'),
            'overdue_amount' => AccountPayable::where('status', 'overdue')->sum('amount'),
        ];

        // Estatísticas de contas a receber
        $receivablesStats = [
            'total' => AccountReceivable::count(),
            'pending' => AccountReceivable::where('status', 'pending')->count(),
            'overdue' => AccountReceivable::where('status', 'overdue')->count(),
            'received' => AccountReceivable::where('status', 'received')->count(),
            'pending_amount' => AccountReceivable::where('status', 'pending')->sum('amount'),
            'overdue_amount' => AccountReceivable::where('status', 'overdue')->sum('amount'),
        ];

        // Fluxo de caixa
        $toReceive = $receivablesStats['pending_amount'] + $receivablesStats['overdue_amount'];
        $toPay = $payablesStats['pending_amount'] + $payablesStats['overdue_amount'];
        $balance = $toReceive - $toPay;

        $this->table(
            ['Tipo', 'Total', 'Pendente', 'Vencida', 'Finalizada'],
            [
                [
                    'Contas a Pagar',
                    $payablesStats['total'],
                    $payablesStats['pending'],
                    $payablesStats['overdue'],
                    $payablesStats['paid']
                ],
                [
                    'Contas a Receber',
                    $receivablesStats['total'],
                    $receivablesStats['pending'],
                    $receivablesStats['overdue'],
                    $receivablesStats['received']
                ]
            ]
        );

        $this->newLine();
        $this->info('💰 FLUXO DE CAIXA');
        $this->line('═══════════════════');
        $this->line("A Receber: R$ " . number_format($toReceive, 2, ',', '.'));
        $this->line("A Pagar: R$ " . number_format($toPay, 2, ',', '.'));
        
        if ($balance >= 0) {
            $this->info("Saldo Projetado: R$ " . number_format($balance, 2, ',', '.') . ' (POSITIVO)');
        } else {
            $this->warn("Saldo Projetado: R$ " . number_format($balance, 2, ',', '.') . ' (NEGATIVO)');
        }
        $this->newLine();
    }

    private function showDetailedReport()
    {
        $this->showSummaryReport();

        // Contas vencidas
        $this->warn('⚠️  CONTAS VENCIDAS - AÇÃO NECESSÁRIA');
        $this->line('══════════════════════════════════════');

        $overduePayables = AccountPayable::where('status', 'overdue')
            ->with('supplier')
            ->orderBy('due_date')
            ->get();

        if ($overduePayables->count() > 0) {
            $this->error("📋 CONTAS A PAGAR VENCIDAS ({$overduePayables->count()}):");
            $payableRows = [];
            foreach ($overduePayables as $payable) {
                $daysOverdue = abs($payable->due_date->diffInDays(now()));
                $payableRows[] = [
                    $payable->supplier->name,
                    $payable->description,
                    'R$ ' . number_format($payable->amount, 2, ',', '.'),
                    $payable->due_date->format('d/m/Y'),
                    "{$daysOverdue} dias"
                ];
            }
            $this->table(['Fornecedor', 'Descrição', 'Valor', 'Vencimento', 'Atraso'], $payableRows);
        }

        $overdueReceivables = AccountReceivable::where('status', 'overdue')
            ->with('client')
            ->orderBy('due_date')
            ->get();

        if ($overdueReceivables->count() > 0) {
            $this->error("📋 CONTAS A RECEBER VENCIDAS ({$overdueReceivables->count()}):");
            $receivableRows = [];
            foreach ($overdueReceivables as $receivable) {
                $daysOverdue = abs($receivable->due_date->diffInDays(now()));
                $receivableRows[] = [
                    $receivable->client->name,
                    $receivable->description,
                    'R$ ' . number_format($receivable->amount, 2, ',', '.'),
                    $receivable->due_date->format('d/m/Y'),
                    "{$daysOverdue} dias"
                ];
            }
            $this->table(['Cliente', 'Descrição', 'Valor', 'Vencimento', 'Atraso'], $receivableRows);
        }

        // Próximos vencimentos
        $this->info('📅 PRÓXIMOS VENCIMENTOS (7 dias)');
        $this->line('══════════════════════════════════');

        $upcomingPayables = AccountPayable::where('status', 'pending')
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(7))
            ->with('supplier')
            ->orderBy('due_date')
            ->get();

        if ($upcomingPayables->count() > 0) {
            $this->warn("Contas a Pagar vencendo ({$upcomingPayables->count()}):");
            foreach ($upcomingPayables as $payable) {
                $this->line("• {$payable->supplier->name} - {$payable->formatted_amount} - {$payable->due_date->format('d/m/Y')}");
            }
            $this->newLine();
        }

        $upcomingReceivables = AccountReceivable::where('status', 'pending')
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(7))
            ->with('client')
            ->orderBy('due_date')
            ->get();

        if ($upcomingReceivables->count() > 0) {
            $this->info("Contas a Receber vencendo ({$upcomingReceivables->count()}):");
            foreach ($upcomingReceivables as $receivable) {
                $this->line("• {$receivable->client->name} - {$receivable->formatted_amount} - {$receivable->due_date->format('d/m/Y')}");
            }
            $this->newLine();
        }
    }
}
