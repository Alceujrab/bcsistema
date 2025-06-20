@echo off
echo ====================================
echo  ACESSAR PASTA BC NO XAMPP
echo ====================================
echo.

REM Definir variáveis - Detecta automaticamente o XAMPP
set XAMPP_PATH=C:\xampp
if exist "D:\xampp\" set XAMPP_PATH=D:\xampp
if exist "E:\xampp\" set XAMPP_PATH=E:\xampp

set PROJECT_PATH=%XAMPP_PATH%\htdocs\bc

echo Detectado XAMPP em: %XAMPP_PATH%
echo Acessando pasta: %PROJECT_PATH%
echo.

REM Verificar se a pasta existe
if not exist "%PROJECT_PATH%" (
    echo ❌ PASTA NAO EXISTE: %PROJECT_PATH%
    echo.
    echo Criando pasta...
    mkdir "%PROJECT_PATH%"
    if errorlevel 1 (
        echo ERRO: Nao foi possivel criar a pasta
        echo Verifique as permissoes ou execute como Administrador
        pause
        exit /b 1
    )
    echo ✅ Pasta criada com sucesso!
)

echo Navegando para a pasta...
cd /d "%PROJECT_PATH%"

echo.
echo ========================================
echo  VOCE ESTA AGORA EM:
echo ========================================
echo %CD%
echo.

echo Listando arquivos e pastas:
echo ----------------------------------------
dir
echo.

echo ========================================
echo  MENU DE ACOES
echo ========================================
echo.
echo 1. Listar arquivos detalhadamente
echo 2. Verificar arquivos do Laravel
echo 3. Abrir pasta no Explorer
echo 4. Executar comando personalizado
echo 5. Verificar status do sistema
echo 6. Voltar ao menu principal
echo 0. Sair
echo.
set /p choice=Escolha uma opcao (0-6): 

if "%choice%"=="1" goto :list_detailed
if "%choice%"=="2" goto :check_laravel
if "%choice%"=="3" goto :open_explorer
if "%choice%"=="4" goto :custom_command
if "%choice%"=="5" goto :check_status
if "%choice%"=="6" goto :main_menu
if "%choice%"=="0" goto :end
echo Opcao invalida!
pause
goto :menu

:list_detailed
echo.
echo ========================================
echo  LISTAGEM DETALHADA
echo ========================================
echo.
dir /A /S | more
echo.
pause
goto :menu

:check_laravel
echo.
echo ========================================
echo  VERIFICANDO ARQUIVOS DO LARAVEL
echo ========================================
echo.

echo Verificando arquivos essenciais:
echo.

if exist "artisan" (
    echo ✅ artisan - Arquivo de comandos do Laravel
) else (
    echo ❌ artisan - FALTANDO (arquivo principal do Laravel)
)

if exist "composer.json" (
    echo ✅ composer.json - Configuracoes de dependencias
) else (
    echo ❌ composer.json - FALTANDO (dependencias do PHP)
)

if exist "package.json" (
    echo ✅ package.json - Configuracoes do Node.js
) else (
    echo ⚠️  package.json - Nao encontrado (opcional)
)

if exist ".env" (
    echo ✅ .env - Configuracoes do ambiente
) else (
    echo ⚠️  .env - Nao encontrado (sera criado na configuracao)
)

echo.
echo Verificando pastas principais:
echo.

if exist "app\" (
    echo ✅ app/ - Codigo da aplicacao
) else (
    echo ❌ app/ - FALTANDO (codigo principal)
)

if exist "config\" (
    echo ✅ config/ - Configuracoes
) else (
    echo ❌ config/ - FALTANDO (arquivos de configuracao)
)

if exist "database\" (
    echo ✅ database/ - Migrations e seeds
) else (
    echo ❌ database/ - FALTANDO (estrutura do banco)
)

if exist "public\" (
    echo ✅ public/ - Arquivos publicos
) else (
    echo ❌ public/ - FALTANDO (pasta web)
)

