#!/bin/bash

# XAMPP Alternativo - BC Sistema
echo "🚀 INICIANDO STACK LAMP PARA BC SISTEMA"
echo "======================================"

# Iniciar MariaDB
echo "📦 Iniciando MariaDB..."
sudo systemctl start mariadb
sudo systemctl enable mariadb

# Verificar status
echo "🔍 Verificando status dos serviços..."
echo "MariaDB: $(systemctl is-active mariadb)"

# Configurar banco se necessário
echo "🗄️ Configurando banco de dados..."
sudo mysql -e "CREATE DATABASE IF NOT EXISTS usadosar_lara962;" 2>/dev/null || true
sudo mysql -e "CREATE USER IF NOT EXISTS 'usadosar_lara962'@'localhost' IDENTIFIED BY '[17pvS1-16';" 2>/dev/null || true
sudo mysql -e "GRANT ALL PRIVILEGES ON usadosar_lara962.* TO 'usadosar_lara962'@'localhost';" 2>/dev/null || true
sudo mysql -e "FLUSH PRIVILEGES;" 2>/dev/null || true

# Testar conexão
echo "🧪 Testando conexão..."
mysql -u usadosar_lara962 -p'[17pvS1-16' -e "SELECT 'Conexão OK' as status;" 2>/dev/null && echo "✅ Banco funcionando!" || echo "❌ Erro na conexão"

# Testar PHP
echo "🐘 Testando PHP..."
php -r "echo 'PHP versão: ' . phpversion() . PHP_EOL;"
php -m | grep -i mysql > /dev/null && echo "✅ Extensões MySQL OK" || echo "❌ Extensões MySQL não encontradas"

# Mostrar informações
echo ""
echo "📋 INFORMAÇÕES DE CONEXÃO:"
echo "Host: 127.0.0.1"
echo "Porta: 3306"
echo "Banco: usadosar_lara962"
echo "Usuário: usadosar_lara962"
echo "Senha: [17pvS1-16"
echo ""
echo "🎯 STACK LAMP PRONTO PARA USO!"
echo "======================================"
