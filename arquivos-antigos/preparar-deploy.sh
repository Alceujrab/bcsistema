#!/bin/bash

# Script de Preparação para Deploy - BC Sistema
# Execute este script no seu ambiente local para preparar o deploy

echo "🚀 PREPARANDO BC SISTEMA PARA DEPLOY"
echo "===================================="
echo ""

# Cores
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# Verificar se estamos no diretório correto
if [ ! -d "bc" ]; then
    echo -e "${RED}❌ Erro: Pasta 'bc' não encontrada!${NC}"
    echo "Execute este script no diretório /workspaces/bcsistema"
    exit 1
fi

echo -e "${BLUE}1. Verificando sistema antes do deploy...${NC}"

# Verificar arquivos essenciais
cd bc/

if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Arquivo artisan não encontrado!${NC}"
    exit 1
fi

if [ ! -f "composer.json" ]; then
    echo -e "${RED}❌ Arquivo composer.json não encontrado!${NC}"
    exit 1
fi

if [ ! -d "app" ]; then
    echo -e "${RED}❌ Pasta app não encontrada!${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Estrutura básica do Laravel verificada${NC}"

# Verificar banco de dados
if [ -f "database/database.sqlite" ]; then
    DB_SIZE=$(du -h database/database.sqlite | cut -f1)
    echo -e "${GREEN}✅ Banco SQLite encontrado (${DB_SIZE})${NC}"
else
    echo -e "${YELLOW}⚠️  Banco SQLite não encontrado - será criado no servidor${NC}"
fi

# Verificar .env
if [ -f ".env" ]; then
    echo -e "${GREEN}✅ Arquivo .env encontrado${NC}"
else
    echo -e "${YELLOW}⚠️  Arquivo .env não encontrado - será criado no servidor${NC}"
fi

cd ..

echo ""
echo -e "${BLUE}2. Criando pacote de deploy otimizado...${NC}"

# Nome do arquivo com timestamp
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
FILENAME="bc-sistema-deploy-${TIMESTAMP}.tar.gz"

# Criar pacote excluindo arquivos desnecessários
echo -e "${YELLOW}📦 Compactando sistema...${NC}"

tar -czf "$FILENAME" \
    --exclude='bc/node_modules' \
    --exclude='bc/.git' \
    --exclude='bc/tests' \
    --exclude='bc/storage/logs/*.log' \
    --exclude='bc/storage/framework/cache/*' \
    --exclude='bc/storage/framework/sessions/*' \
    --exclude='bc/storage/framework/views/*' \
    --exclude='bc/bootstrap/cache/*.php' \
    bc/

# Verificar se foi criado
if [ -f "$FILENAME" ]; then
    SIZE=$(du -h "$FILENAME" | cut -f1)
    echo -e "${GREEN}✅ Pacote criado: ${FILENAME} (${SIZE})${NC}"
else
    echo -e "${RED}❌ Erro ao criar pacote!${NC}"
    exit 1
fi

echo ""
echo -e "${BLUE}3. Criando script de deploy para o servidor...${NC}"

# Criar script de deploy
cat > "deploy-servidor-${TIMESTAMP}.sh" << 'EOF'
#!/bin/bash

# Script de Deploy no Servidor - BC Sistema
# Execute este script no seu servidor após fazer upload do arquivo .tar.gz

echo "🚀 EXECUTANDO DEPLOY DO BC SISTEMA NO SERVIDOR"
echo "=============================================="

# Cores
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# Configurações (EDITE CONFORME SEU SERVIDOR)
PROJECT_NAME="sistema-financeiro"
WEB_DIR="/home/$(whoami)/public_html"  # ou /var/www/html
DOMAIN="seudominio.com"

echo -e "${YELLOW}Configurações atuais:${NC}"
echo "- Diretório web: $WEB_DIR"
echo "- Nome do projeto: $PROJECT_NAME"
echo "- Domínio: $DOMAIN"
echo ""

read -p "As configurações estão corretas? (s/n): " -n 1 -r
echo ""
if [[ ! $REPLY =~ ^[Ss]$ ]]; then
    echo "Edite as configurações no início do script e execute novamente."
    exit 1
fi

# Verificar se o arquivo tar.gz existe
TAR_FILE=$(ls bc-sistema-deploy-*.tar.gz 2>/dev/null | head -1)
if [ -z "$TAR_FILE" ]; then
    echo -e "${RED}❌ Arquivo bc-sistema-deploy-*.tar.gz não encontrado!${NC}"
    echo "Faça upload do arquivo de deploy primeiro."
    exit 1
fi

echo -e "${BLUE}1. Preparando diretório...${NC}"

# Criar backup se já existir
if [ -d "$WEB_DIR/$PROJECT_NAME" ]; then
    echo -e "${YELLOW}📦 Fazendo backup do sistema atual...${NC}"
    mv "$WEB_DIR/$PROJECT_NAME" "$WEB_DIR/${PROJECT_NAME}-backup-$(date +%Y%m%d_%H%M%S)"
fi

# Criar diretório
mkdir -p "$WEB_DIR"
cd "$WEB_DIR"

echo -e "${BLUE}2. Extraindo sistema...${NC}"
tar -xzf ~/"$TAR_FILE"
mv bc "$PROJECT_NAME"
cd "$PROJECT_NAME"

echo -e "${BLUE}3. Configurando permissões...${NC}"
chmod -R 755 .
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# Tentar definir proprietário (pode precisar de sudo)
if command -v chown >/dev/null 2>&1; then
    chown -R www-data:www-data storage/ 2>/dev/null || true
    chown -R www-data:www-data bootstrap/cache/ 2>/dev/null || true
