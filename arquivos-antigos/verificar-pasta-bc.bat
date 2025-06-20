@echo off
echo ====================================
echo  VERIFICANDO PASTA BC NO XAMPP
echo ====================================
echo.

REM Definir variáveis - Detecta automaticamente o XAMPP
set XAMPP_PATH=C:\xampp
if exist "D:\xampp\" set XAMPP_PATH=D:\xampp
if exist "E:\xampp\" set XAMPP_PATH=E:\xampp

set PROJECT_PATH=%XAMPP_PATH%\htdocs\bc

echo Detectado XAMPP em: %XAMPP_PATH%
echo Verificando pasta: %PROJECT_PATH%
echo.

echo ========================================
echo  STATUS DA PASTA BC
echo ========================================
echo.

if not exist "%PROJECT_PATH%" (
    echo ❌ PASTA NAO EXISTE: %PROJECT_PATH%
    echo.
    echo ========================================
    echo  ACOES NECESSARIAS:
    echo ========================================
    echo.
    echo 1. Criar a pasta:
    echo    mkdir "%PROJECT_PATH%"
    echo.
    echo 2. Extrair arquivos do BC Sistema para esta pasta
    echo    Arquivo: bc-sistema-deploy-corrigido-XXXXXXX.tar.gz
    echo.
    echo 3. Executar script de instalacao:
    echo    instalar-bc-xampp.bat
    echo.
    goto :end
)

echo ✅ PASTA EXISTE: %PROJECT_PATH%
echo.

echo Listando conteudo da pasta:
echo ----------------------------------------
dir "%PROJECT_PATH%" /B 2>nul
if errorlevel 1 (
    echo (Pasta vazia ou sem permissao)
)

echo.
echo ========================================
echo  VERIFICANDO ARQUIVOS ESSENCIAIS
echo ========================================
echo.

if exist "%PROJECT_PATH%\artisan" (
    echo ✅ artisan - Encontrado
) else (
    echo ❌ artisan - NAO encontrado
)

if exist "%PROJECT_PATH%\composer.json" (
    echo ✅ composer.json - Encontrado
) else (
    echo ❌ composer.json - NAO encontrado
)

if exist "%PROJECT_PATH%\.env" (
    echo ✅ .env - Encontrado
) else (
    echo ⚠️  .env - NAO encontrado (sera criado)
)

if exist "%PROJECT_PATH%\vendor\" (
    echo ✅ vendor/ - Encontrado
) else (
    echo ❌ vendor/ - NAO encontrado (dependencias precisam ser instaladas)
)

if exist "%PROJECT_PATH%\app\" (
    echo ✅ app/ - Encontrado
) else (
    echo ❌ app/ - NAO encontrado
)

if exist "%PROJECT_PATH%\public\" (
    echo ✅ public/ - Encontrado
) else (
    echo ❌ public/ - NAO encontrado
)

echo.
echo ========================================
echo  DIAGNOSTICO E RECOMENDACOES
echo ========================================
echo.

REM Contar arquivos essenciais
set ESSENTIAL_COUNT=0
if exist "%PROJECT_PATH%\artisan" set /a ESSENTIAL_COUNT+=1
if exist "%PROJECT_PATH%\composer.json" set /a ESSENTIAL_COUNT+=1
if exist "%PROJECT_PATH%\app\" set /a ESSENTIAL_COUNT+=1
if exist "%PROJECT_PATH%\public\" set /a ESSENTIAL_COUNT+=1

if %ESSENTIAL_COUNT% GEQ 4 (
    echo ✅ ARQUIVOS DO BC SISTEMA ENCONTRADOS!
    echo.
    echo Proximos passos:
    echo 1. Execute: instalar-composer-xampp.bat
    echo 2. Execute: configurar-bc-xampp.bat
    echo 3. Acesse: http://localhost/bc/public
    echo.
) else if %ESSENTIAL_COUNT% GEQ 1 (
    echo ⚠️  INSTALACAO INCOMPLETA
    echo.
    echo Alguns arquivos foram encontrados, mas a instalacao parece incompleta.
    echo.
    echo Recomendacoes:
    echo 1. Extrair novamente todos os arquivos do BC Sistema
    echo 2. Verificar se a extracao foi completa
    echo 3. Executar: instalar-bc-xampp.bat
    echo.
) else (
    echo ❌ BC SISTEMA NAO INSTALADO
    echo.
    echo A pasta existe mas os arquivos do BC Sistema nao foram encontrados.
    echo.
    echo Acoes necessarias:
    echo 1. Extrair o arquivo: bc-sistema-deploy-corrigido-XXXXXXX.tar.gz
    echo 2. Para a pasta: %PROJECT_PATH%
    echo 3. Executar: instalar-bc-xampp.bat
    echo.
)

echo ========================================
echo  INFORMACOES TECNICAS
echo ========================================
echo.
echo Pasta do projeto: %PROJECT_PATH%
echo Caminho do PHP: %XAMPP_PATH%\php\php.exe
echo URL de acesso: http://localhost/bc/public
echo.

:end
pause
