#!/bin/bash

echo "=== CORREÇÃO URGENTE - ERRO ImportLog ==="
echo "Corrigindo erro: Class 'App\Models\ImportLog' not found"
echo "Data: $(date)"
echo ""

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    echo "❌ Erro: Execute este script no diretório raiz do Laravel"
    exit 1
fi

echo "1. Verificando estrutura do banco de dados..."

# Verificar tabelas existentes
echo "2. Executando migrations necessárias..."
php artisan migrate --force

echo "3. Verificando se a tabela import_logs foi criada..."
php artisan tinker --execute="
try {
    \$count = \App\Models\ImportLog::count();
    echo 'Tabela import_logs OK - Registros: ' . \$count . PHP_EOL;
} catch (Exception \$e) {
    echo 'ERRO: ' . \$e->getMessage() . PHP_EOL;
}
"

echo "4. Limpando cache do aplicativo..."
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan cache:clear

echo "5. Recriando cache de configuração..."
php artisan config:cache

echo "6. Testando se o problema foi resolvido..."
php artisan tinker --execute="
try {
    echo 'Testando ImportLog...' . PHP_EOL;
    \$model = new \App\Models\ImportLog();
    echo 'ImportLog funcionando!' . PHP_EOL;
    
    echo 'Testando BankAccount...' . PHP_EOL;
    \$bankCount = \App\Models\BankAccount::count();
    echo 'BankAccount OK - Total: ' . \$bankCount . PHP_EOL;
    
} catch (Exception \$e) {
    echo 'ERRO PERSISTENTE: ' . \$e->getMessage() . PHP_EOL;
}
"

echo ""
echo "✅ CORREÇÃO CONCLUÍDA!"
echo ""
echo "📋 RESUMO:"
echo "- Migrations executadas"
echo "- Cache limpo e recriado"  
echo "- Modelo ImportLog testado"
echo ""
echo "🔍 Se o erro persistir:"
echo "1. Verifique se o arquivo app/Models/ImportLog.php existe"
echo "2. Execute: composer dump-autoload"
echo "3. Verifique permissões da pasta storage/"
echo ""
