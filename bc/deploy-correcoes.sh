#!/bin/bash

echo "=== DEPLOY DAS CORREÇÕES CSS E IMPORTAÇÃO - BC SISTEMA ==="
echo "Data: $(date)"
echo ""

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    echo "❌ Erro: Execute este script no diretório raiz do Laravel"
    exit 1
fi

echo "1. Preparando arquivos para deploy..."

# Criar pasta de backup
BACKUP_DIR="backup-pre-deploy-$(date +%Y%m%d_%H%M%S)"
mkdir -p $BACKUP_DIR

echo "2. Fazendo backup dos arquivos principais..."
cp -r app/Http/Controllers/ImportController.php $BACKUP_DIR/ 2>/dev/null
cp -r app/Services/StatementImportService.php $BACKUP_DIR/ 2>/dev/null
cp -r resources/views/layouts/app.blade.php $BACKUP_DIR/ 2>/dev/null
cp -r resources/views/dashboard.blade.php $BACKUP_DIR/ 2>/dev/null
cp -r resources/views/settings/index.blade.php $BACKUP_DIR/ 2>/dev/null
cp -r resources/views/imports/ $BACKUP_DIR/ 2>/dev/null
cp -r resources/views/reconciliations/ $BACKUP_DIR/ 2>/dev/null

echo "3. Limpando cache e otimizando..."
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan cache:clear

echo "4. Otimizando para produção..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "5. Definindo permissões..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chown -R www-data:www-data storage/ bootstrap/cache/ 2>/dev/null || echo "   (Executar como root para aplicar chown)"

echo "6. Verificando integridade dos arquivos..."
php -l app/Http/Controllers/ImportController.php
php -l app/Services/StatementImportService.php

echo ""
echo "✅ DEPLOY CONCLUÍDO!"
echo ""
echo "📁 Backup criado em: $BACKUP_DIR"
echo ""
echo "🚀 Próximos passos no servidor:"
echo "1. Fazer backup do servidor atual"
echo "2. Enviar estes arquivos via FTP/SSH"
echo "3. Executar os comandos de otimização"
echo "4. Testar as funcionalidades"
echo ""
echo "📋 Correções implementadas:"
echo "✅ Notificações com tempo estendido (8s) + pausa no hover"
echo "✅ Botões de configuração com contraste visual"
echo "✅ Suporte completo a importação PDF/Excel (20MB)"
echo "✅ CSS consistente em todas as views"
echo "✅ Animações e transições suaves"
echo "✅ Design system BC unificado"
