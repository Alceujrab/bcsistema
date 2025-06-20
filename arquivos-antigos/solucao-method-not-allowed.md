# Solução para Erro "Method Not Allowed" - Laravel

## PROBLEMA IDENTIFICADO
**Erro**: Method Not Allowed - GET não suportado para rota `/`
**Causa**: Cache de rotas ou problema de configuração

## SOLUÇÕES (Execute na ordem)

### 1. LIMPAR TODOS OS CACHES (OBRIGATÓRIO)
```bash
# Execute estes comandos no terminal do servidor
php artisan cache:clear
php artisan config:clear
php artisan route:clear  
php artisan view:clear
php artisan optimize:clear
composer dump-autoload
```

### 2. VERIFICAR SE O LARAVEL ESTÁ FUNCIONANDO
Teste primeiro: `seusite.com.br/test`
- ✅ Se funcionar: Laravel OK, problema é só nas rotas
- ❌ Se não funcionar: Problema mais grave

### 3. TESTAR DEBUG DO DASHBOARD  
Teste: `seusite.com.br/debug-dashboard`
- ✅ Se funcionar: Controllers OK, problema é na rota principal
- ❌ Se não funcionar: Problema nos Models/Database

### 4. USAR ROTA ALTERNATIVA TEMPORÁRIA
Se `/` não funcionar, teste: `seusite.com.br/dashboard`

### 5. VERIFICAR ARQUIVO .HTACCESS
Certifique-se que existe `.htaccess` na pasta `public/`:
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

### 6. VERIFICAR PERMISSÕES
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod 644 routes/web.php
```

### 7. RECRIAR CACHE (SÓ EM PRODUÇÃO)
```bash
# Só execute depois de limpar os caches
php artisan config:cache
php artisan route:cache
```

## ARQUIVOS ATUALIZADOS

### `/routes/web.php` - Agora tem rotas de debug:
- ✅ `/test` - Testa se Laravel funciona
- ✅ `/debug-dashboard` - Testa se controllers/models funcionam  
- ✅ `/dashboard` - Rota alternativa
- ✅ `/` - Rota principal (corrigida)

### `/clear-all-cache.sh` - Script automático:
```bash
# Execute este script:
bash clear-all-cache.sh
```

## ORDEM DE TESTE

1. **Limpar caches** (comandos acima)
2. **Testar**: `/test`
3. **Testar**: `/debug-dashboard`  
4. **Testar**: `/dashboard`
5. **Testar**: `/` (página principal)

## SE AINDA NÃO FUNCIONAR

### Verificar logs:
```bash
tail -f storage/logs/laravel.log
```

### Ou no cPanel:
- Error Logs → verificar erros PHP

### Comandos de emergência:
```bash
# Modo manutenção
php artisan down

# Verificar se as tabelas existem
php artisan tinker
>>> \App\Models\BankAccount::count()
>>> \App\Models\Transaction::count()

# Sair do modo manutenção
php artisan up
```

---
**IMPORTANTE**: Execute os comandos de limpeza de cache ANTES de testar!
