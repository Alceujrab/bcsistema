#!/bin/bash

# SCRIPT DE PREPARAÇÃO PARA DEPLOY - BC SISTEMA MODERNIZADO
# =========================================================
# Deploy para: public_html/bc/

echo "🚀 PREPARANDO DEPLOY PARA public_html/bc/"
echo "========================================"

# Criar diretório de deploy
DEPLOY_DIR="/workspaces/bcsistema/deploy-ready"
SOURCE_DIR="/workspaces/bcsistema/bc"
BACKUP_DIR="/workspaces/bcsistema/backup-deploy"

echo "📁 Criando diretórios..."
rm -rf "$DEPLOY_DIR"
mkdir -p "$DEPLOY_DIR"
mkdir -p "$BACKUP_DIR"

# Fazer backup do atual
echo "💾 Criando backup..."
if [ -d "$SOURCE_DIR" ]; then
    cp -r "$SOURCE_DIR" "$BACKUP_DIR/bc-backup-$(date +%Y%m%d_%H%M%S)"
    echo "✅ Backup criado em: $BACKUP_DIR/"
fi

# Copiar arquivos essenciais (excluindo vendor, node_modules, .git)
echo "📋 Copiando arquivos do projeto..."
rsync -av \
  --exclude='vendor/' \
  --exclude='node_modules/' \
  --exclude='.git/' \
  --exclude='.env' \
  --exclude='storage/logs/*' \
  --exclude='storage/framework/cache/*' \
  --exclude='storage/framework/sessions/*' \
  --exclude='storage/framework/views/*' \
  --exclude='bootstrap/cache/*' \
  --exclude='.phpunit.result.cache' \
  --exclude='tests/' \
  "$SOURCE_DIR/" "$DEPLOY_DIR/"

# Criar .env para produção
echo "⚙️ Criando .env para produção..."
cat > "$DEPLOY_DIR/.env" << 'EOF'
APP_NAME="BC Sistema"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://seudominio.com/bc

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=usadosar_lara962
DB_USERNAME=usadosar_lara962
DB_PASSWORD=SUA_SENHA_AQUI

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
EOF

# Criar estrutura de pastas necessárias
echo "📂 Criando estrutura de pastas..."
mkdir -p "$DEPLOY_DIR/storage/logs"
mkdir -p "$DEPLOY_DIR/storage/framework/cache"
mkdir -p "$DEPLOY_DIR/storage/framework/sessions" 
mkdir -p "$DEPLOY_DIR/storage/framework/views"
mkdir -p "$DEPLOY_DIR/bootstrap/cache"
mkdir -p "$DEPLOY_DIR/storage/app/public"

# Copiar arquivos .gitkeep para manter estrutura
touch "$DEPLOY_DIR/storage/logs/.gitkeep"
touch "$DEPLOY_DIR/storage/framework/cache/.gitkeep"
touch "$DEPLOY_DIR/storage/framework/sessions/.gitkeep"
touch "$DEPLOY_DIR/storage/framework/views/.gitkeep"
touch "$DEPLOY_DIR/bootstrap/cache/.gitkeep"
touch "$DEPLOY_DIR/storage/app/public/.gitkeep"

# Criar script de comandos para execução no servidor
echo "📜 Criando script de comandos do servidor..."
cat > "$DEPLOY_DIR/comandos-servidor.sh" << 'EOF'
#!/bin/bash
# COMANDOS PARA EXECUTAR NO SERVIDOR após upload

echo "🔧 CONFIGURANDO BC SISTEMA NO SERVIDOR"
echo "====================================="

# Navegar para diretório
cd /home/usadosar/public_html/bc

# Configurar permissões
echo "🔐 Configurando permissões..."
chmod -R 755 .
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chmod 644 .env

# Instalar dependências
echo "📦 Instalando dependências..."
if command -v composer &> /dev/null; then
    composer install --optimize-autoloader --no-dev --no-interaction
else
    php composer.phar install --optimize-autoloader --no-dev --no-interaction
fi

# Gerar chave da aplicação
echo "🔑 Gerando chave da aplicação..."
php artisan key:generate --force

# Executar migrações
echo "🗃️ Executando migrações..."
php artisan migrate --force

# Limpar caches
echo "🧹 Limpando caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Otimizar para produção
echo "⚡ Otimizando para produção..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Criar symlink para storage (se necessário)
echo "🔗 Criando symlink do storage..."
php artisan storage:link

echo ""
echo "✅ CONFIGURAÇÃO CONCLUÍDA!"
echo "📋 URLs para testar:"
echo "   - Dashboard: https://seudominio.com/bc/"
echo "   - Transações: https://seudominio.com/bc/transactions"
echo "   - Relatórios: https://seudominio.com/bc/reports"
echo ""
echo "🔍 Para verificar erros:"
echo "   tail -f storage/logs/laravel.log"
EOF

chmod +x "$DEPLOY_DIR/comandos-servidor.sh"

# Verificar arquivos críticos
echo "🔍 Verificando arquivos críticos..."
CRITICAL_FILES=(
    "app/Http/Controllers/DashboardController.php"
    "app/Http/Controllers/TransactionController.php"
    "resources/views/layouts/app.blade.php"
    "resources/views/dashboard.blade.php"
    "resources/views/transactions/index.blade.php"
    "resources/views/transactions/show.blade.php"
    "resources/views/transactions/edit.blade.php"
    "routes/web.php"
    "composer.json"
    "public/index.php"
)

echo "📋 Verificando arquivos essenciais:"
for file in "${CRITICAL_FILES[@]}"; do
    if [ -f "$DEPLOY_DIR/$file" ]; then
        echo "✅ $file"
    else
        echo "❌ $file - FALTANDO!"
    fi
done

# Criar arquivo comprimido para upload
echo ""
echo "📦 Criando arquivo para upload..."
cd "/workspaces/bcsistema"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
tar -czf "deploy-bc-sistema-$TIMESTAMP.tar.gz" -C deploy-ready .

echo ""
echo "🎉 DEPLOY PREPARADO COM SUCESSO!"
echo "================================"
echo ""
echo "📁 Arquivo criado: deploy-bc-sistema-$TIMESTAMP.tar.gz"
echo "📂 Conteúdo em: $DEPLOY_DIR/"
echo ""
echo "📋 PRÓXIMOS PASSOS:"
echo "1. 📤 Upload do arquivo .tar.gz para seu servidor"
echo "2. 📁 Extrair para /home/usadosar/public_html/bc/"
echo "3. 🔧 Executar: chmod +x comandos-servidor.sh && ./comandos-servidor.sh"
echo "4. ⚙️ Editar .env com suas credenciais do servidor"
echo "5. 🌐 Acessar: https://seudominio.com/bc/"
echo ""
echo "💡 Dica: Use FileZilla, WinSCP ou cPanel File Manager para upload"
echo ""
echo "🎯 SISTEMA BC TOTALMENTE MODERNIZADO PRONTO PARA DEPLOY!"
