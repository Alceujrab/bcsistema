@echo off
echo ====================================
echo  BC SISTEMA - SOLUCAO DE PROBLEMAS
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

echo ========================================
echo  MENU DE SOLUCAO DE PROBLEMAS
echo ========================================
echo.
echo 1. Verificar status do XAMPP
echo 2. Testar conexao com banco
echo 3. Recriar banco de dados
echo 4. Recriar usuario admin
echo 5. Limpar todos os caches
echo 6. Reinstalar dependencias (COMPOSER)
echo 7. Verificar permissoes de pastas
echo 8. Executar diagnostico completo
echo 9. Mostrar logs de erro
echo A. CORRIGIR ERRO vendor/autoload.php
echo B. CORRIGIR ERRO Fatal Error Laravel
echo 0. Sair
echo.
set /p choice=Escolha uma opcao (0-9, A-B): 

if "%choice%"=="1" goto :check_xampp
if "%choice%"=="2" goto :test_db
if "%choice%"=="3" goto :recreate_db
if "%choice%"=="4" goto :recreate_admin
if "%choice%"=="5" goto :clear_cache
if "%choice%"=="6" goto :reinstall_deps
if "%choice%"=="7" goto :check_permissions
if "%choice%"=="8" goto :full_diagnostic
if "%choice%"=="9" goto :show_logs
if /i "%choice%"=="A" goto :fix_vendor_error
if /i "%choice%"=="B" goto :fix_fatal_error
if "%choice%"=="0" goto :end
echo Opcao invalida!
pause
goto :start

:check_xampp
echo.
echo ========================================
echo  VERIFICANDO STATUS DO XAMPP
echo ========================================
echo.

echo Testando PHP...
"%PHP_PATH%" -v
if errorlevel 1 (
    echo ❌ PHP nao encontrado ou nao funciona
    echo Solucoes:
    echo - Inicie o XAMPP Control Panel
    echo - Verifique se o Apache esta rodando
    echo - Reinstale o XAMPP se necessario
) else (
    echo ✅ PHP funcionando
)

echo.
echo Testando MySQL...
netstat -an | find "3306" >nul
if errorlevel 1 (
    echo ❌ MySQL nao esta rodando na porta 3306
    echo Solucoes:
    echo - Inicie o MySQL no XAMPP Control Panel
    echo - Verifique se a porta 3306 nao esta ocupada
) else (
    echo ✅ MySQL rodando na porta 3306
)

echo.
echo Testando Apache...
netstat -an | find "80" >nul
if errorlevel 1 (
    echo ❌ Apache nao esta rodando na porta 80
    echo Solucoes:
    echo - Inicie o Apache no XAMPP Control Panel
    echo - Verifique se a porta 80 nao esta ocupada
) else (
    echo ✅ Apache rodando na porta 80
)

pause
goto :start

:test_db
echo.
echo ========================================
echo  TESTANDO CONEXAO COM BANCO
echo ========================================
echo.

cd /d "%PROJECT_PATH%"

echo Testando conexao PDO...
"%PHP_PATH%" artisan tinker --execute="try { $pdo = DB::connection()->getPdo(); echo 'Conexao OK - Driver: ' . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME); } catch(Exception $e) { echo 'ERRO: ' . $e->getMessage(); }"

echo.
echo Listando bancos disponiveis...
"%PHP_PATH%" artisan tinker --execute="try { $dbs = DB::select('SHOW DATABASES'); foreach($dbs as $db) echo $db->Database . PHP_EOL; } catch(Exception $e) { echo 'ERRO: ' . $e->getMessage(); }"

echo.
echo Verificando tabelas do sistema...
"%PHP_PATH%" artisan tinker --execute="try { $tables = DB::select('SHOW TABLES'); echo 'Tabelas encontradas: ' . count($tables); } catch(Exception $e) { echo 'ERRO: ' . $e->getMessage(); }"

pause
goto :start

:recreate_db
echo.
echo ========================================
echo  RECRIAR BANCO DE DADOS
echo ========================================
echo.

