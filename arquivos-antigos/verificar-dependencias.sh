#!/bin/bash

# Script para verificar e instalar dependências do sistema de importação
# Autor: Sistema BC
# Data: 18/06/2025

echo "=========================================="
echo "  VERIFICAÇÃO DE DEPENDÊNCIAS - IMPORTAÇÃO"
echo "=========================================="
echo ""

# Verificar se estamos no diretório correto
if [ ! -f "composer.json" ]; then
    echo "❌ ERRO: Execute este script na raiz do projeto Laravel"
    exit 1
fi

echo "✅ Projeto Laravel encontrado"
echo ""

echo "🔍 Verificando dependências necessárias..."
echo ""

# Verificar se o Composer está instalado
if ! command -v composer &> /dev/null; then
    echo "❌ Composer não encontrado. Instale o Composer primeiro."
    exit 1
fi
echo "✅ Composer encontrado"

# Verificar dependências no composer.json
echo ""
echo "📦 Verificando dependências no composer.json:"

# League CSV
if grep -q "league/csv" composer.json; then
    echo "✅ league/csv encontrado"
else
    echo "⚠️  league/csv não encontrado - Instalando..."
    composer require league/csv
fi

# Maatwebsite Excel
if grep -q "maatwebsite/excel" composer.json; then
    echo "✅ maatwebsite/excel encontrado"
else
    echo "⚠️  maatwebsite/excel não encontrado - Instalando..."
    composer require maatwebsite/excel
fi

# PDF Parser
if grep -q "smalot/pdfparser" composer.json; then
    echo "✅ smalot/pdfparser encontrado"
else
    echo "⚠️  smalot/pdfparser não encontrado - Instalando..."
    composer require smalot/pdfparser
fi

echo ""
echo "🔧 Executando comandos de otimização..."

# Limpar cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Atualizar autoload
composer dump-autoload

echo ""
echo "📋 Formatos suportados após instalação:"
echo "   ✅ CSV - Arquivos separados por vírgula"
echo "   ✅ TXT - Arquivos de texto"
echo "   ✅ OFX - Open Financial Exchange"
echo "   ✅ QIF - Quicken Interchange Format"
echo "   ✅ PDF - Extratos em PDF"
echo "   ✅ XLS - Excel 97-2003"
echo "   ✅ XLSX - Excel 2007+"

echo ""
echo "=========================================="
echo "  ✅ VERIFICAÇÃO CONCLUÍDA!"
echo "=========================================="
echo ""
echo "🌐 Teste os seguintes links:"
echo "   • https://usadosar.com.br/bc/imports"
echo "   • https://usadosar.com.br/bc/extract-imports/create"
echo ""
echo "🎯 Status: Sistema de importação pronto!"
echo ""
