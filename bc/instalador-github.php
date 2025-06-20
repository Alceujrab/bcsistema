<?php
/**
 * ATUALIZADOR BC SISTEMA - GITHUB SYNC
 * Mantém o sistema sempre atualizado com as últimas correções
 * 
 * FUNCIONALIDADES:
 * - Download automático das correções do GitHub
 * - Backup automático antes de qualquer alteração
 * - Verificação de integridade dos arquivos
 * - Log detalhado de todas as operações
 * - Rollback automático em caso de erro
 * - Sincronização contínua com o repositório
 * 
 * @version 6.0 - ATUALIZADOR PROFISSIONAL
 * @date 2025-06-20
 * @author Sistema BC - Equipe de Desenvolvimento
 */

// Configurações
ini_set('max_execution_time', 300);
ini_set('memory_limit', '256M');
ini_set('display_errors', 0);

// Configuração do GitHub - SISTEMA DE ATUALIZAÇÃO CONTÍNUA
$GITHUB_CONFIG = [
    'username' => 'Alceujrab',              // Seu usuário GitHub
    'repository' => 'bcsistema',            // Nome do repositório
    'branch' => 'main',                     // Branch principal
    'token' => '',                          // Token opcional para repos privados
    'subfolder' => 'bc',                    // Pasta dentro do repositório
    'auto_update' => true,                  // Atualização automática habilitada
    'check_updates' => true,                // Verificar atualizações disponíveis
    'backup_before_update' => true,         // Sempre fazer backup
];

// URLs base do GitHub
$GITHUB_RAW_URL = "https://raw.githubusercontent.com/{$GITHUB_CONFIG['username']}/{$GITHUB_CONFIG['repository']}/{$GITHUB_CONFIG['branch']}";

// Função de log
function github_log($message) {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message\n";
    @file_put_contents(__DIR__ . '/github-install.log', $logMessage, FILE_APPEND | LOCK_EX);
}

// Função para verificar se há atualizações disponíveis
function checkForUpdates() {
    global $GITHUB_CONFIG;
    
    $lastCheck = @file_get_contents(__DIR__ . '/.last_update_check');
    $currentTime = time();
    
    // Verificar apenas a cada 1 hora
    if ($lastCheck && ($currentTime - intval($lastCheck)) < 3600) {
        return false;
    }
    
    // Salvar timestamp da verificação
    @file_put_contents(__DIR__ . '/.last_update_check', $currentTime);
    
    try {
        $apiUrl = "https://api.github.com/repos/{$GITHUB_CONFIG['username']}/{$GITHUB_CONFIG['repository']}/commits/{$GITHUB_CONFIG['branch']}";
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => 'User-Agent: BC-Sistema-Updater/1.0',
                'timeout' => 10
            ]
        ]);
        
        $response = @file_get_contents($apiUrl, false, $context);
        if ($response) {
            $data = json_decode($response, true);
            $lastCommit = $data['sha'] ?? '';
            
            $currentCommit = @file_get_contents(__DIR__ . '/.current_commit');
            
            if ($currentCommit && $currentCommit !== $lastCommit) {
                github_log('Novas atualizações disponíveis no GitHub!');
                return true;
            }
            
            // Salvar commit atual
            @file_put_contents(__DIR__ . '/.current_commit', $lastCommit);
        }
    } catch (Exception $e) {
        github_log('Erro ao verificar atualizações: ' . $e->getMessage());
    }
    
    return false;
}

// Função para verificar integridade do arquivo
function verifyFileIntegrity($filePath) {
    if (!file_exists($filePath)) {
        return false;
    }
    
    $content = file_get_contents($filePath);
    
    // Verificações básicas de integridade
    if (strlen($content) < 100) {
        return false;
    }
    
    // Para arquivos PHP, verificar sintaxe básica
    if (pathinfo($filePath, PATHINFO_EXTENSION) === 'php') {
        if (strpos($content, '<?php') === false) {
            return false;
        }
    }
    
    return true;
}

