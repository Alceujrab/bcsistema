#!/bin/bash

# Script de Verificação Pós-Deploy
# Execute com: bash verificar-sistema.sh

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log() {
    echo -e "${GREEN}[OK] $1${NC}"
}

warn() {
    echo -e "${YELLOW}[AVISO] $1${NC}"
}

error() {
    echo -e "${RED}[ERRO] $1${NC}"
}

info() {
    echo -e "${BLUE}[INFO] $1${NC}"
}

# Contadores
OK_COUNT=0
ERROR_COUNT=0
WARN_COUNT=0

check_ok() {
    log "$1"
    ((OK_COUNT++))
}

check_error() {
    error "$1"
    ((ERROR_COUNT++))
}

check_warn() {
    warn "$1"
    ((WARN_COUNT++))
}

echo "======================================"
echo "   VERIFICAÇÃO PÓS-DEPLOY DO SISTEMA  "
echo "======================================"
echo ""

# 1. Verificar se estamos no diretório correto
info "🔍 Verificando estrutura do projeto..."
if [ -f "artisan" ]; then
    check_ok "Diretório Laravel detectado"
else
    check_error "Arquivo artisan não encontrado - execute no diretório raiz do Laravel"
    exit 1
fi

# 2. Verificar arquivos críticos
info "📁 Verificando arquivos críticos..."

# Controllers
if [ -f "app/Http/Controllers/TransactionController.php" ]; then
    if grep -q "quickUpdate" app/Http/Controllers/TransactionController.php; then
        check_ok "TransactionController modernizado"
    else
        check_warn "TransactionController pode não estar atualizado"
    fi
else
    check_error "TransactionController não encontrado"
fi

if [ -f "app/Http/Controllers/DashboardController.php" ]; then
    check_ok "DashboardController encontrado"
else
    check_error "DashboardController não encontrado"
fi

# Views
if [ -f "resources/views/transactions/index.blade.php" ]; then
    if grep -q "bulk-actions" resources/views/transactions/index.blade.php; then
        check_ok "View de transações modernizada"
    else
        check_warn "View de transações pode não estar atualizada"
    fi
else
    check_error "View de transações não encontrada"
fi

if [ -f "resources/views/transactions/partials/table.blade.php" ]; then
    check_ok "View parcial da tabela encontrada"
else
    check_warn "View parcial da tabela não encontrada"
fi

# JavaScript
if [ -f "public/js/transactions.js" ] || [ -f "resources/js/transactions.js" ]; then
    check_ok "JavaScript de transações encontrado"
else
    check_warn "JavaScript de transações não encontrado"
fi

# 3. Verificar banco de dados
info "🗄️ Verificando conexão com banco de dados..."
if php artisan tinker --execute="DB::connection()->getPdo(); echo 'DB OK';" 2>/dev/null | grep -q "DB OK"; then
    check_ok "Conexão com banco de dados"
else
    check_error "Problema na conexão com banco de dados"
fi

# 4. Verificar migrations
info "📊 Verificando migrations..."
PENDING_MIGRATIONS=$(php artisan migrate:status 2>/dev/null | grep -c "Pending" || echo "0")
if [ "$PENDING_MIGRATIONS" -eq "0" ]; then
    check_ok "Todas as migrations executadas"
else
    check_warn "$PENDING_MIGRATIONS migration(s) pendente(s)"
fi

# 5. Verificar rotas
info "🛣️ Verificando rotas críticas..."
if php artisan route:list | grep -q "transactions.index"; then
    check_ok "Rota de listagem de transações"
else
    check_error "Rota de listagem de transações não encontrada"
fi

if php artisan route:list | grep -q "transactions.quick-update"; then
    check_ok "Rota de edição rápida"
else
    check_warn "Rota de edição rápida não encontrada"
fi

if php artisan route:list | grep -q "transactions.bulk-delete"; then
    check_ok "Rota de exclusão em lote"
else
    check_warn "Rota de exclusão em lote não encontrada"
fi

# 6. Verificar permissões
info "🔐 Verificando permissões..."
if [ -w "storage" ] && [ -w "bootstrap/cache" ]; then
    check_ok "Permissões de escrita nos diretórios críticos"
else
    check_error "Problemas de permissão em storage ou bootstrap/cache"
fi

# 7. Verificar cache
info "💾 Verificando cache..."
if [ -f "bootstrap/cache/config.php" ]; then
    check_ok "Cache de configuração ativo"
else
    check_warn "Cache de configuração não encontrado"
fi

if [ -f "bootstrap/cache/routes-v7.php" ]; then
    check_ok "Cache de rotas ativo"
else
    check_warn "Cache de rotas não encontrado"
fi

