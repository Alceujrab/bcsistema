# 🚀 GUIA DE DEPLOY COMPLETO - Sistema BC Modernizado

Este guia contém todas as instruções para fazer o deploy do sistema BC Sistema **totalmente modernizado** para o servidor de produção em `public_html/bc`.

## 📋 PRÉ-REQUISITOS

### No Servidor (public_html/bc)
- **PHP 8.1+** com extensões: pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, json
- **MySQL 5.7+** ou MariaDB 10.3+
- **Composer** instalado globalmente
- **Apache/Nginx** com mod_rewrite habilitado
- **Permissões de escrita** em storage/ e bootstrap/cache/

### Estrutura do Servidor
```
public_html/
└── bc/                     # Pasta principal do projeto
    ├── app/               # Controllers modernizados
    ├── resources/views/   # Views responsivas
    ├── public/           # Assets públicos
    ├── storage/          # Cache e logs
    └── vendor/           # Dependências
```

### Credenciais Necessárias
- ✅ Acesso FTP/SSH ao servidor
- ✅ Credenciais MySQL do .env
- ✅ Permissões de escrita na pasta public_html/bc

## 🎯 ESTRATÉGIA DE DEPLOY SEGURO

### OPÇÃO 1: DEPLOY VIA FTP (Recomendado para cPanel)
```bash
# 1. Preparar arquivos localmente
chmod +x prepare-deploy.sh
./prepare-deploy.sh

# 2. Comprimir para upload
cd /workspaces/bcsistema
tar -czf deploy-bc-sistema.tar.gz -C deploy-ready .

# 3. Upload via FTP para o servidor
# Use FileZilla, WinSCP ou cPanel File Manager
# Destino: /home/usadosar/public_html/bc/
```

### OPÇÃO 2: DEPLOY VIA SSH/RSYNC (Mais rápido)
```bash
# Se tiver acesso SSH ao servidor
rsync -avz --delete \
  --exclude='vendor' \
  --exclude='node_modules' \
  --exclude='.git' \
  --exclude='storage/logs/*' \
  ./bc/ usuario@servidor:/home/usadosar/public_html/bc/
```

## 📋 CHECKLIST PRÉ-DEPLOY

