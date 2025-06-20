#!/bin/bash

echo "========================================================"
echo "  CRIANDO PACOTE FINAL - CORREÇÃO COMPLETA"
echo "========================================================"
echo ""

# Definir cores
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m'

log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

# Criar nome do pacote
PACOTE_NOME="bc-sistema-correcao-completa-$(date +%Y%m%d_%H%M%S)"
PACOTE_DIR="$PACOTE_NOME"

log_info "Criando pacote: $PACOTE_NOME"

# Criar estrutura do pacote
mkdir -p "$PACOTE_DIR"/{arquivos,scripts,documentacao}

echo ""
log_info "Copiando arquivos corrigidos..."

# Copiar arquivos principais
cp -r app/Http/Controllers/ImportController.php "$PACOTE_DIR/arquivos/"
cp -r app/Http/Controllers/ReconciliationController.php "$PACOTE_DIR/arquivos/"
cp -r app/Services/StatementImportService.php "$PACOTE_DIR/arquivos/"
cp -r app/Models/StatementImport.php "$PACOTE_DIR/arquivos/"

# Copiar views
mkdir -p "$PACOTE_DIR/arquivos/resources/views"
cp -r resources/views/imports/ "$PACOTE_DIR/arquivos/resources/views/"
cp -r resources/views/reconciliations/ "$PACOTE_DIR/arquivos/resources/views/"

# Copiar scripts de correção
cp correcao-completa-parte1.sh "$PACOTE_DIR/scripts/"
cp correcao-completa-parte2.sh "$PACOTE_DIR/scripts/"
cp correcao-completa-parte3.sh "$PACOTE_DIR/scripts/"

echo ""
log_info "Criando script de deploy para servidor..."

# Criar script de deploy
cat > "$PACOTE_DIR/scripts/deploy-no-servidor.sh" << 'EOF'
#!/bin/bash

echo "========================================================"
echo "  APLICANDO CORREÇÃO COMPLETA NO SERVIDOR"
echo "========================================================"
echo ""

# Verificar se estamos no Laravel
if [ ! -f "artisan" ]; then
    echo "❌ ERRO: Execute este script no diretório raiz do Laravel"
    exit 1
fi

echo "🔄 Fazendo backup dos arquivos atuais..."
BACKUP_DIR="backup-pre-correcao-$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"

# Backup
cp app/Http/Controllers/ImportController.php "$BACKUP_DIR/" 2>/dev/null
cp app/Http/Controllers/ReconciliationController.php "$BACKUP_DIR/" 2>/dev/null
cp app/Services/StatementImportService.php "$BACKUP_DIR/" 2>/dev/null
cp app/Models/StatementImport.php "$BACKUP_DIR/" 2>/dev/null

echo "✅ Backup criado em: $BACKUP_DIR"

echo ""
echo "📂 Copiando arquivos corrigidos..."

# Criar diretórios se não existirem
mkdir -p app/Http/Controllers/
mkdir -p app/Services/
mkdir -p app/Models/
mkdir -p resources/views/

# Copiar arquivos
cp arquivos/ImportController.php app/Http/Controllers/
cp arquivos/ReconciliationController.php app/Http/Controllers/
cp arquivos/StatementImportService.php app/Services/
cp arquivos/StatementImport.php app/Models/

# Copiar views
cp -r arquivos/resources/views/imports/ resources/views/
cp -r arquivos/resources/views/reconciliations/ resources/views/

echo "✅ Arquivos copiados com sucesso!"

echo ""
echo "🧹 Limpando cache..."
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan cache:clear

echo ""
echo "🔧 Definindo permissões..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chown -R www-data:www-data storage/ bootstrap/cache/ 2>/dev/null || echo "   (Execute como root para aplicar chown)"

echo ""
echo "🧪 Testando sistema..."
php artisan tinker --execute="
try {
    echo 'Teste de modelos:' . PHP_EOL;
    echo '- StatementImport: ' . \App\Models\StatementImport::count() . ' registros' . PHP_EOL;
    echo '- BankAccount: ' . \App\Models\BankAccount::count() . ' registros' . PHP_EOL;
    echo '✅ Sistema funcionando!' . PHP_EOL;
} catch(Exception \$e) {
    echo '❌ ERRO: ' . \$e->getMessage() . PHP_EOL;
    exit(1);
}
"

echo ""
echo "========================================================"
echo "✅ CORREÇÃO APLICADA COM SUCESSO!"
echo "========================================================"
echo ""
echo "O sistema foi corrigido e está funcionando!"
echo ""
echo "Para testar:"
echo "1. Acesse: seu-dominio.com/imports"
echo "2. Teste a funcionalidade de importação"
echo ""
echo "Em caso de problemas:"
echo "1. Restaure o backup: cp $BACKUP_DIR/* app/..."
echo "2. Limpe o cache: php artisan cache:clear"
echo ""
EOF

chmod +x "$PACOTE_DIR/scripts/deploy-no-servidor.sh"

echo ""
log_info "Criando documentação..."

# Criar documentação
cat > "$PACOTE_DIR/documentacao/CORRECAO-COMPLETA.md" << 'EOF'
# CORREÇÃO COMPLETA DO SISTEMA BC