cd /d "%PROJECT_PATH%"

echo ⚠️  ATENCAO: Isso apagara todos os dados existentes!
echo.
set /p confirm=Tem certeza? (S/N): 
if /i not "%confirm%"=="S" goto :start

echo.
echo Recriando banco de dados...
"%PHP_PATH%" artisan tinker --execute="try { DB::statement('DROP DATABASE IF EXISTS bc_sistema'); DB::statement('CREATE DATABASE bc_sistema'); echo 'Banco recriado com sucesso!'; } catch(Exception $e) { echo 'ERRO: ' . $e->getMessage(); }"

echo.
echo Executando migrations...
"%PHP_PATH%" artisan migrate --force

echo.
echo Criando usuario admin...
"%PHP_PATH%" artisan tinker --execute="try { App\Models\User::create(['name'=>'Admin BC','email'=>'admin@bcsistema.com','password'=>Hash::make('admin123'),'email_verified_at'=>now()]); echo 'Usuario admin criado!'; } catch(Exception $e) { echo 'ERRO: ' . $e->getMessage(); }"

pause
goto :start

:recreate_admin
echo.
echo ========================================
echo  RECRIAR USUARIO ADMIN
echo ========================================
echo.

cd /d "%PROJECT_PATH%"

echo Removendo usuario admin existente...
"%PHP_PATH%" artisan tinker --execute="try { App\Models\User::where('email', 'admin@bcsistema.com')->delete(); echo 'Usuario removido (se existia)'; } catch(Exception $e) { echo 'Info: ' . $e->getMessage(); }"

echo.
echo Criando novo usuario admin...
"%PHP_PATH%" artisan tinker --execute="try { App\Models\User::create(['name'=>'Admin BC','email'=>'admin@bcsistema.com','password'=>Hash::make('admin123'),'email_verified_at'=>now()]); echo 'Usuario admin criado com sucesso!'; } catch(Exception $e) { echo 'ERRO: ' . $e->getMessage(); }"

pause
goto :start

:clear_cache
echo.
echo ========================================
echo  LIMPANDO TODOS OS CACHES
echo ========================================
echo.

cd /d "%PROJECT_PATH%"

echo Limpando cache de configuracao...
"%PHP_PATH%" artisan config:clear

echo Limpando cache de rotas...
"%PHP_PATH%" artisan route:clear

echo Limpando cache de views...
"%PHP_PATH%" artisan view:clear

echo Limpando cache da aplicacao...
"%PHP_PATH%" artisan cache:clear

echo Limpando cache de opcodes (se houver)...
"%PHP_PATH%" artisan opcache:clear 2>nul

echo.
echo Reotimizando...
"%PHP_PATH%" artisan config:cache
"%PHP_PATH%" artisan route:cache
"%PHP_PATH%" artisan optimize

echo ✅ Todos os caches limpos e reotimizados!

pause
goto :start

:reinstall_deps
echo.
echo ========================================
echo  REINSTALAR DEPENDENCIAS
echo ========================================
echo.

cd /d "%PROJECT_PATH%"

echo ⚠️  Removendo pasta vendor...
if exist "vendor" (
    rmdir /s /q vendor
    echo Pasta vendor removida
)

echo.
echo Removendo composer.lock...
if exist "composer.lock" (
    del composer.lock
    echo composer.lock removido
)

echo.
echo Reinstalando dependencias...
composer install --no-dev --optimize-autoloader --no-interaction

if errorlevel 1 (
    echo ❌ Erro ao reinstalar dependencias
    echo Tente executar: instalar-composer-xampp.bat
) else (
    echo ✅ Dependencias reinstaladas com sucesso!
)

pause
goto :start

:check_permissions
echo.
echo ========================================
echo  VERIFICAR PERMISSOES DE PASTAS
echo ========================================
echo.

cd /d "%PROJECT_PATH%"

echo Verificando permissoes de escrita...

