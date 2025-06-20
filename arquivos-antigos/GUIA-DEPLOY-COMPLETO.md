# 🚀 GUIA COMPLETO DE DEPLOY - BC SISTEMA

## 📋 **PASSO A PASSO PARA SUBIR PARA O SERVIDOR**
Data: 17 de Junho de 2025

---

## 🎯 **PREPARAÇÃO LOCAL**

### **Passo 1: Criar Pacote de Deploy**
```bash
# Navegar para o diretório do projeto
cd /workspaces/bcsistema

# Criar backup completo da pasta bc
tar -czf bc-sistema-completo-$(date +%Y%m%d_%H%M%S).tar.gz bc/

# Verificar o arquivo criado
ls -lh bc-sistema-completo-*.tar.gz
```

### **Passo 2: Limpar Arquivos Desnecessários (Opcional)**
```bash
# Entrar na pasta bc
cd bc/

# Remover arquivos de desenvolvimento (se desejar)
rm -rf node_modules/
rm -rf tests/
rm -rf .git/
rm -f .env.example
rm -f README.md

# Voltar e recriar o pacote limpo
cd ..
tar -czf bc-sistema-deploy-$(date +%Y%m%d_%H%M%S).tar.gz bc/
```

---

## 📤 **UPLOAD PARA O SERVIDOR**

### **Método 1: Via SCP (Recomendado)**
```bash
# Substituir pelos dados do seu servidor
scp bc-sistema-completo-*.tar.gz usuario@servidor.com:/home/usuario/
```

### **Método 2: Via FTP/SFTP**
1. Use um cliente FTP como FileZilla
2. Conecte-se ao seu servidor
3. Faça upload do arquivo `.tar.gz`

### **Método 3: Via Painel de Controle**
1. Acesse o painel do seu hospedagem
2. Vá para "Gerenciador de Arquivos"
3. Faça upload do arquivo `.tar.gz`

---

## 🖥️ **CONFIGURAÇÃO NO SERVIDOR**

### **Passo 3: Conectar ao Servidor**
```bash
# SSH no servidor
ssh usuario@servidor.com

# Ou use o terminal do painel de controle
```

### **Passo 4: Extrair o Sistema**
```bash
# Ir para o diretório public_html ou www
cd /home/usuario/public_html  # ou /var/www/html

# Extrair o arquivo
tar -xzf ~/bc-sistema-completo-*.tar.gz

# Renomear se necessário
mv bc sistema-financeiro  # opcional

# Verificar se foi extraído
ls -la sistema-financeiro/
```

### **Passo 5: Configurar Permissões**
```bash
# Entrar na pasta do sistema
cd sistema-financeiro/

# Configurar permissões básicas
chmod -R 755 .
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# Permissões específicas do Laravel
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/

# Se não tiver acesso root, use:
find storage/ -type f -exec chmod 664 {} \;
find storage/ -type d -exec chmod 775 {} \;
find bootstrap/cache/ -type f -exec chmod 664 {} \;
find bootstrap/cache/ -type d -exec chmod 775 {} \;
```

---

## ⚙️ **CONFIGURAÇÃO DO AMBIENTE**

### **Passo 6: Configurar .env**
```bash
# Copiar arquivo de exemplo
cp .env.example .env

# Editar configurações
nano .env  # ou vim .env
```

**Configurações essenciais no .env:**
```env
APP_NAME="BC Sistema Financeiro"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://seudominio.com

DB_CONNECTION=sqlite
DB_DATABASE=/home/usuario/public_html/sistema-financeiro/database/database.sqlite

# OU para MySQL:
# DB_CONNECTION=mysql
# DB_HOST=localhost
# DB_PORT=3306
# DB_DATABASE=seu_banco
# DB_USERNAME=seu_usuario
# DB_PASSWORD=sua_senha

CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file

MAIL_MAILER=smtp
MAIL_HOST=seu-smtp.com
MAIL_PORT=587
MAIL_USERNAME=seu-email@dominio.com
MAIL_PASSWORD=sua-senha
MAIL_ENCRYPTION=tls
```

### **Passo 7: Instalar Dependências**
```bash
# Verificar se o Composer está instalado
composer --version

# Se não estiver, instalar:
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Instalar dependências do projeto
composer install --optimize-autoloader --no-dev

# Gerar chave da aplicação
php artisan key:generate
```

---

## 🗄️ **CONFIGURAÇÃO DO BANCO DE DADOS**

### **Passo 8: Configurar Banco**

#### **Para SQLite (Mais Simples):**
```bash
# Criar arquivo do banco
touch database/database.sqlite
chmod 664 database/database.sqlite

# Executar migrations
php artisan migrate --force

# Popular dados iniciais
php artisan db:seed --class=CategorySeeder --force
php artisan db:seed --class=SystemSettingsSeeder --force
```

#### **Para MySQL:**
```bash
# Primeiro criar o banco no painel/phpMyAdmin
# Depois executar:
php artisan migrate --force
php artisan db:seed --class=CategorySeeder --force
php artisan db:seed --class=SystemSettingsSeeder --force
```

---

## 🌐 **CONFIGURAÇÃO DO SERVIDOR WEB**

### **Passo 9: Apache (.htaccess)**
Criar/verificar arquivo `.htaccess` na pasta `public/`:
```apache
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
```

