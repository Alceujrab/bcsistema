#!/bin/bash

# Script para atualizar status de contas vencidas
# Este script deve ser executado diariamente via cron

echo "========================================="
echo "Atualizando Status de Contas Vencidas"
echo "Data: $(date '+%d/%m/%Y %H:%M:%S')"
echo "========================================="

cd /workspaces/bcsistema/bc

# Atualizar contas a pagar vencidas
echo "Atualizando contas a pagar vencidas..."
php artisan tinker --execute="
App\Models\AccountPayable::where('status', 'pending')
    ->where('due_date', '<', now())
    ->update(['status' => 'overdue']);
echo 'Contas a pagar atualizadas: ' . App\Models\AccountPayable::where('status', 'overdue')->count() . PHP_EOL;
"

# Atualizar contas a receber vencidas
echo "Atualizando contas a receber vencidas..."
php artisan tinker --execute="
App\Models\AccountReceivable::where('status', 'pending')
    ->where('due_date', '<', now())
    ->update(['status' => 'overdue']);
echo 'Contas a receber atualizadas: ' . App\Models\AccountReceivable::where('status', 'overdue')->count() . PHP_EOL;
"

# Relatório resumido
echo ""
echo "========================================="
echo "RESUMO ATUAL DO SISTEMA"
echo "========================================="

php artisan tinker --execute="
echo 'CONTAS A PAGAR:' . PHP_EOL;
echo '- Total: ' . App\Models\AccountPayable::count() . PHP_EOL;
echo '- Pendentes: ' . App\Models\AccountPayable::where('status', 'pending')->count() . PHP_EOL;
echo '- Vencidas: ' . App\Models\AccountPayable::where('status', 'overdue')->count() . PHP_EOL;
echo '- Pagas: ' . App\Models\AccountPayable::where('status', 'paid')->count() . PHP_EOL;
echo '' . PHP_EOL;
echo 'CONTAS A RECEBER:' . PHP_EOL;
echo '- Total: ' . App\Models\AccountReceivable::count() . PHP_EOL;
echo '- Pendentes: ' . App\Models\AccountReceivable::where('status', 'pending')->count() . PHP_EOL;
echo '- Vencidas: ' . App\Models\AccountReceivable::where('status', 'overdue')->count() . PHP_EOL;
echo '- Recebidas: ' . App\Models\AccountReceivable::where('status', 'received')->count() . PHP_EOL;
echo '' . PHP_EOL;
echo 'FLUXO DE CAIXA:' . PHP_EOL;
\$toReceive = App\Models\AccountReceivable::whereIn('status', ['pending', 'overdue'])->sum('amount');
\$toPay = App\Models\AccountPayable::whereIn('status', ['pending', 'overdue'])->sum('amount');
\$balance = \$toReceive - \$toPay;
echo '- A Receber: R$ ' . number_format(\$toReceive, 2, ',', '.') . PHP_EOL;
echo '- A Pagar: R$ ' . number_format(\$toPay, 2, ',', '.') . PHP_EOL;
echo '- Saldo Projetado: R$ ' . number_format(\$balance, 2, ',', '.') . ' (' . (\$balance >= 0 ? 'POSITIVO' : 'NEGATIVO') . ')' . PHP_EOL;
"

echo ""
echo "========================================="
echo "Atualização concluída!"
echo "========================================="
