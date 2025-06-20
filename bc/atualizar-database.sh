#!/bin/bash

echo "========================================================"
echo "  COMANDOS PARA ATUALIZAR DATABASE - SISTEMA BC"
echo "========================================================"
echo ""

# Definir cores
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

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

# Verificar se estamos no Laravel
if [ ! -f "artisan" ]; then
    log_error "Execute este script no diretório raiz do Laravel"
    exit 1
fi

echo "🗄️ COMANDOS BÁSICOS PARA DATABASE:"
echo ""

echo "1️⃣ EXECUTAR MIGRATIONS (aplicar mudanças no banco):"
echo "   php artisan migrate"
echo ""

echo "2️⃣ VERIFICAR STATUS DAS MIGRATIONS:"
echo "   php artisan migrate:status"
echo ""

echo "3️⃣ ROLLBACK (desfazer última migration):"
echo "   php artisan migrate:rollback"
echo ""

echo "4️⃣ RESET COMPLETO (cuidado - apaga tudo):"
echo "   php artisan migrate:reset"
echo ""

echo "5️⃣ REFRESH (reset + migrate):"
echo "   php artisan migrate:refresh"
echo ""

echo "6️⃣ SEED (popular com dados de teste):"
echo "   php artisan db:seed"
echo ""

echo "7️⃣ MIGRATE + SEED (completo):"
echo "   php artisan migrate --seed"
echo ""

echo "========================================================"
echo "  EXECUTAR ATUALIZAÇÕES AGORA?"
echo "========================================================"
echo ""

read -p "Deseja executar as atualizações agora? (s/n): " resposta

if [[ $resposta =~ ^[Ss]$ ]]; then
    echo ""
    log_info "Iniciando atualização da database..."
    
    echo ""
    log_info "1. Verificando status atual..."
    php artisan migrate:status
    
    echo ""
    log_info "2. Executando migrations pendentes..."
    php artisan migrate --force
    
    if [ $? -eq 0 ]; then
        log_success "✅ Migrations executadas com sucesso!"
    else
        log_error "❌ Erro ao executar migrations!"
        exit 1
    fi
    
    echo ""
    log_info "3. Verificando integridade das tabelas..."
    
    # Testar modelos principais
    php artisan tinker --execute="
    try {
        echo 'Verificando tabelas:' . PHP_EOL;
        echo '- users: ' . \App\Models\User::count() . ' registros' . PHP_EOL;
        echo '- bank_accounts: ' . \App\Models\BankAccount::count() . ' registros' . PHP_EOL;
        echo '- transactions: ' . \App\Models\Transaction::count() . ' registros' . PHP_EOL;
        echo '- statement_imports: ' . \App\Models\StatementImport::count() . ' registros' . PHP_EOL;
        echo '- reconciliations: ' . \App\Models\Reconciliation::count() . ' registros' . PHP_EOL;
        echo '✅ Todas as tabelas funcionando!' . PHP_EOL;
    } catch(Exception \$e) {
        echo '❌ ERRO: ' . \$e->getMessage() . PHP_EOL;
        exit(1);
    }
    "
    
    if [ $? -eq 0 ]; then
        log_success "✅ Database verificada e funcionando!"
    else
        log_error "❌ Erro na verificação da database!"
        exit 1
    fi
    
    echo ""
    log_info "4. Limpando cache..."
    php artisan config:clear
    php artisan cache:clear
    
    echo ""
    echo "========================================================"
    log_success "🎉 DATABASE ATUALIZADA COM SUCESSO!"
    echo "========================================================"
    echo ""
    
else
    echo ""
    log_info "Atualização cancelada pelo usuário."
    echo ""
    echo "Para executar manualmente:"
    echo "  php artisan migrate"
    echo ""
fi

echo "📋 COMANDOS ÚTEIS PARA PRODUÇÃO:"
echo ""
echo "🔄 Atualizar database em produção:"
echo "   php artisan migrate --force"
echo ""
echo "🧹 Limpar cache após mudanças:"
echo "   php artisan config:clear"
echo "   php artisan cache:clear"
echo "   php artisan route:clear"
echo "   php artisan view:clear"
echo ""
echo "🔍 Verificar status:"
echo "   php artisan migrate:status"
echo ""
echo "🎯 Para o seu servidor (se tiver SSH):"
echo "   ssh usuario@servidor.com"
echo "   cd /path/do/laravel"
echo "   php artisan migrate --force"
echo "   php artisan cache:clear"
echo ""
