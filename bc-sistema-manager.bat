@echo off
setlocal enabledelayedexpansion
title BC Sistema - XAMPP Manager
color 0A

:main_menu
cls
echo.
echo     ██████╗  ██████╗    ███████╗██╗███████╗████████╗███████╗███╗   ███╗ █████╗ 
echo     ██╔══██╗██╔════╝    ██╔════╝██║██╔════╝╚══██╔══╝██╔════╝████╗ ████║██╔══██╗
echo     ██████╔╝██║         ███████╗██║███████╗   ██║   █████╗  ██╔████╔██║███████║
echo     ██╔══██╗██║         ╚════██║██║╚════██║   ██║   ██╔══╝  ██║╚██╔╝██║██╔══██║
echo     ██████╔╝╚██████╗    ███████║██║███████║   ██║   ███████╗██║ ╚═╝ ██║██║  ██║
echo     ╚═════╝  ╚═════╝    ╚══════╝╚═╝╚══════╝   ╚═╝   ╚══════╝╚═╝     ╚═╝╚═╝  ╚═╝
echo.
echo                           🚀 GERENCIADOR XAMPP 🚀
echo                         Sistema de Gestão Financeira
echo.

REM Detectar XAMPP
set XAMPP_PATH=C:\xampp
if exist "D:\xampp\" set XAMPP_PATH=D:\xampp
if exist "E:\xampp\" set XAMPP_PATH=E:\xampp

echo ========================================
echo  XAMPP DETECTADO: %XAMPP_PATH%
echo  PROJETO: %XAMPP_PATH%\htdocs\bc
echo ========================================
echo.

echo ╔═══════════════════════════════════════╗
echo ║            MENU PRINCIPAL             ║
echo ╠═══════════════════════════════════════╣
echo ║                                       ║
echo ║  1. 🔍 Verificar pasta BC             ║
echo ║  2. 📁 Acessar e gerenciar pasta      ║
echo ║  3. ⚡ Instalação completa (AUTO)     ║
echo ║  4. 🔧 Instalação manual (passo a passo) ║
echo ║  5. 🧪 Testar funcionamento           ║
echo ║  6. 🚨 Solucionar problemas           ║
echo ║  7. 📚 Abrir guia completo            ║
echo ║  8. 🌐 Abrir URLs do sistema          ║
echo ║  9. ℹ️  Informações do sistema        ║
echo ║  0. ❌ Sair                           ║
echo ║                                       ║
echo ╚═══════════════════════════════════════╝
echo.
set /p choice=🎯 Escolha uma opcao (0-9): 

if "%choice%"=="1" goto :verificar_pasta
if "%choice%"=="2" goto :acessar_pasta
if "%choice%"=="3" goto :instalacao_completa
if "%choice%"=="4" goto :instalacao_manual
if "%choice%"=="5" goto :testar_sistema
if "%choice%"=="6" goto :solucionar_problemas
if "%choice%"=="7" goto :abrir_guia
if "%choice%"=="8" goto :abrir_urls
if "%choice%"=="9" goto :info_sistema
if "%choice%"=="0" goto :sair
echo ❌ Opcao invalida!
timeout /t 2 >nul
goto :main_menu

:verificar_pasta
cls
echo.
echo 🔍 EXECUTANDO VERIFICACAO DA PASTA BC...
echo.
if exist "%~dp0verificar-pasta-bc.bat" (
    call "%~dp0verificar-pasta-bc.bat"
) else (
    echo ❌ Script verificar-pasta-bc.bat nao encontrado!
    echo 📁 Pasta atual: %CD%
    echo 📁 Pasta do script: %~dp0
    echo.
    echo Listando scripts disponiveis:
    dir "%~dp0*.bat" /B
)
pause
goto :main_menu

