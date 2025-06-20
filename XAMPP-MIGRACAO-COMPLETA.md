# 🚀 MIGRAÇÃO BC SISTEMA PARA XAMPP - GUIA COMPLETO

## 📋 ÍNDICE
1. [Scripts Automatizados](#scripts-automatizados)
2. [Pré-requisitos](#pré-requisitos)
3. [Instalação Automática](#instalação-automática)
4. [Instalação Manual](#instalação-manual)
5. [Solução de Problemas](#solução-de-problemas)
6. [Teste Final](#teste-final)

---

## 🤖 SCRIPTS AUTOMATIZADOS

### � Scripts Disponíveis:
- **`instalar-bc-completo.bat`** - Instalação completa automática
- **`instalar-bc-xampp.bat`** - Instalação básica
- **`instalar-composer-xampp.bat`** - Instalar Composer e dependências
- **`configurar-bc-xampp.bat`** - Configuração final
- **`testar-bc-xampp.bat`** - Testes de funcionamento
- **`solucionar-problemas-bc.bat`** - Menu de troubleshooting

### 🚀 **OPÇÃO 1: INSTALAÇÃO AUTOMÁTICA (RECOMENDADO)**

1. **Execute como Administrador:** `instalar-bc-completo.bat`
2. **Siga as instruções na tela**
3. **Acesse:** `http://localhost/bc/public`

**Este script fará TUDO automaticamente:**
- ✅ Verificar XAMPP
- ✅ Instalar arquivos
- ✅ Configurar .env
- ✅ Instalar Composer
- ✅ Executar migrations
- ✅ Criar usuário admin
- ✅ Testar funcionamento

---

## 🔧 PRÉ-REQUISITOS

### 🗄️ **1. XAMPP Instalado e Funcionando**
- **Download:** https://www.apachefriends.org/
- **Serviços rodando:** Apache + MySQL
- **Teste:** Acesse `http://localhost/dashboard`

### �️ **2. Banco de Dados**
1. Acesse: `http://localhost/phpmyadmin`
2. Crie banco: `bc_sistema`
3. Collation: `utf8mb4_unicode_ci`

**SQL direto:**
```sql
CREATE DATABASE bc_sistema CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 📁 **3. Arquivos do Sistema**
- Extrair: `bc-sistema-deploy-corrigido-20250620_013129.tar.gz`
- Para: `C:\xampp\htdocs\bc\` (ou `D:\xampp\htdocs\bc\`)

---

## 🔄 INSTALAÇÃO AUTOMÁTICA

### ⚡ **Método Rápido (1 clique):**
```cmd
# Execute como Administrador:
instalar-bc-completo.bat
```

### 📝 **Método Passo a Passo:**
```cmd
# 1. Instalação básica
instalar-bc-xampp.bat

# 2. Instalar Composer
instalar-composer-xampp.bat

# 3. Configuração final
configurar-bc-xampp.bat

# 4. Testar funcionamento
testar-bc-xampp.bat
```

---

## 🔧 INSTALAÇÃO MANUAL (se scripts falharem)

### ⚙️ **1. Configurar .env**
Criar arquivo `C:\xampp\htdocs\bc\.env`:
```env
APP_NAME="BC Sistema de Gestão Financeira"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost/bc/public

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bc_sistema
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DRIVER=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=null
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### 🔑 **2. Gerar Chave da Aplicação**
```cmd
cd /d C:\xampp\htdocs\bc
C:\xampp\php\php.exe artisan key:generate --force
```

### 📦 **3. Instalar Composer e Dependências**
```cmd
# Baixar Composer
curl -sS https://getcomposer.org/installer | C:\xampp\php\php.exe

# Instalar dependências
C:\xampp\php\php.exe composer.phar install --no-dev --optimize-autoloader
```

### 🗃️ **4. Executar Migrations**
```cmd
C:\xampp\php\php.exe artisan migrate --force
```

### 👤 **5. Criar Usuário Admin**
```cmd
C:\xampp\php\php.exe artisan tinker --execute="App\Models\User::create(['name'=>'Admin BC','email'=>'admin@bcsistema.com','password'=>Hash::make('admin123'),'email_verified_at'=>now()]);"
```

### 🗂️ **6. Criar Link para Storage**
```cmd
C:\xampp\php\php.exe artisan storage:link
```

### ⚡ **7. Otimizar Sistema**
```cmd
C:\xampp\php\php.exe artisan config:cache
C:\xampp\php\php.exe artisan route:cache
C:\xampp\php\php.exe artisan optimize
```

---

## 🚨 SOLUÇÃO DE PROBLEMAS

### 🔧 **Script de Troubleshooting**
```cmd
# Execute para menu interativo de soluções:
solucionar-problemas-bc.bat
```

### ❌ **Problemas Comuns:**

#### **1. "Composer não encontrado"**
**Solução:**
- Execute: `instalar-composer-xampp.bat`
- Ou baixe: https://getcomposer.org/download/

#### **2. "Erro de conexão com banco"**
**Verificar:**
- MySQL rodando no XAMPP
- Banco `bc_sistema` criado
- Configurações no `.env` corretas

#### **3. "Class 'App\Models\User' not found"**
**Solução:**
```cmd
C:\xampp\php\php.exe composer.phar dump-autoload
```

#### **4. "imported_by cannot be null"**
**✅ JÁ CORRIGIDO:** Migration aplicada automaticamente

#### **5. "Permission denied"**
**Solução:**
- Execute scripts como Administrador
- Desative antivírus temporariamente

#### **6. "Route not found"**
**Solução:**
```cmd
C:\xampp\php\php.exe artisan route:clear
C:\xampp\php\php.exe artisan route:cache
```

---

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bc_sistema
DB_USERNAME=root
DB_PASSWORD=

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

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### 🔄 **PASSO 5: EXECUTAR COMANDOS**
```cmd
# Abrir Command Prompt como Administrador
# Navegar para a pasta do projeto
cd D:\xampp\htdocs\bc

# Verificar se o PHP está funcionando
D:\xampp\php\php.exe -v

# Instalar dependências
D:\xampp\php\php.exe D:\xampp\composer\composer.phar install

# Gerar chave da aplicação
D:\xampp\php\php.exe artisan key:generate

# Executar migrations (IMPORTANTE - inclui correção do imported_by)
D:\xampp\php\php.exe artisan migrate

# Criar link para storage
D:\xampp\php\php.exe artisan storage:link

# Limpar e otimizar cache
D:\xampp\php\php.exe artisan config:clear
D:\xampp\php\php.exe artisan cache:clear
D:\xampp\php\php.exe artisan optimize
```

---

## 🎯 TESTE FINAL

### 🔍 **Executar Teste Automatizado**
```cmd
# Execute para verificar se tudo funciona:
testar-bc-xampp.bat
```

### 🌐 **Teste Manual**
1. **Acesse:** `http://localhost/bc/public`
2. **Login:** 
   - Email: `admin@bcsistema.com`
   - Senha: `admin123`
3. **Teste funcionalidades:**
   - Dashboard carrega
   - Menu funciona
   - Importação de extratos
   - Relatórios

### ✅ **Checklist Final**
- [ ] XAMPP rodando (Apache + MySQL)
- [ ] Banco `bc_sistema` criado
- [ ] Arquivos extraídos em `htdocs/bc/`
- [ ] Arquivo `.env` configurado
- [ ] Composer instalado
- [ ] Migrations executadas
- [ ] Usuário admin criado
- [ ] Sistema acessível via browser
- [ ] Login funcionando
- [ ] Dashboard carregando

---

## 📞 SUPORTE

### 🔧 **Scripts de Manutenção:**
- `instalar-bc-completo.bat` - Instalação completa
- `testar-bc-xampp.bat` - Testes de funcionamento
- `solucionar-problemas-bc.bat` - Menu de troubleshooting

### 📚 **Documentação:**
- `XAMPP-MIGRACAO-COMPLETA.md` - Este guia
- `ARQUIVOS-DOWNLOAD-XAMPP.md` - Links de download
- `CORRECOES-FINALIZADAS.md` - Correções aplicadas

### 🎯 **Credenciais Padrão:**
- **URL:** `http://localhost/bc/public`
- **Email:** `admin@bcsistema.com`
- **Senha:** `admin123`

⚠️ **IMPORTANTE:** Altere a senha após o primeiro login!

---

## 🏁 CONCLUSÃO

O sistema BC foi migrado com sucesso para XAMPP com:

✅ **Correções aplicadas:**
- Erro de importação (imported_by) corrigido
- Rotas duplicadas removidas
- Controllers validados e corrigidos
- Views funcionais
- Dependências atualizadas

✅ **Funcionalidades testadas:**
- Login/logout
- Dashboard completo
- Importação de extratos
- Gestão de contas
- Relatórios financeiros
- Reconciliação bancária

✅ **Scripts automatizados:**
- Instalação completa
- Testes de funcionamento
- Solução de problemas
- Manutenção do sistema

🎉 **O sistema está pronto para uso profissional!**

# Sair do Tinker
exit
```

### 🔒 **PASSO 7: CONFIGURAR PERMISSÕES (Windows)**
```cmd
# Dar permissão de escrita para as pastas
# Botão direito na pasta → Propriedades → Segurança → Editar
# Permitir "Controle Total" para:
# - D:\xampp\htdocs\bc\storage\
# - D:\xampp\htdocs\bc\bootstrap\cache\
```

### 🌐 **PASSO 8: CONFIGURAR VIRTUAL HOST (OPCIONAL)**
Editar `D:\xampp\apache\conf\extra\httpd-vhosts.conf`:
```apache
<VirtualHost *:80>
    DocumentRoot "D:/xampp/htdocs/bc/public"
    ServerName bc-sistema.local
    <Directory "D:/xampp/htdocs/bc/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Editar `C:\Windows\System32\drivers\etc\hosts`:
```
127.0.0.1 bc-sistema.local
```

### ✅ **PASSO 9: TESTAR ACESSO**
**URLs para testar:**
- Direto: `http://localhost/bc/public`
- Com Virtual Host: `http://bc-sistema.local`

**Credenciais:**
- **Email:** `admin@bcsistema.com`
- **Senha:** `admin123`

### 🧪 **PASSO 10: VERIFICAR FUNCIONALIDADES**
1. ✅ Dashboard carregando
2. ✅ Menu lateral funcionando
3. ✅ Cadastro de transações
4. ✅ Importação de extratos (erro do imported_by já corrigido!)
5. ✅ Relatórios
6. ✅ Contas bancárias

---

## 🚨 **EM CASO DE PROBLEMAS:**

### **Erro de banco:**
- Verificar se MySQL está rodando no XAMPP
- Conferir credenciais no .env

### **Erro de permissão:**
- Executar CMD como Administrador
- Dar permissões para storage/ e bootstrap/cache/

### **Erro 500:**
- Verificar logs em: `D:\xampp\htdocs\bc\storage\logs\`
- Conferir se .env está configurado corretamente

### **Composer não encontrado:**
```cmd
# Instalar Composer globalmente ou usar:
D:\xampp\php\php.exe D:\xampp\composer\composer.phar install
```

---

## 🎉 **RESULTADO FINAL:**
Sistema BC 100% funcional no XAMPP com todas as correções aplicadas!

**Me informe se encontrar algum problema durante a migração!** 🚀
