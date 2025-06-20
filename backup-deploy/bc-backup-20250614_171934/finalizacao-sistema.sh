#!/bin/bash

# Script de Finalização e Otimização do Sistema
# Execute este script após fazer upload dos arquivos para o servidor

echo "=== Sistema de Transações - Finalização e Otimização ==="
echo "Data: $(date)"
echo ""

# Navegar para o diretório do projeto
cd /workspaces/bcsistema/bc

echo "1. Limpando caches do Laravel..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "2. Otimizando configurações..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "3. Executando migrações (se necessário)..."
php artisan migrate --force

echo "4. Verificando permissões..."
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/

echo "5. Compilando assets..."
if [ -f "package.json" ]; then
    npm install --production
    npm run build
else
    echo "package.json não encontrado, pulando compilação de assets"
fi

echo "6. Copiando arquivos JavaScript..."
mkdir -p public/js
cp resources/js/transactions.js public/js/ 2>/dev/null || echo "Arquivo transactions.js já existe"

echo "7. Testando conectividade com banco de dados..."
php artisan tinker --execute="
try {
    \DB::connection()->getPdo();
    echo 'Conexão com banco: ✅ OK\n';
} catch(Exception \$e) {
    echo 'Erro na conexão: ' . \$e->getMessage() . '\n';
}
"

echo "8. Verificando estrutura de tabelas..."
php artisan tinker --execute="
try {
    echo 'Transações: ' . \App\Models\Transaction::count() . ' registros\n';
    echo 'Contas: ' . \App\Models\BankAccount::count() . ' registros\n';
    echo 'Categorias: ' . \App\Models\Category::count() . ' registros\n';
} catch(Exception \$e) {
    echo 'Erro ao verificar tabelas: ' . \$e->getMessage() . '\n';
}
"

echo "9. Testando rotas principais..."
php artisan route:list --path=transactions | head -10

echo ""
echo "=== Finalização Completa ==="
echo "✅ Sistema otimizado e pronto para uso!"
echo ""
echo "🔗 URLs de teste:"
echo "   - Dashboard: http://seu-dominio/"
echo "   - Transações: http://seu-dominio/transactions"
echo "   - API Test: http://seu-dominio/test"
echo ""
echo "📝 Próximos passos:"
echo "   1. Acesse o sistema via navegador"
echo "   2. Teste as funcionalidades de edição inline"
echo "   3. Verifique os filtros e ações em lote"
echo "   4. Teste a criação de categorias"
echo "   5. Valide a exportação de dados"
echo ""
echo "🚨 Se encontrar erros, verifique:"
echo "   - Permissões de arquivos (775/644)"
echo "   - Configuração do banco no .env"
echo "   - Logs em storage/logs/"
echo ""

# Verificar se há erros nos logs recentes
if [ -f "storage/logs/laravel.log" ]; then
    echo "📋 Últimos erros nos logs:"
    tail -10 storage/logs/laravel.log | grep -i error || echo "Nenhum erro encontrado nos logs recentes"
fi

echo ""
echo "Script executado com sucesso em $(date)"