:acessar_pasta
cls
echo.
echo 📁 ACESSANDO PASTA BC...
echo.
if exist "%~dp0acessar-pasta-bc.bat" (
    call "%~dp0acessar-pasta-bc.bat"
) else (
    echo ❌ Script acessar-pasta-bc.bat nao encontrado!
    echo 📁 Executando navegacao manual...
    echo.
    set PROJECT_PATH=%XAMPP_PATH%\htdocs\bc
    echo Navegando para: !PROJECT_PATH!
    if exist "!PROJECT_PATH!" (
        cd /d "!PROJECT_PATH!"
        echo ✅ Pasta encontrada: !PROJECT_PATH!
        echo.
        echo Conteudo da pasta:
        dir
        echo.
        echo Pressione qualquer tecla para abrir no Explorer...
        pause >nul
        start .
    ) else (
        echo ❌ Pasta nao existe: !PROJECT_PATH!
        echo Criando pasta...
        mkdir "!PROJECT_PATH!" 2>nul
        if errorlevel 1 (
            echo ❌ Erro ao criar pasta
        ) else (
            echo ✅ Pasta criada com sucesso!
        )
    )
)
pause
goto :main_menu

:instalacao_completa
cls
echo.
echo ⚡ INICIANDO INSTALACAO COMPLETA AUTOMATICA...
echo.
echo ⚠️  IMPORTANTE: Este processo e automatico!
echo    Certifique-se de que:
echo    ✅ XAMPP esta rodando (Apache + MySQL)
echo    ✅ Banco 'bc_sistema' foi criado
echo    ✅ Arquivos foram extraidos em %XAMPP_PATH%\htdocs\bc
echo.
set /p confirm=📋 Deseja continuar? (S/N): 
if /i not "%confirm%"=="S" goto :main_menu

echo.
echo 🎯 Executando instalacao completa...
if exist "%~dp0instalar-bc-completo.bat" (
    call "%~dp0instalar-bc-completo.bat"
) else (
    echo ❌ Script instalar-bc-completo.bat nao encontrado!
    echo.
    echo Executando instalacao manual passo a passo...
    call :instalacao_manual_inline
)
pause
goto :main_menu

:instalacao_manual
cls
echo.
echo 🔧 INSTALACAO MANUAL - ESCOLHA O PASSO:
echo.
echo ╔═══════════════════════════════════════╗
echo ║         INSTALACAO PASSO A PASSO      ║
echo ╠═══════════════════════════════════════╣
echo ║                                       ║
echo ║  1. 📦 Passo 1: Instalacao basica     ║
echo ║  2. 🎼 Passo 2: Instalar Composer     ║
echo ║  3. ⚙️  Passo 3: Configuracao final    ║
echo ║  4. 🧪 Passo 4: Testar funcionamento  ║
echo ║  0. ⬅️  Voltar ao menu principal       ║
echo ║                                       ║
echo ╚═══════════════════════════════════════╝
echo.
set /p step_choice=🎯 Escolha o passo (0-4): 

if "%step_choice%"=="1" (
    echo.
    echo 📦 Executando instalacao basica...
    if exist "%~dp0instalar-bc-xampp.bat" (
        call "%~dp0instalar-bc-xampp.bat"
    ) else (
        echo ❌ Script nao encontrado! Executando instalacao inline...
        call :instalacao_basica_inline
    )
    pause
)
if "%step_choice%"=="2" (
    echo.
    echo 🎼 Instalando Composer e dependencias...
    if exist "%~dp0instalar-composer-xampp.bat" (
        call "%~dp0instalar-composer-xampp.bat"
    ) else (
        echo ❌ Script nao encontrado! Executando instalacao inline...
        call :composer_inline  
    )
    pause
)
if "%step_choice%"=="3" (
    echo.
    echo ⚙️ Executando configuracao final...  
    if exist "%~dp0configurar-bc-xampp.bat" (
        call "%~dp0configurar-bc-xampp.bat"
    ) else (
        echo ❌ Script nao encontrado! Executando configuracao inline...
        call :configuracao_final_inline
    )
    pause
)
if "%step_choice%"=="4" (
    echo.
    echo 🧪 Testando funcionamento...
    if exist "%~dp0testar-bc-xampp.bat" (
        call "%~dp0testar-bc-xampp.bat"
    ) else (
        echo ❌ Script nao encontrado! Executando teste inline...
        call :teste_manual_inline
    )
    pause
)
if "%step_choice%"=="0" goto :main_menu

