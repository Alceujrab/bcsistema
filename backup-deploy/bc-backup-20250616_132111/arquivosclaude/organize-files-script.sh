#!/bin/bash

# Script para organizar arquivos do Sistema de Conciliação Bancária
# Este script move e renomeia todos os arquivos para suas localizações corretas

echo "=== ORGANIZADOR DE ARQUIVOS DO SISTEMA DE CONCILIAÇÃO ==="
echo ""

# Diretório base do projeto
PROJECT_DIR="/home/seu_usuario/public_html/bc"
# Diretório onde estão os arquivos do Claude
CLAUDE_DIR="$PROJECT_DIR/arquivosclaude"

# Criar diretório de backup
BACKUP_DIR="$PROJECT_DIR/backup_$(date +%Y%m%d_%H%M%S)"
echo "Criando backup em: $BACKUP_DIR"
mkdir -p "$BACKUP_DIR"

# Função para mover arquivo com backup
move_file() {
    local source_file="$1"
    local dest_file="$2"
    local dest_dir=$(dirname "$dest_file")
    
    # Criar diretório de destino se não existir
    mkdir -p "$dest_dir"
    
    # Fazer backup se o arquivo já existir
    if [ -f "$dest_file" ]; then
        local backup_path="$BACKUP_DIR/$(basename "$dest_file")"
        echo "  Backup: $(basename "$dest_file")"
        cp "$dest_file" "$backup_path"
    fi
    
    # Mover e renomear arquivo
    echo "  Movendo: $(basename "$source_file") -> $dest_file"
    cp "$source_file" "$dest_file"
}

echo ""
echo "Processando arquivos..."
echo ""

# MIGRATIONS
echo "📁 Migrations:"
[ -f "$CLAUDE_DIR/migration-bank-accounts.php" ] && move_file "$CLAUDE_DIR/migration-bank-accounts.php" "$PROJECT_DIR/database/migrations/2024_01_01_000001_create_bank_accounts_table.php"
[ -f "$CLAUDE_DIR/migration-transactions.php" ] && move_file "$CLAUDE_DIR/migration-transactions.php" "$PROJECT_DIR/database/migrations/2024_01_01_000002_create_transactions_table.php"
[ -f "$CLAUDE_DIR/migration-reconciliations.php" ] && move_file "$CLAUDE_DIR/migration-reconciliations.php" "$PROJECT_DIR/database/migrations/2024_01_01_000003_create_reconciliations_table.php"
[ -f "$CLAUDE_DIR/migration-imports.php" ] && move_file "$CLAUDE_DIR/migration-imports.php" "$PROJECT_DIR/database/migrations/2024_01_01_000004_create_statement_imports_table.php"

# MODELS
echo ""
echo "📁 Models:"
[ -f "$CLAUDE_DIR/model-bank-account.php" ] && move_file "$CLAUDE_DIR/model-bank-account.php" "$PROJECT_DIR/app/Models/BankAccount.php"
[ -f "$CLAUDE_DIR/model-transaction.php" ] && move_file "$CLAUDE_DIR/model-transaction.php" "$PROJECT_DIR/app/Models/Transaction.php"
[ -f "$CLAUDE_DIR/model-reconciliation.php" ] && move_file "$CLAUDE_DIR/model-reconciliation.php" "$PROJECT_DIR/app/Models/Reconciliation.php"
[ -f "$CLAUDE_DIR/model-statement-import.php" ] && move_file "$CLAUDE_DIR/model-statement-import.php" "$PROJECT_DIR/app/Models/StatementImport.php"
[ -f "$CLAUDE_DIR/model-user-updated.php" ] && move_file "$CLAUDE_DIR/model-user-updated.php" "$PROJECT_DIR/app/Models/User.php"

# CONTROLLERS
echo ""
echo "📁 Controllers:"
[ -f "$CLAUDE_DIR/controller-dashboard.php" ] && move_file "$CLAUDE_DIR/controller-dashboard.php" "$PROJECT_DIR/app/Http/Controllers/DashboardController.php"
[ -f "$CLAUDE_DIR/controller-bank-account.php" ] && move_file "$CLAUDE_DIR/controller-bank-account.php" "$PROJECT_DIR/app/Http/Controllers/BankAccountController.php"
[ -f "$CLAUDE_DIR/controller-transaction.php" ] && move_file "$CLAUDE_DIR/controller-transaction.php" "$PROJECT_DIR/app/Http/Controllers/TransactionController.php"
[ -f "$CLAUDE_DIR/controller-reconciliation.php" ] && move_file "$CLAUDE_DIR/controller-reconciliation.php" "$PROJECT_DIR/app/Http/Controllers/ReconciliationController.php"
[ -f "$CLAUDE_DIR/controller-import.php" ] && move_file "$CLAUDE_DIR/controller-import.php" "$PROJECT_DIR/app/Http/Controllers/ImportController.php"
[ -f "$CLAUDE_DIR/controller-report.php" ] && move_file "$CLAUDE_DIR/controller-report.php" "$PROJECT_DIR/app/Http/Controllers/ReportController.php"
[ -f "$CLAUDE_DIR/controller-base-modified.php" ] && move_file "$CLAUDE_DIR/controller-base-modified.php" "$PROJECT_DIR/app/Http/Controllers/Controller.php"

