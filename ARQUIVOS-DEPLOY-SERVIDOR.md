# ARQUIVOS PARA UPLOAD NO SERVIDOR - BC SISTEMA

## Data: 20/06/2025

### 🚀 ARQUIVOS ESSENCIAIS PARA DEPLOY

#### 📁 **1. CONTROLLERS CORRIGIDOS (PRIORIDADE MÁXIMA)**
```
app/Http/Controllers/
├── AccountManagementController.php ✅
├── DashboardController.php ✅
├── ExtractImportController.php ✅
├── ImportController.php ✅ (CORRIGIDO - imported_by)
├── ReconciliationController.php ✅
├── TransactionController.php ✅
├── BankAccountController.php ✅
├── CategoryController.php ✅
├── ClientController.php ✅
├── SupplierController.php ✅
├── AccountPayableController.php ✅
├── AccountReceivableController.php ✅
├── ReportController.php ✅
├── SettingsController.php ✅
├── SystemUpdateController.php ✅
├── TestController.php ✅
└── UpdateController.php ✅
```

#### 📁 **2. MODELS ATUALIZADOS**
```
app/Models/
├── StatementImport.php ✅
├── ImportLog.php ✅
├── Transaction.php ✅
├── BankAccount.php ✅
├── Reconciliation.php ✅
├── Category.php ✅
├── Client.php ✅
├── Supplier.php ✅
├── AccountPayable.php ✅
├── AccountReceivable.php ✅
└── User.php ✅
```

#### 📁 **3. MIGRATIONS (CRÍTICAS)**
```
database/migrations/
├── 2025_06_20_012639_make_imported_by_nullable_in_statement_imports_table.php ⚠️ NOVA
├── 2024_01_01_000004_create_statement_imports_table.php ✅
├── 2025_06_18_124432_create_import_logs_table.php ✅
├── 2025_06_18_125000_create_updates_table.php ✅
└── [todas as outras migrations existentes] ✅
```

#### 📁 **4. ROTAS CORRIGIDAS**
```
routes/
├── web.php ✅ (CORRIGIDO - duplicatas removidas)
└── api.php ✅
```

#### 📁 **5. VIEWS VALIDADAS (100% FUNCIONAIS)**
```
resources/views/
├── layouts/app.blade.php ✅
├── dashboard.blade.php ✅
├── transactions/ (todas as views) ✅
├── bank-accounts/ (todas as views) ✅
├── imports/ (todas as views) ✅
├── reconciliations/ (todas as views) ✅
├── reports/ (todas as views) ✅
├── settings/ (todas as views) ✅
├── categories/ (todas as views) ✅
├── clients/ (todas as views) ✅
├── suppliers/ (todas as views) ✅
├── account-payables/ (todas as views) ✅
├── account-receivables/ (todas as views) ✅
└── account-management/ (todas as views) ✅
```

#### 📁 **6. SERVICES ATUALIZADOS**
```
app/Services/
├── StatementImportService.php ✅
├── ExtractImportService.php ✅
└── [outros services se existirem] ✅
```

#### 📁 **7. CONFIGURAÇÕES**
```
config/
├── app.php ✅
├── database.php ✅
├── filesystems.php ✅
└── [outras configs necessárias] ✅
```

---

### 📦 **COMANDO PARA CRIAR PACOTE DE DEPLOY**

```bash
# No diretório /workspaces/bcsistema/
tar -czf bc-sistema-deploy-$(date +%Y%m%d_%H%M%S).tar.gz \
  --exclude='*.log' \
  --exclude='storage/logs/*' \
  --exclude='storage/framework/cache/*' \
  --exclude='storage/framework/sessions/*' \
  --exclude='storage/framework/views/*' \
  --exclude='node_modules' \
  --exclude='.git' \
  --exclude='vendor' \
  bc/
```

### 🔧 **COMANDOS NO SERVIDOR (APÓS UPLOAD)**

#### 1. **Backup do Sistema Atual**
```bash
cp -r /var/www/html/bc /var/www/html/bc-backup-$(date +%Y%m%d_%H%M%S)
```

#### 2. **Descompactar e Configurar**
```bash
# Extrair arquivos
tar -xzf bc-sistema-deploy-*.tar.gz

# Copiar arquivos
cp -r bc/* /var/www/html/bc/

# Ajustar permissões
chown -R www-data:www-data /var/www/html/bc
chmod -R 755 /var/www/html/bc
chmod -R 775 /var/www/html/bc/storage
chmod -R 775 /var/www/html/bc/bootstrap/cache
```

#### 3. **Configurar Ambiente**
```bash
cd /var/www/html/bc

# Copiar/ajustar .env
cp .env.example .env
# Editar .env com configurações do servidor

# Instalar dependências
composer install --no-dev --optimize-autoloader

# Executar migrations
php artisan migrate --force

# Limpar e cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### ⚠️ **ARQUIVOS CRÍTICOS QUE DEVEM SER ENVIADOS**

#### **PRIORIDADE MÁXIMA:**
1. ✅ `ImportController.php` (CORRIGIDO - imported_by)
2. ✅ `2025_06_20_012639_make_imported_by_nullable_in_statement_imports_table.php`
3. ✅ `routes/web.php` (CORRIGIDO - rotas duplicadas)
4. ✅ Todos os controllers em `app/Http/Controllers/`
5. ✅ Todas as views em `resources/views/`

#### **PRIORIDADE ALTA:**
1. ✅ Todos os models em `app/Models/`
2. ✅ Services em `app/Services/`
3. ✅ Todas as migrations

### 🚫 **ARQUIVOS QUE NÃO DEVEM SER ENVIADOS**
```
❌ storage/logs/*
❌ storage/framework/cache/*
❌ storage/framework/sessions/*
❌ storage/framework/views/*
❌ vendor/ (será gerado com composer install)
❌ node_modules/
❌ .git/
❌ *.log
❌ .env (criar novo no servidor)
```

### 📋 **CHECKLIST DE DEPLOY**
- [ ] Backup do sistema atual criado
- [ ] Controllers atualizados uploadados
- [ ] Migration nova executada
- [ ] Views todas funcionando
- [ ] Rotas corrigidas aplicadas
- [ ] Composer install executado
- [ ] Migrations executadas
- [ ] Cache limpo e regenerado
- [ ] Permissões ajustadas
- [ ] Teste de importação funcionando

### 🎯 **RESULTADO ESPERADO**
Após o deploy com estes arquivos:
- ✅ Sistema funcionando sem erros
- ✅ Importações funcionando (erro imported_by corrigido)
- ✅ Todas as views renderizando
- ✅ Controllers sem erros de sintaxe
- ✅ Rotas sem duplicatas
- ✅ Interface moderna e responsiva