goto :instalacao_manual

:testar_sistema
cls
echo.
echo 🧪 EXECUTANDO TESTES DE FUNCIONAMENTO...
echo.
if exist "%~dp0testar-bc-xampp.bat" (
    call "%~dp0testar-bc-xampp.bat"
) else (
    echo ❌ Script testar-bc-xampp.bat nao encontrado!
    echo.
    echo 🔧 Executando teste manual...
    call :teste_manual_inline
)
pause
goto :main_menu

:solucionar_problemas
cls
echo.
echo 🚨 ABRINDO MENU DE SOLUCAO DE PROBLEMAS...
echo.
if exist "%~dp0solucionar-problemas-bc.bat" (
    call "%~dp0solucionar-problemas-bc.bat"
) else (
    echo ❌ Script solucionar-problemas-bc.bat nao encontrado!
    echo.
    echo 🔧 Executando diagnostico basico...
    call :diagnostico_basico_inline
)
pause
goto :main_menu

:abrir_guia
cls
echo.
echo 📚 ABRINDO GUIAS DE DOCUMENTACAO...
echo.
echo Abrindo arquivos de documentacao...

if exist "XAMPP-MIGRACAO-COMPLETA.md" (
    echo ✅ Abrindo guia completo...
    start notepad "XAMPP-MIGRACAO-COMPLETA.md"
)

if exist "README-XAMPP-RAPIDO.md" (
    echo ✅ Abrindo guia rapido...
    start notepad "README-XAMPP-RAPIDO.md"
)

if exist "ARQUIVOS-DOWNLOAD-XAMPP.md" (
    echo ✅ Abrindo lista de downloads...
    start notepad "ARQUIVOS-DOWNLOAD-XAMPP.md"
)

echo.
echo 📚 Documentacao aberta!
pause
goto :main_menu

:abrir_urls
cls
echo.
echo 🌐 ABRINDO URLS DO SISTEMA...
echo.

echo 🌐 Abrindo sistema BC...
start http://localhost/bc/public

echo 🔧 Abrindo phpMyAdmin...
start http://localhost/phpmyadmin

echo 📊 Abrindo XAMPP Dashboard...
start http://localhost/dashboard

echo.
echo 🌐 URLs abertas no navegador!
pause
goto :main_menu

:info_sistema
cls
echo.
echo ╔═══════════════════════════════════════╗
echo ║         INFORMACOES DO SISTEMA        ║
echo ╚═══════════════════════════════════════╝
echo.
echo 📋 DETALHES TECNICOS:
echo ─────────────────────────────────────
echo 🔧 XAMPP Path: %XAMPP_PATH%
echo 📁 Projeto: %XAMPP_PATH%\htdocs\bc
echo 🌐 URL Sistema: http://localhost/bc/public
echo 🔧 phpMyAdmin: http://localhost/phpmyadmin
echo 📊 XAMPP Panel: http://localhost/dashboard
echo.
echo 🔐 CREDENCIAIS PADRAO:
echo ─────────────────────────────────────
echo 📧 Email: admin@bcsistema.com
echo 🔑 Senha: admin123
echo ⚠️  (Altere apos primeiro login!)
echo.
echo 📦 ARQUIVOS PRINCIPAIS:
echo ─────────────────────────────────────
echo 📄 Deploy: bc-sistema-deploy-corrigido-20250620_013129.tar.gz
echo 🗄️  Banco: bc_sistema (MySQL)
echo 🐘 PHP: %XAMPP_PATH%\php\php.exe
echo.
echo 🛠️  SCRIPTS DISPONIVEIS:
echo ─────────────────────────────────────
echo ⚡ instalar-bc-completo.bat (Instalacao automatica)
echo 🔍 verificar-pasta-bc.bat (Verificar status)
echo 📁 acessar-pasta-bc.bat (Gerenciar pasta)
echo 🧪 testar-bc-xampp.bat (Testar funcionamento)
echo 🚨 solucionar-problemas-bc.bat (Troubleshooting)
echo.
echo ✅ CORRECOES APLICADAS:
echo ─────────────────────────────────────
echo ✅ Erro imported_by cannot be null
echo ✅ Conflitos de rotas duplicadas
echo ✅ Sintaxe de controllers corrigida
echo ✅ Imports e dependencias ajustadas
echo ✅ Views e Blade templates validados
echo ✅ Migrations com fallback para nullable
echo ✅ Usuario admin padrao configurado
echo ✅ Sistema otimizado para producao
echo.
pause
goto :main_menu

