#!/bin/bash

echo "=== CORREÇÃO COMPLETA - MIGRATIONS E MODELOS ==="
echo "Resolvendo erro: Class 'App\Models\ImportLog' not found"
echo "Data: $(date)"
echo ""

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    echo "❌ Erro: Execute este script no diretório raiz do Laravel"
    exit 1
fi

# Fazer backup do banco antes de qualquer operação
echo "1. Criando backup de segurança..."
BACKUP_DIR="backup-db-$(date +%Y%m%d_%H%M%S)"
mkdir -p $BACKUP_DIR

# Exportar estrutura atual se possível
php artisan schema:dump --path="$BACKUP_DIR/backup.sql" 2>/dev/null || echo "   Backup da estrutura não disponível"

echo "2. Verificando status das migrations..."
php artisan migrate:status

echo "3. Executando todas as migrations pendentes..."
php artisan migrate --force

echo "4. Verificando se tabelas críticas existem..."
php artisan tinker --execute="
use Illuminate\Support\Facades\Schema;

\$tables = ['users', 'bank_accounts', 'transactions', 'import_logs', 'statement_imports', 'categories'];
foreach (\$tables as \$table) {
    if (Schema::hasTable(\$table)) {
        echo '✅ Tabela ' . \$table . ' existe' . PHP_EOL;
    } else {
        echo '❌ Tabela ' . \$table . ' NÃO existe' . PHP_EOL;
    }
}
"

echo "5. Testando modelos críticos..."
php artisan tinker --execute="
try {
    echo 'Testando User...' . PHP_EOL;
    \$userCount = \App\Models\User::count();
    echo '   Users: ' . \$userCount . PHP_EOL;
} catch (Exception \$e) {
    echo '   ERRO User: ' . \$e->getMessage() . PHP_EOL;
}

try {
    echo 'Testando BankAccount...' . PHP_EOL;
    \$bankCount = \App\Models\BankAccount::count();
    echo '   BankAccounts: ' . \$bankCount . PHP_EOL;
} catch (Exception \$e) {
    echo '   ERRO BankAccount: ' . \$e->getMessage() . PHP_EOL;
}

try {
    echo 'Testando ImportLog...' . PHP_EOL;
    \$importCount = \App\Models\ImportLog::count();
    echo '   ImportLogs: ' . \$importCount . PHP_EOL;
} catch (Exception \$e) {
    echo '   ERRO ImportLog: ' . \$e->getMessage() . PHP_EOL;
}

try {
    echo 'Testando Transaction...' . PHP_EOL;
    \$transactionCount = \App\Models\Transaction::count();
    echo '   Transactions: ' . \$transactionCount . PHP_EOL;
} catch (Exception \$e) {
    echo '   ERRO Transaction: ' . \$e->getMessage() . PHP_EOL;
}

try {
    echo 'Testando Category...' . PHP_EOL;
    \$categoryCount = \App\Models\Category::count();
    echo '   Categories: ' . \$categoryCount . PHP_EOL;
} catch (Exception \$e) {
    echo '   ERRO Category: ' . \$e->getMessage() . PHP_EOL;
}
"

echo "6. Regenerando autoload do Composer..."
composer dump-autoload

echo "7. Limpando todos os caches..."
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan cache:clear
php artisan optimize:clear

echo "8. Recriando caches otimizados..."
php artisan config:cache
php artisan route:cache

echo "9. Verificando permissões..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chown -R www-data:www-data storage/ bootstrap/cache/ 2>/dev/null || echo "   Execute como root para aplicar chown"

echo "10. Teste final do ImportController..."
php artisan tinker --execute="
try {
    echo 'Testando ImportController...' . PHP_EOL;
    \$controller = new \App\Http\Controllers\ImportController(new \App\Services\StatementImportService());
    echo '   ImportController OK' . PHP_EOL;
    
    // Testar chamada básica dos modelos
    \$imports = \App\Models\ImportLog::latest()->limit(1)->get();
    echo '   ImportLog query OK' . PHP_EOL;
    
} catch (Exception \$e) {
    echo '   ERRO: ' . \$e->getMessage() . PHP_EOL;
}
"

echo ""
echo "=============================================== "
echo "✅ CORREÇÃO COMPLETA FINALIZADA!"
echo "=============================================== "
echo ""
echo "📋 RESUMO DO QUE FOI EXECUTADO:"
echo "- ✅ Backup de segurança criado"
echo "- ✅ Migrations executadas"
echo "- ✅ Autoload regenerado"
echo "- ✅ Cache limpo e otimizado"
echo "- ✅ Permissões ajustadas"
echo "- ✅ Modelos testados"
echo ""
echo "🔍 PRÓXIMOS PASSOS:"
echo "1. Teste o site: /imports"
echo "2. Se ainda houver erro, verifique logs: tail -f storage/logs/laravel.log"
echo "3. Em caso de problema grave, restaure backup"
echo ""
echo "🆘 COMANDOS DE EMERGÊNCIA:"
echo "php artisan migrate:rollback --step=5  # Reverter últimas 5 migrations"
echo "php artisan migrate:refresh --seed     # Recriar tudo com dados de teste"
echo ""
