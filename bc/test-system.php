<?php

/*
 * TESTE COMPLETO DO SISTEMA BC
 * Executa testes sem depender do terminal
 */

echo "=== TESTE SISTEMA BC - " . date('Y-m-d H:i:s') . " ===\n\n";

// Definir diretório base
$baseDir = '/workspaces/bcsistema/bc';
chdir($baseDir);

// Teste 1: Verificar se o .env existe e está configurado
echo "1. VERIFICANDO CONFIGURAÇÕES:\n";
if (file_exists('.env')) {
    echo "✅ Arquivo .env encontrado\n";
    $env = file_get_contents('.env');
    if (strpos($env, 'usadosar_lara962') !== false) {
        echo "✅ Configurações de banco encontradas\n";
    } else {
        echo "❌ Configurações de banco não encontradas\n";
    }
} else {
    echo "❌ Arquivo .env não encontrado\n";
}

// Teste 2: Verificar extensões PHP
echo "\n2. VERIFICANDO EXTENSÕES PHP:\n";
$extensions = get_loaded_extensions();
$required = ['pdo', 'pdo_mysql', 'mysqli', 'mysql'];
foreach ($required as $ext) {
    if (in_array($ext, $extensions)) {
        echo "✅ $ext carregado\n";
    } else {
        echo "❌ $ext não encontrado\n";
    }
}

// Teste 3: Testar conexão direta
echo "\n3. TESTANDO CONEXÃO MYSQL:\n";
try {
    $pdo = new PDO(
        'mysql:host=127.0.0.1;dbname=usadosar_lara962',
        'usadosar_lara962',
        '[17pvS1-16',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "✅ Conexão estabelecida com sucesso\n";
    
    // Testar consulta
    $stmt = $pdo->query("SELECT DATABASE() as db, VERSION() as version");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ Banco: " . $result['db'] . "\n";
    echo "✅ Versão MySQL: " . $result['version'] . "\n";
    
    // Listar tabelas
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "✅ Tabelas encontradas: " . count($tables) . "\n";
    
} catch (Exception $e) {
    echo "❌ Erro de conexão: " . $e->getMessage() . "\n";
}

// Teste 4: Verificar arquivos do Laravel
echo "\n4. VERIFICANDO ARQUIVOS LARAVEL:\n";
$laravelFiles = [
    'app/Http/Controllers/ImportController.php',
    'app/Http/Controllers/ReconciliationController.php',
    'app/Models/Transaction.php',
    'app/Models/ImportLog.php',
    'resources/views/imports/index.blade.php',
    'resources/views/layouts/app.blade.php'
];

foreach ($laravelFiles as $file) {
    if (file_exists($file)) {
        echo "✅ $file encontrado\n";
    } else {
        echo "❌ $file não encontrado\n";
    }
}

// Teste 5: Verificar sintaxe PHP
echo "\n5. VERIFICANDO SINTAXE DOS CONTROLLERS:\n";
$controllers = [
    'app/Http/Controllers/ImportController.php',
    'app/Http/Controllers/ReconciliationController.php'
];

foreach ($controllers as $controller) {
    if (file_exists($controller)) {
        $output = [];
        $returnCode = 0;
        exec("php -l $controller 2>&1", $output, $returnCode);
        if ($returnCode === 0) {
            echo "✅ $controller - sintaxe OK\n";
        } else {
            echo "❌ $controller - erro de sintaxe\n";
            echo "   " . implode("\n   ", $output) . "\n";
        }
    }
}

// Teste 6: Verificar composer.json
echo "\n6. VERIFICANDO DEPENDÊNCIAS:\n";
if (file_exists('composer.json')) {
    $composer = json_decode(file_get_contents('composer.json'), true);
    if (isset($composer['require']['laravel/framework'])) {
        echo "✅ Laravel framework configurado\n";
    }
    if (isset($composer['require']['barryvdh/laravel-dompdf'])) {
        echo "✅ DomPDF configurado\n";
    }
    if (isset($composer['require']['maatwebsite/excel'])) {
        echo "✅ Laravel Excel configurado\n";
    }
}

echo "\n=== RESUMO DOS TESTES ===\n";
echo "Data: " . date('Y-m-d H:i:s') . "\n";
echo "Sistema: BC - Gestão Financeira\n";
echo "Status: Teste concluído\n";
echo "========================\n";

// Salvar resultado
file_put_contents('test-results.txt', ob_get_contents());