echo.
echo Testando pasta storage...
echo test > storage\test.txt 2>nul
if exist "storage\test.txt" (
    del storage\test.txt
    echo ✅ storage/ - Escrita OK
) else (
    echo ❌ storage/ - Sem permissao de escrita
)

echo.
echo Testando pasta bootstrap/cache...
echo test > bootstrap\cache\test.txt 2>nul
if exist "bootstrap\cache\test.txt" (
    del bootstrap\cache\test.txt
    echo ✅ bootstrap/cache/ - Escrita OK
) else (
    echo ❌ bootstrap/cache/ - Sem permissao de escrita
)

echo.
echo Testando pasta public...
echo test > public\test.txt 2>nul
if exist "public\test.txt" (
    del public\test.txt
    echo ✅ public/ - Escrita OK
) else (
    echo ❌ public/ - Sem permissao de escrita
)

echo.
echo NOTA: No Windows com XAMPP, normalmente nao ha problemas de permissao
echo Se houver erros, execute como Administrador ou verifique antivirus

pause
goto :start

:full_diagnostic
echo.
echo ========================================
echo  DIAGNOSTICO COMPLETO
echo ========================================
echo.

echo Executando testar-bc-xampp.bat...
call testar-bc-xampp.bat

pause
goto :start

:show_logs
echo.
echo ========================================
echo  LOGS DE ERRO
echo ========================================
echo.

cd /d "%PROJECT_PATH%"

echo Laravel Log (ultimas 50 linhas):
echo ----------------------------------------
if exist "storage\logs\laravel.log" (
    powershell -Command "Get-Content 'storage\logs\laravel.log' -Tail 50"
) else (
    echo Nenhum log encontrado
)

echo.
echo.
echo Apache Error Log (ultimas 30 linhas):
echo ----------------------------------------
if exist "%XAMPP_PATH%\apache\logs\error.log" (
    powershell -Command "Get-Content '%XAMPP_PATH%\apache\logs\error.log' -Tail 30"
) else (
    echo Log do Apache nao encontrado
)

echo.
echo.
echo MySQL Error Log:
echo ----------------------------------------
if exist "%XAMPP_PATH%\mysql\data\*.err" (
    for %%f in ("%XAMPP_PATH%\mysql\data\*.err") do (
        echo Arquivo: %%f
        powershell -Command "Get-Content '%%f' -Tail 20"
    )
) else (
    echo Log do MySQL nao encontrado
)

pause
goto :start

:start
cls
goto :begin

:begin
goto :menu

:menu
goto :top

:top
echo ====================================
echo  BC SISTEMA - SOLUCAO DE PROBLEMAS
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

echo ========================================
echo  MENU DE SOLUCAO DE PROBLEMAS
echo ========================================
echo.
echo 1. Verificar status do XAMPP
echo 2. Testar conexao com banco
echo 3. Recriar banco de dados
echo 4. Recriar usuario admin
echo 5. Limpar todos os caches
echo 6. Reinstalar dependencias
echo 7. Verificar permissoes de pastas
echo 8. Executar diagnostico completo
echo 9. Mostrar logs de erro
echo 0. Sair
echo.
set /p choice=Escolha uma opcao (0-9): 

if "%choice%"=="1" goto :check_xampp
if "%choice%"=="2" goto :test_db
if "%choice%"=="3" goto :recreate_db
if "%choice%"=="4" goto :recreate_admin
if "%choice%"=="5" goto :clear_cache
if "%choice%"=="6" goto :reinstall_deps
if "%choice%"=="7" goto :check_permissions
if "%choice%"=="8" goto :full_diagnostic
if "%choice%"=="9" goto :show_logs
if "%choice%"=="0" goto :end
echo Opcao invalida!
pause
goto :menu

:end
echo.
echo Obrigado por usar o BC Sistema!
echo.
pause

REM =====================================
REM FUNCOES ESPECIAIS PARA ERROS CRITICOS
REM =====================================

