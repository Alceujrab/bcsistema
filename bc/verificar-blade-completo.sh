#!/bin/bash

echo "=== VERIFICAÇÃO COMPLETA DE ARQUIVOS BLADE ==="
echo "Verificando integridade da estrutura @push/@endpush em todos os arquivos .blade.php"
echo

# Contador de problemas
PROBLEMS=0

# Função para verificar um arquivo
check_blade_file() {
    local file="$1"
    local filename=$(basename "$file")
    
    # Contar @push e @endpush
    local push_count=$(grep -c "@push" "$file")
    local endpush_count=$(grep -c "@endpush" "$file")
    
    echo -n "Verificando $filename... "
    
    if [ "$push_count" -eq "$endpush_count" ]; then
        echo "✓ OK ($push_count push/$endpush_count endpush)"
    else
        echo "❌ ERRO ($push_count push/$endpush_count endpush)"
        echo "  Arquivo: $file"
        echo "  @push: $push_count, @endpush: $endpush_count"
        echo
        PROBLEMS=$((PROBLEMS + 1))
    fi
}

# Verificar todos os arquivos .blade.php
echo "Verificando arquivos em resources/views/..."
echo

find resources/views -name "*.blade.php" | while read file; do
    check_blade_file "$file"
done

echo
if [ $PROBLEMS -eq 0 ]; then
    echo "✅ Todos os arquivos estão corretos!"
else
    echo "❌ Encontrados $PROBLEMS arquivos com problemas"
fi

echo
echo "=== VERIFICAÇÃO DE SINTAXE BLADE ==="
php artisan view:clear
echo "Cache de views limpo para recompilação"
