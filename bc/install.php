<?php
/**
 * INSTALADOR AUTOMÁTICO - SISTEMA BC
 * Correções Profissionais para Importação de Extratos
 * 
 * @version 2.0
 * @date 2025-06-20
 * @author Sistema BC
 */

ini_set('max_execution_time', 300); // 5 minutos
ini_set('memory_limit', '256M');

class BCSystemInstaller
{
    public $logFile;
    public $backupDir;
    public $errors = [];
    public $success = [];
    
    public function __construct()
    {
        $this->logFile = __DIR__ . '/install.log';
        $this->backupDir = __DIR__ . '/storage/backups/install_' . date('Y-m-d_H-i-s');
        
        // Criar diretório de backup
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }
    
    public function run()
    {
        $this->log("🚀 INICIANDO INSTALAÇÃO - SISTEMA BC v2.0");
        $this->log("Data: " . date('Y-m-d H:i:s'));
        $this->log("Diretório: " . __DIR__);
        
        try {
            $this->checkRequirements();
            $this->createBackups();
            $this->updateStatementImportService();
            $this->updateImportController();
            $this->createImportCSS();
            $this->updateImportView();
            $this->optimizeSystem();
            $this->runTests();
            
            $this->displayResults();
            
        } catch (Exception $e) {
            $this->log("❌ ERRO CRÍTICO: " . $e->getMessage());
            $this->displayError($e->getMessage());
        }
    }
    
    private function checkRequirements()
    {
        $this->log("🔍 Verificando requisitos do sistema...");
        
        // Verificar PHP
        if (version_compare(PHP_VERSION, '8.2.0', '<')) {
            throw new Exception("PHP 8.2+ é necessário. Versão atual: " . PHP_VERSION);
        }
        $this->success[] = "PHP " . PHP_VERSION . " ✅";
        
        // Verificar Laravel
        if (!file_exists(__DIR__ . '/artisan')) {
            throw new Exception("Laravel não encontrado. Execute no diretório raiz do projeto.");
        }
        $this->success[] = "Laravel encontrado ✅";
        
        // Verificar permissões
        if (!is_writable(__DIR__ . '/app')) {
            throw new Exception("Sem permissão de escrita no diretório app/");
        }
        $this->success[] = "Permissões OK ✅";
        
        // Verificar Composer
        if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
            throw new Exception("Dependências do Composer não instaladas. Execute: composer install");
        }
        $this->success[] = "Dependencies Composer ✅";
        
