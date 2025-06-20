#!/bin/bash

echo "=== Análise Completa dos Logs do Laravel ==="

echo ""
echo "1. Últimas 50 linhas do log (com timestamp):"
echo "--------------------------------------------"
tail -50 storage/logs/laravel.log | grep -E "\[[0-9]{4}-[0-9]{2}-[0-9]{2}|SQLSTATE|Exception|Error"

echo ""
echo "2. Erros mais recentes (últimas 24h):"
echo "------------------------------------"
TODAY=$(date +%Y-%m-%d)
grep "$TODAY" storage/logs/laravel.log | grep -E "ERROR|CRITICAL|Exception" | tail -10

echo ""
echo "3. Erros SQL específicos:"
echo "------------------------"
grep -i "SQLSTATE\|Unknown column\|Table.*doesn't exist" storage/logs/laravel.log | tail -5

echo ""
echo "4. Verificando permissões dos logs:"
echo "----------------------------------"
ls -la storage/logs/laravel.log

echo ""
echo "5. Tamanho do arquivo de log:"
echo "----------------------------"
du -h storage/logs/laravel.log

echo ""
echo "6. Testando rotas principais:"
echo "----------------------------"
echo "Dashboard: $(curl -s -o /dev/null -w '%{http_code}' http://localhost/bc/dashboard)"
echo "Transactions: $(curl -s -o /dev/null -w '%{http_code}' http://localhost/bc/transactions)"
echo "Reports: $(curl -s -o /dev/null -w '%{http_code}' http://localhost/bc/reports)"

echo ""
echo "7. Verificando configuração do banco:"
echo "------------------------------------"
php artisan tinker --execute="
echo 'DB_CONNECTION: ' . config('database.default') . \"\n\";
echo 'DB_DATABASE: ' . config('database.connections.mysql.database') . \"\n\";
try {
    \$count = DB::table('transactions')->count();
    echo 'Transações no banco: ' . \$count . \"\n\";
} catch(Exception \$e) {
    echo 'Erro ao contar transações: ' . \$e->getMessage() . \"\n\";
}
"

echo ""
echo "=== Análise Concluída ==="