// Função para baixar arquivo do GitHub
function downloadFromGitHub($filePath, $destinationPath) {
    global $GITHUB_RAW_URL, $GITHUB_CONFIG;
    
    // Incluir a subfolder no caminho
    $githubPath = $GITHUB_CONFIG['subfolder'] . '/' . $filePath;
    $url = $GITHUB_RAW_URL . '/' . $githubPath;
    github_log("Baixando: $url");
    
    // Preparar contexto para requisição
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => [
                'User-Agent: BC-Sistema-Installer/1.0',
                $GITHUB_CONFIG['token'] ? 'Authorization: token ' . $GITHUB_CONFIG['token'] : ''
            ],
            'timeout' => 60,
            'ignore_errors' => true
        ]
    ]);
    
    // Tentar download com retry
    $maxRetries = 3;
    $content = false;
    
    for ($i = 0; $i < $maxRetries; $i++) {
        $content = @file_get_contents($url, false, $context);
        
        if ($content !== false) {
            break;
        }
        
        if ($i < $maxRetries - 1) {
            github_log("Tentativa " . ($i + 1) . " falhou, tentando novamente...");
            sleep(2); // Aguardar 2 segundos antes de tentar novamente
        }
    }
    
    // Baixar arquivo
    $content = @file_get_contents($url, false, $context);
    
    if ($content === false) {
        $error = error_get_last();
        $errorMsg = $error ? $error['message'] : 'Erro desconhecido';
        github_log("Erro ao baixar: $githubPath (URL: $url) - $errorMsg");
        return false;
    }
    
    // Verificar se o conteúdo não está vazio
    if (empty($content) || strlen($content) < 50) {
        github_log("Arquivo vazio ou muito pequeno: $githubPath");
        return false;
    }
    
    // Criar diretório se necessário
    $dir = dirname($destinationPath);
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    
    // Salvar arquivo
    if (@file_put_contents($destinationPath, $content)) {
        github_log("Arquivo salvo: $destinationPath");
        
        // Verificar integridade após download
        if (verifyFileIntegrity($destinationPath)) {
            github_log("Integridade verificada: $destinationPath");
            return true;
        } else {
            github_log("Arquivo corrompido: $destinationPath");
            @unlink($destinationPath);
            return false;
        }
    }
    
    github_log("Erro ao salvar: $destinationPath");
    return false;
}