fi

echo -e "${BLUE}4. Configurando ambiente...${NC}"

# Copiar .env se não existir
if [ ! -f ".env" ]; then
    cp .env.example .env
    echo -e "${YELLOW}⚠️  Arquivo .env criado - EDITE as configurações!${NC}"
fi

# Verificar se o Composer está disponível
if ! command -v composer &> /dev/null; then
    echo -e "${YELLOW}📦 Instalando Composer...${NC}"
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer 2>/dev/null || mv composer.phar ~/composer
    COMPOSER_CMD="~/composer"
else
    COMPOSER_CMD="composer"
fi

echo -e "${BLUE}5. Instalando dependências...${NC}"
$COMPOSER_CMD install --optimize-autoloader --no-dev

echo -e "${BLUE}6. Configurando aplicação...${NC}"
php artisan key:generate --force

# Configurar banco de dados
echo -e "${BLUE}7. Configurando banco de dados...${NC}"

# Criar banco SQLite se não existir
if [ ! -f "database/database.sqlite" ]; then
    touch database/database.sqlite
    chmod 664 database/database.sqlite
fi

# Executar migrations
php artisan migrate --force

# Popular dados iniciais
echo -e "${BLUE}8. Populando dados iniciais...${NC}"
php artisan db:seed --class=CategorySeeder --force
php artisan db:seed --class=SystemSettingsSeeder --force

echo -e "${BLUE}9. Otimizando sistema...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link

echo -e "${BLUE}10. Configuração final...${NC}"

# Criar .htaccess se não existir
if [ ! -f "public/.htaccess" ]; then
    cat > public/.htaccess << 'HTACCESS'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
HTACCESS
fi

echo ""
echo -e "${GREEN}✅ DEPLOY CONCLUÍDO COM SUCESSO!${NC}"
echo ""
echo -e "${YELLOW}PRÓXIMOS PASSOS:${NC}"
echo "1. Edite o arquivo .env com as configurações do seu servidor"
echo "2. Configure o virtual host/DNS para apontar para a pasta public/"
echo "3. Acesse: https://$DOMAIN"
echo ""
echo -e "${YELLOW}ESTRUTURA CRIADA:${NC}"
echo "📁 $WEB_DIR/$PROJECT_NAME/"
echo "   📁 public/ ← Aponte seu domínio aqui"
echo "   📄 .env ← Configure suas variáveis"
echo ""
echo -e "${BLUE}Para testar localmente:${NC}"
echo "cd $WEB_DIR/$PROJECT_NAME && php artisan serve --host=0.0.0.0 --port=8000"
EOF

chmod +x "deploy-servidor-${TIMESTAMP}.sh"

echo -e "${GREEN}✅ Script de deploy criado: deploy-servidor-${TIMESTAMP}.sh${NC}"

echo ""
echo -e "${BLUE}4. Criando guia de upload...${NC}"

# Criar guia de upload
cat > "instrucoes-upload-${TIMESTAMP}.txt" << EOF
📤 INSTRUÇÕES DE UPLOAD PARA O SERVIDOR
======================================

1. ARQUIVOS PARA UPLOAD:
   ✅ ${FILENAME}
   ✅ deploy-servidor-${TIMESTAMP}.sh

2. MÉTODOS DE UPLOAD:

   A) Via SCP (Linux/Mac):
   scp ${FILENAME} usuario@servidor.com:/home/usuario/
   scp deploy-servidor-${TIMESTAMP}.sh usuario@servidor.com:/home/usuario/

   B) Via FileZilla/WinSCP:
   - Conecte-se ao seu servidor
   - Navegue para /home/usuario/ (ou diretório home)
   - Faça upload dos 2 arquivos

   C) Via Painel de Controle:
   - Acesse o gerenciador de arquivos
   - Faça upload dos 2 arquivos

3. EXECUTAR NO SERVIDOR:
   ssh usuario@servidor.com
   chmod +x deploy-servidor-${TIMESTAMP}.sh
   ./deploy-servidor-${TIMESTAMP}.sh

4. CONFIGURAR .env NO SERVIDOR:
   - Editar as configurações de banco de dados
   - Definir APP_URL com seu domínio
   - Configurar email se necessário

5. APONTAR DOMÍNIO:
   - Configure DNS/Virtual Host para apontar para:
   /home/usuario/public_html/sistema-financeiro/public/

PRONTO! Seu sistema estará online! 🎉
EOF

echo -e "${GREEN}✅ Guia de instruções criado: instrucoes-upload-${TIMESTAMP}.txt${NC}"

echo ""
echo -e "${YELLOW}=== RESUMO DOS ARQUIVOS CRIADOS ===${NC}"
echo -e "${GREEN}📦 ${FILENAME}${NC} (Sistema completo)"
echo -e "${GREEN}🔧 deploy-servidor-${TIMESTAMP}.sh${NC} (Script de instalação)"
echo -e "${GREEN}📝 instrucoes-upload-${TIMESTAMP}.txt${NC} (Guia de upload)"

echo ""
echo -e "${BLUE}📋 PRÓXIMOS PASSOS:${NC}"
echo "1. Faça upload dos arquivos para seu servidor"
echo "2. Execute o script de deploy no servidor"
echo "3. Configure o .env com dados do servidor"
echo "4. Aponte seu domínio para a pasta public/"
echo ""
echo -e "${GREEN}🎉 SISTEMA PRONTO PARA DEPLOY!${NC}"