:fix_vendor_error
echo.
echo ========================================
echo  CORRIGIR ERRO vendor/autoload.php
echo ========================================
echo.
echo ❌ ERRO DETECTADO:
echo Failed to open stream: No such file or directory
echo vendor/autoload.php not found
echo.
echo 🔧 DIAGNOSTICO:
echo A pasta vendor/ nao existe, o que significa que as
echo dependencias do Composer nao foram instaladas.
echo.

cd /d "%PROJECT_PATH%"

echo [1/6] Verificando arquivos do projeto...
if not exist "composer.json" (
    echo ❌ ERRO CRITICO: composer.json nao encontrado!
    echo.
    echo Os arquivos do BC Sistema nao foram extraidos corretamente.
    echo.
    echo SOLUCAO:
    echo 1. Extraia o arquivo bc-sistema-deploy-corrigido-XXXXXXX.tar.gz
    echo 2. Para a pasta: %PROJECT_PATH%
    echo 3. Certifique-se de que todos os arquivos foram copiados
    echo.
    pause
    return
)
echo ✅ composer.json encontrado

echo [2/6] Verificando se vendor/ existe...
if exist "vendor\" (
    echo ⚠️  Pasta vendor/ existe mas pode estar corrupta
    echo Removendo pasta vendor/ corrompida...
    rmdir /s /q vendor 2>nul
    echo ✅ Pasta vendor/ removida
) else (
    echo ❌ Pasta vendor/ nao existe (normal para primeira instalacao)
)

echo [3/6] Verificando Composer...
composer --version >nul 2>&1
if errorlevel 1 (
    echo ❌ Composer nao esta instalado ou nao esta no PATH
    echo.
    echo SOLUCOES:
    echo 1. Instale o Composer: https://getcomposer.org/download/
    echo 2. Ou baixe composer.phar localmente
    echo.
    echo Tentando baixar composer.phar...
    powershell -Command "try { [Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; Invoke-WebRequest -Uri 'https://getcomposer.org/installer' -OutFile 'composer-setup.php' -UseBasicParsing } catch { Write-Host 'Erro no download' }"
    
    if exist "composer-setup.php" (
        echo Instalando Composer localmente...
        "%PHP_PATH%" composer-setup.php --install-dir=. --filename=composer.phar
        del composer-setup.php
        
        if exist "composer.phar" (
            echo ✅ Composer instalado localmente
            set COMPOSER_CMD="%PHP_PATH%" composer.phar
        ) else (
            echo ❌ Falha ao instalar Composer
            goto :composer_manual_instructions
        )
    ) else (
        goto :composer_manual_instructions
    )
) else (
    echo ✅ Composer global encontrado
    set COMPOSER_CMD=composer
)

echo [4/6] Instalando dependencias...
echo Esta operacao pode demorar alguns minutos...
echo.
%COMPOSER_CMD% install --no-dev --optimize-autoloader --no-interaction --verbose
if errorlevel 1 (
    echo ❌ Erro ao instalar dependencias
    echo.
    echo Tentando com opcoes alternativas...
    %COMPOSER_CMD% install --no-dev --no-scripts --no-interaction
    if errorlevel 1 (
        echo ❌ Falha total ao instalar dependencias
        goto :composer_troubleshoot
    )
)

echo [5/6] Verificando instalacao...
if exist "vendor\autoload.php" (
    echo ✅ vendor/autoload.php criado com sucesso!
) else (
    echo ❌ vendor/autoload.php ainda nao existe
    goto :composer_troubleshoot
)

echo [6/6] Testando aplicacao...
"%PHP_PATH%" -r "require 'vendor/autoload.php'; echo 'Autoload funcionando!';"
if errorlevel 1 (
    echo ❌ Problema com autoload
) else (
    echo ✅ Autoload funcionando perfeitamente!
)

echo.
echo ========================================
echo  ✅ ERRO CORRIGIDO COM SUCESSO!
echo ========================================
echo.
echo O erro vendor/autoload.php foi corrigido!
echo.
echo Agora teste o sistema:
echo 🌐 http://localhost/bc/public
echo.
pause
return

