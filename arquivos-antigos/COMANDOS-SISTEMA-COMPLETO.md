# 🔧 COMANDOS DO SISTEMA BC - GUIA COMPLETO

## 📋 **ÍNDICE**
1. [Comandos de Configuração](#comandos-de-configuração)
2. [Comandos de Gestão de Contas](#comandos-de-gestão-de-contas)
3. [Comandos de Manutenção](#comandos-de-manutenção)
4. [Comandos de Deploy](#comandos-de-deploy)

---

## ⚙️ **COMANDOS DE CONFIGURAÇÃO**

### 🔧 **Gerenciar Configurações do Sistema**
```bash
# Listar todas as configurações
php artisan settings:manage list

# Listar configurações por categoria
php artisan settings:manage list --category=appearance
php artisan settings:manage list --category=general
php artisan settings:manage list --category=dashboard

# Ver uma configuração específica
php artisan settings:manage get primary_color

# Alterar uma configuração
php artisan settings:manage set primary_color "#ff6b6b"
php artisan settings:manage set company_name "Minha Empresa Ltda"

# Alterar sem confirmação (forçar)
php artisan settings:manage set primary_color "#ff6b6b" --force

# Deletar uma configuração personalizada
php artisan settings:manage delete custom_setting --force

# Resetar todas as configurações para padrão
php artisan settings:manage reset --force

# Limpar cache de configurações
php artisan settings:manage clear-cache
```

### 📊 **Executar Seeders de Configuração**
```bash
# Popular configurações iniciais
php artisan db:seed --class=SystemSettingsSeeder

# Popular dados de exemplo completos
./popular-dados-exemplo.sh
```

---

## 💰 **COMANDOS DE GESTÃO DE CONTAS**

### 📈 **Atualizar Status de Contas**
```bash
# Atualizar status de contas vencidas
php artisan accounts:update-overdue

# Script automatizado completo
./atualizar-status-contas.sh
```

---

## 🛠️ **COMANDOS DE MANUTENÇÃO**

### 🗃️ **Cache e Performance**
```bash
# Limpar todos os caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Otimizar para produção
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Limpar cache completo (script)
./comandos-limpar-cache-completo.sh
```

### 🔄 **Database e Migrations**
```bash
# Verificar status das migrations
php artisan migrate:status

# Executar migrations
php artisan migrate --force

# Rollback migrations
php artisan migrate:rollback --step=1

# Reset e recriar database
php artisan migrate:fresh --seed
```

### 📦 **Autoload e Dependencies**
```bash
# Recarregar autoload
composer dump-autoload

# Instalar dependências
composer install --optimize-autoloader

# Atualizar dependências
composer update
```

---

## 🚀 **COMANDOS DE DEPLOY**

### 📋 **Deploy Automatizado**
```bash
# Deploy completo no servidor
./deploy-automatizado-servidor.sh

# Deploy com backup
./deploy-completo-servidor.sh

# Preparar arquivos para deploy
./prepare-deploy.sh
```

### 📄 **Verificação de Deploy**
```bash
# Verificar aplicação
php artisan about

# Testar rotas
php artisan route:list

# Verificar configuração
php artisan config:show

# Verificar health
curl http://localhost:8000/up
```

---

## 🎯 **COMANDOS ÚTEIS DO DIA A DIA**

### 🔍 **Debugging e Logs**
```bash
# Ver logs em tempo real
tail -f storage/logs/laravel.log

# Limpar logs
echo "" > storage/logs/laravel.log

# Informações do sistema
php artisan about
```

### 🌐 **Servidor de Desenvolvimento**
```bash
# Iniciar servidor local
php artisan serve

# Servidor acessível externamente
php artisan serve --host=0.0.0.0 --port=8000

# Servidor em background
nohup php artisan serve --host=0.0.0.0 --port=8000 > server.log 2>&1 &
```

### 📊 **Tinker (Console Interativo)**
```bash
# Abrir console Laravel
php artisan tinker

# Exemplos no tinker:
App\Models\SystemSetting::count()
App\Helpers\ConfigHelper::get('primary_color')
App\Models\Client::with('accountsPayable')->get()
```

---

## 🎨 **EXEMPLOS PRÁTICOS**

### 🏢 **Configurar Empresa**
```bash
php artisan settings:manage set company_name "TechCorp Solutions" --force
php artisan settings:manage set company_slogan "Inovação em Gestão Financeira" --force
php artisan settings:manage set company_email "contato@techcorp.com" --force
php artisan settings:manage set company_phone "(11) 9999-9999" --force
```

### 🎨 **Personalizar Cores do Sistema**
```bash
# Tema Azul Corporativo
php artisan settings:manage set primary_color "#2563eb" --force
php artisan settings:manage set secondary_color "#1e40af" --force

# Tema Verde Sustentável  
php artisan settings:manage set primary_color "#059669" --force
php artisan settings:manage set secondary_color "#047857" --force

# Tema Roxo Moderno
php artisan settings:manage set primary_color "#7c3aed" --force
php artisan settings:manage set secondary_color "#6d28d9" --force
```

### 📊 **Configurar Dashboard**
```bash
php artisan settings:manage set dashboard_show_welcome true --force
php artisan settings:manage set dashboard_show_stats true --force
php artisan settings:manage set dashboard_auto_refresh false --force
php artisan settings:manage set dashboard_refresh_interval 300 --force
```

---

## ⚠️ **COMANDOS DE EMERGÊNCIA**

### 🚨 **Problemas de Configuração**
```bash
# Reset completo do sistema
php artisan settings:manage reset --force
php artisan cache:clear
php artisan config:clear

# Recriar configurações
php artisan db:seed --class=SystemSettingsSeeder
```

### 🔧 **Problemas de Database**
```bash
# Verificar conexão
php artisan migrate:status

# Recriar tabelas
php artisan migrate:fresh --force
php artisan db:seed --force
```

### 📝 **Problemas de Permissions**
```bash
# Corrigir permissões (Linux/Mac)
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Gerar nova chave da aplicação
php artisan key:generate --force
```

---

## 📚 **REFERÊNCIAS RÁPIDAS**

### 🏷️ **Categorias de Configuração**
- `general` - Configurações gerais do sistema
- `appearance` - Cores, temas, visual
- `dashboard` - Configurações do painel
- `modules` - Configurações dos módulos
- `advanced` - Configurações avançadas

### 🎨 **Tipos de Campo Suportados**
- `string` - Texto simples
- `text` - Texto longo
- `number` - Números
- `boolean` - Verdadeiro/Falso
- `color` - Seletor de cores
- `select` - Lista de opções
- `file` - Upload de arquivos

### 🔑 **Configurações Principais**
- `company_name` - Nome da empresa
- `company_logo` - Logo da empresa
- `primary_color` - Cor primária do sistema
- `secondary_color` - Cor secundária
- `dashboard_show_welcome` - Mostrar mensagem de boas-vindas
- `dashboard_auto_refresh` - Auto-atualização do dashboard

---

*Este documento é atualizado automaticamente. Última versão: {{ date('Y-m-d H:i:s') }}*
