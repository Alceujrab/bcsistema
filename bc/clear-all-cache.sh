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

php artisan event:clear
echo "✅ Cache de eventos limpo"

# Limpar cache do opcache se existir
php artisan optimize:clear
echo "✅ Otimizações limpas"

# Recriar autoload
composer dump-autoload
echo "✅ Autoload recriado"

echo ""
echo "🔄 Regenerando caches otimizados para produção..."

# Regenerar caches otimizados
php artisan config:cache
echo "✅ Cache de configuração regenerado"

php artisan route:cache
echo "✅ Cache de rotas regenerado"

php artisan view:cache
echo "✅ Cache de views regenerado"

echo ""
echo "🔍 Verificando status do sistema..."
echo "Primeiras 10 rotas registradas:"
php artisan route:list --columns=method,uri,name | head -11

echo ""
echo "Configuração da URL:"
php artisan config:show app.url

echo ""
echo "🎉 Todos os caches foram limpos e regenerados!"
echo "🔄 Agora teste o site novamente"
