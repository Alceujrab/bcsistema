#!/bin/bash

# CONFIGURE AQUI O SEU USUÁRIO DO CPANEL
CPANEL_USER="seu_usuario"

# Diretórios
PROJECT_DIR="/home/$CPANEL_USER/public_html/bc"
CLAUDE_DIR="$PROJECT_DIR/arquivosclaude"

echo "=== ORGANIZADOR SIMPLIFICADO ==="
echo ""

# Criar backup
BACKUP_DIR="$PROJECT_DIR/backup_$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"

# Função para copiar arquivo
copy_file() {
    if [ -f "$1" ]; then
        # Criar diretório se não existir
        mkdir -p "$(dirname "$2")"
        
        # Backup se existir
        [ -f "$2" ] && cp "$2" "$BACKUP_DIR/$(basename "$2")"
        
        # Copiar arquivo
        cp "$1" "$2"
        echo "✓ $(basename "$1") -> $2"
    fi
}

# Mover todos os arquivos PHP do Claude
cd "$CLAUDE_DIR"

# Para cada arquivo .php no diretório
for file in *.php; do
    if [ -f "$file" ]; then
        case "$file" in
            # Models
            *bank-account.php) copy_file "$file" "$PROJECT_DIR/app/Models/BankAccount.php" ;;
            *transaction.php) copy_file "$file" "$PROJECT_DIR/app/Models/Transaction.php" ;;
            *reconciliation.php) copy_file "$file" "$PROJECT_DIR/app/Models/Reconciliation.php" ;;
            *statement-import.php) copy_file "$file" "$PROJECT_DIR/app/Models/StatementImport.php" ;;
            *user*.php) copy_file "$file" "$PROJECT_DIR/app/Models/User.php" ;;
            
            # Controllers
            *dashboard*.php) copy_file "$file" "$PROJECT_DIR/app/Http/Controllers/DashboardController.php" ;;
            *bank-account*.php) copy_file "$file" "$PROJECT_DIR/app/Http/Controllers/BankAccountController.php" ;;
            *transaction*.php) copy_file "$file" "$PROJECT_DIR/app/Http/Controllers/TransactionController.php" ;;
            *reconciliation*.php) copy_file "$file" "$PROJECT_DIR/app/Http/Controllers/ReconciliationController.php" ;;
            *import*.php) copy_file "$file" "$PROJECT_DIR/app/Http/Controllers/ImportController.php" ;;
            *report*.php) copy_file "$file" "$PROJECT_DIR/app/Http/Controllers/ReportController.php" ;;
            
            # Outros
            *kernel*.php) copy_file "$file" "$PROJECT_DIR/app/Console/Kernel.php" ;;
            *seeder*.php) copy_file "$file" "$PROJECT_DIR/database/seeders/DatabaseSeeder.php" ;;
            *web*.php) copy_file "$file" "$PROJECT_DIR/routes/web.php" ;;
            *console*.php) copy_file "$file" "$PROJECT_DIR/routes/console.php" ;;
        esac
    fi
done

# Views blade
for file in *.blade.php; do
    if [ -f "$file" ]; then
        case "$file" in
            *app*.blade.php) copy_file "$file" "$PROJECT_DIR/resources/views/layouts/app.blade.php" ;;
            *dashboard*.blade.php) copy_file "$file" "$PROJECT_DIR/resources/views/dashboard.blade.php" ;;
            *bank*index*.blade.php) copy_file "$file" "$PROJECT_DIR/resources/views/bank-accounts/index.blade.php" ;;
            *import*create*.blade.php) copy_file "$file" "$PROJECT_DIR/resources/views/imports/create.blade.php" ;;
        esac
    fi
done

# Arquivo .env
[ -f "env-modified" ] && copy_file "env-modified" "$PROJECT_DIR/.env"

echo ""
echo "✅ Arquivos organizados!"
echo "📁 Backup em: $BACKUP_DIR"
echo ""
echo "Execute agora:"
echo "cd $PROJECT_DIR"
echo "php artisan migrate:fresh --seed"
echo "php artisan optimize"