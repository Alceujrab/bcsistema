#!/bin/bash

# Script para configurar o banco MySQL para o BC Sistema
# Executar este script no servidor de produção

echo "=== Configuração do Banco MySQL para BC Sistema ==="

# Criar banco de dados se não existir
echo "1. Criando banco de dados..."
mysql -u usadosar_lara962 -p'[17pvS1-16' -e "CREATE DATABASE IF NOT EXISTS bcsistema_financeiro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

if [ $? -eq 0 ]; then
    echo "✓ Banco de dados 'bcsistema_financeiro' criado com sucesso!"
else
    echo "❌ Erro ao criar banco de dados"
    exit 1
fi

# Navegar para o diretório do projeto
cd /workspaces/bcsistema/bc

# Limpar cache do Laravel
echo "2. Limpando cache do Laravel..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Executar migrations
echo "3. Executando migrations..."
php artisan migrate:fresh --seed

if [ $? -eq 0 ]; then
    echo "✓ Migrations executadas com sucesso!"
else
    echo "❌ Erro ao executar migrations"
    exit 1
fi

# Popular dados de exemplo
echo "4. Populando dados de exemplo..."
php artisan db:seed --class=SystemSettingsSeeder

# Otimizar aplicação
echo "5. Otimizando aplicação..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "=== Configuração Concluída ==="
echo "Banco: bcsistema_financeiro"
echo "Host: localhost"
echo "Porta: 3306"
echo "Usuário: usadosar_lara962"
echo ""
echo "✓ Sistema pronto para uso com MySQL!"
echo "   Acesse: http://seu-dominio.com.br"
echo "   Admin: admin@bcsistema.com / password"
