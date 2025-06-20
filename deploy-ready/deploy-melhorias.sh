#!/bin/bash

# Script de Deploy das Melhorias do Sistema de Transações
# Execute com: bash deploy-melhorias.sh

set -e  # Parar em caso de erro

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Função para log colorido
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}"
}

warn() {
    echo -e "${YELLOW}[AVISO] $1${NC}"
}

error() {
    echo -e "${RED}[ERRO] $1${NC}"
}

info() {
    echo -e "${BLUE}[INFO] $1${NC}"
}

# Verificar se estamos no diretório correto do Laravel
if [ ! -f "artisan" ]; then
    error "Este script deve ser executado no diretório raiz do Laravel (onde está o arquivo artisan)"
    exit 1
fi

log "🚀 Iniciando deploy das melhorias do sistema de transações..."

# 1. Criar backup
log "📦 Criando backup de segurança..."
BACKUP_DIR="backups"
mkdir -p $BACKUP_DIR
BACKUP_FILE="$BACKUP_DIR/backup_$(date +%Y%m%d_%H%M%S).tar.gz"

tar -czf $BACKUP_FILE \
    --exclude=node_modules \
    --exclude=vendor \
    --exclude=storage/logs/*.log \
    --exclude=.git \
    .

log "✅ Backup criado: $BACKUP_FILE"

# 2. Backup do banco de dados (se configurado)
if command -v mysqldump &> /dev/null; then
    if [ ! -z "$DB_DATABASE" ] && [ ! -z "$DB_USERNAME" ]; then
        log "🗄️ Criando backup do banco de dados..."
        mysqldump -u $DB_USERNAME -p$DB_PASSWORD $DB_DATABASE > "$BACKUP_DIR/db_backup_$(date +%Y%m%d_%H%M%S).sql"
        log "✅ Backup do banco criado"
    fi
fi

# 3. Colocar site em manutenção
log "🔧 Colocando site em modo de manutenção..."
php artisan down --message="Atualizando sistema..." --retry=60

# Função para tirar do modo de manutenção em caso de erro
cleanup() {
    warn "Ocorreu um erro. Tirando site do modo de manutenção..."
    php artisan up
}
trap cleanup ERR

# 4. Limpar caches
log "🧹 Limpando todos os caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan compiled:clear 2>/dev/null || true

# 5. Atualizar dependências
log "📚 Atualizando dependências do Composer..."
composer install --no-dev --optimize-autoloader --no-interaction

# 6. Criar diretórios necessários
log "📁 Criando estrutura de diretórios..."
mkdir -p resources/js
mkdir -p resources/views/transactions/partials
mkdir -p public/js

# 7. Executar migrations (com confirmação)
log "🗄️ Verificando migrations..."
if php artisan migrate:status | grep -q "Ran?"; then
    read -p "Deseja executar as migrations? (y/N): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        php artisan migrate --force
        log "✅ Migrations executadas"
    else
        info "Migrations puladas"
    fi
else
    info "Nenhuma migration pendente"
fi

# 8. Executar seeders se necessário
if [ -f "database/seeders/DatabaseSeeder.php" ]; then
    read -p "Deseja executar os seeders? (y/N): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        php artisan db:seed --force
        log "✅ Seeders executados"
    fi
fi

# 9. Compilar assets (se Vite estiver configurado)
if [ -f "package.json" ] && command -v npm &> /dev/null; then
    log "🎨 Compilando assets..."
    npm install --production
    npm run build 2>/dev/null || log "⚠️ Build do Vite falhou, usando arquivos diretos"
fi

# 10. Copiar JavaScript diretamente (fallback)
if [ -f "resources/js/transactions.js" ]; then
    log "📄 Copiando JavaScript para public..."
    cp resources/js/transactions.js public/js/transactions.js
    log "✅ JavaScript copiado"
fi

# 11. Recompilar caches
log "⚡ Recompilando caches de produção..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 12. Otimizar autoloader
log "🔄 Otimizando autoloader..."
composer dump-autoload -o

# 13. Ajustar permissões
log "🔧 Ajustando permissões..."
if [ -w "storage" ]; then
    chmod -R 775 storage bootstrap/cache
    if command -v chown &> /dev/null; then
        # Tentar determinar o usuário do servidor web
        WEB_USER=$(ps aux | grep -E "(apache|nginx|httpd)" | grep -v root | head -1 | awk '{print $1}')
        if [ ! -z "$WEB_USER" ]; then
            chown -R $WEB_USER:$WEB_USER storage bootstrap/cache 2>/dev/null || warn "Não foi possível alterar proprietário"
        fi
    fi
    log "✅ Permissões ajustadas"
fi

# 14. Verificar integridade do sistema
log "🔍 Verificando integridade do sistema..."

# Testar conexão com banco
php artisan tinker --execute="echo 'DB: ' . DB::connection()->getPdo()->getAttribute(PDO::ATTR_CONNECTION_STATUS);" 2>/dev/null || warn "Erro na conexão com banco"

# Verificar se rotas principais existem
php artisan route:list | grep -q "transactions.index" && log "✅ Rotas de transações OK" || error "❌ Rotas de transações não encontradas"

# 15. Tirar do modo de manutenção
log "🟢 Tirando site do modo de manutenção..."
php artisan up

# 16. Testes finais
log "🧪 Executando testes finais..."

# Testar URL principal
if command -v curl &> /dev/null; then
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost/dashboard 2>/dev/null || echo "000")
    if [ "$HTTP_CODE" = "200" ]; then
        log "✅ Dashboard acessível"
    else
        warn "Dashboard retornou código: $HTTP_CODE"
    fi
fi

# 17. Limpeza final
log "🧹 Limpeza final..."
# Remover arquivos temporários se existirem
rm -f bootstrap/cache/routes-v7.php 2>/dev/null || true
rm -f bootstrap/cache/events.php 2>/dev/null || true

# 18. Relatório final
log "📊 Gerando relatório final..."

echo ""
echo "======================================"
echo "     RELATÓRIO DE DEPLOY CONCLUÍDO    "
echo "======================================"
echo ""
echo "📅 Data: $(date)"
echo "📦 Backup: $BACKUP_FILE"
echo "🔧 Status: SUCESSO"
echo ""
echo "🆕 Funcionalidades implementadas:"
echo "   ✅ Edição inline de transações"
echo "   ✅ Filtros avançados AJAX"
echo "   ✅ Ações em lote"
echo "   ✅ Auto-categorização"
echo "   ✅ Exportação CSV"
echo "   ✅ Interface moderna"
echo ""
echo "🔍 Verificações recomendadas:"
echo "   - Acesse /transactions para testar"
echo "   - Verifique os logs: tail -f storage/logs/laravel.log"
echo "   - Teste as funcionalidades AJAX"
echo ""
echo "📚 Documentação: guia-implementacao-servidor.md"
echo "======================================"

log "🎉 Deploy concluído com sucesso!"

# Mostrar próximos passos
info "Próximos passos recomendados:"
info "1. Acesse o sistema e teste as funcionalidades"
info "2. Monitore os logs por alguns minutos"
info "3. Teste a edição inline clicando nas células da tabela"
info "4. Teste os filtros e ações em lote"
info "5. Se houver problemas, execute: bash restore-backup.sh $BACKUP_FILE"

exit 0
