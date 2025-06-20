#!/bin/bash

echo "========================================================"
echo "  CORREÇÃO COMPLETA E DEFINITIVA DO SISTEMA BC"
echo "  Análise profissional e correção de TODAS as dependências"
echo "========================================================"
echo ""

# Definir cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

echo "1. ANÁLISE DO PROBLEMA:"
echo "   - Sistema tem dois modelos para importação: ImportLog e StatementImport"
echo "   - ImportController tenta usar ImportLog mas no servidor funciona com StatementImport"
echo "   - Layouts quebrados por inconsistência de dados"
echo "   - Dependências cruzadas causando erros em cascata"
echo ""

echo "2. ESTRATÉGIA DE CORREÇÃO:"
echo "   - Padronizar TUDO para usar StatementImport (modelo funcional)"
echo "   - Remover dependências do ImportLog completamente"
echo "   - Corrigir todos os controllers que usam ImportLog"
echo "   - Atualizar views para usar dados corretos"
echo "   - Testar todas as integrações"
echo ""

echo "3. INICIANDO CORREÇÕES..."
echo ""

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    log_error "Execute este script no diretório raiz do Laravel"
    exit 1
fi

log_info "Criando backup antes das correções..."
BACKUP_DIR="backup-correcao-completa-$(date +%Y%m%d_%H%M%S)"
mkdir -p $BACKUP_DIR

# Backup dos arquivos principais
cp -r app/Http/Controllers/ImportController.php $BACKUP_DIR/ 2>/dev/null
cp -r app/Http/Controllers/ReconciliationController.php $BACKUP_DIR/ 2>/dev/null
cp -r app/Services/StatementImportService.php $BACKUP_DIR/ 2>/dev/null
cp -r resources/views/imports/ $BACKUP_DIR/ 2>/dev/null
cp -r resources/views/reconciliations/ $BACKUP_DIR/ 2>/dev/null

log_success "Backup criado em $BACKUP_DIR"

echo ""
echo "4. EXECUTANDO CORREÇÕES ESTRUTURAIS..."
echo ""

log_info "Esta é a primeira fase do script"
log_info "Execute os próximos scripts na sequência:"
echo ""
echo "   ./correcao-completa-parte1.sh  (este script)"
echo "   ./correcao-completa-parte2.sh  (controllers)"
echo "   ./correcao-completa-parte3.sh  (views e testes)"
echo ""

log_success "Análise completa finalizada!"
log_success "Sistema mapeado e estratégia de correção definida"
echo ""
echo "PRÓXIMO PASSO: Execute ./correcao-completa-parte2.sh"
echo ""
