<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criar usuário admin
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@usadosar.com.br',
            'password' => bcrypt('senha123'),
        ]);
        
        $this->command->info('Usuário admin criado: admin@usadosar.com.br (senha: senha123)');
        
        // Criar contas bancárias
        $contaCorrente = BankAccount::create([
            'name' => 'Conta Corrente Principal',
            'bank_name' => 'Banco do Brasil',
            'account_number' => '12345-6',
            'agency' => '1234',
            'type' => 'checking',
            'balance' => 5000.00,
            'active' => true,
        ]);
        
        $cartaoCredito = BankAccount::create([
            'name' => 'Cartão Corporativo',
            'bank_name' => 'Itaú',
            'account_number' => '****1234',
            'agency' => null,
            'type' => 'credit_card',
            'balance' => -2500.00,
            'active' => true,
        ]);
        
        $poupanca = BankAccount::create([
            'name' => 'Poupança Reserva',
            'bank_name' => 'Caixa Econômica',
            'account_number' => '00123456-7',
            'agency' => '0001',
            'type' => 'savings',
            'balance' => 10000.00,
            'active' => true,
        ]);
        
        $this->command->info('3 contas bancárias criadas');
        
        // Criar transações de exemplo para conta corrente
        $transacoesContaCorrente = [
            ['desc' => 'Salário', 'valor' => 8000, 'tipo' => 'credit', 'cat' => 'Salário'],
            ['desc' => 'Aluguel Escritório', 'valor' => 2500, 'tipo' => 'debit', 'cat' => 'Moradia'],
            ['desc' => 'Conta de Luz', 'valor' => 350, 'tipo' => 'debit', 'cat' => 'Moradia'],
            ['desc' => 'Internet Empresa', 'valor' => 150, 'tipo' => 'debit', 'cat' => 'Tecnologia'],
            ['desc' => 'Venda de Produto', 'valor' => 1500, 'tipo' => 'credit', 'cat' => 'Vendas'],
            ['desc' => 'Material de Escritório', 'valor' => 200, 'tipo' => 'debit', 'cat' => 'Outros'],
            ['desc' => 'Combustível', 'valor' => 300, 'tipo' => 'debit', 'cat' => 'Transporte'],
            ['desc' => 'Recebimento Cliente', 'valor' => 3000, 'tipo' => 'credit', 'cat' => 'Vendas'],
        ];
        
        foreach ($transacoesContaCorrente as $index => $trans) {
            Transaction::create([
                'bank_account_id' => $contaCorrente->id,
                'transaction_date' => Carbon::now()->subDays(30 - $index * 3),
                'description' => $trans['desc'],
                'amount' => $trans['valor'],
                'type' => $trans['tipo'],
                'category' => $trans['cat'],
                'status' => $index < 4 ? 'reconciled' : 'pending',
                'reference_number' => 'REF' . str_pad($index + 1, 6, '0', STR_PAD_LEFT),
                'import_hash' => md5($contaCorrente->id . $trans['desc'] . $trans['valor']),
            ]);
        }
        
        // Criar transações para cartão de crédito
        $transacoesCartao = [
            ['desc' => 'Uber Empresa', 'valor' => 150, 'cat' => 'Transporte'],
            ['desc' => 'Restaurante - Reunião Cliente', 'valor' => 380, 'cat' => 'Alimentação'],
            ['desc' => 'Assinatura Software', 'valor' => 200, 'cat' => 'Tecnologia'],
            ['desc' => 'Hotel - Viagem Negócios', 'valor' => 850, 'cat' => 'Viagem'],
            ['desc' => 'Passagem Aérea', 'valor' => 1200, 'cat' => 'Viagem'],
        ];
        
        foreach ($transacoesCartao as $index => $trans) {
            Transaction::create([
                'bank_account_id' => $cartaoCredito->id,
                'transaction_date' => Carbon::now()->subDays(20 - $index * 4),
                'description' => $trans['desc'],
                'amount' => $trans['valor'],
                'type' => 'debit',
                'category' => $trans['cat'],
                'status' => 'pending',
                'reference_number' => 'CC' . str_pad($index + 1, 6, '0', STR_PAD_LEFT),
                'import_hash' => md5($cartaoCredito->id . $trans['desc'] . $trans['valor']),
            ]);
        }
        
        $this->command->info('Transações de exemplo criadas');
        
        // Criar template de importação CSV
        $csvTemplate = "Data,Descrição,Valor,Referência\n";
        $csvTemplate .= "2024-01-01,Pagamento Fornecedor,-500.00,REF001\n";
        $csvTemplate .= "2024-01-02,Recebimento Cliente,1000.00,REF002\n";
        $csvTemplate .= "2024-01-03,Despesa Administrativa,-150.00,REF003\n";
        
        $templatePath = storage_path('app/templates');
        if (!file_exists($templatePath)) {
            mkdir($templatePath, 0755, true);
        }
        
        file_put_contents($templatePath . '/import-template.csv', $csvTemplate);
        
        $this->command->info('Template de importação CSV criado');
        
        // Executar seeder de categorias
        $this->call(CategorySeeder::class);
        
        // Atualizar transações com categorias
        $this->command->info('Atualizando transações com categorias...');
        
        $categories = \App\Models\Category::all();
        $transactions = \App\Models\Transaction::whereNull('category_id')->get();
        
        foreach ($transactions as $transaction) {
            foreach ($categories as $category) {
                if ($category->matchesDescription($transaction->description)) {
                    $transaction->update(['category_id' => $category->id]);
                    break;
                }
            }
        }

        // Executar seeders específicos
        $this->call([
            SystemSettingsSeeder::class,
            CategoriesSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('=== SEEDER CONCLUÍDO COM SUCESSO ===');
        $this->command->info('Email: admin@usadosar.com.br');
        $this->command->info('Senha: senha123');
        $this->command->info('');
    }
}
