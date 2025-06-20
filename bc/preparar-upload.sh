#!/bin/bash

echo "=== PREPARAR UPLOAD PARA SERVIDOR ==="
echo "Script automatizado para facilitar upload das correções"
echo ""

# Verificar se o arquivo de deploy existe
ARQUIVO_DEPLOY="bc-sistema-correcoes-css-20250619_233924.tar.gz"

if [ ! -f "$ARQUIVO_DEPLOY" ]; then
    echo "❌ Arquivo $ARQUIVO_DEPLOY não encontrado!"
    echo "Execute primeiro o script: criar-pacote-deploy.sh"
    exit 1
fi

echo "✅ Arquivo de deploy encontrado: $ARQUIVO_DEPLOY"
echo "📦 Tamanho: $(du -h $ARQUIVO_DEPLOY | cut -f1)"
echo ""

echo "ESCOLHA O MÉTODO DE UPLOAD:"
echo ""
echo "1) 📁 FTP/SFTP (FileZilla, WinSCP)"
echo "2) 🔒 SCP via SSH"
echo "3) 📋 Manual (copiar arquivos individuais)"
echo "4) 🔄 Git (se tiver repositório)"
echo ""

read -p "Digite sua opção (1-4): " opcao

case $opcao in
    1)
        echo ""
        echo "=== MÉTODO FTP/SFTP ==="
        echo ""
        echo "1. Baixe este arquivo para seu computador:"
        echo "   📁 $PWD/$ARQUIVO_DEPLOY"
        echo ""
        echo "2. Conecte no seu servidor via FTP/SFTP"
        echo ""
        echo "3. Envie o arquivo para o diretório do seu site Laravel"
        echo ""
        echo "4. No servidor, execute os comandos:"
        echo ""
        echo "   tar -xzf $ARQUIVO_DEPLOY"
        echo "   cd bc-sistema-correcoes-css-20250619_233924"
        echo "   chmod +x scripts/deploy-correcoes.sh"
        echo "   ./scripts/deploy-correcoes.sh"
        echo ""
        ;;
    2)
        echo ""
        echo "=== MÉTODO SCP/SSH ==="
        echo ""
        read -p "Digite o usuário do servidor: " usuario
        read -p "Digite o domínio/IP do servidor: " servidor
        read -p "Digite o caminho do site no servidor: " caminho
        echo ""
        echo "Execute estes comandos no seu terminal local:"
        echo ""
        echo "# Enviar arquivo"
        echo "scp $ARQUIVO_DEPLOY $usuario@$servidor:$caminho/"
        echo ""
        echo "# Conectar e instalar"
        echo "ssh $usuario@$servidor"
        echo "cd $caminho"
        echo "tar -xzf $ARQUIVO_DEPLOY"
        echo "cd bc-sistema-correcoes-css-20250619_233924"
        echo "chmod +x scripts/deploy-correcoes.sh"
        echo "./scripts/deploy-correcoes.sh"
        echo ""
        ;;
    3)
        echo ""
        echo "=== MÉTODO MANUAL ==="
        echo ""
        echo "Copie estes arquivos para o servidor:"
        echo ""
        find arquivos/ -name "*.php" -o -name "*.blade.php" | sort
        echo ""
        echo "Depois execute no servidor:"
        echo "php artisan config:clear"
        echo "php artisan view:clear"
        echo "php artisan cache:clear"
        echo "chmod -R 755 storage/"
        echo "chmod -R 755 bootstrap/cache/"
        echo ""
        ;;
    4)
        echo ""
        echo "=== MÉTODO GIT ==="
        echo ""
        echo "Se seu projeto está no Git, execute:"
        echo ""
        echo "# No workspace atual:"
        echo "git add ."
        echo "git commit -m \"Correções CSS e Importação PDF/Excel\""
        echo "git push origin main"
        echo ""
        echo "# No servidor:"
        echo "git pull origin main"
        echo "chmod +x deploy-correcoes.sh"
        echo "./deploy-correcoes.sh"
        echo ""
        ;;
    *)
        echo "❌ Opção inválida!"
        exit 1
        ;;
esac

echo "📋 CHECKLIST PÓS-UPLOAD:"
echo "□ Site carrega normalmente"
echo "□ Dashboard com novo visual"
echo "□ Configurações com botões contrastantes"
echo "□ Importação aceita PDF/Excel"
echo "□ Notificações funcionando (8s, pausa no hover)"
echo ""
echo "🆘 Em caso de problemas:"
echo "   php artisan config:clear"
echo "   php artisan view:clear"
echo "   php artisan cache:clear"
echo ""
echo "✅ Upload preparado com sucesso!"
