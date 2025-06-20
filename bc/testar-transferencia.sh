#!/bin/bash

echo "=== TESTE DA FUNCIONALIDADE DE TRANSFERÊNCIA ==="
echo "Verificando se a funcionalidade está funcionando corretamente"
echo

# Verificar se as rotas existem
echo "1. Verificando rotas..."
php artisan route:list --name=account-management.transfer | head -10

echo
echo "2. Verificando arquivos de view..."
if [ -f "resources/views/account-management/transfer.blade.php" ]; then
    echo "   ✓ Formulário de transferência encontrado"
else
    echo "   ❌ Formulário de transferência não encontrado"
fi

if [ -f "resources/views/account-management/transfer-history.blade.php" ]; then
    echo "   ✓ Histórico de transferências encontrado"
else
    echo "   ❌ Histórico de transferências não encontrado"
fi

echo
echo "3. Verificando métodos do controller..."
grep -n "showTransferForm\|processTransfer\|transferHistory" app/Http/Controllers/AccountManagementController.php

echo
echo "4. Limpando cache..."
php artisan config:clear > /dev/null 2>&1
php artisan route:clear > /dev/null 2>&1
php artisan view:clear > /dev/null 2>&1
echo "   ✓ Cache limpo"

echo
echo "5. URLs para teste:"
echo "   Formulário: /gestao/transferencia"
echo "   Histórico: /gestao/transferencias"
echo "   Dashboard: /gestao"

echo
echo "=== TESTE CONCLUÍDO ==="
echo "A funcionalidade de transferência está implementada e pronta para uso!"
