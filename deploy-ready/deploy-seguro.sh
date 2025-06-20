#!/bin/bash

# Script de Deploy Seguro - Sistema de Conciliação Bancária
# Execute este script no diretório raiz do projeto Laravel

echo "=========================================="
echo "  DEPLOY SEGURO - SISTEMA BANCÁRIO"
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

# 1. Backup do banco antes de qualquer alteração
log "1. Criando backup do banco de dados..."
BACKUP_FILE="backup_$(date +%Y%m%d_%H%M%S).sql"
mysqldump -h localhost -P 3306 -u usadosar_lara962 -p'[17pvS1-16' usadosar_lara962 > $BACKUP_FILE
if [ $? -eq 0 ]; then
    log "Backup criado: $BACKUP_FILE"
else
    error "Falha ao criar backup. Abortando deploy."
    exit 1
fi

# 2. Colocar aplicação em modo de manutenção
log "2. Colocando aplicação em modo de manutenção..."
php artisan down --message="Sistema em atualização. Voltamos em breve!" --retry=60

# 3. Limpar caches existentes
log "3. Limpando caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 4. Remover migrations duplicadas ou problemáticas
log "4. Removendo migrations problemáticas..."
if [ -f "database/migrations/2025_06_13_000001_enhance_transactions_table.php" ]; then
    rm database/migrations/2025_06_13_000001_enhance_transactions_table.php
    log "Removida migration duplicada: 2025_06_13_000001_enhance_transactions_table.php"
fi

if [ -f "database/migrations/2025_06_13_000002_enhance_categories_table.php" ]; then
    rm database/migrations/2025_06_13_000002_enhance_categories_table.php
    log "Removida migration duplicada: 2025_06_13_000002_enhance_categories_table.php"
fi

if [ -f "database/migrations/2025_06_13_143049_enhance_transactions_table.php" ]; then
    rm database/migrations/2025_06_13_143049_enhance_transactions_table.php
    log "Removida migration duplicada: 2025_06_13_143049_enhance_transactions_table.php"
fi

if [ -f "database/migrations/2025_06_13_143050_enhance_categories_table.php" ]; then
    rm database/migrations/2025_06_13_143050_enhance_categories_table.php
    log "Removida migration duplicada: 2025_06_13_143050_enhance_categories_table.php"
fi

# 5. Executar migrations seguras
log "5. Executando migrations seguras..."
php artisan migrate --force
if [ $? -ne 0 ]; then
    error "Falha nas migrations. Restaurando backup..."
    mysql -h localhost -P 3306 -u usadosar_lara962 -p'[17pvS1-16' usadosar_lara962 < $BACKUP_FILE
    php artisan up
    exit 1
fi

# 6. Otimizar autoload
log "6. Otimizando autoload..."
composer dump-autoload --optimize

# 7. Cachear configurações para produção
log "7. Criando caches otimizados..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Verificar se o sistema está funcionando
log "8. Verificando sistema..."
php artisan config:show database.default --no-ansi | grep -q "mysql"
if [ $? -eq 0 ]; then
    log "Conexão com banco OK"
else
    error "Problema na conexão com banco"
fi

# 9. Retirar do modo de manutenção
log "9. Retirando do modo de manutenção..."
php artisan up

log "=========================================="
log "DEPLOY CONCLUÍDO COM SUCESSO!"
log "=========================================="
log "Backup criado em: $BACKUP_FILE"
log "Sistema está funcionando normalmente."

# 10. Mostrar status final
log "10. Status final do sistema:"
php artisan migrate:status
