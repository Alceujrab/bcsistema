# 🚀 UPLOAD PARA SERVIDOR - GUIA PRÁTICO

## 📁 Arquivo Pronto para Upload
- `bc-sistema-correcoes-css-20250619_233924.tar.gz` (localizado em `/bc/`)

## ⚡ MÉTODO 1: Upload via FTP/SFTP (Mais Comum)

### 1. Baixar arquivo do workspace
```bash
# O arquivo está em: /workspaces/bcsistema/bc/bc-sistema-correcoes-css-20250619_233924.tar.gz
# Baixe este arquivo para seu computador
```

### 2. Conectar no servidor via FTP
- Use FileZilla, WinSCP ou similar
- Envie o arquivo `.tar.gz` para o diretório do seu site Laravel

### 3. Executar no servidor (via SSH ou painel de controle)
```bash
# Extrair arquivo
tar -xzf bc-sistema-correcoes-css-20250619_233924.tar.gz

# Entrar na pasta
cd bc-sistema-correcoes-css-20250619_233924

# Dar permissão ao script
chmod +x scripts/deploy-correcoes.sh

# Executar deploy
./scripts/deploy-correcoes.sh
```

## ⚡ MÉTODO 2: Upload via SCP (se tiver SSH)

```bash
# Do seu computador local:
scp bc-sistema-correcoes-css-20250619_233924.tar.gz usuario@seudominio.com:/path/do/seu/site/

# No servidor:
ssh usuario@seudominio.com
cd /path/do/seu/site
tar -xzf bc-sistema-correcoes-css-20250619_233924.tar.gz
cd bc-sistema-correcoes-css-20250619_233924
chmod +x scripts/deploy-correcoes.sh
./scripts/deploy-correcoes.sh
```

## ⚡ MÉTODO 3: Manual (Arrastar e soltar arquivos)

Se não tiver SSH, você pode copiar manualmente:

### Arquivos principais para substituir:
```
app/Http/Controllers/ImportController.php
app/Services/StatementImportService.php
resources/views/layouts/app.blade.php
resources/views/dashboard.blade.php
resources/views/settings/index.blade.php
resources/views/imports/index.blade.php
resources/views/imports/create.blade.php
resources/views/imports/show.blade.php
resources/views/reconciliations/index.blade.php
resources/views/reconciliations/create.blade.php
resources/views/reconciliations/show.blade.php
```

### Depois execute (se tiver acesso SSH):
```bash
php artisan config:clear
php artisan view:clear
php artisan cache:clear
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

## 🧪 Como Testar Após Upload

1. **Acesse seu site** - Deve carregar normalmente
2. **Dashboard** - Deve ter visual novo com cards animados
3. **Configurações** - Botões devem ter contraste melhor
4. **Importação** - Deve aceitar arquivos PDF e Excel
5. **Notificações** - Devem durar 8 segundos e pausar no hover

## 🆘 Se Algo der Errado

```bash
# Limpar cache se houver problemas
php artisan config:clear
php artisan view:clear
php artisan cache:clear

# Verificar logs
tail -f storage/logs/laravel.log
```

## ✅ Checklist Pós-Upload

- [ ] Site abre normalmente
- [ ] Dashboard com novo design
- [ ] Menus funcionando  
- [ ] Importação aceitando PDF/Excel
- [ ] Botões de configuração visíveis
- [ ] Notificações aparecendo corretamente

---

**💡 DICA:** Se você usa cPanel, pode extrair o arquivo `.tar.gz` diretamente no File Manager e depois executar os comandos PHP via Terminal no cPanel.
