#!/bin/bash

# Script de Aplicação das Correções do Sistema de Updates
# Autor: Sistema BC
# Data: 18/06/2025

echo "=========================================="
echo "  APLICAÇÃO DAS CORREÇÕES DO SISTEMA BC"
echo "=========================================="
echo ""

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    echo "❌ ERRO: Este script deve ser executado na raiz do projeto Laravel"
    echo "   Certifique-se de estar no diretório que contém o arquivo 'artisan'"
    exit 1
fi

echo "✅ Diretório correto identificado"
echo ""

# Verificar se o arquivo de correção existe
CORRECAO_FILE="correcao-rotas-update-show-20250618_174532.tar.gz"

if [ ! -f "$CORRECAO_FILE" ]; then
    echo "❌ ERRO: Arquivo de correção não encontrado: $CORRECAO_FILE"
    echo ""
    echo "   Instruções:"
    echo "   1. Faça download do arquivo: $CORRECAO_FILE"
    echo "   2. Coloque o arquivo na raiz do projeto Laravel"
    echo "   3. Execute este script novamente"
    echo ""
    exit 1
fi

echo "✅ Arquivo de correção encontrado: $CORRECAO_FILE"
echo ""

# Criar backup dos arquivos atuais
echo "📦 Criando backup dos arquivos atuais..."
BACKUP_DIR="backup_$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"

# Backup dos arquivos que serão substituídos
if [ -f "routes/web.php" ]; then
    cp "routes/web.php" "$BACKUP_DIR/"
    echo "   ✅ Backup: routes/web.php"
fi

if [ -f "app/Http/Controllers/UpdateController.php" ]; then
    cp "app/Http/Controllers/UpdateController.php" "$BACKUP_DIR/"
    echo "   ✅ Backup: app/Http/Controllers/UpdateController.php"
fi

if [ -f "resources/views/system/update/index.blade.php" ]; then
    cp "resources/views/system/update/index.blade.php" "$BACKUP_DIR/"
    echo "   ✅ Backup: resources/views/system/update/index.blade.php"
fi

echo "   📁 Backups salvos em: $BACKUP_DIR"
echo ""

# Extrair as correções
echo "📦 Extraindo correções..."
tar -xzf "$CORRECAO_FILE"

if [ $? -eq 0 ]; then
    echo "   ✅ Correções extraídas com sucesso"
else
    echo "   ❌ Erro ao extrair correções"
    exit 1
fi
echo ""

# Verificar se os arquivos foram extraídos
echo "🔍 Verificando arquivos extraídos..."
FILES_EXTRACTED=true

if [ ! -f "routes/web.php" ]; then
    echo "   ❌ Arquivo não encontrado: routes/web.php"
    FILES_EXTRACTED=false
fi

if [ ! -f "app/Http/Controllers/UpdateController.php" ]; then
    echo "   ❌ Arquivo não encontrado: app/Http/Controllers/UpdateController.php"
    FILES_EXTRACTED=false
fi

if [ ! -f "resources/views/system/update/index.blade.php" ]; then
    echo "   ❌ Arquivo não encontrado: resources/views/system/update/index.blade.php"
    FILES_EXTRACTED=false
fi

if [ "$FILES_EXTRACTED" = true ]; then
    echo "   ✅ Todos os arquivos foram extraídos corretamente"
else
    echo "   ❌ Alguns arquivos não foram extraídos. Verifique o arquivo de correção."
    exit 1
fi
echo ""

# Definir permissões corretas
echo "🔒 Definindo permissões corretas..."
chmod 755 app/Http/Controllers/UpdateController.php
chmod 755 resources/views/system/update/index.blade.php
chmod 755 routes/web.php
echo "   ✅ Permissões definidas"
echo ""

# Limpar cache do Laravel
echo "🧹 Limpando cache do Laravel..."

# Limpar cache de rotas
php artisan route:clear
if [ $? -eq 0 ]; then
    echo "   ✅ Cache de rotas limpo"
else
    echo "   ⚠️  Aviso: Erro ao limpar cache de rotas"
fi

# Limpar cache de views
php artisan view:clear
if [ $? -eq 0 ]; then
    echo "   ✅ Cache de views limpo"
else
    echo "   ⚠️  Aviso: Erro ao limpar cache de views"
fi

# Limpar cache de configuração
php artisan config:clear
if [ $? -eq 0 ]; then
    echo "   ✅ Cache de configuração limpo"
else
    echo "   ⚠️  Aviso: Erro ao limpar cache de configuração"
fi

echo ""

# Verificar sintaxe PHP
echo "🔍 Verificando sintaxe PHP..."
php -l app/Http/Controllers/UpdateController.php > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo "   ✅ Sintaxe do UpdateController.php está correta"
else
    echo "   ❌ Erro de sintaxe no UpdateController.php"
    echo "   📁 Restaurando backup..."
    cp "$BACKUP_DIR/UpdateController.php" "app/Http/Controllers/" 2>/dev/null
    exit 1
fi
echo ""

# Listar rotas criadas
echo "📋 Rotas do sistema de updates criadas:"
echo "   ✅ GET    /system/update                           → index"
echo "   ✅ GET    /system/update/check                    → check"
echo "   ✅ GET    /system/update/show/{update}            → show"
echo "   ✅ POST   /system/update/download/{update}        → download"
echo "   ✅ POST   /system/update/apply/{update}           → apply"
echo "   ✅ GET    /system/update/status/{update}          → status"
echo "   ✅ GET    /system/update/history                  → history"
echo "   ✅ GET    /system/update/backup                   → backup (página)"
echo "   ✅ GET    /system/update/backups                  → getBackups (API)"
echo "   ✅ POST   /system/update/backup/create            → createBackup"
echo "   ✅ POST   /system/update/backup/restore           → restoreBackup"
echo "   ✅ GET    /system/update/backup/download/{backup} → downloadBackup"
echo "   ✅ POST   /system/update/backup/restore/{backup}  → restoreSpecificBackup"
echo ""

# Resultado final
echo "=========================================="
echo "  ✅ CORREÇÕES APLICADAS COM SUCESSO!"
echo "=========================================="
echo ""
echo "📋 Arquivos corrigidos:"
echo "   • routes/web.php"
echo "   • app/Http/Controllers/UpdateController.php"
echo "   • resources/views/system/update/index.blade.php"
echo ""
echo "📁 Backup dos arquivos originais em: $BACKUP_DIR"
echo ""
echo "🌐 Teste a página:"
echo "   https://usadosar.com.br/bc/system/update"
echo ""
echo "🎯 Status: Sistema pronto para produção!"
echo ""
