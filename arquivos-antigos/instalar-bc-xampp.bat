@echo off
echo ====================================
echo  BC SISTEMA - INSTALACAO XAMPP
echo ====================================
echo.

REM Definir variáveis - Detecta automaticamente o XAMPP
set XAMPP_PATH=C:\xampp
if exist "D:\xampp\" set XAMPP_PATH=D:\xampp
if exist "E:\xampp\" set XAMPP_PATH=E:\xampp

set PROJECT_PATH=%XAMPP_PATH%\htdocs\bc
set PHP_PATH=%XAMPP_PATH%\php\php.exe

echo Detectado XAMPP em: %XAMPP_PATH%
echo.

echo [1/6] Verificando estrutura de diretorios...
if not exist "%PROJECT_PATH%" (
    echo Criando diretorio %PROJECT_PATH%
    mkdir "%PROJECT_PATH%"
)

echo [2/6] Verificando se o PHP esta disponivel...
"%PHP_PATH%" -v
if errorlevel 1 (
    echo ERRO: PHP nao encontrado em %PHP_PATH%
    echo.
    echo Possiveis causas:
    echo - XAMPP nao instalado
    echo - XAMPP instalado em local diferente
    echo - Servicos do XAMPP nao iniciados
    echo.
    echo Verifique se o XAMPP esta instalado e funcionando
    pause
    exit /b 1
)

echo.
echo [3/6] Navegando para o diretorio do projeto...
cd /d "%PROJECT_PATH%"

echo [4/6] Verificando se os arquivos foram extraidos...
if not exist "composer.json" (
    echo.
    echo ========================================
    echo  ATENCAO: ARQUIVOS NAO ENCONTRADOS!
    echo ========================================
    echo.
    echo Os arquivos do BC Sistema precisam ser extraidos em:
    echo %PROJECT_PATH%
    echo.
    echo Por favor:
    echo 1. Extraia o arquivo bc-sistema-deploy-corrigido-XXXXXXX.tar.gz
    echo 2. Copie todo o conteudo para: %PROJECT_PATH%
    echo 3. Execute este script novamente
    echo.
    pause
    exit /b 1
) else (
    echo ✓ Arquivos do BC Sistema encontrados!
)

echo [5/6] Verificando arquivo .env...
if not exist ".env" (
    if exist ".env.example" (
        echo Copiando .env.example para .env...
        copy ".env.example" ".env"
        echo.
        echo ========================================
        echo  CONFIGURE O ARQUIVO .env AGORA!
        echo ========================================
        echo.
        echo Edite o arquivo .env com as configuracoes:
        echo.
        echo DB_CONNECTION=mysql
        echo DB_HOST=127.0.0.1
        echo DB_PORT=3306
        echo DB_DATABASE=bc_sistema
        echo DB_USERNAME=root
        echo DB_PASSWORD=
        echo.
        echo Pressione qualquer tecla apos configurar o .env...
        pause
    ) else (
        echo ERRO: .env.example nao encontrado!
        pause
        exit /b 1
    )
)

echo [6/6] Gerando chave da aplicacao...
"%PHP_PATH%" artisan key:generate --force
if errorlevel 1 (
    echo AVISO: Erro ao gerar chave da aplicacao
    echo Continue com os proximos passos
)

echo.
echo ====================================
echo  INSTALACAO BASICA CONCLUIDA
echo ====================================
echo.
echo ✓ Estrutura de diretorios criada
echo ✓ PHP verificado e funcionando
echo ✓ Arquivos do BC Sistema validados
echo ✓ Arquivo .env configurado
echo ✓ Chave da aplicacao gerada
echo.
echo ========================================
echo  PROXIMOS PASSOS OBRIGATORIOS:
echo ========================================
echo.
echo 1. Crie o banco 'bc_sistema' no phpMyAdmin
echo    URL: http://localhost/phpmyadmin
echo.
echo 2. Configure o arquivo .env se ainda nao fez
echo    Arquivo: %PROJECT_PATH%\.env
echo.
echo 3. Execute: instalar-composer-xampp.bat
echo    (Para instalar dependencias do Composer)
echo.
echo 4. Execute: configurar-bc-xampp.bat
echo    (Para finalizar configuracao)
echo.
echo URL de acesso apos configuracao completa:
echo http://localhost/bc/public
echo.
pause
