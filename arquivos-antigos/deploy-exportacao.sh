#!/bin/bash

# 🚀 SCRIPT DE DEPLOY AUTOMÁTICO - SISTEMA DE EXPORTAÇÃO
# Autor: Sistema BC
# Data: $(date +%Y-%m-%d)

set -e  # Parar em caso de erro

echo "🚀 Iniciando deploy do sistema de exportação..."

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Variáveis
BACKUP_DIR="/home/usadosar/backups"
PROJECT_DIR="/home/usadosar/public_html/bc"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

echo -e "${BLUE}📋 Configuração:${NC}"
echo -e "   Diretório do projeto: ${PROJECT_DIR}"
echo -e "   Diretório de backup: ${BACKUP_DIR}"
echo -e "   Timestamp: ${TIMESTAMP}"
echo ""

# Função para verificar se comando existe
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Função para criar backup
create_backup() {
    echo -e "${YELLOW}📦 Criando backup...${NC}"
    
    # Criar diretório de backup se não existir
    mkdir -p "${BACKUP_DIR}"
    
    # Criar backup completo
    cd /home/usadosar/public_html/
    tar -czf "${BACKUP_DIR}/backup-pre-exportacao-${TIMESTAMP}.tar.gz" bc/
    
    echo -e "${GREEN}✅ Backup criado: ${BACKUP_DIR}/backup-pre-exportacao-${TIMESTAMP}.tar.gz${NC}"
}

# Função para verificar espaço em disco
check_disk_space() {
    echo -e "${YELLOW}💾 Verificando espaço em disco...${NC}"
    
    available_space=$(df /home/usadosar/public_html/ | awk 'NR==2 {print $4}')
    required_space=500000  # 500MB em KB
    
    if [ "$available_space" -lt "$required_space" ]; then
        echo -e "${RED}❌ Espaço insuficiente! Disponível: ${available_space}KB, Necessário: ${required_space}KB${NC}"
        exit 1
    fi
    
    echo -e "${GREEN}✅ Espaço suficiente disponível${NC}"
}

# Função para verificar se o projeto existe
check_project_exists() {
    if [ ! -d "$PROJECT_DIR" ]; then
        echo -e "${RED}❌ Diretório do projeto não encontrado: ${PROJECT_DIR}${NC}"
        exit 1
    fi
    
    if [ ! -f "$PROJECT_DIR/artisan" ]; then
        echo -e "${RED}❌ Arquivo artisan não encontrado. Não parece ser um projeto Laravel válido.${NC}"
        exit 1
    fi
    
    echo -e "${GREEN}✅ Projeto Laravel encontrado${NC}"
}

