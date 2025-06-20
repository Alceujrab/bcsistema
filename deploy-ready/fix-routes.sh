#!/bin/bash

# Script para corrigir problema de rotas - Method Not Allowed
# Execute no diretório raiz do projeto Laravel

echo "=========================================="
echo "  CORREÇÃO DE ROTAS - Method Not Allowed"
echo "=========================================="

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Função para log
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}"
}

error() {
    echo -e "${RED}[ERRO] $1${NC}"
}

warning() {
    echo -e "${YELLOW}[AVISO] $1${NC}"
}

# 1. Tirar do modo manutenção (caso esteja)
log "1. Verificando modo manutenção..."
php artisan up 2>/dev/null

# 2. Limpar TODOS os caches
log "2. Limpando todos os caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

# 3. Remover cache de rotas manualmente se existir
log "3. Removendo caches manuais..."
if [ -f "bootstrap/cache/routes.php" ]; then
    rm bootstrap/cache/routes.php
    log "Cache de rotas removido"
fi

if [ -f "bootstrap/cache/config.php" ]; then
    rm bootstrap/cache/config.php
    log "Cache de config removido"
fi

# 4. Verificar se o arquivo de rotas está correto
log "4. Verificando arquivo de rotas..."
if grep -q "Route::get('/', \[DashboardController::class, 'index'\])" routes/web.php; then
    log "Rota principal está configurada corretamente"
else
    error "Problema na configuração da rota principal"
fi

# 5. Recriar autoload
log "5. Recriando autoload..."
composer dump-autoload

# 6. Testar as rotas
log "6. Listando rotas disponíveis..."
php artisan route:list --name=dashboard

# 7. Verificar se o controller existe
log "7. Verificando DashboardController..."
if [ -f "app/Http/Controllers/DashboardController.php" ]; then
    log "DashboardController encontrado"
else
    error "DashboardController não encontrado!"
fi

# 8. Limpar opcache se estiver habilitado
log "8. Limpando opcache PHP..."
php -r "if (function_exists('opcache_reset')) { opcache_reset(); echo 'OPCache limpo\n'; } else { echo 'OPCache não disponível\n'; }"

log "=========================================="
log "CORREÇÃO CONCLUÍDA!"
log "=========================================="
log "Agora teste acessar: https://usadosar.com.br/"
log "Se ainda der erro, execute: php artisan route:list"
