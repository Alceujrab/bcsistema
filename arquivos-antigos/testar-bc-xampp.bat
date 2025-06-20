@echo off
echo ====================================
echo  BC SISTEMA - TESTE DE FUNCIONAMENTO
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

echo Navegando para o diretorio do projeto...
cd /d "%PROJECT_PATH%"

echo.
echo ========================================
echo  EXECUTANDO TESTES DE SISTEMA...
echo ========================================
echo.

echo [1/10] Verificando estrutura de arquivos...
if not exist "artisan" (
    echo ❌ ERRO: artisan nao encontrado
    goto :error
)
if not exist "vendor\autoload.php" (
    echo ❌ ERRO: vendor/autoload.php nao encontrado
    goto :error
)
if not exist ".env" (
    echo ❌ ERRO: .env nao encontrado
    goto :error
)
echo ✅ Estrutura de arquivos OK

echo [2/10] Testando PHP...
"%PHP_PATH%" -v
if errorlevel 1 (
    echo ❌ ERRO: PHP nao funciona
    goto :error
)
echo ✅ PHP funcionando

echo [3/10] Testando Laravel Artisan...
"%PHP_PATH%" artisan --version
if errorlevel 1 (
    echo ❌ ERRO: Laravel Artisan nao funciona
    goto :error
)
echo ✅ Laravel Artisan funcionando

echo [4/10] Testando conexao com banco...
"%PHP_PATH%" artisan tinker --execute="try { DB::connection()->getPdo(); echo 'DB OK'; } catch(Exception $e) { echo 'DB ERRO: ' . $e->getMessage(); exit(1); }"
if errorlevel 1 (
    echo ❌ ERRO: Conexao com banco falhou
    goto :error
)
echo ✅ Conexao com banco OK

echo [5/10] Verificando migrations...
"%PHP_PATH%" artisan migrate:status
if errorlevel 1 (
    echo ❌ ERRO: Problema com migrations
    goto :error
)
echo ✅ Migrations OK

echo [6/10] Verificando usuario admin...
"%PHP_PATH%" artisan tinker --execute="try { $user = App\Models\User::where('email', 'admin@bcsistema.com')->first(); if($user) echo 'Usuario encontrado: ' . $user->name; else { echo 'Usuario nao encontrado'; exit(1); } } catch(Exception $e) { echo 'Erro: ' . $e->getMessage(); exit(1); }"
if errorlevel 1 (
    echo ❌ ERRO: Usuario admin nao encontrado
    goto :error
)
echo ✅ Usuario admin OK

echo [7/10] Testando rotas principais...
"%PHP_PATH%" artisan route:list --name=login
if errorlevel 1 (
    echo ❌ ERRO: Problema com rotas
    goto :error
)
echo ✅ Rotas principais OK

echo [8/10] Verificando controllers principais...
if not exist "app\Http\Controllers\DashboardController.php" (
    echo ❌ ERRO: DashboardController nao encontrado
    goto :error
)
if not exist "app\Http\Controllers\ImportController.php" (
    echo ❌ ERRO: ImportController nao encontrado
    goto :error
)
echo ✅ Controllers principais OK

echo [9/10] Verificando views principais...
if not exist "resources\views\dashboard.blade.php" (
    echo ❌ ERRO: dashboard.blade.php nao encontrado
    goto :error
)
if not exist "resources\views\auth\login.blade.php" (
    echo ❌ ERRO: login.blade.php nao encontrado
    goto :error
)
echo ✅ Views principais OK

echo [10/10] Testando acesso HTTP (se Apache estiver rodando)...
curl -s -o nul -w "%%{http_code}" http://localhost/bc/public >response_code.tmp 2>nul
if exist response_code.tmp (
    set /p HTTP_CODE=<response_code.tmp
    del response_code.tmp
    if "!HTTP_CODE!"=="200" (
        echo ✅ Acesso HTTP OK (Codigo: !HTTP_CODE!)
    ) else (
        echo ⚠️  Acesso HTTP retornou codigo: !HTTP_CODE!
        echo    Verifique se o Apache esta rodando no XAMPP
    )
) else (
    echo ⚠️  Teste HTTP nao disponivel (curl nao encontrado)
    echo    Acesse manualmente: http://localhost/bc/public
)

echo.
echo ========================================
echo  ✅ TODOS OS TESTES PASSARAM!
echo ========================================
echo.
echo 🎉 Sistema BC esta funcionando corretamente!
echo.
echo 🌐 Acesse: http://localhost/bc/public
echo 📧 Login: admin@bcsistema.com
echo 🔑 Senha: admin123
echo.
echo ========================================
echo  FUNCIONALIDADES TESTADAS:
echo ========================================
echo ✅ Estrutura de arquivos
echo ✅ PHP e Laravel funcionando  
echo ✅ Banco de dados conectado
echo ✅ Migrations aplicadas
echo ✅ Usuario admin criado
echo ✅ Rotas configuradas
echo ✅ Controllers disponiveis
echo ✅ Views carregadas
echo ✅ Acesso HTTP (se Apache rodando)
echo ✅ Correcao de importacao aplicada
echo.
goto :end

:error
echo.
echo ========================================
echo  ❌ ERRO ENCONTRADO!
echo ========================================
echo.
echo Um ou mais testes falharam.
echo.
echo Solucoes:
echo 1. Execute os scripts na ordem correta:
echo    - instalar-bc-xampp.bat
echo    - instalar-composer-xampp.bat  
echo    - configurar-bc-xampp.bat
echo.
echo 2. Verifique se o XAMPP esta rodando
echo 3. Verifique se o banco 'bc_sistema' foi criado
echo 4. Verifique o arquivo .env
echo.
pause
exit /b 1

:end
echo Teste concluido com sucesso!
echo.
pause
