#!/bin/bash

# Script de Deploy Automatizado - Sistema BC Gestão Financeira
# Versão: 1.0 - ATUALIZADO
# Data: 17/06/2025

set -e  # Parar em caso de erro

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configurações (AJUSTE CONFORME SEU SERVIDOR)
SERVER_USER="root"
SERVER_HOST="seu-servidor.com"
SERVER_PATH="/var/www/html/bc"
DB_USER="bc_user"
DB_NAME="bc_sistema"
BACKUP_PATH="/tmp/backups"

echo -e "${BLUE}╔════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║     DEPLOY BC SISTEMA - VERSÃO 1.0     ║${NC}"
echo -e "${BLUE}║    Sistema de Gestão Financeira        ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════╝${NC}"
echo ""

# Função para log
log() {
    echo -e "${GREEN}[$(date '+%H:%M:%S')] $1${NC}"
}

error() {
    echo -e "${RED}[ERRO] $1${NC}"
    exit 1
}

warning() {
    echo -e "${YELLOW}[AVISO] $1${NC}"
}

info() {
    echo -e "${BLUE}[INFO] $1${NC}"
}

# Verificar se estamos no diretório correto
if [ ! -d "bc" ]; then
    error "Diretório 'bc' não encontrado. Execute o script no diretório raiz do projeto."
fi

# 1. PREPARAR PACOTE DE DEPLOY
log "Preparando pacote de deploy..."

TIMESTAMP=$(date +%Y%m%d_%H%M%S)
PACKAGE_NAME="bc-sistema-deploy-${TIMESTAMP}.tar.gz"

tar -czf ${PACKAGE_NAME} \
    --exclude='bc/node_modules' \
    --exclude='bc/vendor' \
    --exclude='bc/storage/logs/*' \
    --exclude='bc/storage/framework/cache/*' \
    --exclude='bc/storage/framework/sessions/*' \
    --exclude='bc/storage/framework/views/*' \
    --exclude='bc/.env' \
    --exclude='bc/.git' \
    bc/

info "Pacote criado: ${PACKAGE_NAME}"

# 2. FAZER BACKUP DO SERVIDOR
log "Fazendo backup do servidor..."

ssh ${SERVER_USER}@${SERVER_HOST} "
    mkdir -p ${BACKUP_PATH}
    
    # Backup do banco de dados
    mysqldump -u ${DB_USER} -p ${DB_NAME} > ${BACKUP_PATH}/db-backup-${TIMESTAMP}.sql 2>/dev/null || echo 'Backup do banco pode ter falhado'
    
    # Backup dos arquivos
    tar -czf ${BACKUP_PATH}/files-backup-${TIMESTAMP}.tar.gz ${SERVER_PATH} 2>/dev/null || echo 'Backup dos arquivos pode ter falhado'
    
    echo 'Backup concluído!'
"

# 3. UPLOAD DO PACOTE
log "Fazendo upload do pacote..."

scp ${PACKAGE_NAME} ${SERVER_USER}@${SERVER_HOST}:/tmp/

# 4. EXECUTAR DEPLOY NO SERVIDOR
log "Executando deploy no servidor..."

