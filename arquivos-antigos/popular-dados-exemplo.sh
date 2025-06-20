#!/bin/bash

echo "=== POPULANDO BANCO DE DADOS COM DADOS DE EXEMPLO ==="

cd /workspaces/bcsistema/bc

# Criar alguns clientes de exemplo
echo "=== Criando clientes de exemplo ==="
php artisan tinker << 'EOF'
use App\Models\Client;

Client::create([
    'name' => 'João Silva',
    'document' => '123.456.789-00',
    'email' => 'joao@exemplo.com',
    'phone' => '(11) 99999-1111',
    'address' => 'Rua das Flores, 123',
    'city' => 'São Paulo',
    'state' => 'SP',
    'zipcode' => '01234-567',
    'status' => 'active'
]);

Client::create([
    'name' => 'Maria Santos',
    'document' => '987.654.321-00',
    'email' => 'maria@exemplo.com',
    'phone' => '(11) 88888-2222',
    'address' => 'Av. Principal, 456',
    'city' => 'Rio de Janeiro',
    'state' => 'RJ',
    'zipcode' => '20000-000',
    'status' => 'active'
]);

Client::create([
    'name' => 'Empresa ABC Ltda',
    'document' => '12.345.678/0001-90',
    'email' => 'contato@abc.com',
    'phone' => '(11) 3333-4444',
    'address' => 'Rua Comercial, 789',
    'city' => 'Belo Horizonte',
    'state' => 'MG',
    'zipcode' => '30000-000',
    'status' => 'active'
]);

echo "Clientes criados com sucesso!";
EOF

# Criar alguns fornecedores de exemplo
echo "=== Criando fornecedores de exemplo ==="
php artisan tinker << 'EOF'
use App\Models\Supplier;

Supplier::create([
    'name' => 'Fornecedor XYZ Ltda',
    'document' => '11.222.333/0001-44',
    'email' => 'vendas@xyz.com',
    'phone' => '(11) 5555-6666',
    'address' => 'Rua Industrial, 100',
    'city' => 'São Paulo',
    'state' => 'SP',
    'zipcode' => '05000-000',
    'contact_person' => 'Carlos Mendes',
    'status' => 'active'
]);

Supplier::create([
    'name' => 'Distribuidora 123',
    'document' => '22.333.444/0001-55',
    'email' => 'compras@dist123.com',
    'phone' => '(21) 7777-8888',
    'address' => 'Av. Logística, 500',
    'city' => 'Rio de Janeiro',
    'state' => 'RJ',
    'zipcode' => '21000-000',
    'contact_person' => 'Ana Costa',
    'status' => 'active'
]);

echo "Fornecedores criados com sucesso!";
EOF

# Criar algumas contas a pagar
echo "=== Criando contas a pagar de exemplo ==="
php artisan tinker << 'EOF'
use App\Models\AccountPayable;
use App\Models\Supplier;

$suppliers = Supplier::all();

foreach($suppliers as $supplier) {
    AccountPayable::create([
        'supplier_id' => $supplier->id,
        'description' => 'Fornecimento de materiais - ' . $supplier->name,
        'amount' => rand(500, 5000),
        'due_date' => now()->addDays(rand(1, 30)),
        'issue_date' => now()->subDays(rand(1, 10)),
        'status' => 'pending',
        'notes' => 'Conta gerada automaticamente para teste'
    ]);
    
    AccountPayable::create([
        'supplier_id' => $supplier->id,
        'description' => 'Serviços prestados - ' . $supplier->name,
        'amount' => rand(1000, 3000),
        'due_date' => now()->addDays(rand(5, 45)),
        'issue_date' => now()->subDays(rand(1, 5)),
        'status' => 'pending',
        'notes' => 'Pagamento mensal'
    ]);
}

echo "Contas a pagar criadas com sucesso!";
EOF

# Criar algumas contas a receber
echo "=== Criando contas a receber de exemplo ==="
php artisan tinker << 'EOF'
use App\Models\AccountReceivable;
use App\Models\Client;

$clients = Client::all();

foreach($clients as $client) {
    AccountReceivable::create([
        'client_id' => $client->id,
        'description' => 'Venda de produtos - ' . $client->name,
        'amount' => rand(800, 6000),
        'due_date' => now()->addDays(rand(1, 30)),
        'issue_date' => now()->subDays(rand(1, 10)),
        'status' => 'pending',
        'notes' => 'Faturamento mensal'
    ]);
    
    AccountReceivable::create([
        'client_id' => $client->id,
        'description' => 'Prestação de serviços - ' . $client->name,
        'amount' => rand(1200, 4000),
        'due_date' => now()->addDays(rand(10, 60)),
        'issue_date' => now()->subDays(rand(1, 7)),
        'status' => 'pending',
        'notes' => 'Cobrança de serviços'
    ]);
}

echo "Contas a receber criadas com sucesso!";
EOF

echo ""
echo "=== DADOS DE EXEMPLO CRIADOS COM SUCESSO! ==="
echo ""
echo "Clientes: $(php artisan tinker --execute="echo App\Models\Client::count();")"
echo "Fornecedores: $(php artisan tinker --execute="echo App\Models\Supplier::count();")"
echo "Contas a Pagar: $(php artisan tinker --execute="echo App\Models\AccountPayable::count();")"
echo "Contas a Receber: $(php artisan tinker --execute="echo App\Models\AccountReceivable::count();")"
echo ""
echo "Acesse o sistema em: http://localhost:8000"
echo ""
