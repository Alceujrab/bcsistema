#!/bin/bash

# Script de Verificação Completa da Instalação BC Sistema
# Execute com: bash verificar-instalacao-completa.sh

# Cores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m'

# Contadores
OK_COUNT=0
ERROR_COUNT=0
WARN_COUNT=0

# Funções de log
log_ok() {
    echo -e "${GREEN}✅ $1${NC}"
    ((OK_COUNT++))
}

log_error() {
    echo -e "${RED}❌ $1${NC}"
    ((ERROR_COUNT++))
}

log_warn() {
    echo -e "${YELLOW}⚠️  $1${NC}"
    ((WARN_COUNT++))
}

log_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

log_header() {
    echo -e "${CYAN}🔍 $1${NC}"
}

# Cabeçalho
clear
echo "=================================================="
echo "🚀 VERIFICAÇÃO COMPLETA - BC SISTEMA"
echo "   Data: $(date '+%d/%m/%Y %H:%M:%S')"
echo "=================================================="
echo

# 1. Verificar estrutura básica
log_header "VERIFICANDO ESTRUTURA BÁSICA"
if [ -d "bc" ]; then
    log_ok "Diretório bc/ encontrado"
    cd bc
else
    log_error "Diretório bc/ não encontrado"
    exit 1
fi

# 2. Verificar instaladores
log_header "VERIFICANDO INSTALADORES"

instaladores=(
    "install.php"
    "instalador-ultra.php"
    "instalador-github.php"
    "instalador-automatico.php"
    "instalador-final.php"
    "instalador-simples-web.php"
)

for instalador in "${instaladores[@]}"; do
    if [ -f "$instalador" ]; then
        log_ok "Instalador $instalador presente"
    else
        log_warn "Instalador $instalador não encontrado"
    fi
done

# 3. Verificar arquivos corrigidos
log_header "VERIFICANDO ARQUIVOS CORRIGIDOS"

# StatementImportService
if [ -f "app/Services/StatementImportService.php" ]; then
    if grep -q "normalizeDateFormat" "app/Services/StatementImportService.php"; then
        log_ok "StatementImportService.php com correções aplicadas"
    else
        log_warn "StatementImportService.php pode não ter todas as correções"
    fi
else
    log_error "StatementImportService.php não encontrado"
fi

# ImportController
if [ -f "app/Http/Controllers/ImportController.php" ]; then
    if grep -q "validatePdfFormat" "app/Http/Controllers/ImportController.php"; then
        log_ok "ImportController.php com correções aplicadas"
    else
        log_warn "ImportController.php pode não ter todas as correções"
    fi
else
    log_error "ImportController.php não encontrado"
fi

# View de importação
if [ -f "resources/views/imports/create.blade.php" ]; then
    if grep -q "imports.css" "resources/views/imports/create.blade.php"; then
        log_ok "View create.blade.php com CSS externo configurado"
    else
        log_warn "View create.blade.php pode não ter CSS externo"
    fi
else
    log_error "View create.blade.php não encontrada"
fi

# CSS dedicado
if [ -f "public/css/imports.css" ]; then
    log_ok "CSS imports.css presente"
else
    log_warn "CSS imports.css não encontrado"
fi

# 4. Verificar permissões
log_header "VERIFICANDO PERMISSÕES"

directories=(
    "storage"
    "storage/logs"
    "storage/app"
    "storage/framework"
    "storage/backups"
    "bootstrap/cache"
    "public"
)

for dir in "${directories[@]}"; do
    if [ -d "$dir" ]; then
        if [ -w "$dir" ]; then
            log_ok "Diretório $dir com permissão de escrita"
        else
            log_error "Diretório $dir SEM permissão de escrita"
        fi
    else
        log_warn "Diretório $dir não encontrado"
    fi
done

# 5. Verificar arquivos de configuração
log_header "VERIFICANDO CONFIGURAÇÕES"

