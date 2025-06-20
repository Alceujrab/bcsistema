#!/bin/bash

# Script de instalação do Sistema de Conciliação Bancária
# Execute estes comandos na ordem apresentada

echo "=== INICIANDO INSTALAÇÃO DO SISTEMA DE CONCILIAÇÃO BANCÁRIA ==="

# 1. Criar os Models
echo "Criando Models..."
php artisan make:model BankAccount
php artisan make:model Transaction
php artisan make:model Reconciliation
php artisan make:model StatementImport

# 2. Criar os Controllers
echo "Criando Controllers..."
php artisan make:controller DashboardController
php artisan make:controller BankAccountController --resource
php artisan make:controller TransactionController --resource
php artisan make:controller ReconciliationController --resource
php artisan make:controller ImportController
php artisan make:controller ReportController

# 3. Criar o diretório de Services
echo "Criando diretório de Services..."
mkdir -p app/Services

# 4. Criar Job de processamento
echo "Criando Job..."
php artisan make:job ProcessReconciliation

# 5. Criar comando personalizado
echo "Criando comando Artisan..."
php artisan make:command ImportBankStatements

# 6. Criar estrutura de diretórios para views
echo "Criando estrutura de views..."
mkdir -p resources/views/layouts
mkdir -p resources/views/bank-accounts
mkdir -p resources/views/transactions
mkdir -p resources/views/reconciliations
mkdir -p resources/views/imports
mkdir -p resources/views/reports

# 7. Criar diretório de templates
echo "Criando diretório de templates..."
mkdir -p storage/app/templates

# 8. Executar migrations
echo "Executando migrations..."
php artisan migrate

# 9. Limpar caches
echo "Limpando caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 10. Otimizar aplicação
echo "Otimizando aplicação..."
php artisan optimize

echo "=== INSTALAÇÃO CONCLUÍDA ==="
echo "Agora você precisa:"
echo "1. Copiar o conteúdo dos Models, Controllers e Services fornecidos"
echo "2. Criar as views necessárias"
echo "3. Configurar o cron job no cPanel"
echo "4. Ajustar as permissões das pastas storage e bootstrap/cache"