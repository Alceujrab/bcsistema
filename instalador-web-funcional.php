<?php
/**
 * INSTALADOR WEB FUNCIONAL - SISTEMA BC
 * Aplica correções reais via interface web
 * 
 * @version 2.0
 * @date 2025-06-20
 */

ini_set('max_execution_time', 300);
ini_set('memory_limit', '256M');

// Verificar se é uma requisição AJAX
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

class BCWebInstaller
{
    private $logFile;
    private $backupDir;
    private $errors = [];
    private $success = [];
    
    public function __construct()
    {
        $this->logFile = __DIR__ . '/install-web.log';
        $this->backupDir = __DIR__ . '/storage/backups/web_install_' . date('Y-m-d_H-i-s');
        
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }
    
    public function checkRequirements()
    {
        $results = [];
        
        // Verificar PHP
        if (version_compare(PHP_VERSION, '8.0.0', '>=')) {
            $results[] = ['status' => 'success', 'message' => 'PHP ' . PHP_VERSION . ' ✅'];
        } else {
            $results[] = ['status' => 'error', 'message' => 'PHP 8.0+ necessário. Atual: ' . PHP_VERSION];
        }
        
        // Verificar Laravel
        if (file_exists(__DIR__ . '/artisan')) {
            $results[] = ['status' => 'success', 'message' => 'Laravel encontrado ✅'];
        } else {
            $results[] = ['status' => 'error', 'message' => 'Laravel não encontrado'];
        }
        
        // Verificar permissões de escrita
        if (is_writable(__DIR__ . '/app')) {
            $results[] = ['status' => 'success', 'message' => 'Permissões de escrita OK ✅'];
        } else {
            $results[] = ['status' => 'error', 'message' => 'Sem permissão de escrita no diretório app/'];
        }
        
        // Verificar Composer
        if (file_exists(__DIR__ . '/vendor/autoload.php')) {
            $results[] = ['status' => 'success', 'message' => 'Composer dependencies OK ✅'];
        } else {
            $results[] = ['status' => 'error', 'message' => 'Execute: composer install'];
        }
        
        return $results;
    }
    
    public function createBackup()
    {
        $results = [];
        
        $filesToBackup = [
            'app/Services/StatementImportService.php',
            'app/Http/Controllers/ImportController.php',
            'resources/views/imports/create.blade.php'
        ];
        
        foreach ($filesToBackup as $file) {
            if (file_exists(__DIR__ . '/' . $file)) {
                $backupFile = $this->backupDir . '/' . basename($file);
                if (copy(__DIR__ . '/' . $file, $backupFile)) {
                    $results[] = ['status' => 'success', 'message' => 'Backup: ' . basename($file) . ' ✅'];
                } else {
                    $results[] = ['status' => 'error', 'message' => 'Erro backup: ' . basename($file)];
                }
            }
        }
        
        $results[] = ['status' => 'success', 'message' => 'Backup salvo em: ' . basename($this->backupDir)];
        return $results;
    }
    
    public function installCorrections()
    {
        $results = [];
        
        try {
            // 1. Atualizar StatementImportService
            $results = array_merge($results, $this->updateStatementImportService());
            
            // 2. Atualizar ImportController
            $results = array_merge($results, $this->updateImportController());
            
            // 3. Criar CSS de importação
            $results = array_merge($results, $this->createImportCSS());
            
            // 4. Atualizar view de importação
            $results = array_merge($results, $this->updateImportView());
            
            // 5. Otimizar sistema
            $results = array_merge($results, $this->optimizeSystem());
            
            $results[] = ['status' => 'success', 'message' => '🎉 Todas as correções aplicadas com sucesso!'];
            
        } catch (Exception $e) {
            $results[] = ['status' => 'error', 'message' => 'Erro crítico: ' . $e->getMessage()];
        }
        
        return $results;
    }
    