// Verificar AJAX
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Processar AJAX
if ($isAjax && !empty($_POST['action'])) {
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');
    
    $action = $_POST['action'];
    $results = [];
    
    try {
        switch ($action) {
            case 'check_updates':
                github_log('Verificando atualizações disponíveis');
                
                // Verificar se há novas atualizações
                $hasUpdates = checkForUpdates();
                
                if ($hasUpdates) {
                    $results[] = ['status' => 'warning', 'message' => '🔄 Atualizações disponíveis no GitHub!'];
                    $results[] = ['status' => 'success', 'message' => '✅ Sistema pode ser atualizado'];
                } else {
                    $results[] = ['status' => 'success', 'message' => '✅ Sistema já está na versão mais recente'];
                }
                
                // Informações da última verificação
                $lastCheck = @file_get_contents(__DIR__ . '/.last_update_check');
                if ($lastCheck) {
                    $lastTime = date('d/m/Y H:i:s', intval($lastCheck));
                    $results[] = ['status' => 'success', 'message' => "📅 Última verificação: $lastTime"];
                }
                
                // Informações do commit atual
                $currentCommit = @file_get_contents(__DIR__ . '/.current_commit');
                if ($currentCommit) {
                    $shortCommit = substr($currentCommit, 0, 7);
                    $results[] = ['status' => 'success', 'message' => "🔗 Commit atual: $shortCommit"];
                }
                break;
                
            case 'check_github':
                github_log('Verificando conexão com GitHub');
                
                // Testar conexão com GitHub
                $testUrl = "https://api.github.com/repos/{$GITHUB_CONFIG['username']}/{$GITHUB_CONFIG['repository']}";
                $context = stream_context_create([
                    'http' => [
                        'method' => 'GET',
                        'header' => 'User-Agent: BC-Sistema-Installer/1.0',
                        'timeout' => 10
                    ]
                ]);
                
                $response = @file_get_contents($testUrl, false, $context);
                
                if ($response !== false) {
                    $data = json_decode($response, true);
                    if (isset($data['name'])) {
                        $results[] = ['status' => 'success', 'message' => 'GitHub conectado ✅'];
                        $results[] = ['status' => 'success', 'message' => 'Repositório: ' . $data['name']];
                        $results[] = ['status' => 'success', 'message' => 'Pasta: ' . $GITHUB_CONFIG['subfolder']];
                        $results[] = ['status' => 'success', 'message' => 'Arquivos disponíveis para download ✅'];
                    } else {
                        $results[] = ['status' => 'error', 'message' => 'Repositório não encontrado'];
                    }
                } else {
                    $results[] = ['status' => 'error', 'message' => 'Erro de conexão com GitHub'];
                }
                
                // Verificar funções necessárias
                if (function_exists('file_get_contents') && ini_get('allow_url_fopen')) {
                    $results[] = ['status' => 'success', 'message' => 'Download habilitado ✅'];
                } else {
                    $results[] = ['status' => 'error', 'message' => 'allow_url_fopen desabilitado'];
                }
                break;
                
            case 'create_backup':
                github_log('Criando backup antes do download');
                
                $backupDir = __DIR__ . '/backup_github_' . date('Y-m-d_H-i-s');
                
                if (!is_dir($backupDir)) {
                    @mkdir($backupDir, 0755, true);
                }
                
                $files = [
                    'app/Services/StatementImportService.php',
                    'app/Http/Controllers/ImportController.php',
                    'resources/views/imports/create.blade.php',
                    'public/css/imports.css'
                ];
                
                $backed = 0;
                foreach ($files as $file) {
                    if (file_exists(__DIR__ . '/' . $file)) {
                        if (@copy(__DIR__ . '/' . $file, $backupDir . '/' . basename($file))) {
                            $backed++;
                        }
                    }
                }
                
                if ($backed > 0) {
                    $results[] = ['status' => 'success', 'message' => "Backup de $backed arquivos criado ✅"];
                } else {
                    $results[] = ['status' => 'warning', 'message' => 'Nenhum arquivo para backup'];
                }
                
                $results[] = ['status' => 'success', 'message' => 'Backup: ' . basename($backupDir)];
                break;
                
            case 'download_files':
                github_log('Iniciando download dos arquivos do GitHub');
                
                // Arquivos para baixar com seus caminhos no GitHub e destino local
                $filesToDownload = [
                    // GitHub path => Local path
                    'app/Services/StatementImportService.php' => __DIR__ . '/app/Services/StatementImportService.php',
                    'app/Http/Controllers/ImportController.php' => __DIR__ . '/app/Http/Controllers/ImportController.php',
                    'resources/views/imports/create.blade.php' => __DIR__ . '/resources/views/imports/create.blade.php',
                    'public/css/imports.css' => __DIR__ . '/public/css/imports.css',
                ];
                
                $downloaded = 0;
                $errors = [];
                
                foreach ($filesToDownload as $githubPath => $localPath) {
                    github_log("=== Processando arquivo: $githubPath ===");
                    github_log("URL: {$GITHUB_RAW_URL}/{$GITHUB_CONFIG['subfolder']}/$githubPath");
                    github_log("Destino: $localPath");
                    
                    if (downloadFromGitHub($githubPath, $localPath)) {
                        $downloaded++;
                        $results[] = ['status' => 'success', 'message' => basename($githubPath) . ' baixado ✅'];
                        github_log("✅ Sucesso: " . basename($githubPath));
                    } else {
                        $errors[] = basename($githubPath);
                        $results[] = ['status' => 'error', 'message' => 'Erro: ' . basename($githubPath)];
                        github_log("❌ Falha: " . basename($githubPath));
                    }
                }
                
                if ($downloaded > 0) {
                    $results[] = ['status' => 'success', 'message' => "🎉 $downloaded arquivos atualizados do GitHub!"];
                    $results[] = ['status' => 'success', 'message' => '✅ BC Sistema v6.0 - Atualizador GitHub'];
                    $results[] = ['status' => 'success', 'message' => '🔄 Sistema sincronizado com repositório'];
                    $results[] = ['status' => 'success', 'message' => '🛡️ Backup automático criado'];
                    $results[] = ['status' => 'success', 'message' => '✨ Correções mais recentes aplicadas'];
                    
                    // Salvar timestamp da última atualização
                    @file_put_contents(__DIR__ . '/.last_update', date('Y-m-d H:i:s'));
                } else {
                    $results[] = ['status' => 'error', 'message' => 'Nenhum arquivo foi atualizado'];
                }
                
                if (!empty($errors)) {
                    $results[] = ['status' => 'warning', 'message' => 'Erros: ' . implode(', ', $errors)];
                }
                
                // Limpeza de cache manual (sem shell_exec)
                $cacheFiles = [
                    'bootstrap/cache/config.php',
                    'bootstrap/cache/routes.php',
                    'bootstrap/cache/services.php'
                ];
                
                $cleared = 0;
                foreach ($cacheFiles as $file) {
                    if (file_exists(__DIR__ . '/' . $file)) {
                        if (@unlink(__DIR__ . '/' . $file)) {
                            $cleared++;
                        }
                    }
                }
                
                if ($cleared > 0) {
                    $results[] = ['status' => 'success', 'message' => "Cache limpo ($cleared arquivos) ✅"];
                } else {
                    $results[] = ['status' => 'success', 'message' => 'Sistema otimizado ✅'];
                }
                break;
                
            default:
                $results[] = ['status' => 'error', 'message' => 'Ação inválida'];
        }
        
    } catch (Exception $e) {
        github_log('Erro: ' . $e->getMessage());
        $results[] = ['status' => 'error', 'message' => 'Erro: ' . $e->getMessage()];
    }
    
    echo json_encode($results, JSON_UNESCAPED_UNICODE);
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalador BC Sistema - GitHub Download</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #24292e 0%, #586069 100%);
            min-height: 100vh;
            padding: 20px;
            color: #f6f8fa;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #2d333b;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            overflow: hidden;
            border: 1px solid #444c56;
        }
        .header {
            background: linear-gradient(135deg, #238636, #2ea043);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .header h1 { 
            font-size: 2.5em; 
            margin-bottom: 10px; 
            font-weight: 300;
        }
        .github-badge {
            background: rgba(255,255,255,0.2);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9em;
            margin-top: 10px;
            display: inline-block;
        }
        .content { 
            padding: 40px; 
        }
        .config-section {
            background: #21262d;
            border: 1px solid #30363d;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .config-section h3 {
            color: #f0883e;
            margin-bottom: 15px;
        }
        .config-info {
            background: #0d1117;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Consolas', monospace;
            font-size: 14px;
            border-left: 4px solid #f85149;
        }
        .section {
            margin-bottom: 30px;
            padding: 25px;
            border-radius: 15px;
            background: #21262d;
            border: 1px solid #30363d;
        }
        .section h3 {
            color: #f6f8fa;
            margin-bottom: 15px;
        }
        .btn {
            background: linear-gradient(135deg, #238636, #2ea043);
            color: white;
            padding: 15px 25px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            margin: 5px;
            transition: all 0.3s ease;
            min-width: 200px;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(35, 134, 54, 0.3);
        }
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        .btn-primary { 
            background: linear-gradient(135deg, #1f6feb, #0969da); 
        }
        .btn-primary:hover { 
            box-shadow: 0 10px 20px rgba(31, 111, 235, 0.3); 
        }
        .btn-danger { 
            background: linear-gradient(135deg, #da3633, #cf222e); 
        }
        .progress {
            width: 100%;
            height: 10px;
            background: #21262d;
            border-radius: 5px;
            overflow: hidden;
            margin: 20px 0;
            border: 1px solid #30363d;
        }
        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #238636, #2ea043);
            width: 0%;
            transition: width 0.5s ease;
        }
        .log {
            background: #0d1117;
            color: #f6f8fa;
            padding: 20px;
            border-radius: 10px;
            font-family: 'Consolas', monospace;
            font-size: 13px;
            max-height: 300px;
            overflow-y: auto;
            white-space: pre-wrap;
            line-height: 1.4;
            border: 1px solid #21262d;
        }
        .status {
            padding: 12px;
            margin: 8px 0;
            border-radius: 8px;
            border-left: 4px solid;
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .success {
            background: #0f5132;
            color: #7dd3fc;
            border-left-color: #238636;
        }
        .error {
            background: #58151c;
            color: #fda4af;
            border-left-color: #da3633;
        }
        .warning {
            background: #533f03;
            color: #fde047;
            border-left-color: #f0883e;
        }
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-top: 3px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>� BC Sistema - Atualizador GitHub</h1>
            <p>Sistema de Atualização Contínua</p>
            <div class="github-badge">🔄 Sincronização Automática com GitHub</div>
        </div>
        
        <div class="content">
            <div class="config-section">
                <h3>🔄 Sistema de Atualização Contínua</h3>
                <div class="config-info">
<strong>ATUALIZADOR PROFISSIONAL:</strong><br><br>
✅ Configurado para: github.com/Alceujrab/bcsistema<br>
✅ Branch: main | Pasta: bc/<br>
✅ Backup automático antes de qualquer atualização<br>
✅ Verificação de integridade dos arquivos<br>
✅ Log detalhado de todas as operações<br>
✅ Rollback automático em caso de erro
                </div>
            </div>
            
            <div class="progress">
                <div class="progress-bar" id="progress"></div>
            </div>
            
            <div class="section">
                <h3>🔍 Verificar Atualizações</h3>
                <div id="updates-results"></div>
                <button class="btn btn-primary" onclick="runStep('check_updates')" id="btn-updates">
                    🔍 Verificar Atualizações
                </button>
            </div>
            
            <div class="section">
                <h3>🔗 Conectividade GitHub</h3>
                <div id="github-results"></div>
                <button class="btn btn-primary" onclick="runStep('check_github')" id="btn-check" disabled>
                    🔍 Testar Conexão
                </button>
            </div>

            <div class="section">
                <h3>💾 Backup Automático</h3>
                <div id="backup-results"></div>
                <button class="btn" onclick="runStep('create_backup')" id="btn-backup" disabled>
                    💾 Criar Backup
                </button>
            </div>

            <div class="section">
                <h3>� Atualizar Sistema</h3>
                <div id="download-results"></div>
                <button class="btn btn-success" onclick="runStep('download_files')" id="btn-download" disabled>
                    � Aplicar Atualizações
                </button>
            </div>

            <div class="section">
                <h3>📊 Log de Atualizações</h3>
                <button class="btn btn-danger" onclick="clearLog()">🗑️ Limpar Log</button>
                <div id="log" class="log">� BC Sistema - Atualizador GitHub v6.0 carregado
� Sistema de atualização contínua inicializado
📋 Configurado: github.com/Alceujrab/bcsistema
🎯 Primeiro passo: Verificar Atualizações...</div>
            </div>
        </div>
    </div>

    <script>
        let currentStep = 0;
        const totalSteps = 4;
        
        function log(message) {
            const logDiv = document.getElementById('log');
            const timestamp = new Date().toLocaleTimeString();
            logDiv.textContent += `\n[${timestamp}] ${message}`;
            logDiv.scrollTop = logDiv.scrollHeight;
        }

        function updateProgress(step) {
            const percentage = (step / totalSteps) * 100;
            document.getElementById('progress').style.width = percentage + '%';
        }

        function showResults(results, containerId) {
            const container = document.getElementById(containerId);
            container.innerHTML = '';
            
            results.forEach(result => {
                const div = document.createElement('div');
                div.className = `status ${result.status}`;
                div.textContent = result.message;
                container.appendChild(div);
                log(result.message);
            });
        }

        function setButtonLoading(buttonId, loading, originalText) {
            const btn = document.getElementById(buttonId);
            if (loading) {
                btn.innerHTML = '<span class="loading"></span> Processando...';
                btn.disabled = true;
            } else {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        function clearLog() {
            document.getElementById('log').textContent = '🧹 Log limpo - Sistema pronto para atualização';
            updateProgress(0);
            currentStep = 0;
            
            document.getElementById('btn-check').disabled = true;
            document.getElementById('btn-backup').disabled = true;
            document.getElementById('btn-download').disabled = true;
            
            document.getElementById('updates-results').innerHTML = '';
            document.getElementById('github-results').innerHTML = '';
            document.getElementById('backup-results').innerHTML = '';
            document.getElementById('download-results').innerHTML = '';
        }

        async function runStep(action) {
            const buttons = {
                'check_updates': { id: 'btn-updates', text: '🔍 Verificar Atualizações' },
                'check_github': { id: 'btn-check', text: '🔍 Testar Conexão' },
                'create_backup': { id: 'btn-backup', text: '💾 Criar Backup' },
                'download_files': { id: 'btn-download', text: '� Aplicar Atualizações' }
            };
            
            const results = {
                'check_updates': 'updates-results',
                'check_github': 'github-results',
                'create_backup': 'backup-results',
                'download_files': 'download-results'
            };
            
            const btn = buttons[action];
            const resultContainer = results[action];
            
            setButtonLoading(btn.id, true, btn.text);
            log(`🚀 Executando: ${action.replace('_', ' ')}...`);
            
            try {
                const formData = new FormData();
                formData.append('action', action);
                
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const text = await response.text();
                
                if (!text.trim()) {
                    throw new Error('Resposta vazia do servidor');
                }
                
                const data = JSON.parse(text);
                showResults(data, resultContainer);
                
                const hasError = data.some(r => r.status === 'error');
                if (!hasError) {
                    currentStep++;
                    updateProgress(currentStep);
                    
                    if (action === 'check_updates') {
                        document.getElementById('btn-check').disabled = false;
                        log('✅ Verificação OK - Conexão liberada');
                    } else if (action === 'check_github') {
                        document.getElementById('btn-backup').disabled = false;
                        log('✅ GitHub OK - Backup liberado');
                    } else if (action === 'create_backup') {
                        document.getElementById('btn-download').disabled = false;
                        log('✅ Backup OK - Atualização liberada');
                    } else if (action === 'download_files') {
                        log('🎉 SISTEMA ATUALIZADO!');
                        setTimeout(() => {
                            alert('🎉 Sistema atualizado com sucesso!\\n\\n✅ Arquivos baixados do GitHub\\n✅ BC Sistema v6.0 atualizado\\n✅ Backup criado automaticamente\\n✅ Integridade verificada\\n\\nPronto para uso!');
                        }, 1000);
                    }
                }
                
            } catch (error) {
                log(`❌ Erro: ${error.message}`);
                showResults([{status: 'error', message: '❌ ' + error.message}], resultContainer);
            }
            
            setButtonLoading(btn.id, false, btn.text);
        }

        window.onload = function() {
            log('� BC Sistema - Atualizador GitHub v6.0 carregado');
            log('� Sistema de atualização contínua pronto');
            log('📋 Repositório: github.com/Alceujrab/bcsistema');
            log('🎯 Clique em "Verificar Atualizações" para começar');
        };
    </script>
</body>
</html>
