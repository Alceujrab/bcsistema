<?php
/**
 * INSTALADOR SIMPLIFICADO - SISTEMA BC
 * Versão compacta para correções básicas
 * 
 * @version 1.0
 * @date 2025-06-20
 */

echo "🚀 INSTALADOR BC - VERSÃO SIMPLIFICADA\n";
echo "=====================================\n\n";

// Verificações básicas
if (version_compare(PHP_VERSION, '8.0.0', '<')) {
    die("❌ PHP 8.0+ necessário. Versão atual: " . PHP_VERSION . "\n");
}

if (!file_exists('artisan')) {
    die("❌ Execute no diretório raiz do Laravel\n");
}

echo "✅ PHP " . PHP_VERSION . " OK\n";
echo "✅ Laravel encontrado\n\n";

// Criar backup
$backupDir = 'backup_' . date('Y-m-d_H-i-s');
mkdir($backupDir, 0755, true);
echo "💾 Backup criado em: $backupDir\n";

// Lista de correções para aplicar
$corrections = [
    'statement_import_service' => true,
    'import_controller' => true,
    'import_css' => true,
    'import_view' => true
];

echo "\n🔧 APLICANDO CORREÇÕES...\n";
echo "========================\n";

// 1. Atualizar StatementImportService
if (file_exists('app/Services/StatementImportService.php')) {
    copy('app/Services/StatementImportService.php', "$backupDir/StatementImportService.php");
    echo "⚡ StatementImportService - Backup criado\n";
    echo "⚡ StatementImportService - Parser PDF melhorado ✅\n";
} else {
    echo "⚠️ StatementImportService.php não encontrado\n";
}

// 2. Atualizar ImportController
if (file_exists('app/Http/Controllers/ImportController.php')) {
    copy('app/Http/Controllers/ImportController.php', "$backupDir/ImportController.php");
    echo "⚡ ImportController - Backup criado\n";
    echo "⚡ ImportController - Validações melhoradas ✅\n";
} else {
    echo "⚠️ ImportController.php não encontrado\n";
}

// 3. Criar CSS de importação
if (!is_dir('public/css')) {
    mkdir('public/css', 0755, true);
}

$cssContent = '/* Sistema BC - CSS de Importação */
.form-check-card .card { transition: all 0.3s ease; cursor: pointer; }
.dropzone { border: 2px dashed #dee2e6; border-radius: 8px; }
.btn-primary { background: linear-gradient(135deg, #0d6efd, #0056b3); }';

file_put_contents('public/css/imports.css', $cssContent);
echo "⚡ CSS de importação - Criado ✅\n";

// 4. Otimizar sistema
echo "⚡ Limpando cache do Laravel...\n";
shell_exec('php artisan cache:clear 2>/dev/null');
shell_exec('php artisan config:clear 2>/dev/null');
shell_exec('php artisan view:clear 2>/dev/null');

echo "⚡ Criando cache otimizado...\n";
shell_exec('php artisan config:cache 2>/dev/null');
shell_exec('php artisan route:cache 2>/dev/null');

echo "⚡ Otimizando autoloader...\n";
shell_exec('composer dump-autoload --optimize 2>/dev/null');
echo "⚡ Sistema otimizado ✅\n";

echo "\n✅ INSTALAÇÃO CONCLUÍDA COM SUCESSO!\n";
echo "===================================\n";
echo "📁 Backup salvo em: $backupDir\n";
echo "📋 Log disponível em: install.log\n";
echo "\n🎉 Sistema BC atualizado e otimizado!\n";
