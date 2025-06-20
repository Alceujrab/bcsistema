#!/bin/bash

echo "===================================="
echo "  VERIFICANDO ESTRUTURA BC SISTEMA"
echo "===================================="
echo

# Verificar se temos os arquivos do BC Sistema no workspace atual
PROJECT_PATH="/workspaces/bcsistema"

if [ -d "$PROJECT_PATH/bc" ]; then
    echo "✅ Pasta 'bc' encontrada no workspace"
    echo "Conteúdo da pasta bc:"
    echo "--------------------"
    ls -la "$PROJECT_PATH/bc/"
    echo
    
    echo "Verificando arquivos essenciais:"
    echo "--------------------------------"
    
    if [ -f "$PROJECT_PATH/bc/artisan" ]; then
        echo "✅ artisan - Encontrado"
    else
        echo "❌ artisan - NÃO encontrado"
    fi
    
    if [ -f "$PROJECT_PATH/bc/composer.json" ]; then
        echo "✅ composer.json - Encontrado"
    else
        echo "❌ composer.json - NÃO encontrado"
    fi
    
    if [ -d "$PROJECT_PATH/bc/app" ]; then
        echo "✅ app/ - Encontrado"
    else
        echo "❌ app/ - NÃO encontrado"
    fi
    
    if [ -d "$PROJECT_PATH/bc/public" ]; then
        echo "✅ public/ - Encontrado"
    else
        echo "❌ public/ - NÃO encontrado"
    fi
    
    if [ -d "$PROJECT_PATH/bc/resources" ]; then
        echo "✅ resources/ - Encontrado"
    else
        echo "❌ resources/ - NÃO encontrado"
    fi
    
    if [ -d "$PROJECT_PATH/bc/database" ]; then
        echo "✅ database/ - Encontrado"
    else
        echo "❌ database/ - NÃO encontrado"
    fi
    
    if [ -d "$PROJECT_PATH/bc/vendor" ]; then
        echo "✅ vendor/ - Encontrado"
    else
        echo "⚠️  vendor/ - NÃO encontrado (normal, será criado pelo Composer)"
    fi
    
    echo
    echo "===================================="
    echo "  RESUMO"
    echo "===================================="
    echo "✅ Arquivos do BC Sistema estão disponíveis"
    echo "📦 Pronto para ser copiado para o XAMPP"
    echo
    
else
    echo "❌ Pasta 'bc' não encontrada"
    echo
    echo "Verificando arquivos de deploy disponíveis:"
    echo "-------------------------------------------"
    find "$PROJECT_PATH" -name "*deploy*.tar.gz" -o -name "*deploy*.zip" | head -5
    echo
fi

echo "===================================="
echo "  INSTRUÇÕES PARA XAMPP"
echo "===================================="
echo
echo "1. No Windows, acesse a pasta D:\\xampp\\htdocs\\"
echo "2. Crie a pasta: D:\\xampp\\htdocs\\bc"
echo "3. Extraia o arquivo de deploy para essa pasta"
echo "4. Execute os scripts de instalação"
echo
echo "Scripts disponíveis para Windows:"
echo "- verificar-pasta-bc.bat"
echo "- acessar-pasta-bc.bat"  
echo "- instalar-bc-completo.bat"
echo
