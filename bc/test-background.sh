#!/bin/bash

# Executa teste e salva resultado em arquivo
{
    echo "=== TESTE SISTEMA BC - $(date) ==="
    
    cd /workspaces/bcsistema/bc
    
    # Teste PHP
    echo "1. TESTE PHP:"
    php --version | head -1
    
    # Teste extensões
    echo "2. EXTENSÕES MYSQL:"
    php -m | grep -i mysql || echo "Nenhuma extensão MySQL encontrada"
    
    # Teste PDO direto
    echo "3. TESTE PDO:"
    php -r "
    try {
        \$pdo = new PDO('mysql:host=127.0.0.1;dbname=usadosar_lara962', 'usadosar_lara962', '[17pvS1-16');
        echo 'PDO: OK';
    } catch (Exception \$e) {
        echo 'PDO: ERRO - ' . \$e->getMessage();
    }
    echo PHP_EOL;
    "
    
    # Teste Laravel
    echo "4. TESTE LARAVEL:"
    if php artisan --version 2>/dev/null; then
        echo "Laravel: OK"
        php artisan config:clear 2>/dev/null || echo "Config clear: ERRO"
        php artisan migrate:status 2>/dev/null || echo "Migrate status: ERRO"
    else
        echo "Laravel: ERRO"
    fi
    
    echo "=== FIM DO TESTE ==="
    
} > /tmp/bc-final-test.txt 2>&1

# Executar em segundo plano
echo "Teste executado! Resultado em /tmp/bc-final-test.txt"
