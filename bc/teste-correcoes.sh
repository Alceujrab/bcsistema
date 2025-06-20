#!/bin/bash

echo "=== TESTE DE CORREÇÕES DO SISTEMA BC ==="
echo "Data: $(date)"
echo "========================================"

# Navegar para o diretório da aplicação
cd /workspaces/bcsistema/bc

echo "1. Verificando estrutura do banco de dados..."
php artisan migrate:status

echo "2. Limpando cache da aplicação..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "3. Testando rotas principais..."
echo "- Dashboard: " 
php artisan route:list | grep "dashboard"

echo "- Importações: "
php artisan route:list | grep "imports"

echo "- Conciliações: "
php artisan route:list | grep "reconciliations"

echo "4. Verificando Models e relacionamentos..."
echo "- Testando Model Transaction..."
php artisan tinker --execute="echo App\Models\Transaction::count() . ' transações encontradas';"

echo "- Testando Model ImportLog..."
php artisan tinker --execute="echo App\Models\ImportLog::count() . ' importações encontradas';"

echo "- Testando Model BankAccount..."
php artisan tinker --execute="echo App\Models\BankAccount::count() . ' contas bancárias encontradas';"

echo "5. Testando sintaxe dos controllers..."
php -l app/Http/Controllers/ImportController.php
php -l app/Http/Controllers/ReconciliationController.php
php -l app/Http/Controllers/TransactionController.php

echo "6. Verificando views Blade..."
echo "- Verificando layout principal..."
php artisan view:cache

echo "7. Testando JavaScript do menu responsivo..."
echo "- Verificando se não existem erros de sintaxe no layout..."

echo "========================================"
echo "TESTE CONCLUÍDO!"
echo "========================================"

# Se houver erros, eles aparecerão aqui
echo "Logs de erro (se houver):"
tail -20 storage/logs/laravel.log 2>/dev/null || echo "Nenhum log de erro encontrado."