:sair
cls
echo.
echo ╔═══════════════════════════════════════╗
echo ║              OBRIGADO!                ║
echo ╚═══════════════════════════════════════╝
echo.
echo 🎉 Obrigado por usar o BC Sistema!
echo.
echo 📞 SUPORTE:
echo ─────────────────────────────────────
echo 🌐 Sistema: http://localhost/bc/public
echo 📧 Email: admin@bcsistema.com
echo 🔑 Senha: admin123
echo.
echo 💡 DICA: Execute este script sempre que precisar!
echo    Arquivo: bc-sistema-manager.bat
echo.
echo ✅ Sistema pronto para uso!
echo.
pause
exit

REM =====================================
REM FUNCOES INLINE (FALLBACK)
REM =====================================

:teste_manual_inline
echo ========================================
echo  TESTE MANUAL DE FUNCIONAMENTO
echo ========================================
echo.
set PROJECT_PATH=%XAMPP_PATH%\htdocs\bc
set PHP_PATH=%XAMPP_PATH%\php\php.exe

echo [1/8] Verificando pasta do projeto...
if exist "%PROJECT_PATH%" (
    echo ✅ Pasta existe: %PROJECT_PATH%
) else (
    echo ❌ Pasta nao existe: %PROJECT_PATH%
    goto :end_teste_inline
)

echo [2/8] Verificando arquivos essenciais...
cd /d "%PROJECT_PATH%"
if exist "artisan" (
    echo ✅ artisan encontrado
) else (
    echo ❌ artisan nao encontrado
    goto :end_teste_inline
)

echo [3/8] Testando PHP...
"%PHP_PATH%" -v >nul 2>&1
if errorlevel 1 (
    echo ❌ PHP nao funciona
    goto :end_teste_inline
) else (
    echo ✅ PHP funcionando
)

echo [4/8] Testando Laravel...
"%PHP_PATH%" artisan --version >nul 2>&1
if errorlevel 1 (
    echo ❌ Laravel nao funciona (verifique dependencias)
    goto :end_teste_inline
) else (
    echo ✅ Laravel funcionando
)

echo [5/8] Verificando .env...
if exist ".env" (
    echo ✅ .env encontrado
) else (
    echo ⚠️  .env nao encontrado
)

echo [6/8] Verificando vendor...
if exist "vendor\autoload.php" (
    echo ✅ Dependencias instaladas
) else (
    echo ❌ Dependencias nao instaladas (execute composer install)
)

echo [7/8] Testando acesso HTTP...
echo Verificando se o sistema responde...
curl -s http://localhost/bc/public >nul 2>&1
if errorlevel 1 (
    echo ⚠️  Sistema nao responde (verifique Apache)
) else (
    echo ✅ Sistema responde via HTTP
)

echo [8/8] Resumo do teste...
echo.
echo ========================================
echo  RESULTADO DOS TESTES  
echo ========================================
echo 🌐 URL: http://localhost/bc/public
echo 📧 Login: admin@bcsistema.com  
echo 🔑 Senha: admin123
echo.

:end_teste_inline
return

:diagnostico_basico_inline
echo ========================================
echo  DIAGNOSTICO BASICO DO SISTEMA
echo ========================================
echo.
set PROJECT_PATH=%XAMPP_PATH%\htdocs\bc
set PHP_PATH=%XAMPP_PATH%\php\php.exe

