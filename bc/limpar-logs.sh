#!/bin/bash

echo "=== Limpeza e Reinicialização dos Logs ==="

echo ""
echo "1. Backup do log atual:"
cp storage/logs/laravel.log storage/logs/laravel-backup-$(date +%Y%m%d_%H%M%S).log
echo "✓ Backup criado em storage/logs/laravel-backup-$(date +%Y%m%d_%H%M%S).log"

echo ""
echo "2. Limpando log atual:"
echo "" > storage/logs/laravel.log
echo "✓ Log limpo"

echo ""
echo "3. Verificando permissões:"
chmod 664 storage/logs/laravel.log
echo "✓ Permissões ajustadas"

echo ""
echo "4. Limpando todos os caches:"
php artisan config:clear
php artisan cache:clear  
php artisan view:clear
php artisan route:clear
echo "✓ Caches limpos"

echo ""
echo "5. Recriando caches otimizados:"
php artisan config:cache
php artisan route:cache
echo "✓ Caches recriados"

echo ""
echo "6. Testando uma requisição simples:"
php artisan tinker --execute="echo 'Sistema inicializado com sucesso!';"

echo ""
echo "=== Log Limpo - Agora teste o sistema no navegador ==="
echo "Acesse: https://usadosar.com.br/bc/"
echo "Logs novos aparecerão em: storage/logs/laravel.log"
