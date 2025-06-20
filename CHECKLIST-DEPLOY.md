# ✅ CHECKLIST DE DEPLOY - BC SISTEMA

## 🔧 CONFIGURAÇÕES INICIAIS

### Antes de começar:
- [ ] **Configurar variáveis no script** `deploy-automatizado-servidor.sh`:
  ```bash
  SERVER_USER="seu-usuario"      # Ex: root, ubuntu, admin
  SERVER_HOST="seu-dominio.com"  # Ex: meusite.com.br
  SERVER_PATH="/var/www/html/bc" # Caminho no servidor
  DB_USER="usuario_banco"        # Usuário do MySQL
  DB_NAME="nome_banco"          # Nome do banco de dados
  ```

- [ ] **Testar conexão SSH:**
  ```bash
  ssh seu-usuario@seu-servidor.com
  ```

---

## 🚀 OPÇÕES DE DEPLOY

### 📋 OPÇÃO 1: Deploy Automatizado (Recomendado)
```bash
# 1. Configurar o script (editar variáveis acima)
nano deploy-automatizado-servidor.sh

# 2. Executar deploy completo
./deploy-automatizado-servidor.sh
```

### 📋 OPÇÃO 2: Deploy Manual (Passo a Passo)
Siga o guia detalhado: `GUIA-DEPLOY-SERVIDOR.md`

---

## ✅ VERIFICAÇÕES PÓS-DEPLOY

### 1. Testar páginas principais:
- [ ] Dashboard: `http://seu-site.com/dashboard`
- [ ] Clientes: `http://seu-site.com/clients`
- [ ] Fornecedores: `http://seu-site.com/suppliers`
- [ ] Contas a Pagar: `http://seu-site.com/account-payables`
- [ ] Contas a Receber: `http://seu-site.com/account-receivables`
- [ ] Relatório: `http://seu-site.com/reports/financial-management`

### 2. Testar funcionalidades:
- [ ] Login no sistema
- [ ] Cadastrar novo cliente
- [ ] Cadastrar novo fornecedor
- [ ] Criar conta a pagar
- [ ] Criar conta a receber
- [ ] Visualizar dashboard

### 3. Configurar automação (opcional):
```bash
# Adicionar ao crontab do servidor
crontab -e

# Linha para adicionar (executa diariamente às 6h):
0 6 * * * cd /var/www/html/bc && php artisan accounts:update-overdue
```

---

## 🆘 PROBLEMAS COMUNS

### Erro de Permissões:
```bash
cd /var/www/html/bc
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
sudo chmod -R 775 storage bootstrap/cache
```

### Erro de Composer:
```bash
cd /var/www/html/bc
composer install --no-dev --optimize-autoloader
```

### Erro de Migrações:
```bash
cd /var/www/html/bc
php artisan migrate:status
php artisan migrate --force
```

### Erro de Cache:
```bash
cd /var/www/html/bc
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

---

## 📞 COMANDOS ÚTEIS

### Verificar status do sistema:
```bash
cd /var/www/html/bc
php artisan accounts:update-overdue --report
```

### Criar dados de exemplo:
```bash
cd /var/www/html/bc
# Execute o script que está em popular-dados-exemplo.sh
```

### Verificar logs:
```bash
tail -f /var/www/html/bc/storage/logs/laravel.log
```

---

## 🎉 SUCESSO!

Se todas as verificações passaram, seu sistema está funcionando!

**Recursos disponíveis:**
- ✨ Dashboard financeiro integrado
- 👥 Gestão de clientes
- 🏢 Gestão de fornecedores  
- 💳 Contas a pagar
- 💰 Contas a receber
- 📊 Relatórios de gestão
- 🤖 Automação via comando

**Próximos passos:**
1. Cadastrar seus dados reais
2. Configurar backup automático
3. Treinar usuários no sistema
4. Personalizar conforme necessidade