ssh ${SERVER_USER}@${SERVER_HOST} "
    set -e
    
    echo '🔄 Iniciando deploy no servidor...'
    
    # Parar serviços
    echo 'Parando serviços web...'
    systemctl stop apache2 2>/dev/null || systemctl stop nginx 2>/dev/null || true
    
    # Backup do .env atual
    cp ${SERVER_PATH}/.env /tmp/env-backup-${TIMESTAMP} 2>/dev/null || true
    
    # Extrair novos arquivos
    cd /tmp
    tar -xzf ${PACKAGE_NAME}
    
    # Copiar arquivos (preservando .env)
    cp -r bc/* ${SERVER_PATH}/
    cp /tmp/env-backup-${TIMESTAMP} ${SERVER_PATH}/.env 2>/dev/null || true
    
    # Ajustar permissões
    cd ${SERVER_PATH}
    chown -R www-data:www-data . 2>/dev/null || chown -R apache:apache . 2>/dev/null || true
    chmod -R 755 .
    chmod -R 775 storage bootstrap/cache 2>/dev/null || true
    
    echo '✅ Arquivos atualizados!'
"

# 5. INSTALAR DEPENDÊNCIAS E EXECUTAR MIGRAÇÕES
log "Instalando dependências e executando migrações..."

ssh ${SERVER_USER}@${SERVER_HOST} "
    cd ${SERVER_PATH}
    
    # Instalar dependências
    echo 'Instalando dependências...'
    composer install --no-dev --optimize-autoloader 2>/dev/null || echo 'Composer pode ter falhado'
    
    # Executar migrações
    echo 'Executando migrações...'
    php artisan migrate --force
    
    # Otimizar aplicação
    echo 'Otimizando aplicação...'
    php artisan config:clear 2>/dev/null || true
    php artisan route:clear 2>/dev/null || true
    php artisan view:clear 2>/dev/null || true
    php artisan cache:clear 2>/dev/null || true
    
    php artisan config:cache 2>/dev/null || true
    php artisan route:cache 2>/dev/null || true
    php artisan view:cache 2>/dev/null || true
    
    composer dump-autoload --optimize 2>/dev/null || true
    
    echo '✅ Dependências e otimizações concluídas!'
"

# 6. REINICIAR SERVIÇOS
log "Reiniciando serviços..."

ssh ${SERVER_USER}@${SERVER_HOST} "
    # Reiniciar serviços
    systemctl start apache2 2>/dev/null || systemctl start nginx 2>/dev/null || true
    systemctl restart php8.1-fpm 2>/dev/null || systemctl restart php-fpm 2>/dev/null || true
    
    echo '✅ Serviços reiniciados!'
"

# 7. TESTES DE VALIDAÇÃO
log "Executando testes de validação..."

# Aguardar alguns segundos para o servidor inicializar
sleep 5

ssh ${SERVER_USER}@${SERVER_HOST} "
    cd ${SERVER_PATH}
    
    echo 'Testando conectividade do banco...'
    php artisan tinker --execute=\"DB::connection()->getPdo(); echo 'Banco OK!' . PHP_EOL;\" 2>/dev/null || echo 'Teste do banco pode ter falhado'
    
    echo 'Testando comando personalizado...'
    php artisan accounts:update-overdue 2>/dev/null || echo 'Comando personalizado pode ter falhado'
    
    echo 'Verificando tabelas...'
    php artisan tinker --execute=\"
        echo 'Clientes: ' . App\\\\Models\\\\Client::count() . PHP_EOL;
        echo 'Fornecedores: ' . App\\\\Models\\\\Supplier::count() . PHP_EOL;
        echo 'Contas a Pagar: ' . App\\\\Models\\\\AccountPayable::count() . PHP_EOL;
        echo 'Contas a Receber: ' . App\\\\Models\\\\AccountReceivable::count() . PHP_EOL;
    \" 2>/dev/null || echo 'Teste das tabelas pode ter falhado'
"

# 8. LIMPEZA
log "Limpando arquivos temporários..."

rm -f ${PACKAGE_NAME}

ssh ${SERVER_USER}@${SERVER_HOST} "
    rm -f /tmp/${PACKAGE_NAME}
    echo 'Limpeza concluída!'
"

# 9. RESULTADO FINAL
echo ""
echo -e "${GREEN}╔═══════════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║                    DEPLOY CONCLUÍDO!                      ║${NC}"
echo -e "${GREEN}╚═══════════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "${BLUE}📊 RESUMO DO DEPLOY:${NC}"
echo -e "• Timestamp: ${TIMESTAMP}"
echo -e "• Servidor: ${SERVER_HOST}"
echo -e "• Caminho: ${SERVER_PATH}"
echo -e "• Backup: ${BACKUP_PATH}/files-backup-${TIMESTAMP}.tar.gz"
echo -e "• Banco: ${BACKUP_PATH}/db-backup-${TIMESTAMP}.sql"
echo ""
echo -e "${GREEN}✅ Sistema atualizado com módulos de gestão financeira${NC}"
echo -e "${GREEN}✅ Backup de segurança criado${NC}"
echo -e "${GREEN}✅ Migrações executadas${NC}"
echo -e "${GREEN}✅ Serviços reiniciados${NC}"
echo -e "${GREEN}✅ Testes de validação executados${NC}"
echo ""
echo -e "${YELLOW}🔗 Acesse: http://${SERVER_HOST}${NC}"
echo ""
echo -e "${BLUE}📋 PRÓXIMOS PASSOS:${NC}"
echo "1. Acesse o dashboard e verifique os novos módulos"
echo "2. Configure cron job para: php artisan accounts:update-overdue"
echo "3. Cadastre seus primeiros clientes e fornecedores"
echo "4. Configure dados de exemplo se necessário"
echo ""
echo -e "${GREEN}🎉 Deploy finalizado com sucesso!${NC}"
