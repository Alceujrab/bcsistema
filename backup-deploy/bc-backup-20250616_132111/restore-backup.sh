#!/bin/bash

# Script de Restore - Reverter Deploy em Caso de Problemas
# Execute com: bash restore-backup.sh caminho/para/backup.tar.gz

set -e

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}"
}

warn() {
    echo -e "${YELLOW}[AVISO] $1${NC}"
}

error() {
    echo -e "${RED}[ERRO] $1${NC}"
}

# Verificar se o arquivo de backup foi fornecido
if [ -z "$1" ]; then
    error "Por favor, forneça o caminho para o arquivo de backup"
    echo "Uso: bash restore-backup.sh caminho/para/backup.tar.gz"
    echo ""
    echo "Backups disponíveis:"
    ls -la backups/ 2>/dev/null || echo "Nenhum backup encontrado"
    exit 1
fi

BACKUP_FILE="$1"

# Verificar se o arquivo de backup existe
if [ ! -f "$BACKUP_FILE" ]; then
    error "Arquivo de backup não encontrado: $BACKUP_FILE"
    exit 1
fi

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    error "Este script deve ser executado no diretório raiz do Laravel"
    exit 1
fi

log "🔄 Iniciando processo de restore..."
log "📦 Arquivo de backup: $BACKUP_FILE"

# Confirmar ação
read -p "⚠️  ATENÇÃO: Isso irá substituir todos os arquivos atuais. Continuar? (y/N): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    log "Operação cancelada pelo usuário"
    exit 0
fi

# Colocar em manutenção
log "🔧 Colocando site em modo de manutenção..."
php artisan down --message="Restaurando backup..." --retry=120

# Criar backup do estado atual antes do restore
log "💾 Criando backup do estado atual..."
CURRENT_BACKUP="backups/pre_restore_$(date +%Y%m%d_%H%M%S).tar.gz"
mkdir -p backups
tar -czf "$CURRENT_BACKUP" \
    --exclude=backups \
    --exclude=node_modules \
    --exclude=vendor \
    --exclude=storage/logs/*.log \
    .

log "✅ Backup atual criado: $CURRENT_BACKUP"

# Restaurar arquivos
log "📂 Restaurando arquivos do backup..."

# Preservar algumas pastas importantes
TEMP_DIR="temp_restore_$(date +%s)"
mkdir -p "$TEMP_DIR"

# Salvar vendor, node_modules e storage se existirem
[ -d "vendor" ] && mv vendor "$TEMP_DIR/" || true
[ -d "node_modules" ] && mv node_modules "$TEMP_DIR/" || true
[ -d "storage/logs" ] && cp -r storage/logs "$TEMP_DIR/" || true

# Extrair backup
tar -xzf "$BACKUP_FILE"

# Restaurar pastas salvas
[ -d "$TEMP_DIR/vendor" ] && mv "$TEMP_DIR/vendor" . || true
[ -d "$TEMP_DIR/node_modules" ] && mv "$TEMP_DIR/node_modules" . || true
[ -d "$TEMP_DIR/logs" ] && cp -r "$TEMP_DIR/logs" storage/ || true

# Limpar temp
rm -rf "$TEMP_DIR"

# Reinstalar dependências se necessário
if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    log "📚 Reinstalando dependências..."
    composer install --no-dev --optimize-autoloader
fi

# Limpar caches
log "🧹 Limpando caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Recriar caches
log "⚡ Recriando caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ajustar permissões
log "🔧 Ajustando permissões..."
chmod -R 775 storage bootstrap/cache

# Verificar integridade
log "🔍 Verificando integridade do sistema..."
php artisan --version > /dev/null && log "✅ Laravel OK" || error "❌ Problema com Laravel"

# Tirar do modo de manutenção
log "🟢 Tirando site do modo de manutenção..."
php artisan up

log "🎉 Restore concluído com sucesso!"
log "💾 Backup do estado anterior salvo em: $CURRENT_BACKUP"

echo ""
echo "======================================"
echo "      RESTORE CONCLUÍDO               "
echo "======================================"
echo "📅 Data: $(date)"
echo "📦 Backup restaurado: $BACKUP_FILE"
echo "💾 Backup anterior: $CURRENT_BACKUP"
echo "✅ Status: SUCESSO"
echo "======================================"

exit 0
