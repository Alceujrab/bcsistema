# Como Atualizar o Sistema - Guia Completo

## 1. ARQUIVOS PARA UPLOAD

### Controllers Corrigidos (OBRIGATÓRIO):
```
/app/Http/Controllers/DashboardController.php
/app/Http/Controllers/BankAccountController.php  
/app/Http/Controllers/TransactionController.php
/app/Http/Controllers/ReportController.php
/app/Http/Controllers/ImportController.php
/app/Http/Controllers/ReconciliationController.php
/app/Http/Controllers/CategoryController.php
```

### Views Modernizadas (RECOMENDADO):
```
/resources/views/dashboard.blade.php
/resources/views/categories/index.blade.php
/resources/views/categories/create.blade.php
/resources/views/categories/edit.blade.php
/resources/views/imports/index.blade.php
/resources/views/imports/create.blade.php
/resources/views/imports/show.blade.php
/resources/views/reports/index.blade.php
/resources/views/reports/transactions.blade.php
/resources/views/reports/cash-flow.blade.php
/resources/views/reports/reconciliations.blade.php
/resources/views/reports/categories.blade.php
/resources/views/reconciliations/index.blade.php
/resources/views/reconciliations/create.blade.php
/resources/views/reconciliations/show.blade.php
/resources/views/bank-accounts/index.blade.php
/resources/views/bank-accounts/create.blade.php
/resources/views/bank-accounts/edit.blade.php
/resources/views/bank-accounts/show.blade.php
/resources/views/transactions/index.blade.php
/resources/views/transactions/create.blade.php
/resources/views/transactions/edit.blade.php
/resources/views/transactions/show.blade.php
```

## 2. MÉTODOS DE UPLOAD

### Opção A: Via FTP/SFTP
1. Use FileZilla, WinSCP ou similar
2. Conecte no servidor
3. Navegue até a pasta do Laravel
4. Faça upload dos arquivos mantendo a estrutura de pastas

### Opção B: Via cPanel File Manager
1. Acesse o cPanel
2. Abra "File Manager"
3. Navegue até public_html ou pasta do Laravel
4. Faça upload dos arquivos

### Opção C: Via Git (se configurado)
```bash
git add .
git commit -m "Correções de SQL e modernização das views"
git push origin main
```

## 3. COMANDOS PÓS-UPLOAD

### Execute no terminal do servidor:
```bash
# 1. Limpar cache do Laravel
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 2. Otimizar (PRODUÇÃO)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Atualizar autoload
composer dump-autoload

# 4. Verificar permissões
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

## 4. VERIFICAÇÕES PÓS-UPDATE

### Teste estas páginas:
1. ✅ Dashboard: `/`
2. ✅ Contas Bancárias: `/bank-accounts`
3. ✅ Transações: `/transactions`
4. ✅ Relatórios: `/reports`
5. ✅ Categorias: `/categories`
6. ✅ Importações: `/imports`
7. ✅ Conciliações: `/reconciliations`

### Verifique se:
- ✅ Não há mais erros SQLSTATE[42S22]
- ✅ Dashboard carrega com estatísticas
- ✅ Filtros funcionam corretamente
- ✅ Paginação funciona
- ✅ Modais abrem/fecham
- ✅ Design responsivo funciona

## 5. SOLUÇÃO DE PROBLEMAS

### Se ainda houver erros SQL:
```bash
# Verificar logs
tail -f storage/logs/laravel.log

# Ou verificar no cPanel em "Error Logs"
```

### Se views não carregarem:
```bash
# Limpar cache de views
php artisan view:clear
```

### Se CSS/JS não carregarem:
```bash
# Recompilar assets (se usando Vite/Mix)
npm run build
# ou
npm run production
```

## 6. BACKUP ANTES DE ATUALIZAR

### IMPORTANTE - Faça backup:
```
1. Backup do banco de dados
2. Backup dos arquivos atuais
3. Anote a versão atual do sistema
```

## 7. ORDEM RECOMENDADA DE UPLOAD

1. **PRIMEIRO**: Faça backup
2. **SEGUNDO**: Upload dos Controllers (corrige erros SQL)
3. **TERCEIRO**: Teste o dashboard
4. **QUARTO**: Upload das Views (melhora interface)
5. **QUINTO**: Limpe todos os caches
6. **SEXTO**: Teste todas as páginas

## 8. COMANDOS ESSENCIAIS

```bash
# Para sistema em produção
php artisan down              # Colocar em manutenção
# ... fazer upload ...
php artisan cache:clear       # Limpar caches
php artisan up                # Retirar de manutenção

# Para desenvolvimento
php artisan serve            # Testar localmente
```

## 9. CONTATO/SUPORTE

Se houver problemas após a atualização:
1. Verifique os logs de erro
2. Teste uma página por vez
3. Compare com os arquivos de backup
4. Reverta se necessário e tente novamente

---
**IMPORTANTE**: Os Controllers DEVEM ser atualizados primeiro para corrigir os erros SQL!
