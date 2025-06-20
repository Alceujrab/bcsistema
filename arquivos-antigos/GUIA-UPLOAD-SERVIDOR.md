# GUIA RÁPIDO - UPLOAD PARA SERVIDOR

## 📦 Arquivos Prontos para Deploy
- `bc-sistema-correcoes-css-20250619_233924.tar.gz`
- `bc-sistema-correcoes-css-20250619_233924.zip`

## 🚀 Opção 1: Upload via FTP/SFTP

```bash
# 1. Baixar o arquivo do workspace
# 2. Conectar no seu servidor via FTP
# 3. Enviar o arquivo .tar.gz para o diretório do projeto
# 4. Extrair no servidor:
tar -xzf bc-sistema-correcoes-css-20250619_233924.tar.gz
cd bc-sistema-correcoes-css-20250619_233924
chmod +x scripts/deploy-correcoes.sh
./scripts/deploy-correcoes.sh
```

## 🚀 Opção 2: Upload via SCP (se tiver SSH)

```bash
# Do seu computador local:
scp bc-sistema-correcoes-css-20250619_233924.tar.gz usuario@seuservidor.com:/path/do/projeto/

# No servidor:
ssh usuario@seuservidor.com
cd /path/do/projeto
tar -xzf bc-sistema-correcoes-css-20250619_233924.tar.gz
cd bc-sistema-correcoes-css-20250619_233924
chmod +x scripts/deploy-correcoes.sh
./scripts/deploy-correcoes.sh
```

## 🚀 Opção 3: Upload via Git (se tiver repositório)

```bash
# No workspace atual:
git add .
git commit -m "Correções CSS e Importação PDF/Excel implementadas"
git push origin main

# No servidor:
git pull origin main
chmod +x deploy-correcoes.sh
./deploy-correcoes.sh
```

## ⚡ Comandos Rápidos no Servidor

```bash
# 1. Backup atual
cp -r /path/do/projeto /backup/bc-$(date +%Y%m%d)

# 2. Aplicar correções
cd /path/do/projeto
./scripts/deploy-correcoes.sh

# 3. Testar
./scripts/teste-correcoes-css.sh
./scripts/teste-consistencia-css.sh

# 4. Verificar logs
tail -f storage/logs/laravel.log
```

## 🔧 Permissões Importantes

```bash
# Aplicar permissões corretas
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chown -R www-data:www-data storage/ bootstrap/cache/
```

## 🧪 Como Testar Após Deploy

1. **Dashboard** - Verificar cards com animações
2. **Configurações** - Testar botões com contraste
3. **Importação** - Tentar upload de PDF/Excel
4. **Conciliação** - Verificar interface visual
5. **Notificações** - Testar tempo de exibição

## 🆘 Se Algo der Errado

```bash
# Restaurar backup
cp -r /backup/bc-YYYYMMDD/* /path/do/projeto/
php artisan config:clear
php artisan view:clear
service apache2 restart
# ou
service nginx restart
```

## ✅ Checklist Pós-Deploy

- [ ] Site carregando normalmente
- [ ] Dashboard com visual novo
- [ ] Configurações com botões contrastantes  
- [ ] Importação aceitando PDF/Excel
- [ ] Notificações ficando 8 segundos
- [ ] CSS consistente em todas as telas
- [ ] Responsividade funcionando
- [ ] Logs sem erros críticos