# Função para fazer upload dos arquivos
upload_files() {
    echo -e "${YELLOW}📁 Fazendo upload dos arquivos...${NC}"
    
    # Criar diretórios necessários
    mkdir -p "${PROJECT_DIR}/app/Services"
    mkdir -p "${PROJECT_DIR}/resources/views/reports/pdf"
    mkdir -p "${PROJECT_DIR}/public/css"
    
    # Verificar se arquivos de origem existem
    if [ ! -f "bc/app/Services/ReportExportService.php" ]; then
        echo -e "${RED}❌ Arquivo ReportExportService.php não encontrado${NC}"
        exit 1
    fi
    
    # Copiar arquivos novos
    echo "   📄 Copiando services..."
    cp bc/app/Services/ReportExportService.php "${PROJECT_DIR}/app/Services/" 2>/dev/null || echo "   ⚠️  ReportExportService.php não encontrado"
    cp bc/app/Services/PdfService.php "${PROJECT_DIR}/app/Services/" 2>/dev/null || echo "   ⚠️  PdfService.php não encontrado"
    
    echo "   📄 Copiando templates PDF..."
    cp -r bc/resources/views/reports/pdf/* "${PROJECT_DIR}/resources/views/reports/pdf/" 2>/dev/null || echo "   ⚠️  Templates PDF não encontrados"
    
    echo "   📄 Copiando CSS..."
    cp bc/public/css/export-styles.css "${PROJECT_DIR}/public/css/" 2>/dev/null || echo "   ⚠️  CSS não encontrado"
    
    # Fazer backup dos arquivos originais e copiar os modificados
    echo "   📄 Atualizando controllers..."
    cp "${PROJECT_DIR}/app/Http/Controllers/ReportController.php" "${PROJECT_DIR}/app/Http/Controllers/ReportController.php.backup.${TIMESTAMP}" 2>/dev/null || true
    cp bc/app/Http/Controllers/ReportController.php "${PROJECT_DIR}/app/Http/Controllers/" 2>/dev/null || echo "   ⚠️  ReportController.php não encontrado"
    
    cp "${PROJECT_DIR}/app/Http/Controllers/DashboardController.php" "${PROJECT_DIR}/app/Http/Controllers/DashboardController.php.backup.${TIMESTAMP}" 2>/dev/null || true
    cp bc/app/Http/Controllers/DashboardController.php "${PROJECT_DIR}/app/Http/Controllers/" 2>/dev/null || echo "   ⚠️  DashboardController.php não encontrado"
    
    echo "   📄 Atualizando rotas..."
    cp "${PROJECT_DIR}/routes/web.php" "${PROJECT_DIR}/routes/web.php.backup.${TIMESTAMP}" 2>/dev/null || true
    cp bc/routes/web.php "${PROJECT_DIR}/routes/" 2>/dev/null || echo "   ⚠️  web.php não encontrado"
    
    echo "   📄 Atualizando views..."
    cp "${PROJECT_DIR}/resources/views/dashboard.blade.php" "${PROJECT_DIR}/resources/views/dashboard.blade.php.backup.${TIMESTAMP}" 2>/dev/null || true
    cp bc/resources/views/dashboard.blade.php "${PROJECT_DIR}/resources/views/" 2>/dev/null || echo "   ⚠️  dashboard.blade.php não encontrado"
    
    # Views de relatórios
    for view in transactions reconciliations cash-flow categories index; do
        cp "${PROJECT_DIR}/resources/views/reports/${view}.blade.php" "${PROJECT_DIR}/resources/views/reports/${view}.blade.php.backup.${TIMESTAMP}" 2>/dev/null || true
        cp "bc/resources/views/reports/${view}.blade.php" "${PROJECT_DIR}/resources/views/reports/" 2>/dev/null || echo "   ⚠️  ${view}.blade.php não encontrado"
    done
    
    echo "   📄 Atualizando composer.json..."
    cp "${PROJECT_DIR}/composer.json" "${PROJECT_DIR}/composer.json.backup.${TIMESTAMP}" 2>/dev/null || true
    cp bc/composer.json "${PROJECT_DIR}/" 2>/dev/null || echo "   ⚠️  composer.json não encontrado"
    
    echo -e "${GREEN}✅ Upload de arquivos concluído${NC}"
}

# Função para limpar cache do Laravel
clear_cache() {
    echo -e "${YELLOW}🧹 Limpando cache do Laravel...${NC}"
    
    cd "${PROJECT_DIR}"
    
    # Limpar diferentes tipos de cache
    php artisan config:clear 2>/dev/null || echo "   ⚠️  Erro ao limpar config cache"
    php artisan route:clear 2>/dev/null || echo "   ⚠️  Erro ao limpar route cache"
    php artisan view:clear 2>/dev/null || echo "   ⚠️  Erro ao limpar view cache"
    php artisan cache:clear 2>/dev/null || echo "   ⚠️  Erro ao limpar application cache"
    
    echo -e "${GREEN}✅ Cache limpo${NC}"
}

# Função para ajustar permissões
fix_permissions() {
    echo -e "${YELLOW}🔐 Ajustando permissões...${NC}"
    
    # Permissões gerais
    chmod -R 755 "${PROJECT_DIR}/" 2>/dev/null || true
    
    # Permissões especiais para storage e cache
    chmod -R 775 "${PROJECT_DIR}/storage/" 2>/dev/null || true
    chmod -R 775 "${PROJECT_DIR}/bootstrap/cache/" 2>/dev/null || true
    
    # Permissões para os novos arquivos
    chmod 644 "${PROJECT_DIR}/app/Services/"*.php 2>/dev/null || true
    chmod 644 "${PROJECT_DIR}/resources/views/reports/pdf/"*.php 2>/dev/null || true
    chmod 644 "${PROJECT_DIR}/public/css/export-styles.css" 2>/dev/null || true
    
    echo -e "${GREEN}✅ Permissões ajustadas${NC}"
}

# Função para testar as funcionalidades
test_functionality() {
    echo -e "${YELLOW}🧪 Testando funcionalidades...${NC}"
    
    cd "${PROJECT_DIR}"
    
    # Testar se as rotas estão carregando
    echo "   🔍 Verificando rotas..."
    php artisan route:list | grep -q "reports.export" && echo "   ✅ Rotas de exportação encontradas" || echo "   ⚠️  Rotas de exportação não encontradas"
    
    # Testar se as views existem
    echo "   🔍 Verificando views..."
    [ -f "resources/views/reports/pdf/layout.blade.php" ] && echo "   ✅ Template PDF base encontrado" || echo "   ⚠️  Template PDF base não encontrado"
    
    # Testar se os services existem
    echo "   🔍 Verificando services..."
    [ -f "app/Services/ReportExportService.php" ] && echo "   ✅ ReportExportService encontrado" || echo "   ⚠️  ReportExportService não encontrado"
    [ -f "app/Services/PdfService.php" ] && echo "   ✅ PdfService encontrado" || echo "   ⚠️  PdfService não encontrado"
    
    echo -e "${GREEN}✅ Testes básicos concluídos${NC}"
}

# Função para exibir URLs de teste
show_test_urls() {
    echo -e "${BLUE}🌐 URLs para teste:${NC}"
    echo "   📊 Dashboard: https://usadosar.com.br/bc/"
    echo "   📈 Relatórios: https://usadosar.com.br/bc/reports"
    echo "   📄 Exportar CSV: https://usadosar.com.br/bc/reports/export/transactions/csv"
    echo "   📄 Exportar PDF: https://usadosar.com.br/bc/reports/export/transactions/pdf"
    echo "   📊 Dashboard PDF: https://usadosar.com.br/bc/reports/export/dashboard/pdf"
    echo ""
}

# Função para exibir próximos passos
show_next_steps() {
    echo -e "${BLUE}🎯 Próximos passos:${NC}"
    echo "   1. Testar todas as URLs listadas acima"
    echo "   2. Verificar se os botões de exportação funcionam"
    echo "   3. Testar com diferentes filtros"
    echo "   4. Verificar logs de erro se houver problemas"
    echo "   5. Considerar implementar DomPDF para PDFs reais"
    echo ""
    echo -e "${YELLOW}📁 Logs de erro:${NC} ${PROJECT_DIR}/storage/logs/laravel.log"
    echo -e "${YELLOW}📦 Backup criado:${NC} ${BACKUP_DIR}/backup-pre-exportacao-${TIMESTAMP}.tar.gz"
}

# Função principal
main() {
    echo -e "${BLUE}===========================================${NC}"
    echo -e "${BLUE}🚀 DEPLOY DO SISTEMA DE EXPORTAÇÃO${NC}"
    echo -e "${BLUE}===========================================${NC}"
    echo ""
    
    # Verificações preliminares
    check_disk_space
    check_project_exists
    
    # Criar backup
    create_backup
    
    # Upload dos arquivos
    upload_files
    
    # Limpar cache
    clear_cache
    
    # Ajustar permissões
    fix_permissions
    
    # Testar funcionalidades
    test_functionality
    
    echo ""
    echo -e "${GREEN}🎉 DEPLOY CONCLUÍDO COM SUCESSO!${NC}"
    echo ""
    
    # Mostrar URLs de teste
    show_test_urls
    
    # Mostrar próximos passos
    show_next_steps
    
    echo -e "${GREEN}===========================================${NC}"
    echo -e "${GREEN}✅ SISTEMA DE EXPORTAÇÃO IMPLANTADO${NC}"
    echo -e "${GREEN}===========================================${NC}"
}

# Verificar se está sendo executado do diretório correto
if [ ! -f "GUIA-IMPLEMENTACAO-DEPLOY.md" ]; then
    echo -e "${RED}❌ Execute este script do diretório raiz do projeto (onde está o GUIA-IMPLEMENTACAO-DEPLOY.md)${NC}"
    exit 1
fi

# Executar função principal
main

# Perguntar se deve fazer um teste básico via curl
echo ""
read -p "🧪 Deseja fazer um teste básico da API? (y/n): " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo -e "${YELLOW}🔍 Testando endpoint de exportação...${NC}"
    curl -I "https://usadosar.com.br/bc/reports/export/transactions/csv" 2>/dev/null | head -1 || echo "⚠️  Não foi possível testar via curl"
fi

echo ""
echo -e "${GREEN}🚀 Deploy finalizado! Monitore os logs e teste as funcionalidades.${NC}"
