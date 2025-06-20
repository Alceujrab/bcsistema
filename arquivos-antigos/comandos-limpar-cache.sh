#!/bin/bash

# COMANDOS COMPLETOS PARA LIMPAR CACHE NO LARAVEL
# Data: $(date +%Y-%m-%d)

echo "=== LIMPEZA COMPLETA DE CACHE DO LARAVEL ==="
echo

echo "1. Limpando Application Cache..."
php artisan cache:clear

echo "2. Limpando Configuration Cache..."
php artisan config:clear

echo "3. Limpando Route Cache..."
php artisan route:clear

echo "4. Limpando View Cache..."
php artisan view:clear

echo "5. Limpando Compiled Classes..."
php artisan clear-compiled

echo "6. Limpando Event Cache..."
php artisan event:clear

echo "7. Limpando Queue Cache..."
php artisan queue:clear

echo "8. Limpando Schedule Cache..."
php artisan schedule:clear-cache

echo "9. Removendo arquivos de cache manualmente..."
# Cache de aplicação
rm -rf storage/framework/cache/data/*
echo "   ✓ Cache de dados removido"

# Cache de views
rm -rf storage/framework/views/*
echo "   ✓ Cache de views removido"

# Cache de sessões (cuidado em produção)
# rm -rf storage/framework/sessions/*
# echo "   ✓ Cache de sessões removido"

# Logs antigos (opcional)
# rm -rf storage/logs/*.log
# echo "   ✓ Logs antigos removidos"

echo
echo "10. Limpando cache do Composer..."
composer clear-cache

echo
echo "11. Limpando cache do NPM (se existir)..."
if [ -f "package.json" ]; then
    npm cache clean --force
    echo "   ✓ Cache do NPM limpo"
else
    echo "   - package.json não encontrado, pulando NPM"
fi

echo
echo "12. Recriando caches otimizados..."
php artisan config:cache
echo "   ✓ Configuration cache recriado"

php artisan route:cache
echo "   ✓ Route cache recriado"

php artisan view:cache
echo "   ✓ View cache recriado"

echo
echo "13. Otimizando autoloader do Composer..."
composer dump-autoload -o

echo
echo "14. Verificando permissões..."
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
echo "   ✓ Permissões ajustadas"

echo
echo "=== LIMPEZA COMPLETA FINALIZADA ==="
echo "Todos os caches foram limpos e recriados!"
echo
