@echo off
echo ====================================
echo  BC SISTEMA - INSTALACAO COMPLETA
echo ====================================
echo.

REM Definir variáveis - Detecta automaticamente o XAMPP
set XAMPP_PATH=C:\xampp
if exist "D:\xampp\" set XAMPP_PATH=D:\xampp
if exist "E:\xampp\" set XAMPP_PATH=E:\xampp

set PROJECT_PATH=%XAMPP_PATH%\htdocs\bc

echo Detectado XAMPP em: %XAMPP_PATH%
echo Projeto sera instalado em: %PROJECT_PATH%
echo.

echo ========================================
echo  PRE-REQUISITOS OBRIGATORIOS:
echo ========================================
echo.
echo ✅ XAMPP instalado e funcionando
echo ✅ Apache e MySQL rodando no XAMPP
echo ✅ Banco 'bc_sistema' criado no phpMyAdmin
echo ✅ Arquivos do BC Sistema extraidos em: %PROJECT_PATH%
echo ✅ Arquivo .env configurado (ou sera configurado)
echo.
echo ⚠️  IMPORTANTE: Este script executara TODOS os passos automaticamente!
echo.
echo Pressione qualquer tecla para continuar ou Ctrl+C para cancelar...
pause >nul

echo.
echo ========================================
echo  INICIANDO INSTALACAO AUTOMATICA...
echo ========================================
echo.

echo [PASSO 1/4] Executando instalacao basica...
echo.
call instalar-bc-xampp.bat
if errorlevel 1 (
    echo ❌ ERRO na instalacao basica!
    goto :error
)

echo ✅ Instalacao basica concluida!
echo.

echo [PASSO 2/4] Instalando Composer e dependencias...
echo.
call instalar-composer-xampp.bat
if errorlevel 1 (
    echo ❌ ERRO na instalacao do Composer!
    goto :error
)

echo ✅ Composer e dependencias instalados!
echo.

echo [PASSO 3/4] Configurando sistema final...
echo.
call configurar-bc-xampp.bat
if errorlevel 1 (
    echo ❌ ERRO na configuracao final!
    goto :error
)

echo ✅ Sistema configurado com sucesso!
echo.

echo [PASSO 4/4] Executando testes de funcionamento...
echo.
call testar-bc-xampp.bat
if errorlevel 1 (
    echo ❌ ERRO nos testes de funcionamento!
    goto :error
)

echo ✅ Todos os testes passaram!
echo.

echo ========================================
echo  🎉 INSTALACAO COMPLETA COM SUCESSO!
echo ========================================
echo.
echo O sistema BC foi instalado e configurado com sucesso!
echo.
echo 🌐 URL do Sistema: http://localhost/bc/public
echo 📧 Email de Login: admin@bcsistema.com
echo 🔑 Senha: admin123
echo.
echo ========================================
echo  RESUMO DO QUE FOI FEITO:
echo ========================================
echo ✅ Estrutura de diretorios criada
echo ✅ Arquivos do sistema validados
echo ✅ Arquivo .env configurado
echo ✅ Chave da aplicacao gerada
echo ✅ Composer instalado
echo ✅ Dependencias do Laravel instaladas
echo ✅ Conexao com banco testada
echo ✅ Migrations executadas (com correcao imported_by)
echo ✅ Usuario administrador criado
echo ✅ Sistema otimizado para producao
echo ✅ Testes de funcionamento aprovados
echo.
echo ⚠️  IMPORTANTE: Altere a senha no primeiro login!
echo.
echo Pressione qualquer tecla para abrir o sistema no navegador...
pause >nul

REM Tentar abrir o sistema no navegador padrão
start http://localhost/bc/public

goto :end

:error
echo.
echo ========================================
echo  ❌ ERRO NA INSTALACAO!
echo ========================================
echo.
echo A instalacao foi interrompida devido a um erro.
echo.
echo Solucoes:
echo 1. Verifique se o XAMPP esta rodando
echo 2. Verifique se o banco 'bc_sistema' foi criado
echo 3. Execute os scripts individualmente para identificar o problema:
echo    - instalar-bc-xampp.bat
echo    - instalar-composer-xampp.bat
echo    - configurar-bc-xampp.bat
echo    - testar-bc-xampp.bat
echo.
echo 4. Consulte o arquivo XAMPP-MIGRACAO-COMPLETA.md para mais detalhes
echo.
pause
exit /b 1

:end
echo.
echo 🎉 Sistema BC pronto para uso!
echo.
pause