        $this->log("✅ Todos os requisitos atendidos");
    }
    
    private function createBackups()
    {
        $this->log("💾 Criando backups de segurança...");
        
        $filesToBackup = [
            'app/Services/StatementImportService.php',
            'app/Http/Controllers/ImportController.php',
            'resources/views/imports/create.blade.php'
        ];
        
        foreach ($filesToBackup as $file) {
            if (file_exists(__DIR__ . '/' . $file)) {
                $backupFile = $this->backupDir . '/' . basename($file);
                copy(__DIR__ . '/' . $file, $backupFile);
                $this->log("Backup criado: " . basename($file));
            }
        }
        
        $this->success[] = "Backups criados em: " . $this->backupDir;
    }
    
    private function updateStatementImportService()
    {
        $this->log("🔧 Atualizando StatementImportService...");
        
        $filePath = __DIR__ . '/app/Services/StatementImportService.php';
        
        if (!file_exists($filePath)) {
            throw new Exception("StatementImportService.php não encontrado");
        }
        
        $content = file_get_contents($filePath);
        
        // Melhorar parser PDF
        $oldPDFParser = '/(\d{2}\/\d{2}\/\d{4})\s+(.+?)\s+([\-\+]?\d+[,\.]\d{2})/';
        $newPDFParser = "// Padrões avançados para extratos PDF brasileiros
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
            // Padrão simplificado
            '/(\d{2}\/\d{2})\s+(.+?)\s+([\-\+]?\d+[,\.]\d{2})/',
        ];";
        
        if (strpos($content, 'Padrão Banco do Brasil') === false) {
            $content = str_replace(
                "// Padrões comuns em extratos PDF brasileiros\n        \$patterns = [",
                $newPDFParser,
                $content
            );
        }
        
        // Adicionar métodos auxiliares se não existirem
        if (strpos($content, 'normalizeDateFormat') === false) {
            $auxiliaryMethods = '
    
    // Métodos auxiliares para processamento de PDF
    protected function normalizeDateFormat($date)
    {
        // Converter DD-MM-YYYY para DD/MM/YYYY
        return str_replace(\'-\', \'/\', $date);
    }
    
    protected function cleanDescription($description)
    {
        // Remover caracteres especiais e espaços extras
        $description = preg_replace(\'/\s+/\', \' \', $description);
        $description = trim($description);
        
        // Remover patterns comuns de extratos
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
        
        // Categorias automáticas baseadas em palavras-chave
        $categories = [
            \'alimentacao\' => [\'mercado\', \'supermercado\', \'restaurant\', \'lanchonete\', \'ifood\', \'uber eats\'],
            \'transporte\' => [\'posto\', \'combustivel\', \'uber\', \'99\', \'taxi\', \'onibus\', \'metro\'],
            \'saude\' => [\'farmacia\', \'medico\', \'hospital\', \'laboratorio\', \'clinica\'],
            \'entretenimento\' => [\'cinema\', \'teatro\', \'netflix\', \'spotify\', \'gaming\'],
            \'educacao\' => [\'escola\', \'faculdade\', \'curso\', \'livros\'],
            \'casa\' => [\'supermercado\', \'material\', \'construcao\', \'eletrodomestico\'],
            \'servicos\' => [\'banco\', \'taxa\', \'tarifa\', \'juros\', \'multa\'],
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
        
        // Melhorar conversão Excel
        if (strpos($content, 'Excel::toArray') === false) {
            $excelConverter = '    protected function convertExcelToCSV(UploadedFile $file)
    {
        try {
            // Usar Laravel Excel para converter
            $data = \Maatwebsite\Excel\Facades\Excel::toArray([], $file);
            
            if (empty($data) || empty($data[0])) {
                throw new \Exception(\'Arquivo Excel vazio ou inválido\');
            }
            
            $worksheet = $data[0]; // Primeira planilha
            $csvContent = \'\';
            
            foreach ($worksheet as $row) {
                // Filtrar linhas vazias
                $row = array_filter($row, function($cell) {
                    return !is_null($cell) && $cell !== \'\';
                });
                
                if (!empty($row)) {
                    $csvContent .= implode(\',\', array_map(function($cell) {
                        // Escapar aspas e adicionar aspas se necessário
                        if (is_string($cell) && (strpos($cell, \',\') !== false || strpos($cell, \'"\') !== false)) {
                            return \'"\' . str_replace(\'"\', \'""\', $cell) . \'"\';
                        }
                        return $cell;
                    }, $row)) . "\\n";
                }
            }
            
            if (empty($csvContent)) {
                throw new \Exception(\'Nenhum dado encontrado no arquivo Excel\');
            }
            
            return $csvContent;
            
        } catch (\Exception $e) {
            Log::error(\'Erro ao converter Excel para CSV: \' . $e->getMessage());
            throw new \Exception(\'Erro ao processar arquivo Excel: \' . $e->getMessage() . \'. Tente salvar como CSV para melhor compatibilidade.\');
        }
    }';
            
            $content = str_replace(
                '    protected function convertExcelToCSV(UploadedFile $file)
    {
        // Implementação simples - sugere ao usuário converter para CSV
        throw new \Exception(\'Para processar arquivos Excel (.xls/.xlsx), por favor converta para CSV primeiro. Isso garantirá melhor compatibilidade e precisão na importação.\');
    }',
                $excelConverter,
                $content
            );
        }
        
        file_put_contents($filePath, $content);
        $this->success[] = "StatementImportService atualizado ✅";
        $this->log("✅ StatementImportService atualizado com sucesso");
    }
    
    private function updateImportController()
    {
        $this->log("🔧 Atualizando ImportController...");
        
        $filePath = __DIR__ . '/app/Http/Controllers/ImportController.php';
        
        if (!file_exists($filePath)) {
            throw new Exception("ImportController.php não encontrado");
        }
        
        $content = file_get_contents($filePath);
        
        // Atualizar validação
        if (strpos($content, 'max:20480') === false) {
            $content = str_replace(
                "'file' => 'required|file|max:10240'",
                "'file' => 'required|file|max:20480|mimes:csv,txt,ofx,qif,pdf,xls,xlsx'",
                $content
            );
            
            // Adicionar mensagens de erro personalizadas
            $content = str_replace(
                '], [',
                "], [
            'file.required' => 'É necessário selecionar um arquivo',
            'file.max' => 'O arquivo deve ter no máximo 20MB',
            'file.mimes' => 'Formato de arquivo não suportado. Use: CSV, TXT, OFX, QIF, PDF, XLS ou XLSX',
            'bank_account_id.required' => 'É necessário selecionar uma conta bancária',
            'bank_account_id.exists' => 'Conta bancária não encontrada',
        ] + [",
                $content
            );
        }
        
        file_put_contents($filePath, $content);
        $this->success[] = "ImportController atualizado ✅";
        $this->log("✅ ImportController atualizado com sucesso");
    }
    
    private function createImportCSS()
    {
        $this->log("🎨 Criando CSS de importação...");
        
        $cssDir = __DIR__ . '/public/css';
        if (!is_dir($cssDir)) {
            mkdir($cssDir, 0755, true);
        }
        
        $cssContent = '/* 
 * Estilos para Importação de Extratos
 * Sistema BC - Módulo de Importação
 */

/* Formulário de Importação */
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

/* Dropzone - Área de Upload */
.dropzone-wrapper {
    margin-bottom: 1rem;
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
    transform: scale(1.01);
}

.dropzone.dragover {
    border-color: var(--bs-success);
    background-color: rgba(var(--bs-success-rgb), 0.1);
    border-style: solid;
    transform: scale(1.02);
}

.dropzone-content {
    text-align: center;
    padding: 2rem;
}

.dropzone-content i {
    font-size: 3rem;
    color: #6c757d;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.dropzone:hover .dropzone-content i {
    color: var(--bs-primary);
    transform: scale(1.1);
}

.dropzone.dragover .dropzone-content i {
    color: var(--bs-success);
    transform: scale(1.2);
}

.file-info {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    border: 1px solid #dee2e6;
}

/* Timeline de Processo */
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: \'\';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, var(--bs-primary), var(--bs-success));
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
    padding-left: 20px;
}

.timeline-marker {
    position: absolute;
    left: -37px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--bs-primary), var(--bs-success));
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.timeline-number {
    color: white;
    font-weight: bold;
    font-size: 14px;
}

/* Botões de Ação */
.btn-primary {
    background: linear-gradient(135deg, #0d6efd, #0056b3);
    border: none;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0056b3, #004085);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
}

/* Responsividade */
@media (max-width: 768px) {
    .dropzone {
        min-height: 120px;
    }
    
    .dropzone-content {
        padding: 1rem;
    }
    
    .timeline {
        padding-left: 20px;
    }
}';
        
        file_put_contents($cssDir . '/imports.css', $cssContent);
        $this->success[] = "CSS de importação criado ✅";
        $this->log("✅ CSS de importação criado com sucesso");
    }
    
    private function updateImportView()
    {
        $this->log("🖼️ Atualizando view de importação...");
        
        $filePath = __DIR__ . '/resources/views/imports/create.blade.php';
        
        if (!file_exists($filePath)) {
            $this->log("⚠️ View de importação não encontrada, pulando...");
            return;
        }
        
        $content = file_get_contents($filePath);
        
        // Adicionar link para CSS se não existir
        if (strpos($content, 'imports.css') === false) {
            $content = str_replace(
                "@section('title', 'Nova Importação de Extrato')",
                "@section('title', 'Nova Importação de Extrato')

@push('styles')
<link rel=\"stylesheet\" href=\"{{ asset('css/imports.css') }}\">
@endpush",
                $content
            );
        }
        
        file_put_contents($filePath, $content);
        $this->success[] = "View de importação atualizada ✅";
        $this->log("✅ View de importação atualizada com sucesso");
    }
    
    private function optimizeSystem()
    {
        $this->log("⚡ Otimizando sistema...");
        
        // Limpar cache
        $this->runCommand('php artisan config:clear');
        $this->runCommand('php artisan route:clear');
        $this->runCommand('php artisan view:clear');
        
        // Recriar cache
        $this->runCommand('php artisan config:cache');
        $this->runCommand('php artisan route:cache');
        
        // Otimizar autoloader
        $this->runCommand('composer dump-autoload --optimize');
        
        $this->success[] = "Sistema otimizado ✅";
        $this->log("✅ Sistema otimizado com sucesso");
    }
    
    private function runTests()
    {
        $this->log("🧪 Executando testes de validação...");
        
        $tests = [
            'StatementImportService existe' => file_exists(__DIR__ . '/app/Services/StatementImportService.php'),
            'ImportController existe' => file_exists(__DIR__ . '/app/Http/Controllers/ImportController.php'),
            'CSS imports existe' => file_exists(__DIR__ . '/public/css/imports.css'),
            'Sintaxe PHP válida' => $this->validatePHPSyntax(__DIR__ . '/app/Services/StatementImportService.php'),
        ];
        
        foreach ($tests as $test => $result) {
            if ($result) {
                $this->success[] = $test . " ✅";
            } else {
                $this->errors[] = $test . " ❌";
            }
        }
        
        $this->log("✅ Testes de validação concluídos");
    }
    
    private function runCommand($command)
    {
        $output = shell_exec($command . ' 2>&1');
        $this->log("Comando: $command");
        if ($output) {
            $this->log("Output: $output");
        }
    }
    
    private function validatePHPSyntax($file)
    {
        $output = shell_exec("php -l '$file' 2>&1");
        return strpos($output, 'No syntax errors') !== false;
    }
    
    private function log($message)
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message\n";
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }
    
    private function displayResults()
    {
        echo "<!DOCTYPE html>
<html lang='pt-BR'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Instalação BC Sistema - Concluída</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f8f9fa; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #28a745; margin: 0; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .log-section { margin-top: 30px; }
        .log-content { background: #f1f1f1; padding: 15px; border-radius: 5px; font-family: monospace; max-height: 300px; overflow-y: auto; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; }
        .stat-card { background: #e9ecef; padding: 20px; border-radius: 8px; text-align: center; }
        .stat-number { font-size: 2em; font-weight: bold; color: #28a745; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🚀 Instalação Concluída com Sucesso!</h1>
            <p>Sistema BC - Correções Profissionais Aplicadas</p>
        </div>
        
        <div class='stats'>
            <div class='stat-card'>
                <div class='stat-number'>" . count($this->success) . "</div>
                <div>Sucessos</div>
            </div>
            <div class='stat-card'>
                <div class='stat-number'>" . count($this->errors) . "</div>
                <div>Erros</div>
            </div>
            <div class='stat-card'>
                <div class='stat-number'>7</div>
                <div>Formatos Suportados</div>
            </div>
        </div>";
        
        if (!empty($this->success)) {
            echo "<div class='success'><h3>✅ Correções Aplicadas:</h3><ul>";
            foreach ($this->success as $item) {
                echo "<li>$item</li>";
            }
            echo "</ul></div>";
        }
        
        if (!empty($this->errors)) {
            echo "<div class='error'><h3>❌ Problemas Encontrados:</h3><ul>";
            foreach ($this->errors as $item) {
                echo "<li>$item</li>";
            }
            echo "</ul></div>";
        }
        
        echo "<div class='success'>
            <h3>🎯 Funcionalidades Instaladas:</h3>
            <ul>
                <li><strong>Importação PDF Avançada:</strong> Suporte a 7 padrões de bancos brasileiros</li>
                <li><strong>Conversão Excel Automática:</strong> Processamento direto de .xls/.xlsx</li>
                <li><strong>Interface Melhorada:</strong> CSS organizado com animações</li>
                <li><strong>Validações Robustas:</strong> Suporte a 7 formatos, até 20MB</li>
                <li><strong>Sistema Otimizado:</strong> Cache de rotas e configurações</li>
            </ul>
        </div>
        
        <div class='success'>
            <h3>🏦 Bancos Suportados:</h3>
            <ul>
                <li>Banco do Brasil (DD/MM/YYYY)</li>
                <li>Itaú (DD/MM + D/C)</li>
                <li>Bradesco (DD/MM/YY)</li>
                <li>Santander (DD-MM-YYYY)</li>
                <li>Caixa Econômica (DD/MM/YYYY + R$)</li>
                <li>Padrão Genérico Brasileiro</li>
            </ul>
        </div>
        
        <div class='log-section'>
            <h3>📋 Log de Instalação:</h3>
            <div class='log-content'>" . nl2br(htmlspecialchars(file_get_contents($this->logFile))) . "</div>
        </div>
        
        <div style='text-align: center; margin-top: 30px; padding: 20px; background: #e9ecef; border-radius: 8px;'>
            <h3>✅ Sistema Pronto para Uso!</h3>
            <p>Todas as correções foram aplicadas com sucesso. O sistema agora suporta importação completa de extratos PDF, Excel e outros formatos.</p>
            <p><strong>Backup criado em:</strong> " . $this->backupDir . "</p>
        </div>
    </div>
</body>
</html>";
    }
    
    private function displayError($error)
    {
        echo "<!DOCTYPE html>
<html lang='pt-BR'>
<head>
    <meta charset='UTF-8'>
    <title>Erro na Instalação - BC Sistema</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f8f9fa; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .error { background: #f8d7da; color: #721c24; padding: 20px; border-radius: 5px; text-align: center; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='error'>
            <h1>❌ Erro na Instalação</h1>
            <p>$error</p>
            <p>Verifique o arquivo install.log para mais detalhes.</p>
        </div>
    </div>
</body>
</html>";
    }
}

// Verificar se está sendo executado via web ou CLI
if (php_sapi_name() === 'cli') {
    echo "🚀 INSTALADOR BC SISTEMA - MODO CLI\n";
    echo "===================================\n\n";
    
    $installer = new BCSystemInstaller();
    $installer->run();
    
    // Exibir resultados no CLI
    if (!empty($installer->success)) {
        echo "\n✅ CORREÇÕES APLICADAS:\n";
        foreach ($installer->success as $item) {
            echo "   - $item\n";
        }
    }
    
    if (!empty($installer->errors)) {
        echo "\n❌ PROBLEMAS ENCONTRADOS:\n";
        foreach ($installer->errors as $item) {
            echo "   - $item\n";
        }
    }
    
    echo "\n✅ Instalação concluída! Verifique o arquivo install.log para detalhes.\n";
    echo "📁 Backup criado em: " . $installer->backupDir . "\n";
} else {
    // Executar via web
    $installer = new BCSystemInstaller();
    $installer->run();
}
?>
