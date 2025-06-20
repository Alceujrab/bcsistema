#!/bin/bash

# Script para limpar todos os caches do Laravel

echo "🔄 Limpando caches do Laravel..."

# Limpar todos os caches
php artisan cache:clear
echo "✅ Cache de aplicação limpo"

php artisan config:clear
echo "✅ Cache de configuração limpo"

php artisan route:clear
echo "✅ Cache de rotas limpo"

php artisan view:clear
echo "✅ Cache de views limpo"

# Limpar cache do opcache se existir
php artisan optimize:clear
echo "✅ Otimizações limpas"

# Recriar autoload
composer dump-autoload
echo "✅ Autoload recriado"

echo ""
echo "🎉 Todos os caches foram limpos!"
echo "🔄 Agora teste o site novamente"
