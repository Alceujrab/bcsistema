#!/bin/bash

echo "Limpando caches e testando sistema..."

# Limpar todos os caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Recarregar cache otimizado
php artisan config:cache
php artisan route:cache

echo "Cache limpo! Teste agora no navegador."
