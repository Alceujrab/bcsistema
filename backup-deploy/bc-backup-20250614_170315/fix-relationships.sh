#!/bin/bash

echo "Corrigindo todos os erros de relacionamento..."

# 1. Remover todas as referências a ->with('category') no TransactionController
sed -i "s/with\(\['bankAccount', 'category'\]\)/with('bankAccount')/g" /workspaces/bcsistema/bc/app/Http/Controllers/TransactionController.php

# 2. Remover where('active', true) das categorias
sed -i "s/Category::where('active', true)/Category::/g" /workspaces/bcsistema/bc/app/Http/Controllers/TransactionController.php

# 3. Corrigir BankAccount active
sed -i "s/BankAccount::where('active', true)/BankAccount::/g" /workspaces/bcsistema/bc/app/Http/Controllers/ReportController.php

echo "Correções aplicadas!"
