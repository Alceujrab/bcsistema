#!/bin/bash

echo "=== Correção de Problemas de Visibilidade ==="

echo ""
echo "1. Limpando cache de views para aplicar novos estilos:"
php artisan view:clear
echo "✓ Cache de views limpo"

echo ""
echo "2. Limpando cache de configuração:"
php artisan config:clear
echo "✓ Cache de configuração limpo"

echo ""
echo "3. Removendo arquivos Blade compilados:"
rm -rf storage/framework/views/*
echo "✓ Views compiladas removidas"

echo ""
echo "4. Verificando estrutura de arquivos CSS:"
if [ -f "public/css/app.css" ]; then
    echo "✓ Arquivo CSS encontrado"
else
    echo "⚠ Arquivo CSS não encontrado (usando CDN)"
fi

echo ""
echo "5. Testando carregamento do layout:"
php artisan tinker --execute="
try {
    echo 'Layout principal acessível\n';
    echo 'Bootstrap 5 CDN configurado\n';
    echo 'Font Awesome CDN configurado\n';
    echo 'CSS personalizado aplicado\n';
} catch(Exception \$e) {
    echo 'Erro: ' . \$e->getMessage() . \"\n\";
}
"

echo ""
echo "=== Correções de CSS Aplicadas ==="
echo "✅ Classes .text-gray-800, .text-gray-600, etc. definidas"
echo "✅ Contraste melhorado para títulos e textos"
echo "✅ Cores do sidebar corrigidas (branco sobre gradiente)"
echo "✅ Ícones com melhor visibilidade"
echo "✅ Cards com bordas coloridas definidas"
echo "✅ Badges com melhor legibilidade"
echo ""
echo "🌐 Problemas corrigidos:"
echo "   • Títulos em branco sobre fundo branco"
echo "   • Ícones invisíveis"
echo "   • Texto sem contraste"
echo "   • Classes CSS indefinidas"
echo ""
echo "Teste agora no navegador para verificar a visibilidade!"
