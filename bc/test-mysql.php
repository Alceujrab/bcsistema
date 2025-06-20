<?php

// Teste direto de conexão PHP/MySQL
echo "=== TESTE DIRETO PHP/MYSQL ===\n";

try {
    // Teste 1: Extensões PHP
    echo "1. Testando extensões PHP...\n";
    $extensions = get_loaded_extensions();
    $mysql_extensions = array_filter($extensions, function($ext) {
        return stripos($ext, 'mysql') !== false || stripos($ext, 'pdo') !== false;
    });
    echo "Extensões MySQL/PDO encontradas: " . implode(', ', $mysql_extensions) . "\n";
    
    // Teste 2: Conexão PDO
    echo "2. Testando conexão PDO...\n";
    $dsn = 'mysql:host=127.0.0.1;dbname=usadosar_lara962';
    $username = 'usadosar_lara962';
    $password = '[17pvS1-16';
    
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Conexão PDO estabelecida com sucesso!\n";
    
    // Teste 3: Consulta básica
    echo "3. Testando consulta...\n";
    $stmt = $pdo->query("SELECT DATABASE() as current_db, NOW() as current_time");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Banco atual: " . $result['current_db'] . "\n";
    echo "Hora atual: " . $result['current_time'] . "\n";
    
    // Teste 4: Listar tabelas
    echo "4. Testando listagem de tabelas...\n";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tabelas encontradas: " . count($tables) . "\n";
    if (count($tables) > 0) {
        echo "Primeiras tabelas: " . implode(', ', array_slice($tables, 0, 5)) . "\n";
    }
    
    echo "\n✅ TODOS OS TESTES PASSARAM!\n";
    echo "MySQL está funcionando perfeitamente.\n";
    
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Código do erro: " . $e->getCode() . "\n";
}

echo "\n=== FIM DO TESTE ===\n";