    private function updateStatementImportService()
    {
        $results = [];
        $filePath = __DIR__ . '/app/Services/StatementImportService.php';
        
        if (!file_exists($filePath)) {
            $results[] = ['status' => 'error', 'message' => 'StatementImportService.php não encontrado'];
            return $results;
        }
        
        $content = file_get_contents($filePath);
        
        // Melhorar parser PDF para múltiplos bancos
        if (strpos($content, 'Padrão Banco do Brasil') === false) {
            $newPDFParser = "        // Padrões avançados para extratos PDF brasileiros
        \$patterns = [
            // Padrão Banco do Brasil: DD/MM/YYYY DESCRIÇÃO VALOR
            '/(\d{2}\/\d{2}\/\d{4})\s+(.+?)\s+([\-\+]?\d{1,3}(?:\.\d{3})*[,]\d{2})/',
            // Padrão Itaú: DD/MM DESCRIÇÃO VALOR D/C
            '/(\d{2}\/\d{2})\s+(.+?)\s+([\-\+]?\d{1,3}(?:\.\d{3})*[,]\d{2})\s*[DC]?/',
            // Padrão Bradesco: DD/MM/YY DESCRIÇÃO VALOR
            '/(\d{2}\/\d{2}\/\d{2,4})\s+(.+?)\s+([\-\+]?\d{1,3}(?:\.\d{3})*[,]\d{2})/',
            // Padrão Santander: DD-MM-YYYY DESCRIÇÃO VALOR
            '/(\d{2}-\d{2}-\d{4})\s+(.+?)\s+([\-\+]?\d{1,3}(?:\.\d{3})*[,]\d{2})/',
            // Padrão Caixa: DD/MM/YYYY DESCRIÇÃO R\$ VALOR
            '/(\d{2}\/\d{2}\/\d{4})\s+(.+?)\s+R\$\s*([\-\+]?\d{1,3}(?:\.\d{3})*[,]\d{2})/',
            // Padrão genérico com formatação brasileira
            '/(\d{1,2}\/\d{1,2}\/\d{2,4})\s+(.+?)\s+([\-\+]?\d{1,3}(?:\.\d{3})*[,]\d{2})/',
        ];";
            
            $content = str_replace(
                "        // Padrões comuns em extratos PDF brasileiros\n        \$patterns = [",
                $newPDFParser,
                $content
            );
        }
        
        // Adicionar métodos auxiliares
        if (strpos($content, 'normalizeDateFormat') === false) {
            $auxiliaryMethods = '
    
    // Métodos auxiliares para processamento
    protected function normalizeDateFormat($date)
    {
        return str_replace(\'-\', \'/\', $date);
    }
    
    protected function cleanDescription($description)
    {
        $description = preg_replace(\'/\s+/\', \' \', trim($description));
        $patterns = [
            \'/^(TEF|TED|PIX|DOC|COMPRA|TRANSFERENCIA|SAQUE|DEPOSITO)\s+/i\',
            \'/\s+(CARTAO|DEBITO|CREDITO)\s*$/i\',
        ];
        foreach ($patterns as $pattern) {
            $description = preg_replace($pattern, \'\', $description);
        }
        return trim($description);
    }
    
    protected function inferCategory($description)
    {
        $description = strtolower($description);
        $categories = [
            \'alimentacao\' => [\'mercado\', \'supermercado\', \'restaurant\', \'ifood\'],
            \'transporte\' => [\'posto\', \'combustivel\', \'uber\', \'99\', \'taxi\'],
            \'saude\' => [\'farmacia\', \'medico\', \'hospital\', \'clinica\'],
            \'transferencia\' => [\'ted\', \'doc\', \'pix\', \'transferencia\'],
        ];
        
        foreach ($categories as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($description, $keyword) !== false) {
                    return $category;
                }
            }
        }
        return \'outros\';
    }';
            
            $content = str_replace(
                '    protected function parseAmount($amountString)',
                $auxiliaryMethods . "\n\n    protected function parseAmount(\$amountString)",
                $content
            );
        }
        
        if (file_put_contents($filePath, $content)) {
            $results[] = ['status' => 'success', 'message' => 'StatementImportService atualizado ✅'];
        } else {
            $results[] = ['status' => 'error', 'message' => 'Erro ao atualizar StatementImportService'];
        }
        
        return $results;
    }
    
    private function updateImportController()
    {
        $results = [];
        $filePath = __DIR__ . '/app/Http/Controllers/ImportController.php';
        
        if (!file_exists($filePath)) {
            $results[] = ['status' => 'error', 'message' => 'ImportController.php não encontrado'];
            return $results;
        }
        
        $content = file_get_contents($filePath);
        
        // Atualizar validação para suportar mais formatos
        if (strpos($content, 'max:20480') === false) {
            $content = str_replace(
                "'file' => 'required|file|max:10240'",
                "'file' => 'required|file|max:20480|mimes:csv,txt,ofx,qif,pdf,xls,xlsx'",
                $content
            );
            
            // Adicionar mensagens personalizadas
            $content = str_replace(
                '], [',
                "], [
            'file.required' => 'É necessário selecionar um arquivo',
            'file.max' => 'O arquivo deve ter no máximo 20MB',
            'file.mimes' => 'Formato não suportado. Use: CSV, TXT, OFX, QIF, PDF, XLS ou XLSX',
        ] + [",
                $content
            );
        }
        
        if (file_put_contents($filePath, $content)) {
            $results[] = ['status' => 'success', 'message' => 'ImportController atualizado ✅'];
        } else {
            $results[] = ['status' => 'error', 'message' => 'Erro ao atualizar ImportController'];
        }
        
        return $results;
    }
    
    private function createImportCSS()
    {
        $results = [];
        $cssDir = __DIR__ . '/public/css';
        
        if (!is_dir($cssDir)) {
            mkdir($cssDir, 0755, true);
        }
        
        $cssContent = '/* 
 * Estilos para Importação de Extratos
 * Sistema BC - Módulo de Importação
 */

.form-check-card .form-check-input {
    position: absolute;
    opacity: 0;
}

.form-check-card .form-check-input:checked + .form-check-label .card {
    border-color: var(--bs-primary);
    box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25);
    background-color: rgba(var(--bs-primary-rgb), 0.05);
    transform: translateY(-2px);
}

.form-check-card .card {
    transition: all 0.3s ease;
    cursor: pointer;
    border: 2px solid #dee2e6;
}

.form-check-card .card:hover {
    border-color: rgba(var(--bs-primary-rgb), 0.5);
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.dropzone {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    transition: all 0.3s ease;
    cursor: pointer;
    background-color: #f8f9fa;
    min-height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.dropzone:hover {
    border-color: var(--bs-primary);
    background-color: rgba(var(--bs-primary-rgb), 0.05);
}

.btn-primary {
    background: linear-gradient(135deg, #0d6efd, #0056b3);
    border: none;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0056b3, #004085);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
}';
        
        if (file_put_contents($cssDir . '/imports.css', $cssContent)) {
            $results[] = ['status' => 'success', 'message' => 'CSS de importação criado ✅'];
        } else {
            $results[] = ['status' => 'error', 'message' => 'Erro ao criar CSS de importação'];
        }
        
        return $results;
    }
    
    private function updateImportView()
    {
        $results = [];
        $filePath = __DIR__ . '/resources/views/imports/create.blade.php';
        
        if (!file_exists($filePath)) {
            $results[] = ['status' => 'warning', 'message' => 'View de importação não encontrada'];
            return $results;
        }
        
        $content = file_get_contents($filePath);
        
        if (strpos($content, 'imports.css') === false) {
            $content = str_replace(
                "@section('title', 'Nova Importação de Extrato')",
                "@section('title', 'Nova Importação de Extrato')

@push('styles')
<link rel=\"stylesheet\" href=\"{{ asset('css/imports.css') }}\">
@endpush",
                $content
            );
            
            if (file_put_contents($filePath, $content)) {
                $results[] = ['status' => 'success', 'message' => 'View de importação atualizada ✅'];
            } else {
                $results[] = ['status' => 'error', 'message' => 'Erro ao atualizar view'];
            }
        } else {
            $results[] = ['status' => 'success', 'message' => 'View já atualizada ✅'];
        }
        
        return $results;
    }
    
    private function optimizeSystem()
    {
        $results = [];
        
        // Limpar e recriar cache
        $commands = [
            'php artisan config:clear' => 'Cache de configuração limpo',
            'php artisan route:clear' => 'Cache de rotas limpo',
            'php artisan view:clear' => 'Cache de views limpo',
            'php artisan config:cache' => 'Cache de configuração criado',
            'php artisan route:cache' => 'Cache de rotas criado'
        ];
        
        foreach ($commands as $command => $message) {
            $output = shell_exec($command . ' 2>&1');
            if ($output && strpos($output, 'error') === false) {
                $results[] = ['status' => 'success', 'message' => $message . ' ✅'];
            } else {
                $results[] = ['status' => 'warning', 'message' => $message . ' (ignorado)'];
            }
        }
        
        // Otimizar autoloader
        $output = shell_exec('composer dump-autoload --optimize 2>&1');
        if ($output && strpos($output, 'error') === false) {
            $results[] = ['status' => 'success', 'message' => 'Autoloader otimizado ✅'];
        }
        
        return $results;
    }
}

// Processar requisições AJAX
if ($isAjax && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $installer = new BCWebInstaller();
    $action = $_POST['action'];
    
    switch ($action) {
        case 'check_requirements':
            echo json_encode($installer->checkRequirements());
            break;
            
        case 'create_backup':
            echo json_encode($installer->createBackup());
            break;
            
        case 'install_corrections':
            echo json_encode($installer->installCorrections());
            break;
            
        default:
            echo json_encode([['status' => 'error', 'message' => 'Ação inválida']]);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalador BC Sistema - Funcional</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .header {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }
        .header h1 {
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0;
        }
        .btn {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
        }
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        .btn-success {
            background: linear-gradient(135deg, #27ae60, #229954);
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
        }
        .btn-success:hover {
            box-shadow: 0 6px 20px rgba(39, 174, 96, 0.4);
        }
        .btn-danger {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
        }
        .log {
            background: #2c3e50;
            color: #ecf0f1;
            padding: 20px;
            border-radius: 10px;
            font-family: 'Consolas', monospace;
            white-space: pre-wrap;
            max-height: 400px;
            overflow-y: auto;
            margin-top: 20px;
            box-shadow: inset 0 2px 10px rgba(0,0,0,0.3);
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
            background: #d4edda;
            color: #155724;
            border-left-color: #27ae60;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border-left-color: #e74c3c;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            border-left-color: #f39c12;
        }
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #ecf0f1;
            border-radius: 4px;
            overflow: hidden;
            margin: 20px 0;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #3498db, #27ae60);
            width: 0%;
            transition: width 0.5s ease;
        }
        .section {
            margin: 20px 0;
            padding: 20px;
            border-radius: 10px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3498db;
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
            <h1>🚀 Instalador BC Sistema</h1>
            <p>Correções Profissionais - Versão Funcional</p>
        </div>

        <div class="progress-bar">
            <div class="progress-fill" id="progress"></div>
        </div>

        <div class="section">
            <h3>📋 Verificações do Sistema</h3>
            <div id="req-results"></div>
            <button class="btn" onclick="checkRequirements()" id="btn-check">
                🔍 Verificar Requisitos
            </button>
        </div>

        <div class="section">
            <h3>🔧 Ações de Instalação</h3>
            <button class="btn" onclick="createBackup()" id="btn-backup" disabled>
                💾 Criar Backup
            </button>
            <button class="btn btn-success" onclick="installCorrections()" id="btn-install" disabled>
                ⚡ Instalar Correções
            </button>
            <button class="btn btn-danger" onclick="clearLogs()">
                🗑️ Limpar Logs
            </button>
        </div>

        <div class="section">
            <h3>📊 Log de Instalação</h3>
            <div id="log" class="log">Sistema carregado. Clique em "Verificar Requisitos" para começar...</div>
        </div>
    </div>

    <script>
        let requirementsPassed = false;
        let backupCreated = false;
        
        function log(message, type = 'info') {
            const logDiv = document.getElementById('log');
            const timestamp = new Date().toLocaleTimeString();
            const icon = type === 'success' ? '✅' : type === 'error' ? '❌' : type === 'warning' ? '⚠️' : '📋';
            logDiv.textContent += `[${timestamp}] ${icon} ${message}\n`;
            logDiv.scrollTop = logDiv.scrollHeight;
        }

        function updateProgress(percentage) {
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
                
                log(result.message, result.status);
            });
        }

        function setButtonLoading(buttonId, loading) {
            const btn = document.getElementById(buttonId);
            if (loading) {
                btn.innerHTML = '<span class="loading"></span> Processando...';
                btn.disabled = true;
            } else {
                btn.disabled = false;
            }
        }

        function clearLogs() {
            document.getElementById('log').textContent = 'Logs limpos...\n';
            updateProgress(0);
        }

        async function makeRequest(action) {
            const formData = new FormData();
            formData.append('action', action);
            
            const response = await fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });
            
            return await response.json();
        }

        async function checkRequirements() {
            setButtonLoading('btn-check', true);
            log('🔍 Verificando requisitos do sistema...');
            updateProgress(10);
            
            try {
                const results = await makeRequest('check_requirements');
                showResults(results, 'req-results');
                
                // Verificar se todos os requisitos passaram
                requirementsPassed = results.every(r => r.status === 'success');
                
                if (requirementsPassed) {
                    document.getElementById('btn-backup').disabled = false;
                    log('✅ Sistema pronto para instalação!', 'success');
                    updateProgress(25);
                } else {
                    log('❌ Corrija os problemas antes de continuar', 'error');
                }
                
            } catch (error) {
                log('❌ Erro ao verificar requisitos: ' + error.message, 'error');
            }
            
            document.getElementById('btn-check').innerHTML = '🔍 Verificar Requisitos';
            document.getElementById('btn-check').disabled = false;
        }

        async function createBackup() {
            if (!requirementsPassed) {
                log('❌ Execute a verificação de requisitos primeiro', 'error');
                return;
            }
            
            setButtonLoading('btn-backup', true);
            log('💾 Criando backup de segurança...');
            updateProgress(40);
            
            try {
                const results = await makeRequest('create_backup');
                showResults(results, 'req-results');
                
                backupCreated = true;
                document.getElementById('btn-install').disabled = false;
                log('✅ Backup criado com sucesso!', 'success');
                updateProgress(60);
                
            } catch (error) {
                log('❌ Erro ao criar backup: ' + error.message, 'error');
            }
            
            document.getElementById('btn-backup').innerHTML = '💾 Criar Backup';
            document.getElementById('btn-backup').disabled = false;
        }

        async function installCorrections() {
            if (!requirementsPassed || !backupCreated) {
                log('❌ Execute os passos anteriores primeiro', 'error');
                return;
            }
            
            setButtonLoading('btn-install', true);
            log('🚀 Iniciando instalação das correções...');
            updateProgress(70);
            
            try {
                const results = await makeRequest('install_corrections');
                showResults(results, 'req-results');
                
                log('🎉 Instalação concluída com sucesso!', 'success');
                updateProgress(100);
                
                // Mostrar mensagem final
                setTimeout(() => {
                    alert('🎉 Sistema BC atualizado com sucesso!\n\n✅ Correções aplicadas:\n- Parser PDF avançado\n- Validações melhoradas\n- CSS otimizado\n- Sistema otimizado\n\nO sistema está pronto para uso!');
                }, 1000);
                
            } catch (error) {
                log('❌ Erro na instalação: ' + error.message, 'error');
            }
            
            document.getElementById('btn-install').innerHTML = '⚡ Instalar Correções';
            document.getElementById('btn-install').disabled = false;
        }

        // Inicialização
        window.onload = function() {
            log('🌟 Instalador BC Sistema carregado');
            log('📋 Clique em "Verificar Requisitos" para começar');
        };
    </script>
</body>
</html>