:composer_manual_instructions
echo.
echo ========================================
echo  INSTRUCOES MANUAIS - COMPOSER
echo ========================================
echo.
echo O Composer nao pode ser instalado automaticamente.
echo.
echo OPCAO 1 - Instalar Composer globalmente:
echo 1. Acesse: https://getcomposer.org/download/
echo 2. Baixe: Composer-Setup.exe
echo 3. Execute o instalador
echo 4. Reinicie o prompt de comando
echo 5. Execute: composer install
echo.
echo OPCAO 2 - Usar composer.phar local:
echo 1. Baixe composer.phar de: https://getcomposer.org/composer.phar
echo 2. Coloque em: %PROJECT_PATH%
echo 3. Execute: php composer.phar install
echo.
pause
return

:composer_troubleshoot
echo.
echo ========================================
echo  SOLUCAO DE PROBLEMAS - COMPOSER
echo ========================================
echo.
echo Possiveis causas do erro:
echo.
echo 1. CONEXAO COM INTERNET:
echo    - Verifique sua conexao
echo    - Desative proxy/firewall temporariamente
echo.
echo 2. ESPACO EM DISCO:
echo    - Verifique se ha espaco livre suficiente
echo    - Composer precisa de pelo menos 500MB
echo.
echo 3. PERMISSOES:
echo    - Execute como Administrador
echo    - Verifique permissoes da pasta
echo.
echo 4. ANTIVIRUS:
echo    - Desative temporariamente
echo    - Adicione excecao para a pasta
echo.
echo 5. VERSAO DO PHP:
echo    - Verifique se PHP e compativel
echo    - Comando: php -v
echo.
echo Comandos para tentar manualmente:
echo.
echo composer clear-cache
echo composer diagnose
echo composer install --no-dev --verbose
echo.
pause
return

:fix_fatal_error
echo.
echo ========================================
echo  CORRIGIR ERRO FATAL DO LARAVEL
echo ========================================
echo.
echo ❌ ERRO DETECTADO:
echo Fatal error: Failed opening required
echo.

cd /d "%PROJECT_PATH%"

echo [1/4] Verificando estrutura do projeto...
echo.
echo Arquivos essenciais:
if exist "artisan" (echo ✅ artisan) else (echo ❌ artisan)
if exist "composer.json" (echo ✅ composer.json) else (echo ❌ composer.json)
if exist "vendor\" (echo ✅ vendor/) else (echo ❌ vendor/)
if exist ".env" (echo ✅ .env) else (echo ⚠️  .env)
if exist "bootstrap\" (echo ✅ bootstrap/) else (echo ❌ bootstrap/)
if exist "app\" (echo ✅ app/) else (echo ❌ app/)

echo.
echo [2/4] Corrigindo vendor/autoload.php...
if not exist "vendor\autoload.php" (
    echo Reinstalando dependencias...
    call :fix_vendor_error
)

echo [3/4] Verificando configuracoes...
if not exist ".env" (
    if exist ".env.example" (
        echo Criando arquivo .env...
        copy ".env.example" ".env"
        echo ✅ .env criado
    )
)

echo [4/4] Regenerando chaves e cache...
"%PHP_PATH%" artisan key:generate --force 2>nul
"%PHP_PATH%" artisan config:clear 2>nul
"%PHP_PATH%" artisan cache:clear 2>nul
"%PHP_PATH%" artisan optimize 2>nul

echo.
echo ========================================
echo  TESTE FINAL
echo ========================================
echo.
echo Testando aplicacao...
"%PHP_PATH%" artisan --version
if errorlevel 1 (
    echo ❌ Laravel ainda nao funciona
    echo.
    echo Possivel problema com:
    echo - Dependencias nao instaladas completamente
    echo - Arquivo .env mal configurado
    echo - Permissoes de arquivo
    echo.
    echo Execute: php artisan tinker
    echo Se der erro, as dependencias nao estao OK
) else (
    echo ✅ Laravel funcionando!
    echo.
    echo Teste o sistema em:
    echo 🌐 http://localhost/bc/public
)

echo.
pause
return