echo 🔍 Informacoes do sistema:
echo XAMPP Path: %XAMPP_PATH%
echo Projeto: %PROJECT_PATH%
echo PHP: %PHP_PATH%
echo.

echo 🔍 Verificando servicos...
netstat -an | find "80" >nul
if errorlevel 1 (
    echo ❌ Apache (porta 80) - Nao rodando
) else (
    echo ✅ Apache (porta 80) - Rodando
)

netstat -an | find "3306" >nul  
if errorlevel 1 (
    echo ❌ MySQL (porta 3306) - Nao rodando
) else (
    echo ✅ MySQL (porta 3306) - Rodando
)

echo.
echo 🔍 Verificando pasta do projeto...
if exist "%PROJECT_PATH%" (
    echo ✅ Pasta existe
    echo Arquivos principais:
    cd /d "%PROJECT_PATH%"
    if exist "artisan" echo ✅ artisan
    if exist "composer.json" echo ✅ composer.json
    if exist ".env" echo ✅ .env
    if exist "vendor\" echo ✅ vendor/
) else (
    echo ❌ Pasta nao existe
    echo.
    echo 🔧 Solucoes:
    echo 1. Crie a pasta: mkdir "%PROJECT_PATH%"
    echo 2. Extraia os arquivos do BC Sistema
    echo 3. Execute a instalacao
)

echo.
echo 💡 URLs uteis:
echo http://localhost/bc/public
echo http://localhost/phpmyadmin
echo http://localhost/dashboard
echo.
return

:instalacao_basica_inline
echo ========================================
echo  INSTALACAO BASICA INLINE
echo ========================================
echo.
set PROJECT_PATH=%XAMPP_PATH%\htdocs\bc

echo Criando pasta se nao existir...
if not exist "%PROJECT_PATH%" mkdir "%PROJECT_PATH%"

echo Verificando arquivos...
cd /d "%PROJECT_PATH%"
if not exist "artisan" (
    echo ❌ ERRO: Arquivos do BC Sistema nao encontrados!
    echo.
    echo Extraia o arquivo de deploy para: %PROJECT_PATH%
    return
)

echo ✅ Instalacao basica OK
return

:composer_inline
echo ========================================
echo  INSTALACAO COMPOSER INLINE
echo ========================================
echo.
set PROJECT_PATH=%XAMPP_PATH%\htdocs\bc
set PHP_PATH=%XAMPP_PATH%\php\php.exe

cd /d "%PROJECT_PATH%"

echo Verificando se Composer esta disponivel...
composer --version >nul 2>&1
if not errorlevel 1 (
    echo ✅ Composer global encontrado
    echo Instalando dependencias...
    composer install --no-dev --optimize-autoloader
) else (
    echo ❌ Composer nao encontrado
    echo.
    echo 🔧 Solucoes:
    echo 1. Instale o Composer: https://getcomposer.org/download/
    echo 2. Ou baixe composer.phar manualmente
)
return

:configuracao_final_inline
echo ========================================
echo  CONFIGURACAO FINAL INLINE
echo ========================================
echo.
set PROJECT_PATH=%XAMPP_PATH%\htdocs\bc
set PHP_PATH=%XAMPP_PATH%\php\php.exe

cd /d "%PROJECT_PATH%"

echo Verificando arquivo .env...
if not exist ".env" (
    if exist ".env.example" (
        copy ".env.example" ".env"
        echo ✅ .env criado
    )
)

echo Executando comandos essenciais...
"%PHP_PATH%" artisan key:generate --force
"%PHP_PATH%" artisan migrate --force
"%PHP_PATH%" artisan optimize

echo ✅ Configuracao basica concluida
return

:instalacao_manual_inline
echo ========================================
echo  INSTALACAO MANUAL COMPLETA INLINE
echo ========================================
echo.
call :instalacao_basica_inline
call :composer_inline
call :configuracao_final_inline
call :teste_manual_inline
return
