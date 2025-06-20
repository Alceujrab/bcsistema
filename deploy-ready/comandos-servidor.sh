#!/bin/bash
# COMANDOS PARA EXECUTAR NO SERVIDOR após upload

echo "🔧 CONFIGURANDO BC SISTEMA NO SERVIDOR"
echo "====================================="

# Navegar para diretório
cd /home/usadosar/public_html/bc

# Configurar permissões
echo "🔐 Configurando permissões..."
chmod -R 755 .
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chmod 644 .env

# Instalar dependências
echo "📦 Instalando dependências..."
if command -v composer &> /dev/null; then
    composer install --optimize-autoloader --no-dev --no-interaction
else
    php composer.phar install --optimize-autoloader --no-dev --no-interaction
fi

# Gerar chave da aplicação
echo "🔑 Gerando chave da aplicação..."
php artisan key:generate --force

# Executar migrações
echo "🗃️ Executando migrações..."
php artisan migrate --force

# Limpar caches
echo "🧹 Limpando caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Otimizar para produção
echo "⚡ Otimizando para produção..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Criar symlink para storage (se necessário)
echo "🔗 Criando symlink do storage..."
php artisan storage:link

echo ""
echo "✅ CONFIGURAÇÃO CONCLUÍDA!"
echo "📋 URLs para testar:"
echo "   - Dashboard: https://seudominio.com/bc/"
echo "   - Transações: https://seudominio.com/bc/transactions"
echo "   - Relatórios: https://seudominio.com/bc/reports"
echo ""
echo "🔍 Para verificar erros:"
echo "   tail -f storage/logs/laravel.log"
