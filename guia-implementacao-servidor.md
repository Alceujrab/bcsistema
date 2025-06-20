# Guia de Implementação no Servidor

## 1. Preparação do Ambiente

### Conectar ao Servidor
```bash
# SSH para o servidor
ssh usuario@seu-servidor.com

# Navegar para o diretório do projeto
cd /path/to/your/laravel/project
```

### Fazer Backup
```bash
# Backup completo do projeto
tar -czf backup_$(date +%Y%m%d_%H%M%S).tar.gz .

# Backup específico do banco de dados
mysqldump -u usuario -p nome_banco > backup_db_$(date +%Y%m%d_%H%M%S).sql
```

## 2. Atualizar os Arquivos

### Controller de Transações
```bash
# Backup do controller atual
cp app/Http/Controllers/TransactionController.php app/Http/Controllers/TransactionController.php.bak

# Editar o controller
nano app/Http/Controllers/TransactionController.php
```

**Cole o código do TransactionController modernizado que criamos**

### Views de Transações
```bash
# Backup das views
cp -r resources/views/transactions resources/views/transactions_backup

# Atualizar a view principal
nano resources/views/transactions/index.blade.php
```

**Cole o código da view modernizada**

### Criar View Parcial da Tabela
```bash
# Criar diretório se não existir
mkdir -p resources/views/transactions/partials

# Criar a view parcial
nano resources/views/transactions/partials/table.blade.php
```

**Cole o código da tabela modernizada**

### JavaScript
```bash
# Criar diretório se não existir
mkdir -p resources/js

# Criar arquivo JavaScript
nano resources/js/transactions.js
```

**Cole o código JavaScript modernizado**

### Copiar para Public (alternativa simples)
```bash
# Copiar JavaScript diretamente para public
cp resources/js/transactions.js public/js/transactions.js
```

## 3. Atualizar Rotas

```bash
# Backup das rotas
cp routes/web.php routes/web.php.bak

# Editar rotas
nano routes/web.php
```

**Adicione as novas rotas AJAX:**
```php
// Rotas AJAX para transações
Route::patch('transactions/{transaction}/quick-update', [TransactionController::class, 'quickUpdate'])->name('transactions.quick-update');
Route::delete('transactions/bulk-delete', [TransactionController::class, 'bulkDelete'])->name('transactions.bulk-delete');
Route::post('transactions/bulk-categorize', [TransactionController::class, 'bulkCategorize'])->name('transactions.bulk-categorize');
Route::post('transactions/bulk-status', [TransactionController::class, 'bulkUpdateStatus'])->name('transactions.bulk-status');
Route::post('transactions/auto-categorize', [TransactionController::class, 'autoCategorize'])->name('transactions.auto-categorize');
Route::get('transactions/export', [TransactionController::class, 'export'])->name('transactions.export');
```

## 4. Executar Migrations (se necessário)

```bash
# Criar migrations para melhorar as tabelas
php artisan make:migration enhance_transactions_table
php artisan make:migration enhance_categories_table
```

**Cole o código das migrations que criamos e depois execute:**
```bash
php artisan migrate
```

## 5. Limpar Cache e Otimizar

```bash
# Limpar todos os caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Recompilar caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Otimizar autoloader
composer dump-autoload -o
```

## 6. Verificar Permissões

```bash
# Ajustar permissões se necessário
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

## 7. Teste do Sistema

### Teste Básico
```bash
# Testar se o Laravel está funcionando
curl http://seu-site.com/test
```

### Teste das Rotas
```bash
# Testar dashboard
curl http://seu-site.com/dashboard

# Testar transações
curl http://seu-site.com/transactions
```

## 8. Configuração do Servidor Web

### Apache (.htaccess)
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

### Nginx
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    include fastcgi_params;
}
```

## 9. Monitoramento e Logs

```bash
# Verificar logs de erro
tail -f storage/logs/laravel.log

# Verificar logs do servidor web
# Apache
tail -f /var/log/apache2/error.log

# Nginx
tail -f /var/log/nginx/error.log
```

## 10. Script de Deploy Completo

Crie um script para automatizar o processo:

```bash
nano deploy.sh
```

```bash
#!/bin/bash

echo "🚀 Iniciando deploy das melhorias..."

# Backup
echo "📦 Criando backup..."
tar -czf backup_$(date +%Y%m%d_%H%M%S).tar.gz --exclude=node_modules --exclude=vendor .

# Limpar caches
echo "🧹 Limpando caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Atualizar dependências se necessário
echo "📚 Atualizando dependências..."
composer install --no-dev --optimize-autoloader

# Executar migrations
echo "🗄️ Executando migrations..."
php artisan migrate --force

# Recompilar caches
echo "⚡ Recompilando caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ajustar permissões
echo "🔧 Ajustando permissões..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "✅ Deploy concluído!"
```

```bash
chmod +x deploy.sh
```

## 11. Troubleshooting Comum

### Erro 500
```bash
# Verificar logs
tail -f storage/logs/laravel.log

# Verificar permissões
ls -la storage/
ls -la bootstrap/cache/
```

### Rotas não funcionam
```bash
# Limpar cache de rotas
php artisan route:clear

# Verificar se mod_rewrite está ativo (Apache)
a2enmod rewrite
systemctl reload apache2
```

### JavaScript não carrega
```bash
# Verificar se o arquivo existe
ls -la public/js/transactions.js

# Verificar permissões
chmod 644 public/js/transactions.js
```

### Banco de dados
```bash
# Testar conexão
php artisan tinker
>>> DB::connection()->getPdo();
```

## 12. Comandos Úteis

```bash
# Ver todas as rotas
php artisan route:list

# Ver configuração atual
php artisan config:show

# Executar seeders se necessário
php artisan db:seed

# Executar queue workers se necessário
php artisan queue:work

# Monitorar performance
php artisan horizon (se usando Redis)
```

---

**⚠️ Importante:**
- Sempre faça backup antes de qualquer alteração
- Teste em ambiente de desenvolvimento primeiro
- Monitore os logs após o deploy
- Mantenha uma versão de rollback pronta
