#!/bin/bash

# Script personalizado para organizar os arquivos do Claude
# Baseado nos arquivos existentes em /public_html/bc/arquivosclaude

echo "=== ORGANIZADOR DE ARQUIVOS - SISTEMA DE CONCILIAÇÃO ==="
echo ""

# CONFIGURE SEU USUÁRIO AQUI
CPANEL_USER="usadoar"

# Diretórios
PROJECT_DIR="/home/$CPANEL_USER/public_html/bc"
CLAUDE_DIR="$PROJECT_DIR/arquivosclaude"

# Criar backup
BACKUP_DIR="$PROJECT_DIR/backup_$(date +%Y%m%d_%H%M%S)"
echo "Criando backup em: $BACKUP_DIR"
mkdir -p "$BACKUP_DIR"

# Função para copiar arquivo
copy_file() {
    local source="$CLAUDE_DIR/$1"
    local dest="$PROJECT_DIR/$2"
    
    if [ -f "$source" ]; then
        # Criar diretório de destino se não existir
        mkdir -p "$(dirname "$dest")"
        
        # Fazer backup se o arquivo de destino já existir
        if [ -f "$dest" ]; then
            cp "$dest" "$BACKUP_DIR/$(basename "$dest")_backup"
            echo "  ✓ Backup: $(basename "$dest")"
        fi
        
        # Copiar arquivo
        cp "$source" "$dest"
        echo "  ✓ Copiado: $1 -> $2"
    else
        echo "  ⚠️  Não encontrado: $1"
    fi
}

echo ""
echo "📁 Processando Models..."
copy_file "model-bank-account.php" "app/Models/BankAccount.php"
copy_file "model-transaction.php" "app/Models/Transaction.php"
copy_file "model-reconciliation.php" "app/Models/Reconciliation.php"
copy_file "model-statement-import.php" "app/Models/StatementImport.php"
copy_file "model-user-updated.php" "app/Models/User.php"

echo ""
echo "📁 Processando Controllers..."
copy_file "controller-dashboard.php" "app/Http/Controllers/DashboardController.php"
copy_file "controller-bank-account.php" "app/Http/Controllers/BankAccountController.php"
copy_file "controller-transaction.php" "app/Http/Controllers/TransactionController.php"
copy_file "controller-reconciliation.php" "app/Http/Controllers/ReconciliationController.php"
copy_file "controller-import.php" "app/Http/Controllers/ImportController.php"
copy_file "controller-report.php" "app/Http/Controllers/ReportController.php"

echo ""
echo "📁 Processando Jobs..."
copy_file "job-process-reconciliation.php" "app/Jobs/ProcessReconciliation.php"

echo ""
echo "📁 Processando Commands..."
copy_file "command-import-bank-statements.php" "app/Console/Commands/ImportBankStatements.php"

echo ""
echo "📁 Processando Console..."
copy_file "kernel-complete.php" "app/Console/Kernel.php"

echo ""
echo "📁 Processando Seeders..."
copy_file "database-seeder.php" "database/seeders/DatabaseSeeder.php"

echo ""
echo "📁 Processando Views (convertendo .txt para .blade.php)..."
# Views com extensão .txt
if [ -f "$CLAUDE_DIR/view-layout-app.txt" ]; then
    mkdir -p "$PROJECT_DIR/resources/views/layouts"
    cp "$CLAUDE_DIR/view-layout-app.txt" "$PROJECT_DIR/resources/views/layouts/app.blade.php"
    echo "  ✓ view-layout-app.txt -> resources/views/layouts/app.blade.php"
fi

if [ -f "$CLAUDE_DIR/view-dashboard.txt" ]; then
    cp "$CLAUDE_DIR/view-dashboard.txt" "$PROJECT_DIR/resources/views/dashboard.blade.php"
    echo "  ✓ view-dashboard.txt -> resources/views/dashboard.blade.php"
fi

if [ -f "$CLAUDE_DIR/view-bank-accounts-index.txt" ]; then
    mkdir -p "$PROJECT_DIR/resources/views/bank-accounts"
    cp "$CLAUDE_DIR/view-bank-accounts-index.txt" "$PROJECT_DIR/resources/views/bank-accounts/index.blade.php"
    echo "  ✓ view-bank-accounts-index.txt -> resources/views/bank-accounts/index.blade.php"
fi

if [ -f "$CLAUDE_DIR/view-imports-create.txt" ]; then
    mkdir -p "$PROJECT_DIR/resources/views/imports"
    cp "$CLAUDE_DIR/view-imports-create.txt" "$PROJECT_DIR/resources/views/imports/create.blade.php"
    echo "  ✓ view-imports-create.txt -> resources/views/imports/create.blade.php"
fi

echo ""
echo "=== ORGANIZAÇÃO CONCLUÍDA! ==="
echo ""
echo "📁 Backup salvo em: $BACKUP_DIR"
echo ""
echo "✅ Arquivos que ainda precisam ser criados manualmente:"
echo "   1. app/Services/StatementImportService.php (copie o código fornecido)"
echo "   2. routes/web.php (copie o código fornecido)"
echo "   3. routes/console.php (copie o código fornecido)"
echo "   4. .env (ajuste as configurações fornecidas)"
echo "   5. Migrations (crie os 4 arquivos de migration)"
echo ""
echo "📝 Próximos comandos a executar:"
echo ""
echo "cd $PROJECT_DIR"
echo "php artisan migrate:fresh --seed"
echo "php artisan optimize"
echo "chmod -R 755 storage bootstrap/cache"
echo ""
echo "🔑 Login padrão após o seed:"
echo "   Email: admin@usadosar.com.br"
echo "   Senha: senha123"
echo ""