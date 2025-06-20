<?php
/**
 * INSTALADOR BC - VERSÃO DEBUG
 * Para identificar e corrigir problemas
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Log de debug
function debug_log($message) {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] DEBUG: $message\n";
    file_put_contents(__DIR__ . '/debug.log', $logMessage, FILE_APPEND | LOCK_EX);
    
    // Só ecoar se não for AJAX
    global $isAjax;
    if (!$isAjax) {
        echo "<!-- DEBUG: $message -->\n";
    }
}

debug_log('Iniciando instalador debug');

// Verificar se é AJAX
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

debug_log('Is AJAX: ' . ($isAjax ? 'true' : 'false'));

if ($isAjax && isset($_POST['action'])) {
    debug_log('Processando ação AJAX: ' . $_POST['action']);
    
    header('Content-Type: application/json');
    header('Cache-Control: no-cache, must-revalidate');
    
    try {
        $action = $_POST['action'];
        debug_log('Ação recebida: ' . $action);
        
        switch ($action) {
            case 'check_requirements':
                debug_log('Verificando requisitos...');
                $result = [
                    ['status' => 'success', 'message' => 'PHP ' . PHP_VERSION . ' ✅'],
                    ['status' => 'success', 'message' => 'Debug mode ativo ✅'],
                    ['status' => 'success', 'message' => 'Sistema funcionando ✅']
                ];
                break;
                
            case 'create_backup':
                debug_log('Criando backup...');
                $result = [
                    ['status' => 'success', 'message' => 'Backup simulado ✅'],
                    ['status' => 'success', 'message' => 'Arquivos protegidos ✅']
                ];
                break;
                
            case 'install_corrections':
                debug_log('Instalando correções...');
                $result = [
                    ['status' => 'success', 'message' => 'StatementImportService atualizado ✅'],
                    ['status' => 'success', 'message' => 'ImportController atualizado ✅'],
                    ['status' => 'success', 'message' => 'CSS criado ✅'],
                    ['status' => 'success', 'message' => 'Sistema otimizado ✅'],
                    ['status' => 'success', 'message' => '🎉 Instalação concluída!']
                ];
                break;
                
            default:
                $result = [['status' => 'error', 'message' => 'Ação inválida: ' . $action]];
        }
        
        debug_log('Resultado preparado: ' . json_encode($result));
        echo json_encode($result);
        debug_log('JSON enviado com sucesso');
        
    } catch (Exception $e) {
        debug_log('Erro capturado: ' . $e->getMessage());
        echo json_encode([['status' => 'error', 'message' => 'Erro: ' . $e->getMessage()]]);
    }
    
    debug_log('Finalizando processamento AJAX');
    exit;
}

debug_log('Carregando interface HTML');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalador BC - Debug</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; margin: 10px; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        .log { background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; border-radius: 5px; font-family: monospace; white-space: pre-wrap; max-height: 400px; overflow-y: auto; }
        .status { padding: 10px; margin: 5px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .debug-info { background: #e7f3ff; padding: 10px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>🔧 Instalador BC - Modo Debug</h1>
    
    <div class="debug-info">
        <h3>📊 Informações de Debug</h3>
        <p><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></p>
        <p><strong>Servidor:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'N/A'; ?></p>
        <p><strong>Diretório:</strong> <?php echo __DIR__; ?></p>
        <p><strong>Arquivo artisan:</strong> <?php echo file_exists(__DIR__ . '/artisan') ? '✅ Encontrado' : '❌ Não encontrado'; ?></p>
    </div>
    
    <div>
        <button class="btn" onclick="testAjax('check_requirements')">🔍 Testar Requisitos</button>
        <button class="btn" onclick="testAjax('create_backup')">💾 Testar Backup</button>
        <button class="btn" onclick="testAjax('install_corrections')">⚡ Testar Instalação</button>
        <button class="btn" onclick="clearLog()">🗑️ Limpar Log</button>
    </div>
    
    <h3>📋 Log de Debug</h3>
    <div id="results"></div>
    <div id="log" class="log">Sistema debug carregado...\n</div>

    <script>
        function log(message) {
            const logDiv = document.getElementById('log');
            const timestamp = new Date().toLocaleTimeString();
            logDiv.textContent += `[${timestamp}] ${message}\n`;
            logDiv.scrollTop = logDiv.scrollHeight;
        }

        function clearLog() {
            document.getElementById('log').textContent = 'Log limpo...\n';
            document.getElementById('results').innerHTML = '';
        }

        function showResults(results) {
            const container = document.getElementById('results');
            container.innerHTML = '';
            
            results.forEach(result => {
                const div = document.createElement('div');
                div.className = `status ${result.status}`;
                div.textContent = result.message;
                container.appendChild(div);
                log(result.message);
            });
        }

        async function testAjax(action) {
            log(`🚀 Testando ação: ${action}`);
            
            try {
                const formData = new FormData();
                formData.append('action', action);
                
                log('📤 Enviando requisição...');
                
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                
                log(`📥 Resposta recebida: ${response.status} ${response.statusText}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const text = await response.text();
                log(`📄 Conteúdo da resposta (${text.length} chars): ${text.substring(0, 200)}...`);
                
                if (!text.trim()) {
                    throw new Error('Resposta vazia do servidor');
                }
                
                let data;
                try {
                    data = JSON.parse(text);
                    log('✅ JSON parseado com sucesso');
                } catch (e) {
                    log('❌ Erro ao parsear JSON: ' + e.message);
                    log('📄 Conteúdo completo: ' + text);
                    throw new Error('JSON inválido: ' + text.substring(0, 100));
                }
                
                showResults(data);
                log('✅ Teste concluído com sucesso');
                
            } catch (error) {
                log('❌ Erro no teste: ' + error.message);
                const errorDiv = document.createElement('div');
                errorDiv.className = 'status error';
                errorDiv.textContent = '❌ Erro: ' + error.message;
                document.getElementById('results').appendChild(errorDiv);
            }
        }

        window.onload = function() {
            log('🌟 Instalador debug carregado');
            log('📋 Clique nos botões para testar cada função');
        };
    </script>
</body>
</html>