### **Passo 10: Configurar Virtual Host**

#### **Apache Virtual Host:**
```apache
<VirtualHost *:80>
    ServerName seudominio.com
    DocumentRoot /home/usuario/public_html/sistema-financeiro/public

    <Directory /home/usuario/public_html/sistema-financeiro/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/bc-sistema_error.log
    CustomLog ${APACHE_LOG_DIR}/bc-sistema_access.log combined
</VirtualHost>
```

#### **Nginx (se aplicável):**
```nginx
server {
    listen 80;
    server_name seudominio.com;
    root /home/usuario/public_html/sistema-financeiro/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 🔧 **OTIMIZAÇÕES FINAIS**

### **Passo 11: Otimizar para Produção**
```bash
# Limpar e otimizar caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Otimizar autoload
composer dump-autoload --optimize

# Criar link simbólico para storage
php artisan storage:link
```

### **Passo 12: Configurar Tarefas Agendadas**
Adicionar ao crontab do servidor:
```bash
# Editar crontab
crontab -e

# Adicionar linha:
* * * * * cd /home/usuario/public_html/sistema-financeiro && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🔒 **SEGURANÇA**

### **Passo 13: Configurações de Segurança**
```bash
# Proteger arquivos sensíveis
chmod 600 .env
chmod -R 600 storage/logs/

# Remover arquivos desnecessários
rm -f .env.example
rm -rf tests/
rm -rf .git/

# Configurar backup automático (opcional)
# Criar script de backup em /home/usuario/backup-bc.sh
```

---

## ✅ **VERIFICAÇÃO FINAL**

### **Passo 14: Testar o Sistema**
1. **Acesse:** `https://seudominio.com`
2. **Verifique:**
   - ✅ Página inicial carrega
   - ✅ Login funciona
   - ✅ Dashboard aparece
   - ✅ Contas a Pagar/Receber funcionam
   - ✅ Configurações acessíveis
   - ✅ Importação de extratos

### **Comandos de Teste:**
```bash
# Testar se o Laravel está funcionando
php artisan --version

# Verificar configurações
php artisan config:show

# Testar banco de dados
php artisan tinker
>>> App\Models\User::count()
>>> exit
```

---

## 📞 **TROUBLESHOOTING**

### **Problemas Comuns:**

1. **Erro 500:**
   ```bash
   # Verificar logs
   tail -f storage/logs/laravel.log
   
   # Verificar permissões
   chmod -R 775 storage/
   ```

2. **Erro de Banco:**
   ```bash
   # Verificar conexão
   php artisan migrate:status
   
   # Recriar banco se necessário
   php artisan migrate:fresh --seed
   ```

3. **Erro de Cache:**
   ```bash
   # Limpar todos os caches
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

---

## 📦 **SCRIPT AUTOMATIZADO DE DEPLOY**

Vou criar um script que automatiza todo o processo:

```bash
#!/bin/bash
# Script de deploy automático - execute no servidor

echo "🚀 Iniciando deploy do BC Sistema..."

# Configurações (edite conforme necessário)
PROJECT_DIR="/home/usuario/public_html/sistema-financeiro"
BACKUP_DIR="/home/usuario/backups"
DOMAIN="seudominio.com"

# Criar backup se o sistema já existir
if [ -d "$PROJECT_DIR" ]; then
    echo "📦 Criando backup..."
    mkdir -p $BACKUP_DIR
    tar -czf $BACKUP_DIR/bc-backup-$(date +%Y%m%d_%H%M%S).tar.gz $PROJECT_DIR
fi

# Extrair novo sistema
echo "📤 Extraindo sistema..."
tar -xzf ~/bc-sistema-completo-*.tar.gz -C $(dirname $PROJECT_DIR)

# Configurar permissões
echo "🔧 Configurando permissões..."
cd $PROJECT_DIR
chmod -R 755 .
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# Instalar dependências
echo "📦 Instalando dependências..."
composer install --optimize-autoloader --no-dev

# Configurar ambiente
echo "⚙️ Configurando ambiente..."
php artisan key:generate --force
php artisan migrate --force
php artisan db:seed --class=CategorySeeder --force
php artisan db:seed --class=SystemSettingsSeeder --force

# Otimizar
echo "🚀 Otimizando sistema..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link

echo "✅ Deploy concluído!"
echo "🌐 Acesse: https://$DOMAIN"
```

---

## 🎯 **RESUMO DOS COMANDOS ESSENCIAIS**

### **No seu computador:**
```bash
cd /workspaces/bcsistema
tar -czf bc-sistema-completo-$(date +%Y%m%d_%H%M%S).tar.gz bc/
scp bc-sistema-completo-*.tar.gz usuario@servidor.com:/home/usuario/
```

### **No servidor:**
```bash
cd /home/usuario/public_html
tar -xzf ~/bc-sistema-completo-*.tar.gz
mv bc sistema-financeiro
cd sistema-financeiro
chmod -R 755 .
chmod -R 775 storage/ bootstrap/cache/
cp .env.example .env
# Editar .env com as configurações do servidor
composer install --optimize-autoloader --no-dev
php artisan key:generate --force
php artisan migrate --force
php artisan db:seed --class=CategorySeeder --force
php artisan db:seed --class=SystemSettingsSeeder --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

---

**✨ Seu sistema BC estará online e funcionando perfeitamente! ✨**
