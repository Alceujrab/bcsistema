#!/bin/bash

# Script para fazer deploy das correções do ReconciliationController
# Data: $(date)
# Correções: Blade sections e Collection error

echo "=== DEPLOY DAS CORREÇÕES - RECONCILIATION ==="
echo "Data: $(date)"
echo ""

# Definir variáveis
LOCAL_PATH="/workspaces/bcsistema/bc"
REMOTE_PATH="/var/www/bc/public"  # Ajuste conforme seu servidor
SERVER_USER="seu_usuario"        # Ajuste conforme seu servidor
SERVER_HOST="usadosar.com.br"    # Seu servidor

echo "📁 Arquivos que serão atualizados:"
echo "1. resources/views/reconciliations/create.blade.php"
echo "2. app/Http/Controllers/ReconciliationController.php"
echo ""

# Criar backup dos arquivos no servidor (opcional)
echo "💾 Criando backup dos arquivos atuais..."
ssh $SERVER_USER@$SERVER_HOST "
    cd $REMOTE_PATH && 
    mkdir -p backup-reconciliation-$(date +%Y%m%d_%H%M%S) &&
    cp resources/views/reconciliations/create.blade.php backup-reconciliation-$(date +%Y%m%d_%H%M%S)/ 2>/dev/null || true &&
    cp app/Http/Controllers/ReconciliationController.php backup-reconciliation-$(date +%Y%m%d_%H%M%S)/ 2>/dev/null || true
"

# Upload dos arquivos corrigidos
echo "🚀 Fazendo upload dos arquivos corrigidos..."

# Upload da view
scp "$LOCAL_PATH/resources/views/reconciliations/create.blade.php" \
    $SERVER_USER@$SERVER_HOST:$REMOTE_PATH/resources/views/reconciliations/

# Upload do controller
scp "$LOCAL_PATH/app/Http/Controllers/ReconciliationController.php" \
    $SERVER_USER@$SERVER_HOST:$REMOTE_PATH/app/Http/Controllers/

# Limpar cache no servidor
echo "🧹 Limpando cache no servidor..."
ssh $SERVER_USER@$SERVER_HOST "
    cd $REMOTE_PATH && 
    php artisan view:clear &&
    php artisan cache:clear &&
    php artisan config:clear &&
    php artisan route:clear
"

echo ""
echo "✅ Deploy concluído com sucesso!"
echo "📝 Correções aplicadas:"
echo "   - Erro de seções Blade corrigido"
echo "   - Erro de Collection convertida para int corrigido"
echo ""
echo "🔍 Teste a página: https://usadosar.com.br/bc/public/reconciliations/create"
echo ""