if exist "resources\" (
    echo ✅ resources/ - Views e assets
) else (
    echo ❌ resources/ - FALTANDO (interface)
)

if exist "routes\" (
    echo ✅ routes/ - Definicoes de rotas
) else (
    echo ❌ routes/ - FALTANDO (rotas web)
)

if exist "storage\" (
    echo ✅ storage/ - Arquivos e logs
) else (
    echo ❌ storage/ - FALTANDO (sistema de arquivos)
)

if exist "vendor\" (
    echo ✅ vendor/ - Dependencias instaladas
) else (
    echo ⚠️  vendor/ - Nao encontrado (execute instalar-composer-xampp.bat)
)

echo.
pause
goto :menu

:open_explorer
echo.
echo Abrindo pasta no Windows Explorer...
start . 
goto :menu

:custom_command
echo.
echo ========================================
echo  COMANDO PERSONALIZADO
echo ========================================
echo.
echo Voce esta em: %CD%
echo.
echo Digite o comando que deseja executar:
echo (ou pressione Enter para voltar)
echo.
set /p custom_cmd=Comando: 

if "%custom_cmd%"=="" goto :menu

echo.
echo Executando: %custom_cmd%
echo ----------------------------------------
%custom_cmd%
echo ----------------------------------------
echo Comando concluido
echo.
pause
goto :menu

:check_status
echo.
echo ========================================
echo  STATUS DO SISTEMA BC
echo ========================================
echo.

echo Pasta atual: %CD%
echo.

REM Verificar se é um projeto Laravel válido
if exist "artisan" (
    echo ✅ Projeto Laravel detectado
    echo.
    echo Verificando versao do Laravel...
    "%XAMPP_PATH%\php\php.exe" artisan --version 2>nul
    if errorlevel 1 (
        echo ❌ Erro ao executar artisan (verifique dependencias)
    )
    echo.
    
    echo Verificando arquivo .env...
    if exist ".env" (
        echo ✅ .env encontrado
        echo.
        echo Configuracoes do banco:
        findstr "DB_" .env 2>nul
    ) else (
        echo ⚠️  .env nao encontrado
    )
    
) else (
    echo ❌ Nao e um projeto Laravel valido
    echo.
    echo Certifique-se de que os arquivos do BC Sistema foram extraidos aqui
)

echo.
echo URLs de acesso:
echo - Sistema: http://localhost/bc/public
echo - phpMyAdmin: http://localhost/phpmyadmin
echo.

pause
goto :menu

:main_menu
echo.
echo Voltando ao menu principal...
echo Executando scripts disponiveis...
echo.
echo Scripts de instalacao:
echo - instalar-bc-xampp.bat
echo - instalar-composer-xampp.bat  
echo - configurar-bc-xampp.bat
echo - testar-bc-xampp.bat
echo.
echo Script completo:
echo - instalar-bc-completo.bat
echo.
pause
goto :end

:menu
cls
echo ========================================
echo  ACESSAR PASTA BC NO XAMPP
echo ========================================
echo.
echo Pasta atual: %CD%
echo.
echo ========================================
echo  MENU DE ACOES
echo ========================================
echo.
echo 1. Listar arquivos detalhadamente
echo 2. Verificar arquivos do Laravel
echo 3. Abrir pasta no Explorer
echo 4. Executar comando personalizado
echo 5. Verificar status do sistema
echo 6. Voltar ao menu principal
echo 0. Sair
echo.
set /p choice=Escolha uma opcao (0-6): 

if "%choice%"=="1" goto :list_detailed
if "%choice%"=="2" goto :check_laravel
if "%choice%"=="3" goto :open_explorer
if "%choice%"=="4" goto :custom_command
if "%choice%"=="5" goto :check_status
if "%choice%"=="6" goto :main_menu
if "%choice%"=="0" goto :end
echo Opcao invalida!
pause
goto :menu

:end
echo.
echo Obrigado por usar o BC Sistema!
echo.
pause
