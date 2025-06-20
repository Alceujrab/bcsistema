#!/bin/bash

echo "=== Limpeza Completa Pós-Correção ==="

echo ""
echo "1. Limpando cache de views compiladas (importante!):"
php artisan view:clear
echo "✓ Cache de views limpo"

echo ""
echo "2. Limpando todos os outros caches:"
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo "✓ Todos os caches limpos"

echo ""
echo "3. Recriando caches otimizados:"
php artisan config:cache
php artisan route:cache
echo "✓ Caches recriados"

echo ""
echo "4. Limpeza manual de arquivos compilados do Blade:"
rm -rf storage/framework/views/*
echo "✓ Arquivos Blade compilados removidos"

echo ""
echo "5. Verificando estrutura de permissões:"
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
echo "✓ Permissões ajustadas"

echo ""
echo "6. Teste básico do sistema:"
php artisan tinker --execute="
try {
    echo 'Conectado ao DB: ' . DB::connection()->getDatabaseName() . \"\n\";
    echo 'Transações: ' . DB::table('transactions')->count() . \"\n\";
    echo 'Sistema funcionando!' . \"\n\";
} catch(Exception \$e) {
    echo 'Erro: ' . \$e->getMessage() . \"\n\";
}
"

echo ""
echo "=== Correções Aplicadas ==="
echo "✅ Erros SQL 'Unknown column year' corrigidos"
echo "✅ Erros de operação matemática string+int corrigidos"  
echo "✅ Cache de views compiladas limpo"
echo ""
echo "🌐 Teste agora no navegador:"
echo "   https://usadosar.com.br/bc/dashboard"
echo "   https://usadosar.com.br/bc/reports/categories"
