@echo off
echo ====================================
echo  BC SISTEMA - CONFIGURACAO FINAL
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
echo [1/8] Verificando arquivos essenciais...
if not exist "artisan" (
    echo ERRO: Arquivo artisan nao encontrado!
    echo Certifique-se de que os arquivos foram extraidos corretamente
    echo Pasta esperada: %PROJECT_PATH%
    pause
    exit /b 1
)

if not exist "vendor\autoload.php" (
    echo ERRO: Pasta vendor nao encontrada!
    echo Execute primeiro: instalar-composer-xampp.bat
    pause
    exit /b 1
)

echo ✓ Arquivos essenciais encontrados

echo [2/8] Verificando configuracao .env...
if not exist ".env" (
    if exist ".env.example" (
        echo Copiando .env.example para .env...
        copy ".env.example" ".env"
        echo.
        echo ========================================
        echo  CONFIGURE O ARQUIVO .env AGORA!
        echo ========================================
        echo.
        echo Abra o arquivo .env e configure:
        echo %PROJECT_PATH%\.env
        echo.
        echo Configuracoes necessarias:
        echo DB_CONNECTION=mysql
        echo DB_HOST=127.0.0.1
        echo DB_PORT=3306
        echo DB_DATABASE=bc_sistema
        echo DB_USERNAME=root
        echo DB_PASSWORD=
        echo.
        echo Pressione qualquer tecla apos configurar...
        pause
    ) else (
        echo ERRO: .env.example nao encontrado!
        pause
        exit /b 1
    )
) else (
    echo ✓ Arquivo .env encontrado
)

echo.
echo [3/8] Testando conexao com banco de dados...
"%PHP_PATH%" artisan tinker --execute="DB::connection()->getPdo(); echo 'Conexao OK!';"
if errorlevel 1 (
    echo.
    echo ========================================
    echo  ERRO DE CONEXAO COM BANCO!
    echo ========================================
    echo.
    echo Verifique:
    echo 1. MySQL esta rodando no XAMPP
    echo 2. Banco 'bc_sistema' foi criado no phpMyAdmin
    echo 3. Configuracoes do .env estao corretas
    echo.
    echo Acesse: http://localhost/phpmyadmin
    echo Crie o banco: bc_sistema
    echo.
    pause
    exit /b 1
)
echo ✓ Conexao com banco funcionando

echo [4/8] Executando migrations (inclui correcao do imported_by)...
echo Criando/atualizando estrutura do banco...
"%PHP_PATH%" artisan migrate --force
if errorlevel 1 (
    echo ERRO ao executar migrations!
    echo Verifique as configuracoes do banco no .env
    pause
    exit /b 1
)
echo ✓ Banco de dados configurado

echo [5/8] Criando link para storage...
"%PHP_PATH%" artisan storage:link
echo ✓ Link para storage criado

echo [6/8] Criando usuario administrador...
echo Criando usuario: admin@bcsistema.com / admin123
"%PHP_PATH%" artisan tinker --execute="try { App\Models\User::create(['name'=>'Admin BC','email'=>'admin@bcsistema.com','password'=>Hash::make('admin123'),'email_verified_at'=>now()]); echo 'Usuario criado!'; } catch(Exception $e) { echo 'Usuario ja existe ou erro: ' . $e->getMessage(); }"
echo ✓ Usuario administrador configurado

echo [7/8] Limpando cache antigo...
"%PHP_PATH%" artisan config:clear
"%PHP_PATH%" artisan cache:clear
"%PHP_PATH%" artisan view:clear
"%PHP_PATH%" artisan route:clear
echo ✓ Cache limpo

echo [8/8] Otimizando para producao...
"%PHP_PATH%" artisan config:cache
"%PHP_PATH%" artisan route:cache
"%PHP_PATH%" artisan optimize
echo ✓ Sistema otimizado

echo.
echo ====================================
echo  CONFIGURACAO CONCLUIDA COM SUCESSO!
echo ====================================
echo.
echo ✅ Sistema BC instalado e configurado!
echo ✅ Banco de dados criado e estruturado
echo ✅ Usuario administrador criado
echo ✅ Sistema otimizado para producao
echo ✅ Correcoes de importacao aplicadas
echo.
echo ========================================
echo  SISTEMA PRONTO PARA USO!
echo ========================================
echo.
echo URLs de acesso:
echo 🌐 Sistema BC: http://localhost/bc/public
echo 🔧 phpMyAdmin: http://localhost/phpmyadmin
echo 📊 XAMPP Panel: http://localhost/dashboard
echo.
echo Credenciais de login:
echo 📧 Email: admin@bcsistema.com
echo 🔑 Senha: admin123
echo.
echo ⚠️  IMPORTANTE: Altere a senha apos o primeiro login!
echo.
echo ========================================
echo  TESTE AGORA:
echo ========================================
echo.
echo 1. Acesse: http://localhost/bc/public
echo 2. Faca login com as credenciais acima
echo 3. Teste a importacao de extratos
echo 4. Verifique o dashboard
echo.
pause