# SERVICES
echo ""
echo "📁 Services:"
[ -f "$CLAUDE_DIR/service-statement-import.php" ] && move_file "$CLAUDE_DIR/service-statement-import.php" "$PROJECT_DIR/app/Services/StatementImportService.php"

# JOBS
echo ""
echo "📁 Jobs:"
[ -f "$CLAUDE_DIR/job-process-reconciliation.php" ] && move_file "$CLAUDE_DIR/job-process-reconciliation.php" "$PROJECT_DIR/app/Jobs/ProcessReconciliation.php"

# COMMANDS
echo ""
echo "📁 Commands:"
[ -f "$CLAUDE_DIR/command-import-bank-statements.php" ] && move_file "$CLAUDE_DIR/command-import-bank-statements.php" "$PROJECT_DIR/app/Console/Commands/ImportBankStatements.php"

# KERNEL E CONSOLE
echo ""
echo "📁 Console:"
[ -f "$CLAUDE_DIR/console-kernel.php" ] && move_file "$CLAUDE_DIR/console-kernel.php" "$PROJECT_DIR/app/Console/Kernel.php"
[ -f "$CLAUDE_DIR/kernel-complete.php" ] && move_file "$CLAUDE_DIR/kernel-complete.php" "$PROJECT_DIR/app/Console/Kernel.php"
[ -f "$CLAUDE_DIR/routes-console.php" ] && move_file "$CLAUDE_DIR/routes-console.php" "$PROJECT_DIR/routes/console.php"

# SEEDERS
echo ""
echo "📁 Seeders:"
[ -f "$CLAUDE_DIR/database-seeder.php" ] && move_file "$CLAUDE_DIR/database-seeder.php" "$PROJECT_DIR/database/seeders/DatabaseSeeder.php"

# ROTAS
echo ""
echo "📁 Rotas:"
[ -f "$CLAUDE_DIR/web-php-modified.php" ] && move_file "$CLAUDE_DIR/web-php-modified.php" "$PROJECT_DIR/routes/web.php"

# CONFIGURAÇÕES
echo ""
echo "📁 Configurações:"
[ -f "$CLAUDE_DIR/env-modified" ] && move_file "$CLAUDE_DIR/env-modified" "$PROJECT_DIR/.env"

# VIEWS
echo ""
echo "📁 Views:"
# Layout
[ -f "$CLAUDE_DIR/view-layout-app.blade.php" ] && move_file "$CLAUDE_DIR/view-layout-app.blade.php" "$PROJECT_DIR/resources/views/layouts/app.blade.php"

# Dashboard
[ -f "$CLAUDE_DIR/view-dashboard.blade.php" ] && move_file "$CLAUDE_DIR/view-dashboard.blade.php" "$PROJECT_DIR/resources/views/dashboard.blade.php"

# Bank Accounts
[ -f "$CLAUDE_DIR/view-bank-accounts-index.blade.php" ] && move_file "$CLAUDE_DIR/view-bank-accounts-index.blade.php" "$PROJECT_DIR/resources/views/bank-accounts/index.blade.php"

# Imports
[ -f "$CLAUDE_DIR/view-imports-create.blade.php" ] && move_file "$CLAUDE_DIR/view-imports-create.blade.php" "$PROJECT_DIR/resources/views/imports/create.blade.php"

echo ""
echo "=== ORGANIZAÇÃO CONCLUÍDA ==="
echo ""
echo "📁 Backup salvo em: $BACKUP_DIR"
echo ""
echo "Próximos passos:"
echo "1. Execute: php artisan migrate:fresh --seed"
echo "2. Execute: php artisan optimize"
echo "3. Ajuste as permissões: chmod -R 755 storage bootstrap/cache"
echo "4. Acesse o sistema em: https://usadosar.com.br/bc"
echo ""
echo "Login padrão:"
echo "Email: admin@usadosar.com.br"
echo "Senha: senha123"
echo ""