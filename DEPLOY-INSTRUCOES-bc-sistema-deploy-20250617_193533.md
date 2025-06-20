# 📦 DEPLOY BC SISTEMA - GESTÃO FINANCEIRA

## 🎯 ARQUIVO DE DEPLOY CRIADO

**Arquivo:** `bc-sistema-deploy-20250617_193533.zip`  
**Tamanho:** ~26MB  
**Data:** 17/06/2025 19:35:33  

---

## 📋 CONTEÚDO DO ARQUIVO

Este ZIP contém todos os arquivos necessários para atualizar seu sistema BC com os **novos módulos de gestão financeira**:

### ✅ Novos Módulos Incluídos:
- 👥 **Gestão de Clientes** (CRUD completo)
- 🏢 **Gestão de Fornecedores** (CRUD completo)
- 💳 **Contas a Pagar** (controle avançado)
- 💰 **Contas a Receber** (controle avançado)
- 📊 **Dashboard Financeiro Integrado**
- 📈 **Relatórios de Gestão Financeira**

### 📁 Estrutura dos Arquivos:
```
bc/
├── app/
│   ├── Http/Controllers/
│   │   ├── ClientController.php ✨ NOVO
│   │   ├── SupplierController.php ✨ NOVO
│   │   ├── AccountPayableController.php ✨ NOVO
│   │   ├── AccountReceivableController.php ✨ NOVO
│   │   └── DashboardController.php ✨ ATUALIZADO
│   ├── Models/
│   │   ├── Client.php ✨ NOVO
│   │   ├── Supplier.php ✨ NOVO
│   │   ├── AccountPayable.php ✨ NOVO
│   │   └── AccountReceivable.php ✨ NOVO
│   └── Console/Commands/
│       └── UpdateOverdueAccounts.php ✨ NOVO
├── database/migrations/
│   ├── *_create_clients_table.php ✨ NOVO
│   ├── *_create_suppliers_table.php ✨ NOVO
│   ├── *_create_account_payables_table.php ✨ NOVO
│   └── *_create_account_receivables_table.php ✨ NOVO
├── resources/views/
│   ├── clients/ ✨ NOVO (4 views)
│   ├── suppliers/ ✨ NOVO (4 views)
│   ├── account-payables/ ✨ NOVO (4 views)
│   ├── account-receivables/ ✨ NOVO (4 views)
│   ├── reports/ ✨ NOVO
│   ├── dashboard.blade.php ✨ ATUALIZADO
│   └── layouts/app.blade.php ✨ ATUALIZADO
├── routes/web.php ✨ ATUALIZADO
└── public/ (assets e CSS atualizados)
```

---

## 🚀 COMO FAZER O DEPLOY

### 1️⃣ **Baixar o Arquivo**
- Faça download do arquivo `bc-sistema-deploy-20250617_193533.zip`

### 2️⃣ **Fazer Backup do Servidor**
```bash
# Conectar ao servidor
ssh usuario@seu-servidor.com

# Backup do banco
mysqldump -u usuario -p nome_banco > backup-$(date +%Y%m%d).sql

# Backup dos arquivos
tar -czf backup-bc-$(date +%Y%m%d).tar.gz /var/www/html/bc
```

### 3️⃣ **Upload e Extração**
```bash
# Upload do ZIP para o servidor
scp bc-sistema-deploy-20250617_193533.zip usuario@servidor:/tmp/

# No servidor, extrair os arquivos
cd /tmp
unzip bc-sistema-deploy-20250617_193533.zip

# Parar servidor web
sudo systemctl stop apache2

# Backup do .env atual
cp /var/www/html/bc/.env /tmp/env-backup

# Copiar novos arquivos
cp -r bc/* /var/www/html/bc/

# Restaurar .env
cp /tmp/env-backup /var/www/html/bc/.env

# Ajustar permissões
cd /var/www/html/bc
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
sudo chmod -R 775 storage bootstrap/cache
```

### 4️⃣ **Instalar e Configurar**
```bash
cd /var/www/html/bc

# Instalar dependências
composer install --no-dev --optimize-autoloader

# Executar migrações (4 novas tabelas)
php artisan migrate --force

# Limpar e otimizar cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

# Reiniciar servidor
sudo systemctl start apache2
```

### 5️⃣ **Testar Sistema**
```bash
# Testar comando personalizado
php artisan accounts:update-overdue

# Verificar páginas principais
curl -I http://seu-site.com/clients
curl -I http://seu-site.com/suppliers
curl -I http://seu-site.com/account-payables
curl -I http://seu-site.com/account-receivables
curl -I http://seu-site.com/reports/financial-management
```

---

## 🎯 **NOVAS FUNCIONALIDADES APÓS DEPLOY**

### Páginas Disponíveis:
1. **Dashboard:** `/dashboard` (com seção de gestão financeira)
2. **Clientes:** `/clients` (CRUD completo)
3. **Fornecedores:** `/suppliers` (CRUD completo)
4. **Contas a Pagar:** `/account-payables` (controle avançado)
5. **Contas a Receber:** `/account-receivables` (controle avançado)
6. **Relatório Financeiro:** `/reports/financial-management`

### Recursos Incluídos:
- ✅ Interface moderna e responsiva
- ✅ Filtros e busca avançada
- ✅ Controle de status automático
- ✅ Alertas de vencimento
- ✅ Cálculo de fluxo de caixa
- ✅ Formatação de valores brasileira
- ✅ Validação de documentos (CPF/CNPJ)
- ✅ Comando para automação via cron

---

## 🔧 **CONFIGURAÇÃO AUTOMÁTICA (OPCIONAL)**

Para atualização automática de contas vencidas:
```bash
# Adicionar ao crontab
crontab -e

# Executar diariamente às 6h
0 6 * * * cd /var/www/html/bc && php artisan accounts:update-overdue
```

---

## 🆘 **SUPORTE**

Se tiver problemas:
1. Verifique logs: `tail -f /var/www/html/bc/storage/logs/laravel.log`
2. Teste banco: `php artisan tinker --execute="DB::connection()->getPdo()"`
3. Verifique permissões: `ls -la storage bootstrap/cache`

---

## ✅ **VALIDAÇÃO FINAL**

Após o deploy, verifique:
- [ ] Dashboard carregando com seção financeira
- [ ] Menu lateral com novos módulos
- [ ] Páginas de clientes/fornecedores funcionando
- [ ] Páginas de contas a pagar/receber operacionais
- [ ] Relatório de gestão financeira acessível
- [ ] Comando `php artisan accounts:update-overdue` funcionando

---

**🎉 Sistema BC atualizado com sucesso!**  
**Versão:** Gestão Financeira 1.0  
**Data:** 17/06/2025