if [ -f ".env" ]; then
    log_ok "Arquivo .env presente"
    
    # Verificar configurações importantes
    if grep -q "DB_CONNECTION" ".env"; then
        log_ok "Configuração de banco presente"
    else
        log_warn "Configuração de banco pode estar incompleta"
    fi
    
    if grep -q "APP_KEY" ".env"; then
        log_ok "APP_KEY configurada"
    else
        log_error "APP_KEY não configurada"
    fi
else
    log_error "Arquivo .env não encontrado"
fi

# 6. Verificar logs
log_header "VERIFICANDO LOGS DE INSTALAÇÃO"

logs=(
    "install.log"
    "install-ultra.log"
    "github-install.log"
    "auto-install.log"
)

for log_file in "${logs[@]}"; do
    if [ -f "$log_file" ]; then
        log_ok "Log $log_file presente"
    else
        log_info "Log $log_file não presente (normal se instalador não foi usado)"
    fi
done

# 7. Verificar backups
log_header "VERIFICANDO BACKUPS"

if [ -d "storage/backups" ]; then
    backup_count=$(ls -1 storage/backups/ 2>/dev/null | wc -l)
    if [ "$backup_count" -gt 0 ]; then
        log_ok "Encontrados $backup_count backup(s) automático(s)"
    else
        log_info "Nenhum backup automático encontrado"
    fi
else
    log_warn "Diretório de backups não encontrado"
fi

# 8. Teste básico do Laravel
log_header "TESTANDO LARAVEL"

if [ -f "artisan" ]; then
    log_ok "Arquivo artisan presente"
    
    # Teste de comandos básicos
    if php artisan --version > /dev/null 2>&1; then
        log_ok "Laravel funcionando corretamente"
    else
        log_error "Erro ao executar comandos Laravel"
    fi
else
    log_error "Arquivo artisan não encontrado"
fi

# 9. Verificar composer
log_header "VERIFICANDO DEPENDÊNCIAS"

if [ -f "composer.json" ]; then
    log_ok "composer.json presente"
    
    if [ -d "vendor" ]; then
        log_ok "Dependências instaladas"
    else
        log_warn "Dependências podem não estar instaladas (execute: composer install)"
    fi
else
    log_error "composer.json não encontrado"
fi

# 10. Resumo final
echo
echo "=================================================="
echo "📊 RESUMO DA VERIFICAÇÃO"
echo "=================================================="
echo -e "${GREEN}✅ Sucessos: $OK_COUNT${NC}"
echo -e "${YELLOW}⚠️  Avisos: $WARN_COUNT${NC}"
echo -e "${RED}❌ Erros: $ERROR_COUNT${NC}"
echo

if [ $ERROR_COUNT -eq 0 ]; then
    echo -e "${GREEN}🎉 SISTEMA PRONTO PARA USO!${NC}"
    echo
    echo "Próximos passos recomendados:"
    echo "1. Teste os instaladores web acessando-os pelo navegador"
    echo "2. Execute um teste completo de importação PDF"
    echo "3. Verifique se o sistema responde normalmente"
    echo
elif [ $ERROR_COUNT -le 2 ]; then
    echo -e "${YELLOW}⚠️  SISTEMA PARCIALMENTE CONFIGURADO${NC}"
    echo "Corrija os erros encontrados antes de usar em produção"
    echo
else
    echo -e "${RED}❌ SISTEMA COM PROBLEMAS CRÍTICOS${NC}"
    echo "Resolva os erros antes de continuar"
    echo
fi

echo "📋 Log completo salvo em: verificacao-$(date +%Y%m%d_%H%M%S).log"

# Salvar log
{
    echo "Verificação BC Sistema - $(date)"
    echo "Sucessos: $OK_COUNT"
    echo "Avisos: $WARN_COUNT"
    echo "Erros: $ERROR_COUNT"
    echo "Verificação executada em: $(pwd)"
} > "verificacao-$(date +%Y%m%d_%H%M%S).log"

echo
echo "=================================================="
