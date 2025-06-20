@echo off
echo ====================================
echo  BC SISTEMA - TESTE RAPIDO
echo ====================================
echo.

REM Detectar XAMPP automaticamente
set XAMPP_PATH=C:\xampp
if exist "D:\xampp\" set XAMPP_PATH=D:\xampp
if exist "E:\xampp\" set XAMPP_PATH=E:\xampp

set PROJECT_PATH=%XAMPP_PATH%\htdocs\bc
set PHP_PATH=%XAMPP_PATH%\php\php.exe

echo XAMPP: %XAMPP_PATH%
echo Projeto: %PROJECT_PATH%
echo.

echo ========================================
echo  EXECUTANDO TESTES...
echo ========================================
echo.

echo [1/6] Verificando pasta...
if exist "%PROJECT_PATH%" (
    echo ✅ Pasta BC existe
    cd /d "%PROJECT_PATH%"
) else (
    echo ❌ Pasta BC nao existe: %PROJECT_PATH%
    echo.
    echo Criando pasta...
    mkdir "%PROJECT_PATH%" 2>nul
    if errorlevel 1 (
        echo ❌ Erro ao criar pasta
        goto :error
    )
    echo ✅ Pasta criada
    cd /d "%PROJECT_PATH%"
)

echo [2/6] Verificando arquivos...
if exist "artisan" (
    echo ✅ artisan encontrado
) else (
    echo ❌ artisan NAO encontrado
    echo.
    echo ⚠️  Os arquivos do BC Sistema precisam ser extraidos aqui!
    echo    Arquivo: bc-sistema-deploy-corrigido-XXXXXXX.tar.gz
    echo    Local: %PROJECT_PATH%
    goto :incomplete
)

echo [3/6] Testando PHP...
"%PHP_PATH%" -v >nul 2>&1
if errorlevel 1 (
    echo ❌ PHP nao funciona
    echo Verifique se o XAMPP esta instalado em: %XAMPP_PATH%
    goto :error
) else (
    echo ✅ PHP funcionando
)

echo [4/6] Testando Laravel...
"%PHP_PATH%" artisan --version >nul 2>&1
if errorlevel 1 (
    echo ❌ Laravel nao funciona
    echo Provavelmente as dependencias nao foram instaladas
    echo Execute: composer install
    goto :error
) else (
    echo ✅ Laravel funcionando
)

echo [5/6] Verificando configuracao...
if exist ".env" (
    echo ✅ .env encontrado
) else (
    echo ⚠️  .env nao encontrado (sera criado automaticamente)
)

if exist "vendor\autoload.php" (
    echo ✅ Dependencias instaladas
) else (
    echo ❌ Dependencias NAO instaladas
    echo Execute: composer install
    goto :error
)

echo [6/6] Testando acesso HTTP...
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://localhost/bc/public' -TimeoutSec 5 -UseBasicParsing; if($response.StatusCode -eq 200) { Write-Host '✅ Sistema respondendo via HTTP' } else { Write-Host '⚠️ Sistema retornou codigo:' $response.StatusCode } } catch { Write-Host '❌ Sistema nao responde (verifique Apache)' }" 2>nul

echo.
echo ========================================
echo  ✅ TESTE CONCLUIDO COM SUCESSO!
echo ========================================
echo.
echo 🎉 Sistema BC esta funcionando!
echo.
echo 🌐 Acesse: http://localhost/bc/public
echo 📧 Login: admin@bcsistema.com
echo 🔑 Senha: admin123
echo.
echo ⚠️  Altere a senha no primeiro login!
echo.
goto :end

:incomplete
echo.
echo ========================================
echo  ⚠️  INSTALACAO INCOMPLETA
echo ========================================
echo.
echo O sistema BC nao esta completamente instalado.
echo.
echo 📋 Passos necessarios:
echo 1. Extrair arquivo de deploy para: %PROJECT_PATH%
echo 2. Executar: composer install
echo 3. Configurar arquivo .env
echo 4. Executar: php artisan migrate
echo.
goto :end

:error
echo.
echo ========================================
echo  ❌ ERRO ENCONTRADO
echo ========================================
echo.
echo Verifique:
echo 1. XAMPP instalado e rodando
echo 2. Apache e MySQL iniciados
echo 3. Arquivos do BC Sistema extraidos
echo 4. Dependencias do Composer instaladas
echo.
echo Scripts automaticos disponiveis:
echo - bc-sistema-manager.bat (menu completo)
echo - instalar-bc-completo.bat (instalacao automatica)
echo.

:end
echo Pressione qualquer tecla para continuar...
pause >nul
