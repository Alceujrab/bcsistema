@echo off
echo ====================================
echo  INSTALAR COMPOSER - BC SISTEMA
echo ====================================
echo.

REM Definir variáveis - Detecta automaticamente o XAMPP
set XAMPP_PATH=C:\xampp
if exist "D:\xampp\" set XAMPP_PATH=D:\xampp
if exist "E:\xampp\" set XAMPP_PATH=E:\xampp

set PROJECT_PATH=%XAMPP_PATH%\htdocs\bc
set PHP_PATH=%XAMPP_PATH%\php\php.exe

echo Detectado XAMPP em: %XAMPP_PATH%
echo Projeto em: %PROJECT_PATH%
echo.

REM Verificar se PHP está disponível
echo Verificando PHP...
if not exist "%PHP_PATH%" (
    echo ERRO: PHP nao encontrado em %PHP_PATH%
    echo Verifique se o XAMPP esta instalado corretamente
    pause
    exit /b 1
)

echo Navegando para o diretorio do projeto...
cd /d "%PROJECT_PATH%"

if not exist "%PROJECT_PATH%" (
    echo ERRO: Diretorio do projeto nao existe: %PROJECT_PATH%
    echo Crie o diretorio primeiro ou verifique se o BC Sistema foi extraido
    pause
    exit /b 1
)

echo.
echo [1/5] Verificando se Composer ja esta instalado...

REM Verificar Composer global
composer --version >nul 2>&1
if not errorlevel 1 (
    echo ✓ Composer global encontrado!
    goto :install_dependencies
)

REM Verificar Composer no XAMPP
if exist "%XAMPP_PATH%\composer\composer.phar" (
    echo ✓ Composer do XAMPP encontrado!
    goto :install_dependencies
)

REM Verificar Composer local no projeto
if exist "%PROJECT_PATH%\composer.phar" (
    echo ✓ Composer local encontrado!
    goto :install_dependencies
)

echo ✗ Composer nao encontrado em nenhum local

echo.
echo [2/5] Baixando Composer installer...
echo Baixando de https://getcomposer.org/installer...

REM Usar PowerShell para download (mais confiável no Windows)
echo Usando PowerShell para download...
powershell -Command "try { Write-Host 'Iniciando download...'; [Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; Invoke-WebRequest -Uri 'https://getcomposer.org/installer' -OutFile 'composer-setup.php' -UseBasicParsing; Write-Host 'Download concluido!' } catch { Write-Host 'Erro no download:' $_.Exception.Message; exit 1 }"
if errorlevel 1 (
    echo Tentando com curl...
    curl --version >nul 2>&1
    if not errorlevel 1 (
        curl -sS https://getcomposer.org/installer -o composer-setup.php
    ) else (
        echo.
        echo ERRO: Nao foi possivel baixar o Composer installer
        echo.
        echo Alternativas:
        echo 1. Baixe manualmente: https://getcomposer.org/download/
        echo 2. Use o Composer Installer para Windows
        echo 3. Verifique sua conexao com a internet
        echo 4. Desative temporariamente o antivirus/firewall
        echo.
        pause
        exit /b 1
    )
)

echo ✓ Installer baixado com sucesso

echo.
echo [3/5] Verificando integridade do installer...
echo (Pulando verificacao de integridade - continuando...)

echo.
echo [4/5] Instalando Composer localmente...
"%PHP_PATH%" composer-setup.php --install-dir="%PROJECT_PATH%" --filename=composer.phar
if not exist "%PROJECT_PATH%\composer.phar" (
    echo ERRO: Falha na instalacao do Composer
    echo Verifique se o PHP tem permissoes de escrita
    pause
    exit /b 1
)

echo ✓ Composer instalado com sucesso!

echo Limpando arquivo temporario...
del composer-setup.php >nul 2>&1

echo.
:install_dependencies
echo [5/5] Instalando dependencias do projeto...

REM Verificar se composer.json existe
if not exist "composer.json" (
    echo ERRO: composer.json nao encontrado em %PROJECT_PATH%
    echo.
    echo Certifique-se de que:
    echo 1. Os arquivos do BC Sistema foram extraidos em %PROJECT_PATH%
    echo 2. O arquivo composer.json existe na pasta raiz do projeto
    echo.
    pause
    exit /b 1
)

echo ✓ composer.json encontrado

REM Determinar qual versão do Composer usar e instalar dependências
echo Detectando versao do Composer para instalacao...

REM Tentar Composer global primeiro
composer --version >nul 2>&1
if not errorlevel 1 (
    echo ✓ Usando Composer global...
    echo Instalando dependencias (isso pode demorar alguns minutos)...
    composer install --no-dev --optimize-autoloader --no-interaction --verbose
    if not errorlevel 1 (
        echo ✓ Dependencias instaladas com sucesso!
        goto :success
    ) else (
        echo ✗ Erro com Composer global, tentando alternativas...
    )
)

REM Tentar Composer do XAMPP
if exist "%XAMPP_PATH%\composer\composer.phar" (
    echo ✓ Usando Composer do XAMPP...
    echo Instalando dependencias (isso pode demorar alguns minutos)...
    "%PHP_PATH%" "%XAMPP_PATH%\composer\composer.phar" install --no-dev --optimize-autoloader --no-interaction --verbose
    if not errorlevel 1 (
        echo ✓ Dependencias instaladas com sucesso!
        goto :success
    ) else (
        echo ✗ Erro com Composer do XAMPP, tentando Composer local...
    )
)

REM Tentar Composer local
if exist "%PROJECT_PATH%\composer.phar" (
    echo ✓ Usando Composer local...
    echo Instalando dependencias (isso pode demorar alguns minutos)...
    "%PHP_PATH%" "%PROJECT_PATH%\composer.phar" install --no-dev --optimize-autoloader --no-interaction --verbose
    if not errorlevel 1 (
        echo ✓ Dependencias instaladas com sucesso!
        goto :success
    ) else (
        echo ✗ Erro com Composer local!
    )
)

echo.
echo ========================================
echo  ERRO: NENHUMA VERSAO DO COMPOSER FUNCIONOU!
echo ========================================
echo.
echo Tente executar manualmente:
echo 1. cd /d "%PROJECT_PATH%"
echo 2. "%PHP_PATH%" composer.phar install --no-dev
echo.
echo Ou instale o Composer globalmente:
echo https://getcomposer.org/download/
echo.
pause
exit /b 1

:success
echo.
echo ====================================
echo  COMPOSER CONFIGURADO COM SUCESSO!
echo ====================================
echo.
echo ✓ Composer instalado e configurado
echo ✓ Dependencias do Laravel instaladas
echo ✓ Autoloader otimizado para producao
echo ✓ Pasta vendor/ criada com todas as bibliotecas
echo.
echo ========================================
echo  PROXIMO PASSO OBRIGATORIO:
echo ========================================
echo.
echo Execute: configurar-bc-xampp.bat
echo (Para configurar banco, migrations e usuario admin)
echo.
pause
