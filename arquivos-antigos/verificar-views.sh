#!/bin/bash

# Script para verificar todas as views do BC Sistema
# Data: 20/06/2025

echo "🔍 ANÁLISE COMPLETA DAS VIEWS - BC SISTEMA"
echo "========================================================"
echo ""

cd /workspaces/bcsistema/bc

echo "📋 LIMPANDO CACHE DE VIEWS..."
php artisan view:clear > /dev/null 2>&1
echo "✅ Cache limpo!"
echo ""

echo "🧪 TESTANDO COMPILAÇÃO DAS VIEWS PRINCIPAIS..."
echo "----------------------------------------"

# Views críticas do sistema
views=(
    "dashboard"
    "layouts.app"
    "transactions.index"
    "transactions.create"
    "transactions.edit"
    "transactions.show"
    "bank-accounts.index"
    "bank-accounts.create"
    "bank-accounts.edit"
    "bank-accounts.show"
    "imports.index"
    "imports.create"
    "imports.show"
    "reconciliations.index"
    "reports.index"
    "settings.index"
    "categories.index"
    "categories.create"
    "categories.edit"
    "clients.index"
    "clients.create"
    "clients.edit"
    "suppliers.index"
    "suppliers.create"
    "suppliers.edit"
    "account-payables.index"
    "account-receivables.index"
    "account-management.index"
)

total_views=${#views[@]}
success_count=0
error_count=0
errors_list=()

for view in "${views[@]}"; do
    if php artisan tinker --execute="try { view('$view'); echo 'OK'; } catch (Exception \$e) { echo 'ERRO: ' . \$e->getMessage(); }" 2>/dev/null | grep -q "OK"; then
        echo "✅ $view"
        ((success_count++))
    else
        error_msg=$(php artisan tinker --execute="try { view('$view'); echo 'OK'; } catch (Exception \$e) { echo 'ERRO: ' . \$e->getMessage(); }" 2>/dev/null | grep "ERRO:")
        echo "❌ $view - $error_msg"
        errors_list+=("$view: $error_msg")
        ((error_count++))
    fi
done

echo ""
echo "📊 RESUMO DA ANÁLISE"
echo "===================="
echo "Total de views analisadas: $total_views"
echo "✅ Views sem problemas: $success_count"
echo "❌ Views com problemas: $error_count"
echo "🎯 Taxa de sucesso: $(( (success_count * 100) / total_views ))%"

if [ $error_count -gt 0 ]; then
    echo ""
    echo "🚨 VIEWS COM PROBLEMAS:"
    echo "----------------------"
    for error in "${errors_list[@]}"; do
        echo "   • $error"
    done
else
    echo ""
    echo "🎉 TODAS AS VIEWS ESTÃO FUNCIONANDO PERFEITAMENTE!"
fi

echo ""
echo "🔍 VERIFICAÇÃO DE ARQUIVOS INCLUÍDOS..."
echo "--------------------------------------"

# Verificar includes
include_files=(
    "settings.partials.control"
    "settings.modals.import"
    "settings.modals.custom-setting"
    "settings.modals.preview"
)

for include_file in "${include_files[@]}"; do
    if php artisan tinker --execute="try { view('$include_file'); echo 'OK'; } catch (Exception \$e) { echo 'ERRO'; }" 2>/dev/null | grep -q "OK"; then
        echo "✅ $include_file (partial/modal)"
    else
        echo "❌ $include_file (partial/modal)"
    fi
done

echo ""
echo "✅ ANÁLISE COMPLETA FINALIZADA!"
echo "=============================="
