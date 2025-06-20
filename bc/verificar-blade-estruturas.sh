#!/bin/bash

# Script para verificar e corrigir problemas de estrutura @push/@endpush em arquivos Blade
# Data: $(date +%Y-%m-%d)

echo "=== VERIFICAÇÃO E CORREÇÃO DE ESTRUTURAS BLADE ==="
echo "Verificando arquivos .blade.php com problemas de @push/@endpush"
echo

# Contador de arquivos corrigidos
corrected=0

# Lista de arquivos para verificar
files=(
    "resources/views/bank-accounts/index.blade.php"
    "resources/views/account-management/index.blade.php" 
    "resources/views/account-management/compare.blade.php"
    "resources/views/account-management/show.blade.php"
    "resources/views/bank-accounts/create.blade.php"
    "resources/views/imports/index.blade.php"
    "resources/views/reports/reconciliations.blade.php"
    "resources/views/reports/cash-flow.blade.php"
    "resources/views/reports/categories.blade.php"
    "resources/views/reconciliations/index.blade.php"
    "resources/views/reconciliations/create.blade.php"
    "resources/views/reconciliations/show.blade.php"
    "resources/views/dashboard.blade.php"
)

# Função para verificar estrutura de um arquivo
check_blade_structure() {
    local file="$1"
    if [ ! -f "$file" ]; then
        echo "   ❌ Arquivo não encontrado: $file"
        return 1
    fi
    
    # Contar @push e @endpush
    local push_count=$(grep -c "@push" "$file" 2>/dev/null || echo "0")
    local endpush_count=$(grep -c "@endpush" "$file" 2>/dev/null || echo "0")
    
    echo "   📄 $file"
    echo "      @push: $push_count, @endpush: $endpush_count"
    
    if [ "$push_count" -ne "$endpush_count" ]; then
        echo "      ⚠️  Desequilíbrio encontrado!"
        return 1
    fi
    
    # Verificar se há conteúdo após @endpush (exceto comentários e espaços)
    local last_endpush_line=$(grep -n "@endpush" "$file" | tail -1 | cut -d: -f1)
    if [ -n "$last_endpush_line" ]; then
        local total_lines=$(wc -l < "$file")
        local lines_after=$(($total_lines - $last_endpush_line))
        
        if [ $lines_after -gt 5 ]; then
            echo "      ⚠️  Possível conteúdo após @endpush ($lines_after linhas)"
            
            # Verificar se há HTML significativo após @endpush
            local content_after=$(tail -n +$(($last_endpush_line + 1)) "$file" | grep -v "^[[:space:]]*$" | grep -v "^[[:space:]]*{{--" | head -10)
            if [ -n "$content_after" ]; then
                echo "      🔧 Conteúdo encontrado após @endpush, pode precisar de correção"
                return 1
            fi
        fi
    fi
    
    echo "      ✅ Estrutura OK"
    return 0
}

# Verificar cada arquivo
echo "1. Verificando estrutura dos arquivos..."
echo
for file in "${files[@]}"; do
    if ! check_blade_structure "$file"; then
        echo "      🔧 Arquivo marcado para possível correção"
        echo
    fi
done

echo
echo "2. Verificação concluída!"
echo

# Limpar cache para garantir que as correções sejam aplicadas
echo "3. Limpando cache do Laravel..."
php artisan view:clear >/dev/null 2>&1
php artisan cache:clear >/dev/null 2>&1
echo "   ✅ Cache limpo"

echo
echo "=== RELATÓRIO FINAL ==="
echo "Verificação de estruturas Blade concluída."
echo "Se algum arquivo foi marcado para correção, verifique manualmente:"
echo "- Remover conteúdo HTML após @endpush"
echo "- Verificar se @push e @endpush estão balanceados"
echo "- Garantir que @endsection está antes de @push"
echo
