#!/bin/bash

# Script para testar MySQL/MariaDB
echo "=== TESTE DE CONEXÃO MYSQL/MARIADB ==="

# Testar se MariaDB está rodando
echo "1. Verificando status do MariaDB..."
systemctl is-active mariadb

# Testar conexão como root
echo "2. Testando conexão como root..."
mysql -u root -e "SHOW DATABASES;" 2>/dev/null && echo "✅ Conexão root OK" || echo "❌ Erro na conexão root"

# Testar conexão com usuário específico
echo "3. Testando conexão com usuário usadosar_lara962..."
mysql -u usadosar_lara962 -p'[17pvS1-16' -e "SHOW DATABASES;" 2>/dev/null && echo "✅ Conexão usuário OK" || echo "❌ Erro na conexão usuário"

# Testar extensões PHP
echo "4. Verificando extensões PHP MySQL..."
php -m | grep -i mysql && echo "✅ Extensões PHP OK" || echo "❌ Extensões PHP não encontradas"

# Testar conexão PHP
echo "5. Testando conexão PHP..."
php -r "
try {
    \$pdo = new PDO('mysql:host=127.0.0.1;dbname=usadosar_lara962', 'usadosar_lara962', '[17pvS1-16');
    echo '✅ Conexão PHP PDO OK\n';
} catch (Exception \$e) {
    echo '❌ Erro PHP PDO: ' . \$e->getMessage() . '\n';
}
"

echo "=== FIM DO TESTE ==="
