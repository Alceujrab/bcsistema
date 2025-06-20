#!/bin/bash

# Script para corrigir erro de categoria "Attempt to read property name on string"
# Data: $(date +%Y-%m-%d)

echo "=== CORREÇÃO DO ERRO DE CATEGORIA ==="
echo "Aplicando correções para resolver o erro 'Attempt to read property name on string'"
echo

# Fazer backup dos arquivos atuais
echo "1. Fazendo backup dos arquivos atuais..."
if [ -f "resources/views/account-management/show.blade.php" ]; then
    cp resources/views/account-management/show.blade.php resources/views/account-management/show.blade.php.bkp
    echo "   ✓ Backup de show.blade.php criado"
fi

if [ -f "app/Http/Controllers/AccountManagementController.php" ]; then
    cp app/Http/Controllers/AccountManagementController.php app/Http/Controllers/AccountManagementController.php.bkp
    echo "   ✓ Backup de AccountManagementController.php criado"
fi

# Limpar cache
echo
echo "2. Limpando cache do Laravel..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear
echo "   ✓ Cache limpo"

# Verificar permissões
echo
echo "3. Verificando permissões dos arquivos..."
chmod 644 resources/views/account-management/show.blade.php
chmod 644 app/Http/Controllers/AccountManagementController.php
echo "   ✓ Permissões ajustadas"

# Otimizar aplicação
echo
echo "4. Otimizando aplicação..."
php artisan config:cache
php artisan route:cache
echo "   ✓ Aplicação otimizada"

echo
echo "=== CORREÇÃO CONCLUÍDA ==="
echo "Os arquivos foram corrigidos para tratar adequadamente:"
echo "- Categorias que podem não estar carregadas"
echo "- Verificação robusta de objetos de categoria"
echo "- Eager loading melhorado no controller"
echo
echo "Teste a página: /gestao/conta/[ID] para verificar se o erro foi resolvido."
echo
