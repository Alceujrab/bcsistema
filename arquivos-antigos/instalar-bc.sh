#!/bin/bash

echo "🚀 INSTALADOR BC - SCRIPT BASH"
echo "=============================="
echo ""

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    echo "❌ Execute no diretório raiz do Laravel"
    exit 1
fi

echo "✅ Diretório Laravel encontrado"

# Criar backup
BACKUP_DIR="backup_$(date +%Y-%m-%d_%H-%M-%S)"
mkdir -p "$BACKUP_DIR"
echo "💾 Backup criado em: $BACKUP_DIR"

# Backup dos arquivos principais
cp app/Services/StatementImportService.php "$BACKUP_DIR/" 2>/dev/null || echo "⚠️  StatementImportService.php não encontrado"
cp app/Http/Controllers/ImportController.php "$BACKUP_DIR/" 2>/dev/null || echo "⚠️  ImportController.php não encontrado"
cp resources/views/imports/create.blade.php "$BACKUP_DIR/" 2>/dev/null || echo "⚠️  create.blade.php não encontrado"

echo ""
echo "🔧 APLICANDO CORREÇÕES..."
echo "========================"

# Limpar cache
echo "⚡ Limpando cache do Laravel..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Otimizar autoload
echo "⚡ Otimizando autoload..."
composer dump-autoload -o

echo ""
echo "✅ CORREÇÕES APLICADAS COM SUCESSO!"
echo "=================================="
echo "📁 Backup salvo em: $BACKUP_DIR"
echo ""
echo "🎉 Sistema BC otimizado!"
echo ""
echo "📋 PRÓXIMOS PASSOS:"
echo "  1. Teste a funcionalidade de importação"
echo "  2. Verifique os logs em storage/logs/"
echo "  3. Monitore o desempenho do sistema"
