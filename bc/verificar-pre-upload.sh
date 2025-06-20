#!/bin/bash

echo "=== VERIFICAÇÃO PRÉ-UPLOAD ==="
echo "Verificando se todos os arquivos estão prontos para envio ao servidor"
echo ""

SUCESSO=true

# Verificar se o pacote de deploy existe
echo "1. Verificando pacote de deploy..."
if [ -f "bc-sistema-correcoes-css-20250619_233924.tar.gz" ]; then
    echo "   ✅ Pacote de deploy encontrado"
    echo "   📦 Tamanho: $(du -h bc-sistema-correcoes-css-20250619_233924.tar.gz | cut -f1)"
else
    echo "   ❌ Pacote de deploy não encontrado!"
    echo "   Execute: ./criar-pacote-deploy.sh"
    SUCESSO=false
fi

# Verificar arquivos principais
echo ""
echo "2. Verificando arquivos principais..."

ARQUIVOS=(
    "app/Http/Controllers/ImportController.php"
    "app/Services/StatementImportService.php"
    "resources/views/layouts/app.blade.php"
    "resources/views/dashboard.blade.php"
    "resources/views/settings/index.blade.php"
    "resources/views/imports/index.blade.php"
    "resources/views/imports/create.blade.php"
    "resources/views/imports/show.blade.php"
    "resources/views/reconciliations/index.blade.php"
    "resources/views/reconciliations/create.blade.php"
    "resources/views/reconciliations/show.blade.php"
)

for arquivo in "${ARQUIVOS[@]}"; do
    if [ -f "$arquivo" ]; then
        echo "   ✅ $arquivo"
    else
        echo "   ❌ $arquivo - FALTANDO!"
        SUCESSO=false
    fi
done

# Verificar scripts de deploy
echo ""
echo "3. Verificando scripts de deploy..."

SCRIPTS=(
    "deploy-correcoes.sh"
    "teste-correcoes-css.sh"
    "teste-consistencia-css.sh"
    "preparar-upload.sh"
)

for script in "${SCRIPTS[@]}"; do
    if [ -f "$script" ]; then
        if [ -x "$script" ]; then
            echo "   ✅ $script (executável)"
        else
            echo "   ⚠️  $script (sem permissão de execução)"
            chmod +x "$script"
            echo "      → Permissão corrigida"
        fi
    else
        echo "   ❌ $script - FALTANDO!"
        SUCESSO=false
    fi
done

# Verificar sintaxe PHP
echo ""
echo "4. Verificando sintaxe dos arquivos PHP..."

for arquivo in "${ARQUIVOS[@]}"; do
    if [[ "$arquivo" == *.php ]]; then
        if [ -f "$arquivo" ]; then
            if php -l "$arquivo" > /dev/null 2>&1; then
                echo "   ✅ $arquivo - Sintaxe OK"
            else
                echo "   ❌ $arquivo - ERRO DE SINTAXE!"
                php -l "$arquivo"
                SUCESSO=false
            fi
        fi
    fi
done

# Verificar documentação
echo ""
echo "5. Verificando documentação..."

DOCS=(
    "../UPLOAD-SERVIDOR-SIMPLES.md"
    "../GUIA-UPLOAD-SERVIDOR.md"
    "../CORRECOES-CSS-IMPORTACAO-COMPLETAS.md"
)

for doc in "${DOCS[@]}"; do
    if [ -f "$doc" ]; then
        echo "   ✅ $(basename $doc)"
    else
        echo "   ⚠️  $(basename $doc) - Faltando (opcional)"
    fi
done

# Resultado final
echo ""
echo "==============================================="

if [ "$SUCESSO" = true ]; then
    echo "✅ TUDO PRONTO PARA UPLOAD!"
    echo ""
    echo "Próximos passos:"
    echo "1. Execute: ./preparar-upload.sh"
    echo "2. Escolha seu método de upload preferido"
    echo "3. Siga as instruções do script"
    echo ""
    echo "📁 Arquivo principal: bc-sistema-correcoes-css-20250619_233924.tar.gz"
    echo ""
else
    echo "❌ PROBLEMAS ENCONTRADOS!"
    echo ""
    echo "Corrija os erros acima antes de fazer o upload."
    echo ""
fi

echo "==============================================="
