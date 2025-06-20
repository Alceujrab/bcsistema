# 🚀 GUIA DE DEPLOY - SISTEMA BC GESTÃO FINANCEIRA

## 📋 PASSO A PASSO PARA ATUALIZAR O SERVIDOR

**Data:** 17 de junho de 2025  
**Versão:** Sistema BC - Gestão Financeira v1.0  
**Ambiente:** Produção

---

## ⚠️ PRÉ-REQUISITOS

### Antes de começar, certifique-se que tem:
- [ ] Acesso SSH ao servidor
- [ ] Backup completo do banco de dados atual
- [ ] Backup dos arquivos atuais do sistema
- [ ] PHP 8.1+ instalado
- [ ] Composer instalado
- [ ] Node.js e NPM (se necessário)

---

## 🔒 ETAPA 1: BACKUP DE SEGURANÇA

```bash
# 1. Conectar ao servidor
ssh usuario@seu-servidor.com

# 2. Criar backup do banco de dados
cd /var/www/html/bc
mysqldump -u [usuario] -p[senha] [nome_banco] > backup-bc-$(date +%Y%m%d_%H%M%S).sql

# 3. Backup dos arquivos atuais
tar -czf backup-bc-files-$(date +%Y%m%d_%H%M%S).tar.gz /var/www/html/bc

# 4. Verificar se os backups foram criados
ls -la backup-bc-*
```

---

## 📦 ETAPA 2: PREPARAR ARQUIVOS PARA UPLOAD

### No seu ambiente local (VS Code):

```bash
# 1. Navegar para o diretório do projeto
cd /workspaces/bcsistema

# 2. Criar pacote de deploy
tar -czf bc-sistema-gestao-financeira-$(date +%Y%m%d_%H%M%S).tar.gz \
    --exclude='bc/node_modules' \
    --exclude='bc/vendor' \
    --exclude='bc/storage/logs/*' \
    --exclude='bc/storage/framework/cache/*' \
    --exclude='bc/storage/framework/sessions/*' \
    --exclude='bc/storage/framework/views/*' \
    --exclude='bc/.env' \
    bc/

# 3. Verificar o arquivo criado
ls -la bc-sistema-*.tar.gz
```

### Arquivos críticos que DEVEM ser incluídos:
- ✅ `app/` (Models, Controllers, Commands)
- ✅ `database/migrations/` (Novas tabelas)
- ✅ `resources/views/` (Templates atualizados)
- ✅ `routes/web.php` (Novas rotas)
- ✅ `composer.json` (Dependências)
- ✅ `public/` (Assets, CSS, JS)

---

## 🚛 ETAPA 3: UPLOAD DOS ARQUIVOS

### Opção A - Via SCP:
```bash
# Upload do pacote para o servidor
scp bc-sistema-gestao-financeira-*.tar.gz usuario@servidor:/tmp/
```

### Opção B - Via FTP/SFTP:
- Use seu cliente FTP preferido (FileZilla, WinSCP, etc.)
- Faça upload do arquivo `.tar.gz` para `/tmp/` no servidor

### Opção C - Via Git (se usando repositório):
```bash
# No servidor
cd /var/www/html/bc
git pull origin main
```

---

## 🔧 ETAPA 4: ATUALIZAR ARQUIVOS NO SERVIDOR

```bash
# 1. Conectar ao servidor
ssh usuario@seu-servidor.com

# 2. Ir para o diretório temporário
cd /tmp

# 3. Extrair arquivos
tar -xzf bc-sistema-gestao-financeira-*.tar.gz

# 4. Parar servidor web (se necessário)
sudo systemctl stop apache2
# OU
sudo systemctl stop nginx

# 5. Fazer backup rápido do .env atual
cp /var/www/html/bc/.env /tmp/env-backup-$(date +%Y%m%d_%H%M%S)

# 6. Copiar arquivos novos (preservando .env)
cp -r bc/* /var/www/html/bc/
cp /tmp/env-backup-* /var/www/html/bc/.env

# 7. Ajustar permissões
cd /var/www/html/bc
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
sudo chmod -R 775 storage bootstrap/cache
```

---

## 🎯 ETAPA 5: INSTALAR DEPENDÊNCIAS

```bash
cd /var/www/html/bc

# 1. Instalar dependências PHP
composer install --no-dev --optimize-autoloader

# 2. Se houver assets para compilar
npm install
npm run build
```

---

## 💾 ETAPA 6: EXECUTAR MIGRAÇÕES DO BANCO

```bash
cd /var/www/html/bc

# 1. Verificar status das migrações
php artisan migrate:status

# 2. Executar novas migrações
php artisan migrate --force

# 3. Verificar se as tabelas foram criadas
php artisan tinker --execute="
echo 'Tabela clients: ' . (Schema::hasTable('clients') ? 'OK' : 'ERRO') . PHP_EOL;
echo 'Tabela suppliers: ' . (Schema::hasTable('suppliers') ? 'OK' : 'ERRO') . PHP_EOL;
echo 'Tabela account_payables: ' . (Schema::hasTable('account_payables') ? 'OK' : 'ERRO') . PHP_EOL;
echo 'Tabela account_receivables: ' . (Schema::hasTable('account_receivables') ? 'OK' : 'ERRO') . PHP_EOL;
"
```

---

## 📊 ETAPA 7: POPULAR DADOS DE EXEMPLO (OPCIONAL)

