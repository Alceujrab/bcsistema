#!/bin/bash

echo "=== CRIANDO PACOTE DE DEPLOY - CORREÇÕES CSS E IMPORTAÇÃO ==="
echo "Data: $(date)"
echo ""

# Nome do pacote
PACKAGE_NAME="bc-sistema-correcoes-css-$(date +%Y%m%d_%H%M%S)"

echo "1. Criando estrutura do pacote..."
mkdir -p $PACKAGE_NAME/app/Http/Controllers
mkdir -p $PACKAGE_NAME/app/Services  
mkdir -p $PACKAGE_NAME/resources/views/layouts
mkdir -p $PACKAGE_NAME/resources/views/imports
mkdir -p $PACKAGE_NAME/resources/views/reconciliations
mkdir -p $PACKAGE_NAME/resources/views/settings
mkdir -p $PACKAGE_NAME/scripts

echo "2. Copiando arquivos alterados..."

# Controllers e Services
cp app/Http/Controllers/ImportController.php $PACKAGE_NAME/app/Http/Controllers/
cp app/Services/StatementImportService.php $PACKAGE_NAME/app/Services/

# Views principais
cp resources/views/layouts/app.blade.php $PACKAGE_NAME/resources/views/layouts/
cp resources/views/dashboard.blade.php $PACKAGE_NAME/resources/views/
cp resources/views/settings/index.blade.php $PACKAGE_NAME/resources/views/settings/

# Views de importação
cp resources/views/imports/index.blade.php $PACKAGE_NAME/resources/views/imports/
cp resources/views/imports/create.blade.php $PACKAGE_NAME/resources/views/imports/
cp resources/views/imports/show.blade.php $PACKAGE_NAME/resources/views/imports/ 2>/dev/null

# Views de conciliação
cp resources/views/reconciliations/index.blade.php $PACKAGE_NAME/resources/views/reconciliations/
cp resources/views/reconciliations/create.blade.php $PACKAGE_NAME/resources/views/reconciliations/ 2>/dev/null
cp resources/views/reconciliations/show.blade.php $PACKAGE_NAME/resources/views/reconciliations/ 2>/dev/null

# Scripts
cp deploy-correcoes.sh $PACKAGE_NAME/scripts/
cp teste-correcoes-css.sh $PACKAGE_NAME/scripts/
cp teste-consistencia-css.sh $PACKAGE_NAME/scripts/

echo "3. Criando documentação do deploy..."
cat > $PACKAGE_NAME/README-DEPLOY.md << 'EOF'
# DEPLOY - CORREÇÕES CSS E IMPORTAÇÃO

## Data
$(date)

## Arquivos Incluídos

### Backend
- `app/Http/Controllers/ImportController.php` - Suporte a PDF/Excel
- `app/Services/StatementImportService.php` - Parsers PDF/Excel

### Frontend
- `resources/views/layouts/app.blade.php` - CSS global + notificações
- `resources/views/dashboard.blade.php` - Cards padronizados
- `resources/views/settings/index.blade.php` - Botões contrastantes  
- `resources/views/imports/` - Interface moderna
- `resources/views/reconciliations/` - Estatísticas visuais

### Scripts
- `scripts/deploy-correcoes.sh` - Script de deploy automático
- `scripts/teste-*.sh` - Scripts de validação

## Como Instalar

1. **Backup do servidor atual**
   ```bash
   cp -r /seu/path/bc /backup/bc-$(date +%Y%m%d)
   ```

2. **Copiar arquivos**
   ```bash
   # Via FTP ou rsync
   rsync -av ./ usuario@servidor:/path/do/projeto/
   ```

3. **Executar no servidor**
   ```bash
   cd /path/do/projeto
   chmod +x scripts/deploy-correcoes.sh
   ./scripts/deploy-correcoes.sh
   ```

4. **Testar**
   ```bash
   ./scripts/teste-correcoes-css.sh
   ./scripts/teste-consistencia-css.sh
   ```

## Correções Implementadas

✅ **Notificações melhoradas**
- Tempo de exibição: 8 segundos (antes 5s)
- Pausa automática no hover
- Animações suaves

✅ **Configurações visuais**
- Botões com contraste adequado
- Estados visuais bem definidos
- Estilos robustos com !important

✅ **Importação PDF/Excel**
- Validação para PDF, XLS, XLSX
- Tamanho máximo: 20MB
- Parsers específicos implementados
- Interface atualizada

✅ **Design System BC**
- CSS consistente em todas as views
- Paleta de cores unificada
- Animações padronizadas
- Responsividade completa

## Verificação Pós-Deploy

1. Testar dashboard - cards e animações
2. Testar configurações - botões e contraste  
3. Testar importação - PDF/Excel upload
4. Testar conciliação - interface visual
5. Verificar notificações - tempo e hover

## Rollback

Se necessário, restaurar do backup:
```bash
cp -r /backup/bc-YYYYMMDD/* /path/do/projeto/
php artisan config:clear
php artisan view:clear
```
EOF

echo "4. Criando arquivo compactado..."
tar -czf $PACKAGE_NAME.tar.gz $PACKAGE_NAME/
zip -r $PACKAGE_NAME.zip $PACKAGE_NAME/ >/dev/null 2>&1

echo "5. Limpando arquivos temporários..."
rm -rf $PACKAGE_NAME/

echo ""
echo "✅ PACOTE CRIADO COM SUCESSO!"
echo ""
echo "📦 Arquivos gerados:"
echo "   - $PACKAGE_NAME.tar.gz (formato Linux/Mac)"
echo "   - $PACKAGE_NAME.zip (formato Windows)"
echo ""
echo "📁 Conteúdo:"
echo "   - Todos os arquivos PHP alterados"
echo "   - Todas as views com CSS corrigido"
echo "   - Scripts de deploy e teste"
echo "   - Documentação completa"
echo ""
echo "🚀 Para enviar ao servidor:"
echo "   1. Baixe o arquivo .tar.gz ou .zip"
echo "   2. Envie via FTP/SCP para seu servidor"
echo "   3. Extraia e execute os scripts"
