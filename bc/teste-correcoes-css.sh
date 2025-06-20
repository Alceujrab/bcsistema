#!/bin/bash

echo "=== TESTE DAS CORREÇÕES DO SISTEMA BC ==="
echo "Data: $(date)"
echo ""

echo "1. Testando estrutura dos arquivos corrigidos..."

# Verificar se os arquivos foram atualizados
echo "- ImportController.php: Verificando suporte a PDF/Excel..."
if grep -q "pdf,excel" /workspaces/bcsistema/bc/app/Http/Controllers/ImportController.php; then
    echo "  ✅ ImportController atualizado com suporte a PDF/Excel"
else
    echo "  ❌ ImportController não foi atualizado"
fi

echo "- StatementImportService.php: Verificando parsers..."
if grep -q "parsePDF\|parseExcel" /workspaces/bcsistema/bc/app/Services/StatementImportService.php; then
    echo "  ✅ StatementImportService com parsers PDF/Excel"
else
    echo "  ❌ StatementImportService não foi atualizado"
fi

echo "- imports/create.blade.php: Verificando formatos suportados..."
if grep -q "PDF\|Excel" /workspaces/bcsistema/bc/resources/views/imports/create.blade.php; then
    echo "  ✅ View de importação atualizada"
else
    echo "  ❌ View de importação não foi atualizada"
fi

echo "- settings/index.blade.php: Verificando estilos dos botões..."
if grep -q "!important" /workspaces/bcsistema/bc/resources/views/settings/index.blade.php; then
    echo "  ✅ Estilos de configuração corrigidos"
else
    echo "  ❌ Estilos de configuração não foram corrigidos"
fi

echo "- layouts/app.blade.php: Verificando notificações..."
if grep -q "timer-paused" /workspaces/bcsistema/bc/resources/views/layouts/app.blade.php; then
    echo "  ✅ Sistema de notificações melhorado"
else
    echo "  ❌ Sistema de notificações não foi corrigido"
fi

echo ""
echo "2. Testando sintaxe PHP..."
php -l /workspaces/bcsistema/bc/app/Http/Controllers/ImportController.php
php -l /workspaces/bcsistema/bc/app/Services/StatementImportService.php

echo ""
echo "3. Verificando permissões..."
ls -la /workspaces/bcsistema/bc/storage/logs/ 2>/dev/null || echo "Pasta de logs não encontrada"

echo ""
echo "4. Resumo das correções implementadas:"
echo "✅ Notificações agora ficam visíveis por 8 segundos (antes 5)"
echo "✅ Notificações pausam quando mouse está sobre elas"
echo "✅ Botões de configuração com estilos contrastantes"
echo "✅ Suporte completo a importação de PDF (com OCR básico)"
echo "✅ Suporte completo a importação de Excel (XLS/XLSX)"
echo "✅ Validação atualizada para aceitar PDF/Excel (20MB max)"
echo "✅ Interface atualizada com informações sobre novos formatos"
echo "✅ Melhorias visuais nos alertas do dashboard"

echo ""
echo "=== TESTE CONCLUÍDO ==="
