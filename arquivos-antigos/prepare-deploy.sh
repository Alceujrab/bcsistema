#!/bin/bash

# SCRIPT DE DEPLOY SEGURO PARA SERVIDOR - BC SISTEMA
# ==================================================
# Este script prepara os arquivos para upload e deploy no servidor

echo "🚀 PREPARANDO DEPLOY PARA SERVIDOR"
echo "=================================="
echo ""

# Definir diretórios
LOCAL_DIR="/workspaces/bcsistema/bc"
BACKUP_DIR="/workspaces/bcsistema/deploy-backup"
DEPLOY_DIR="/workspaces/bcsistema/deploy-ready"

# Criar diretórios de backup e deploy
mkdir -p "$BACKUP_DIR"
mkdir -p "$DEPLOY_DIR"

echo "📁 Criando backup dos arquivos atuais..."
cp -r "$LOCAL_DIR" "$BACKUP_DIR/bc-$(date +%Y%m%d-%H%M%S)"

echo "📦 Preparando arquivos para deploy..."

# Copiar arquivos essenciais para deploy
rsync -av --exclude='node_modules' \
          --exclude='vendor' \
          --exclude='.git' \
          --exclude='storage/logs/*' \
          --exclude='storage/framework/cache/*' \
          --exclude='storage/framework/sessions/*' \
          --exclude='storage/framework/views/*' \
          --exclude='.env' \
          "$LOCAL_DIR/" "$DEPLOY_DIR/"

echo "🔧 Criando estrutura de deploy..."

# Criar arquivo de dependências do composer
echo "📋 Verificando composer.json..."
if [ -f "$DEPLOY_DIR/composer.json" ]; then
    echo "✅ composer.json encontrado"
else
    echo "❌ composer.json não encontrado"
fi

# Criar diretórios necessários no servidor
mkdir -p "$DEPLOY_DIR/storage/logs"
mkdir -p "$DEPLOY_DIR/storage/framework/cache"
mkdir -p "$DEPLOY_DIR/storage/framework/sessions"
mkdir -p "$DEPLOY_DIR/storage/framework/views"
mkdir -p "$DEPLOY_DIR/bootstrap/cache"

# Definir permissões corretas
echo "🔐 Definindo permissões..."
find "$DEPLOY_DIR/storage" -type d -exec chmod 755 {} \;
find "$DEPLOY_DIR/storage" -type f -exec chmod 644 {} \;
find "$DEPLOY_DIR/bootstrap/cache" -type d -exec chmod 755 {} \;

echo ""
echo "✅ ARQUIVOS PREPARADOS PARA DEPLOY!"
echo "=================================="
echo ""
echo "📂 Arquivos preparados em: $DEPLOY_DIR"
echo "💾 Backup criado em: $BACKUP_DIR"
echo ""
echo "📋 PRÓXIMOS PASSOS:"
echo "1. Comprimir arquivos: tar -czf deploy-bc-sistema.tar.gz -C $DEPLOY_DIR ."
echo "2. Fazer upload via FTP/SFTP"
echo "3. Executar comandos no servidor"
echo ""