# 8. Verificar logs
info "📋 Verificando logs recentes..."
if [ -f "storage/logs/laravel.log" ]; then
    RECENT_ERRORS=$(tail -n 100 storage/logs/laravel.log | grep -c "ERROR" || echo "0")
    if [ "$RECENT_ERRORS" -eq "0" ]; then
        check_ok "Nenhum erro recente nos logs"
    else
        check_warn "$RECENT_ERRORS erro(s) recente(s) encontrado(s) nos logs"
    fi
else
    check_warn "Arquivo de log não encontrado"
fi

# 9. Verificar modelos
info "🏗️ Verificando modelos..."
if [ -f "app/Models/Transaction.php" ]; then
    check_ok "Modelo Transaction encontrado"
else
    check_error "Modelo Transaction não encontrado"
fi

if [ -f "app/Models/BankAccount.php" ]; then
    check_ok "Modelo BankAccount encontrado"
else
    check_error "Modelo BankAccount não encontrado"
fi

if [ -f "app/Models/Category.php" ]; then
    check_ok "Modelo Category encontrado"
else
    check_error "Modelo Category não encontrado"
fi

# 10. Teste de conectividade HTTP (se curl estiver disponível)
if command -v curl &> /dev/null; then
    info "🌐 Testando conectividade HTTP..."
    
    # Tentar diferentes URLs
    for url in "http://localhost" "http://localhost/dashboard" "http://127.0.0.1"; do
        HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$url" 2>/dev/null || echo "000")
        if [ "$HTTP_CODE" = "200" ]; then
            check_ok "Conectividade HTTP: $url ($HTTP_CODE)"
            break
        elif [ "$HTTP_CODE" != "000" ]; then
            check_warn "HTTP $HTTP_CODE para $url"
        fi
    done
else
    check_warn "curl não disponível para teste HTTP"
fi

# 11. Verificar estrutura de tabelas críticas
info "🗂️ Verificando estrutura de tabelas..."
TABLES_CHECK=$(php artisan tinker --execute="
try {
    \$tables = ['transactions', 'bank_accounts', 'categories'];
    foreach(\$tables as \$table) {
        if (Schema::hasTable(\$table)) {
            echo \$table . ' OK ';
        } else {
            echo \$table . ' MISSING ';
        }
    }
} catch (Exception \$e) {
    echo 'ERROR: ' . \$e->getMessage();
}
" 2>/dev/null || echo "ERROR")

if echo "$TABLES_CHECK" | grep -q "transactions OK"; then
    check_ok "Tabela transactions existe"
else
    check_error "Tabela transactions não encontrada"
fi

if echo "$TABLES_CHECK" | grep -q "bank_accounts OK"; then
    check_ok "Tabela bank_accounts existe"
else
    check_error "Tabela bank_accounts não encontrada"
fi

if echo "$TABLES_CHECK" | grep -q "categories OK"; then
    check_ok "Tabela categories existe"
else
    check_error "Tabela categories não encontrada"
fi

# 12. Resumo final
echo ""
echo "======================================"
echo "         RESUMO DA VERIFICAÇÃO        "
echo "======================================"
echo ""
echo "📊 Estatísticas:"
echo "   ✅ Verificações OK: $OK_COUNT"
echo "   ⚠️  Avisos: $WARN_COUNT"
echo "   ❌ Erros: $ERROR_COUNT"
echo ""

if [ $ERROR_COUNT -eq 0 ]; then
    if [ $WARN_COUNT -eq 0 ]; then
        echo "🎉 SISTEMA TOTALMENTE FUNCIONAL!"
        echo "   Todas as verificações passaram com sucesso."
    else
        echo "✅ SISTEMA FUNCIONAL COM RESSALVAS"
        echo "   Existem alguns avisos que podem ser verificados."
    fi
else
    echo "❌ SISTEMA COM PROBLEMAS"
    echo "   Existem erros que precisam ser corrigidos."
fi

echo ""
echo "🔍 Recomendações:"
if [ $ERROR_COUNT -gt 0 ]; then
    echo "   1. Corrija os erros listados acima"
    echo "   2. Execute 'php artisan migrate' se houver migrations pendentes"
    echo "   3. Verifique as permissões: chmod -R 775 storage bootstrap/cache"
    echo "   4. Limpe os caches: php artisan cache:clear"
fi

if [ $WARN_COUNT -gt 0 ]; then
    echo "   • Revise os avisos para otimizar o sistema"
    echo "   • Monitore os logs: tail -f storage/logs/laravel.log"
fi

echo "   • Teste as funcionalidades manualmente"
echo "   • Monitore o sistema por alguns minutos após o deploy"
echo ""
echo "📚 Documentação: guia-implementacao-servidor.md"
echo "======================================"

# Código de saída baseado nos resultados
if [ $ERROR_COUNT -gt 0 ]; then
    exit 1
elif [ $WARN_COUNT -gt 0 ]; then
    exit 2
else
    exit 0
fi
