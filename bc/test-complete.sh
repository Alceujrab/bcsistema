#!/bin/bash

# Força nova sessão limpa
exec > /tmp/bc-test-results.log 2>&1

echo "=== TESTE COMPLETO DO SISTEMA BC ==="
echo "Data: $(date)"
echo "======================================"

cd /workspaces/bcsistema/bc

echo "1. TESTANDO CONEXÃO MYSQL..."
mysql -u usadosar_lara962 -p'[17pvS1-16' -e "SELECT 'MySQL OK' as status;" && echo "✅ MySQL funcionando" || echo "❌ Erro MySQL"

echo "2. TESTANDO EXTENSÕES PHP..."
php -m | grep -i mysql && echo "✅ PHP MySQL OK" || echo "❌ PHP MySQL não encontrado"

echo "3. TESTANDO CONEXÃO PDO..."
php -r "
try {
    \$pdo = new PDO('mysql:host=127.0.0.1;dbname=usadosar_lara962', 'usadosar_lara962', '[17pvS1-16');
    echo '✅ PDO funcionando\n';
} catch (Exception \$e) {
    echo '❌ Erro PDO: ' . \$e->getMessage() . '\n';
}
"

echo "4. LIMPANDO CACHE LARAVEL..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "5. TESTANDO MIGRAÇÕES..."
php artisan migrate:status

echo "6. TESTANDO ROTAS..."
php artisan route:list | grep -E "imports|reconciliations|dashboard"

echo "7. TESTANDO CONTROLLERS..."
php -l app/Http/Controllers/ImportController.php
php -l app/Http/Controllers/ReconciliationController.php

echo "8. TESTANDO MODELS..."
php artisan tinker --execute="echo 'Transações: ' . App\Models\Transaction::count();"
php artisan tinker --execute="echo 'Contas: ' . App\Models\BankAccount::count();"

echo "======================================"
echo "TESTE CONCLUÍDO - Verifique /tmp/bc-test-results.log"
echo "======================================"
