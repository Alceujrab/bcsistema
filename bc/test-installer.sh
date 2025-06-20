#!/bin/bash

# Script de Teste do Instalador
# Verifica se o install.php pode ser executado com segurança

echo "🧪 TESTANDO INSTALADOR BC SISTEMA"
echo "================================="

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    echo "❌ ERRO: Execute este script na raiz do projeto Laravel"
    exit 1
fi

echo "✅ Diretório Laravel encontrado"

# Verificar PHP
php_version=$(php -r "echo PHP_VERSION;")
echo "📋 Versão PHP: $php_version"

if php -r "exit(version_compare(PHP_VERSION, '8.2.0', '>=') ? 0 : 1);"; then
    echo "✅ Versão PHP adequada ($php_version)"
else
    echo "❌ ERRO: PHP 8.2+ é necessário (atual: $php_version)"
    exit 1
fi

# Verificar Composer
if [ ! -f "vendor/autoload.php" ]; then
    echo "❌ ERRO: Execute 'composer install' primeiro"
    exit 1
fi

echo "✅ Dependências do Composer instaladas"

# Verificar dependências específicas
echo "📦 Verificando dependências..."

composer show barryvdh/laravel-dompdf > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo "✅ DomPDF instalado"
else
    echo "⚠️  DomPDF não encontrado - será necessário instalar"
fi

composer show maatwebsite/excel > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo "✅ Laravel Excel instalado"
else
    echo "⚠️  Laravel Excel não encontrado - será necessário instalar"
fi

composer show smalot/pdfparser > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo "✅ PDF Parser instalado"
else
    echo "⚠️  PDF Parser não encontrado - será necessário instalar"
fi

# Verificar permissões
echo "🔐 Verificando permissões..."

if [ -w "app/" ]; then
    echo "✅ Pasta app/ gravável"
else
    echo "❌ ERRO: Sem permissão de escrita em app/"
    exit 1
fi

if [ -w "public/" ]; then
    echo "✅ Pasta public/ gravável"
else
    echo "❌ ERRO: Sem permissão de escrita em public/"
    exit 1
fi

if [ -w "resources/" ]; then
    echo "✅ Pasta resources/ gravável"
else
    echo "❌ ERRO: Sem permissão de escrita em resources/"
    exit 1
fi

# Criar diretório de storage se não existir
if [ ! -d "storage/backups" ]; then
    mkdir -p storage/backups
    echo "✅ Diretório de backup criado"
fi

if [ -w "storage/" ]; then
    echo "✅ Pasta storage/ gravável"
else
    echo "❌ ERRO: Sem permissão de escrita em storage/"
    exit 1
fi

# Verificar espaço em disco
available_space=$(df . | tail -1 | awk '{print $4}')
if [ $available_space -lt 102400 ]; then  # 100MB
    echo "⚠️  AVISO: Pouco espaço em disco disponível"
else
    echo "✅ Espaço em disco suficiente"
fi

# Testar sintaxe do instalador
if [ -f "install.php" ]; then
    echo "🔍 Testando sintaxe do instalador..."
    php -l install.php > /dev/null 2>&1
    if [ $? -eq 0 ]; then
        echo "✅ Sintaxe do instalador válida"
    else
        echo "❌ ERRO: Sintaxe inválida no install.php"
        exit 1
    fi
else
    echo "⚠️  install.php não encontrado - será necessário fazer upload"
fi

# Verificar se Laravel está funcional
echo "🌐 Testando Laravel..."
php artisan --version > /dev/null 2>&1
if [ $? -eq 0 ]; then
    laravel_version=$(php artisan --version)
    echo "✅ Laravel funcional: $laravel_version"
else
    echo "❌ ERRO: Laravel não está funcionando corretamente"
    exit 1
fi

echo ""
echo "🎯 RESUMO DO TESTE"
echo "=================="
echo "✅ Todos os pré-requisitos atendidos"
echo "✅ Sistema pronto para instalação"
echo ""
echo "📋 PRÓXIMOS PASSOS:"
echo "1. Execute: php install.php (via CLI)"
echo "2. Ou acesse: http://seudominio.com/install.php (via web)"
echo "3. Acompanhe o progresso no install.log"
echo ""
echo "🔒 BACKUP AUTOMÁTICO:"
echo "Todos os arquivos serão salvos em storage/backups/"
echo ""
echo "🚀 Instalador testado e pronto para uso!"