### ✅ Arquivos Modernizados:
- [x] app/Http/Controllers/DashboardController.php (com gráficos)
- [x] app/Http/Controllers/TransactionController.php (CRUD completo)
- [x] resources/views/layouts/app.blade.php (responsivo)
- [x] resources/views/dashboard.blade.php (Chart.js)
- [x] resources/views/transactions/*.blade.php (todas modernas)
- [x] routes/web.php (rotas atualizadas)

### ✅ Funcionalidades Implementadas:
- [x] Dashboard com gráficos interativos
- [x] Menu lateral responsivo com ícones
- [x] Views de transações completas (show/edit/create)
- [x] Sistema de alertas inteligentes
- [x] Validação robusta de formulários
- [x] Tratamento de erros aprimorado
- Dashboard: https://seusite.com/
- Transações: https://seusite.com/transactions
- Teste as funcionalidades de edição inline

---

## ⚠️ IMPORTANTE:

1. **BACKUP AUTOMÁTICO**: O script já faz backup do banco antes das alterações
2. **ROLLBACK AUTOMÁTICO**: Se algo der errado, o sistema restaura automaticamente
3. **MODO MANUTENÇÃO**: Durante o deploy, o site fica em manutenção temporariamente
4. **LOGS**: Todos os passos são logados para facilitar debug

---

## 🔧 FUNCIONALIDADES ADICIONADAS:

### Transações:
- ✅ Edição inline (clique na célula para editar)
- ✅ Filtros avançados em tempo real
- ✅ Ações em lote (deletar, categorizar, exportar)
- ✅ Auto-categorização inteligente
- ✅ Exportação CSV personalizada
- ✅ Interface moderna e responsiva

### Dashboard:
- ✅ Cards de estatísticas atualizados
- ✅ Gráficos interativos
- ✅ Alertas inteligentes
- ✅ Performance otimizada

### Categorias:
- ✅ Criação rápida via modal
- ✅ Hierarquia de categorias
- ✅ Regras de auto-categorização
- ✅ Limites de orçamento

---

## 📞 SUPORTE:

Se algo der errado durante o deploy:

1. **Restaurar backup**: 
   ```bash
   mysql -h localhost -P 3306 -u usadosar_lara962 -p'[17pvS1-16' usadosar_lara962 < backup_XXXXXX.sql
   ```

2. **Verificar logs**:
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Limpar caches**:
   ```bash
   php artisan cache:clear && php artisan config:clear
   ```

## 🔄 PASSO 4: COMANDOS LARAVEL NO SERVIDOR

### 4.1 Gerar Chave da Aplicação
```bash
cd /home/usadosar/public_html/bc

# Gerar nova chave (se não existir)
php artisan key:generate --force

# Limpar e otimizar caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### 4.2 Executar Migrações
```bash
# Verificar status das migrações
php artisan migrate:status

# Executar migrações (cuidado em produção!)
php artisan migrate --force

# Se houver seeders para executar
php artisan db:seed --force
```

### 4.3 Otimizar para Produção
```bash
# Cache de configuração
php artisan config:cache

# Cache de rotas
php artisan route:cache

# Cache de views
php artisan view:cache

# Otimizar autoload
composer dump-autoload --optimize
```

## 🌐 PASSO 5: CONFIGURAR APACHE/NGINX

### Para Apache (.htaccess já incluído)
```apache
# Arquivo: public_html/bc/public/.htaccess (já existe)
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

### Configurar Document Root
```bash
# No cPanel, configurar o Document Root para:
/home/usadosar/public_html/bc/public

# Ou criar symlink se não puder alterar Document Root:
ln -s /home/usadosar/public_html/bc/public/* /home/usadosar/public_html/
```

## 🧪 PASSO 6: TESTES DE FUNCIONALIDADE

### 6.1 Teste de Conectividade
```bash
# Testar conexão com banco
php artisan tinker
> DB::connection()->getPdo();
> exit
```

### 6.2 Verificar URLs Principais
- ✅ **Dashboard**: `https://seudominio.com/bc/`
- ✅ **Transações**: `https://seudominio.com/bc/transactions`
- ✅ **Contas**: `https://seudominio.com/bc/bank-accounts`
- ✅ **Relatórios**: `https://seudominio.com/bc/reports`

### 6.3 Testar Funcionalidades
1. **Login/Dashboard** - Verificar carregamento dos gráficos
2. **Menu Responsivo** - Testar no mobile
3. **Transações** - Criar, editar, visualizar
4. **Filtros** - Testar busca e filtros
5. **Responsividade** - Testar em diferentes dispositivos

## 🔧 PASSO 7: CONFIGURAÇÕES FINAIS

### 7.1 SSL/HTTPS
```bash
# Forçar HTTPS no .env
APP_URL=https://seudominio.com/bc

# No arquivo .htaccess (adicionar no topo)
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### 7.2 Backup Automático
```bash
# Criar script de backup (crontab)
# Arquivo: /home/usadosar/backup-bc.sh
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u usadosar_lara962 -p[senha] usadosar_lara962 > /home/usadosar/backups/bc_$DATE.sql
tar -czf /home/usadosar/backups/bc_files_$DATE.tar.gz -C /home/usadosar/public_html/ bc/

# Adicionar ao crontab (executar diariamente às 2h)
0 2 * * * /home/usadosar/backup-bc.sh
```

### 7.3 Monitoramento
```bash
# Verificar logs de erro
tail -f /home/usadosar/public_html/bc/storage/logs/laravel.log

# Verificar logs do Apache
tail -f /home/usadosar/logs/error_log
```

## 🚀 COMANDOS RÁPIDOS DE DEPLOY

### Script Completo de Deploy
```bash
#!/bin/bash
# deploy-completo.sh

echo "🚀 INICIANDO DEPLOY DO BC SISTEMA MODERNIZADO..."

# 1. Preparar arquivos
cd /workspaces/bcsistema
./prepare-deploy.sh

# 2. Comprimir
tar -czf deploy-bc-$(date +%Y%m%d_%H%M%S).tar.gz -C deploy-ready .

echo "✅ Arquivos preparados em: deploy-bc-$(date +%Y%m%d_%H%M%S).tar.gz"
echo ""
echo "📋 PRÓXIMOS PASSOS:"
echo "1. Fazer upload do arquivo .tar.gz para public_html/"
echo "2. Extrair para public_html/bc/"
echo "3. Executar: cd bc && composer install --no-dev"
echo "4. Executar: php artisan migrate --force"
echo "5. Executar: php artisan config:cache"
echo "6. Testar: https://seudominio.com/bc/"
echo ""
echo "🎉 DEPLOY CONCLUÍDO COM SUCESSO!"
```

## ✅ CHECKLIST FINAL

### Antes do Deploy
- [ ] Backup do banco atual
- [ ] Backup dos arquivos atuais
- [ ] Verificar .env com credenciais corretas
- [ ] Testar conexão MySQL local

### Durante o Deploy
- [ ] Upload dos arquivos modernizados
- [ ] Configurar permissões (755/644)
- [ ] Instalar dependências: `composer install --no-dev`
- [ ] Executar migrações: `php artisan migrate --force`
- [ ] Cache de produção: `php artisan config:cache`

### Após o Deploy
- [ ] Testar dashboard com gráficos
- [ ] Verificar menu responsivo
- [ ] Testar CRUD de transações
- [ ] Validar filtros e busca
- [ ] Confirmar responsividade mobile
- [ ] Verificar logs de erro

## 🆘 RESOLUÇÃO DE PROBLEMAS

### Erro 500 - Internal Server Error
```bash
# Verificar logs
tail -20 storage/logs/laravel.log

# Verificar permissões
chmod -R 775 storage/ bootstrap/cache/

# Limpar cache
php artisan config:clear
php artisan cache:clear
```

### Erro de Banco de Dados
```bash
# Testar conexão
php artisan tinker
> DB::connection()->getPdo();

# Verificar .env
cat .env | grep DB_
```

### Problemas de CSS/JS
```bash
# Limpar cache de views
php artisan view:clear

# Verificar se assets existem
ls -la public/css/ public/js/
```

### Menu Não Responsivo
```bash
# Verificar se Bootstrap e jQuery estão carregando
curl -I https://seudominio.com/bc/
```

---

## 🎉 CONCLUSÃO

Com este guia, você tem tudo para fazer o deploy completo do **Sistema BC Modernizado** em `public_html/bc/`. O sistema agora conta com:

- ✅ **Dashboard moderno** com gráficos Chart.js
- ✅ **Menu responsivo** com ícones Font Awesome  
- ✅ **Interface mobile-first** totalmente adaptativa
- ✅ **CRUD completo** para transações
- ✅ **Sistema de alertas** inteligente
- ✅ **Filtros avançados** e busca
- ✅ **Validação robusta** de formulários

**URLs principais após o deploy:**
- Dashboard: `https://seudominio.com/bc/`
- Transações: `https://seudominio.com/bc/transactions`
- Relatórios: `https://seudominio.com/bc/reports`

**Suporte**: Em caso de problemas, verifique os logs em `storage/logs/laravel.log` e siga a seção de resolução de problemas deste guia.
