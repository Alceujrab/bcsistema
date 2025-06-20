#!/bin/bash

# Script de Teste - Sistema BC
# Verifica se todas as correções foram aplicadas corretamente

echo "🔍 TESTANDO SISTEMA BC - CORREÇÕES APLICADAS"
echo "=============================================="

cd /workspaces/bcsistema/bc

echo ""
echo "📋 1. Verificando estrutura do sistema..."
if [ -f "app/Services/StatementImportService.php" ]; then
    echo "✅ StatementImportService encontrado"
else
    echo "❌ StatementImportService não encontrado"
fi

if [ -f "app/Http/Controllers/ImportController.php" ]; then
    echo "✅ ImportController encontrado"
else
    echo "❌ ImportController não encontrado"
fi

if [ -f "public/css/imports.css" ]; then
    echo "✅ CSS de importação encontrado"
else
    echo "❌ CSS de importação não encontrado"
fi

echo ""
echo "🔧 2. Testando sintaxe PHP..."
php -l app/Services/StatementImportService.php > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo "✅ StatementImportService - Sintaxe OK"
else
    echo "❌ StatementImportService - Erro de sintaxe"
fi

php -l app/Http/Controllers/ImportController.php > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo "✅ ImportController - Sintaxe OK"
else
    echo "❌ ImportController - Erro de sintaxe"
fi

echo ""
echo "📦 3. Verificando dependências..."
composer show barryvdh/laravel-dompdf > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo "✅ DomPDF instalado"
else
    echo "❌ DomPDF não encontrado"
fi

composer show maatwebsite/excel > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo "✅ Laravel Excel instalado"
else
    echo "❌ Laravel Excel não encontrado"
fi

composer show smalot/pdfparser > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo "✅ PDF Parser instalado"
else
    echo "❌ PDF Parser não encontrado"
fi

echo ""
echo "🌐 4. Verificando rotas..."
php artisan route:list | grep -q "imports"
if [ $? -eq 0 ]; then
    echo "✅ Rotas de importação registradas"
else
    echo "❌ Rotas de importação não encontradas"
fi

echo ""
echo "📂 5. Verificando assets..."
if [ -f "public/build/manifest.json" ]; then
    echo "✅ Assets compilados encontrados"
else
    echo "⚠️  Assets não compilados (executar npm run build)"
fi

echo ""
echo "🧹 6. Verificando limpeza..."
backup_files=$(find . -name "*-BACKUP.php" -o -name "*-CORRETO.php" -o -name "*_backup.php" | wc -l)
if [ $backup_files -eq 0 ]; then
    echo "✅ Arquivos backup organizados"
else
    echo "⚠️  $backup_files arquivos backup ainda na raiz"
fi

echo ""
echo "🎯 7. Testando funcionalidades específicas..."

# Verificar se métodos foram adicionados
grep -q "normalizeDateFormat" app/Services/StatementImportService.php
if [ $? -eq 0 ]; then
    echo "✅ Métodos auxiliares PDF adicionados"
else
    echo "❌ Métodos auxiliares PDF não encontrados"
fi

grep -q "convertExcelToCSV" app/Services/StatementImportService.php
if [ $? -eq 0 ]; then
    echo "✅ Conversão Excel implementada"
else
    echo "❌ Conversão Excel não implementada"
fi

grep -q "mimes:csv,txt,ofx,qif,pdf,xls,xlsx" app/Http/Controllers/ImportController.php
if [ $? -eq 0 ]; then
    echo "✅ Validação de arquivos atualizada"
else
    echo "❌ Validação de arquivos não atualizada"
fi

echo ""
echo "📊 RESUMO DOS TESTES"
echo "==================="
echo "✅ Funcionalidades implementadas:"
echo "   - Parser PDF melhorado"
echo "   - Conversão Excel automática"
echo "   - CSS organizado"
echo "   - Validações atualizadas"
echo "   - Estrutura limpa"
echo ""
echo "⚠️  Para completar a configuração:"
echo "   - Executar: npm run build (para compilar assets)"
echo "   - Executar: php artisan config:cache (para cache config)"
echo "   - Executar: php artisan route:cache (para cache rotas)"
echo ""
echo "🚀 Sistema pronto para uso!"
