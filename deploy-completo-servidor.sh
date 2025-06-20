#!/bin/bash

# SCRIPT COMPLETO DE DEPLOY - BC SISTEMA
# Execute estes comandos no seu servidor via SSH

echo "🚀 INICIANDO DEPLOY DO BC SISTEMA"
echo "================================="

# 1. Navegar e preparar
cd /home/usadosar/public_html/
echo "📁 Fazendo backup se existir..."
if [ -d "bc" ]; then
    mv bc bc-backup-$(date +%Y%m%d-%H%M%S)
fi

# 2. Criar e entrar na pasta
mkdir -p bc
cd bc

# 3. Extrair arquivos
echo "📦 Extraindo arquivos..."
tar -xzf ../deploy-bc-sistema-20250616_132113.tar.gz

# 4. Configurar permissões
echo "🔐 Configurando permissões..."
chmod -R 755 .
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chmod 644 .env

# 5. Executar configuração
echo "⚙️ Executando configuração..."
chmod +x comandos-servidor.sh
./comandos-servidor.sh

echo ""
echo "✅ DEPLOY CONCLUÍDO!"
echo "📋 PRÓXIMOS PASSOS:"
echo "1. Editar .env com suas credenciais"
echo "2. Configurar Document Root para: /home/usadosar/public_html/bc/public"
echo "3. Acessar: https://seudominio.com/bc/"
echo ""
echo "🔍 Para verificar erros:"
echo "tail -f storage/logs/laravel.log"