### Se quiser dados de exemplo no servidor:

```bash
cd /var/www/html/bc

# Criar dados de exemplo
php artisan tinker --execute="
// Criar clientes
App\Models\Client::create([
    'name' => 'Empresa ABC Ltda',
    'email' => 'contato@empresaabc.com.br',
    'phone' => '(11) 1234-5678',
    'document' => '12.345.678/0001-90',
    'document_type' => 'cnpj',
    'address' => 'Rua das Empresas, 123',
    'city' => 'São Paulo',
    'state' => 'SP',
    'zip_code' => '01234-567',
    'active' => true
]);

// Criar fornecedores
App\Models\Supplier::create([
    'name' => 'Fornecedor XYZ',
    'email' => 'vendas@fornecedorxyz.com.br',
    'phone' => '(11) 9876-5432',
    'document' => '98.765.432/0001-10',
    'document_type' => 'cnpj',
    'address' => 'Av. dos Fornecedores, 456',
    'city' => 'São Paulo',
    'state' => 'SP',
    'zip_code' => '01987-654',
    'active' => true
]);

echo 'Dados de exemplo criados com sucesso!' . PHP_EOL;
"
```

---

## 🧹 ETAPA 8: LIMPEZA E OTIMIZAÇÃO

```bash
cd /var/www/html/bc

# 1. Limpar caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 2. Otimizar para produção
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Otimizar autoloader
composer dump-autoload --optimize
```

---

## 🚀 ETAPA 9: REINICIAR SERVIÇOS

```bash
# 1. Iniciar servidor web
sudo systemctl start apache2
# OU
sudo systemctl start nginx

# 2. Reiniciar PHP-FPM (se aplicável)
sudo systemctl restart php8.1-fpm

# 3. Verificar status dos serviços
sudo systemctl status apache2
sudo systemctl status php8.1-fpm
```

---

## ✅ ETAPA 10: TESTES DE VALIDAÇÃO

### 1. Teste das páginas principais:
```bash
# Teste básico de conectividade
curl -I http://seu-dominio.com

# Teste das páginas principais
curl -I http://seu-dominio.com/dashboard
curl -I http://seu-dominio.com/clients
curl -I http://seu-dominio.com/suppliers
curl -I http://seu-dominio.com/account-payables
curl -I http://seu-dominio.com/account-receivables
```

### 2. Teste do comando personalizado:
```bash
cd /var/www/html/bc
php artisan accounts:update-overdue
```

### 3. Teste visual no navegador:
- [ ] Dashboard carregando corretamente
- [ ] Menu de navegação funcionando
- [ ] Páginas de clientes/fornecedores acessíveis
- [ ] Páginas de contas a pagar/receber funcionando
- [ ] Formulários de cadastro operacionais

---

## 🔄 ETAPA 11: CONFIGURAR AUTOMAÇÃO (OPCIONAL)

### Configurar cron job para atualização automática:
```bash
# Editar crontab
crontab -e

# Adicionar linha para executar diariamente às 6h da manhã
0 6 * * * cd /var/www/html/bc && php artisan accounts:update-overdue >/dev/null 2>&1
```

---

## 🆘 ROLLBACK (EM CASO DE PROBLEMAS)

### Se algo der errado, faça rollback:
```bash
# 1. Parar servidor web
sudo systemctl stop apache2

# 2. Restaurar arquivos
rm -rf /var/www/html/bc/*
tar -xzf backup-bc-files-*.tar.gz -C /

# 3. Restaurar banco de dados
mysql -u [usuario] -p[senha] [nome_banco] < backup-bc-*.sql

# 4. Reiniciar servidor
sudo systemctl start apache2
```

---

## 📋 CHECKLIST FINAL

### Antes de finalizar, confirme:
- [ ] ✅ Backup realizado com sucesso
- [ ] ✅ Arquivos atualizados no servidor
- [ ] ✅ Migrações executadas (4 novas tabelas)
- [ ] ✅ Dependências instaladas
- [ ] ✅ Cache limpo e otimizado
- [ ] ✅ Serviços reiniciados
- [ ] ✅ Dashboard acessível
- [ ] ✅ Módulos funcionando (clientes, fornecedores, contas)
- [ ] ✅ Relatório de gestão financeira operacional
- [ ] ✅ Comando personalizado funcionando

---

## 📞 SUPORTE

### Em caso de dúvidas ou problemas:

1. **Verificar logs de erro:**
   ```bash
   tail -f /var/www/html/bc/storage/logs/laravel.log
   tail -f /var/log/apache2/error.log
   ```

2. **Verificar permissões:**
   ```bash
   ls -la /var/www/html/bc/storage
   ls -la /var/www/html/bc/bootstrap/cache
   ```

3. **Testar conectividade do banco:**
   ```bash
   cd /var/www/html/bc
   php artisan tinker --execute="DB::connection()->getPdo(); echo 'Conexão OK!' . PHP_EOL;"
   ```

---

## 🎉 CONCLUSÃO

Após seguir todos esses passos, seu sistema BC estará atualizado com:

- ✨ **4 novos módulos** (Clientes, Fornecedores, Contas a Pagar/Receber)
- 📊 **Dashboard financeiro integrado**
- 📈 **Relatórios de gestão financeira**
- 🤖 **Automação via linha de comando**
- 🎨 **Interface moderna e responsiva**

**Sistema pronto para produção!** 🚀✅