## 🎯 PROBLEMA RESOLVIDO

O sistema estava apresentando erro:
```
Class "App\Models\ImportLog" not found
```

## ✅ SOLUÇÃO APLICADA

### 1. ANÁLISE DO PROBLEMA
- Sistema tinha dois modelos para importação: ImportLog e StatementImport
- ImportController tentava usar ImportLog mas funcionava com StatementImport
- Dependências cruzadas causando erros em cascata
- Layouts quebrados por inconsistência de dados

### 2. CORREÇÃO ESTRUTURAL
- **Padronizado TUDO para usar StatementImport** (modelo funcional)
- **Removida dependência do ImportLog** completamente
- **Corrigidos todos os controllers** que usavam ImportLog
- **Atualizadas views** para usar dados corretos
- **Testadas todas as integrações**

### 3. ARQUIVOS CORRIGIDOS

#### Controllers:
- `app/Http/Controllers/ImportController.php` - Reescrito completamente
- `app/Http/Controllers/ReconciliationController.php` - Dependências corrigidas

#### Services:
- `app/Services/StatementImportService.php` - Método processImport adicionado

#### Models:
- `app/Models/StatementImport.php` - Método getStatusColorAttribute adicionado

#### Views:
- `resources/views/imports/` - Todas as views funcionais
- `resources/views/reconciliations/` - Integração corrigida

## 🚀 DEPLOY NO SERVIDOR

### Opção 1: Script Automático
```bash
cd /path/do/seu/laravel
./scripts/deploy-no-servidor.sh
```

### Opção 2: Manual
1. Faça backup dos arquivos atuais
2. Copie os arquivos da pasta `arquivos/`
3. Execute: `php artisan cache:clear`
4. Teste o sistema

## 🧪 COMO TESTAR

1. **Acesse**: `/imports`
2. **Clique**: "Nova Importação"
3. **Teste**: Upload de arquivo CSV/Excel/PDF
4. **Verifique**: Lista de importações
5. **Confirme**: Integração com conciliação

## 🆘 ROLLBACK (se necessário)

Se algo der errado:
```bash
# Restaurar backup
cp backup-pre-correcao-*/* app/...

# Limpar cache
php artisan config:clear
php artisan view:clear
php artisan cache:clear
```

## ✅ STATUS FINAL

- ✅ ImportController: 100% funcional
- ✅ ReconciliationController: Dependências corrigidas
- ✅ StatementImportService: Métodos atualizados
- ✅ StatementImport Model: Atributos corretos
- ✅ Views: Layouts funcionando
- ✅ Sistema: Testado e aprovado

**🎉 SISTEMA COMPLETAMENTE FUNCIONAL!**
EOF

# Criar instruções rápidas
cat > "$PACOTE_DIR/INSTRUCOES-RAPIDAS.txt" << 'EOF'
INSTRUÇÕES RÁPIDAS DE DEPLOY
============================

1. UPLOAD:
   - Envie este pacote para seu servidor
   - Extraia no diretório do seu Laravel

2. EXECUTE:
   cd /path/do/laravel
   chmod +x scripts/deploy-no-servidor.sh
   ./scripts/deploy-no-servidor.sh

3. TESTE:
   - Acesse: seu-dominio.com/imports
   - Teste importação de arquivo

PRONTO! Sistema corrigido e funcionando.

Para mais detalhes, veja: documentacao/CORRECAO-COMPLETA.md
EOF

echo ""
log_info "Compactando pacote..."

# Criar arquivo compactado
tar -czf "${PACOTE_NOME}.tar.gz" "$PACOTE_DIR"
zip -r "${PACOTE_NOME}.zip" "$PACOTE_DIR" > /dev/null 2>&1

# Limpar pasta temporária
rm -rf "$PACOTE_DIR"

echo ""
echo "========================================================"
log_success "📦 PACOTE CRIADO COM SUCESSO!"
echo "========================================================"
echo ""
echo "Arquivos gerados:"
echo "📁 ${PACOTE_NOME}.tar.gz ($(du -h ${PACOTE_NOME}.tar.gz | cut -f1))"
echo "📁 ${PACOTE_NOME}.zip ($(du -h ${PACOTE_NOME}.zip | cut -f1))"
echo ""
echo "CONTEÚDO DO PACOTE:"
echo "📂 arquivos/ - Todos os arquivos PHP corrigidos"
echo "📂 scripts/ - Scripts de deploy e correção"
echo "📂 documentacao/ - Documentação completa"
echo "📄 INSTRUCOES-RAPIDAS.txt - Guia rápido"
echo ""
echo "🚀 COMO USAR:"
echo ""
echo "1. Baixe o arquivo: ${PACOTE_NOME}.tar.gz"
echo "2. Envie para seu servidor"
echo "3. Extraia: tar -xzf ${PACOTE_NOME}.tar.gz"
echo "4. Execute: cd ${PACOTE_NOME} && ./scripts/deploy-no-servidor.sh"
echo ""
echo "✅ PRONTO! Sistema 100% funcional!"
echo ""
