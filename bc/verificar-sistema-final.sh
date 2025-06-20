#!/bin/bash

echo "🔍 VERIFICAÇÃO FINAL DO SISTEMA BC"
echo "=================================="
echo ""

# Verificar se o arquivo .env existe
if [ -f ".env" ]; then
    echo "✅ Arquivo .env encontrado"
else
    echo "❌ Arquivo .env não encontrado"
    echo "   Copie o arquivo .env.example para .env e configure as variáveis"
fi

# Verificar se o banco de dados existe
if [ -f "database/database.sqlite" ]; then
    echo "✅ Banco de dados SQLite encontrado"
else
    echo "❌ Banco de dados SQLite não encontrado"
    echo "   Execute: touch database/database.sqlite"
fi

# Verificar se o vendor está instalado
if [ -d "vendor" ]; then
    echo "✅ Dependências do Composer instaladas"
else
    echo "❌ Dependências do Composer não instaladas"
    echo "   Execute: composer install"
fi

# Verificar se o node_modules existe
if [ -d "node_modules" ]; then
    echo "✅ Dependências do Node.js instaladas"
else
    echo "❌ Dependências do Node.js não instaladas"
    echo "   Execute: npm install"
fi

echo ""
echo "🛠️  COMANDOS DE MANUTENÇÃO RECOMENDADOS:"
echo "======================================="
echo ""
echo "# Limpeza de cache completa:"
echo "php artisan cache:clear"
echo "php artisan config:clear"
echo "php artisan route:clear"
echo "php artisan view:clear"
echo "php artisan optimize:clear"
echo ""
echo "# Otimização:"
echo "php artisan config:cache"
echo "php artisan route:cache"
echo "php artisan view:cache"
echo ""
echo "# Verificar migrações:"
echo "php artisan migrate:status"
echo ""
echo "# Executar migrações (se necessário):"
echo "php artisan migrate --force"
echo ""
echo "# Verificar rotas:"
echo "php artisan route:list"
echo ""
echo "# Testar aplicação:"
echo "php artisan serve"
echo ""
echo "📋 CHECKLIST FINAL:"
echo "=================="
echo "□ Arquivo .env configurado com dados do banco"
echo "□ Banco de dados criado e migrações executadas"
echo "□ Cache limpo e otimizado"
echo "□ Dependências instaladas (composer + npm)"
echo "□ Assets compilados (npm run build)"
echo "□ Permissões corretas nas pastas storage/ e bootstrap/cache/"
echo "□ Todas as páginas testadas no navegador"
echo ""
echo "🚀 DEPLOY EM PRODUÇÃO:"
echo "====================="
echo "1. Faça upload de todos os arquivos via File Manager"
echo "2. Execute os comandos de limpeza de cache no Terminal"
echo "3. Teste todas as funcionalidades"
echo "4. Monitore os logs em storage/logs/"
echo ""
echo "Para mais detalhes, consulte o arquivo GUIA-DEPLOY.md"
