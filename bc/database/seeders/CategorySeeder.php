<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Despesas - Alimentação
            [
                'name' => 'Supermercado',
                'icon' => 'fa-shopping-cart',
                'color' => '#28a745',
                'keywords' => ['mercado', 'supermercado', 'hiper', 'atacadao', 'carrefour', 'extra', 'pao de acucar', 'assai'],
                'type' => 'expense',
                'sort_order' => 1
            ],
            [
                'name' => 'Restaurante',
                'icon' => 'fa-utensils',
                'color' => '#fd7e14',
                'keywords' => ['restaurante', 'lanchonete', 'fast food', 'mcdonalds', 'burger king', 'subway', 'pizza'],
                'type' => 'expense',
                'sort_order' => 2
            ],
            [
                'name' => 'Padaria/Café',
                'icon' => 'fa-coffee',
                'color' => '#795548',
                'keywords' => ['padaria', 'cafe', 'cafeteria', 'starbucks', 'padoca', 'confeitaria'],
                'type' => 'expense',
                'sort_order' => 3
            ],
            [
                'name' => 'Delivery',
                'icon' => 'fa-motorcycle',
                'color' => '#e91e63',
                'keywords' => ['ifood', 'uber eats', 'rappi', 'delivery', 'entrega'],
                'type' => 'expense',
                'sort_order' => 4
            ],

            // Despesas - Transporte
            [
                'name' => 'Combustível',
                'icon' => 'fa-gas-pump',
                'color' => '#f44336',
                'keywords' => ['posto', 'gasolina', 'combustivel', 'shell', 'ipiranga', 'petrobras', 'alcool', 'diesel'],
                'type' => 'expense',
                'sort_order' => 5
            ],
            [
                'name' => 'Transporte App',
                'icon' => 'fa-car',
                'color' => '#9c27b0',
                'keywords' => ['uber', '99', 'cabify', 'taxi', 'corrida'],
                'type' => 'expense',
                'sort_order' => 6
            ],
            [
                'name' => 'Estacionamento',
                'icon' => 'fa-parking',
                'color' => '#3f51b5',
                'keywords' => ['estacionamento', 'zona azul', 'estapar', 'multipark', 'parking'],
                'type' => 'expense',
                'sort_order' => 7
            ],
            [
                'name' => 'Pedágio',
                'icon' => 'fa-road',
                'color' => '#00bcd4',
                'keywords' => ['pedagio', 'autoban', 'ecovias', 'artesp', 'sem parar', 'veloe'],
                'type' => 'expense',
                'sort_order' => 8
            ],
            [
                'name' => 'Manutenção Veículo',
                'icon' => 'fa-wrench',
                'color' => '#ff5722',
                'keywords' => ['oficina', 'mecanica', 'manutencao', 'pneu', 'oleo', 'revisao', 'auto'],
                'type' => 'expense',
                'sort_order' => 9
            ],

            // Despesas - Moradia
            [
                'name' => 'Aluguel',
                'icon' => 'fa-home',
                'color' => '#607d8b',
                'keywords' => ['aluguel', 'locacao', 'imobiliaria'],
                'type' => 'expense',
                'sort_order' => 10
            ],
            [
                'name' => 'Condomínio',
                'icon' => 'fa-building',
                'color' => '#455a64',
                'keywords' => ['condominio', 'taxa condominial'],
                'type' => 'expense',
                'sort_order' => 11
            ],
            [
                'name' => 'Energia Elétrica',
                'icon' => 'fa-bolt',
                'color' => '#ffc107',
                'keywords' => ['luz', 'energia', 'eletrica', 'cpfl', 'enel', 'eletropaulo', 'cemig'],
                'type' => 'expense',
                'sort_order' => 12
            ],
            [
                'name' => 'Água',
                'icon' => 'fa-tint',
                'color' => '#03a9f4',
                'keywords' => ['agua', 'sabesp', 'saneamento', 'saae'],
                'type' => 'expense',
                'sort_order' => 13
            ],
            [
                'name' => 'Gás',
                'icon' => 'fa-fire',
                'color' => '#ff9800',
                'keywords' => ['gas', 'comgas', 'ultragaz', 'liquigas'],
                'type' => 'expense',
                'sort_order' => 14
            ],
            [
                'name' => 'Internet/Telefone',
                'icon' => 'fa-wifi',
                'color' => '#2196f3',
                'keywords' => ['internet', 'telefone', 'vivo', 'claro', 'oi', 'tim', 'net', 'banda larga'],
                'type' => 'expense',
                'sort_order' => 15
            ],

            // Despesas - Saúde
            [
                'name' => 'Farmácia',
                'icon' => 'fa-pills',
                'color' => '#4caf50',
                'keywords' => ['farmacia', 'drogaria', 'droga raia', 'drogasil', 'pague menos', 'medicamento', 'remedio'],
                'type' => 'expense',
                'sort_order' => 16
            ],
            [
                'name' => 'Médico/Hospital',
                'icon' => 'fa-hospital',
                'color' => '#f44336',
                'keywords' => ['medico', 'hospital', 'clinica', 'consulta', 'exame', 'laboratorio', 'saude'],
                'type' => 'expense',
                'sort_order' => 17
            ],
            [
                'name' => 'Plano de Saúde',
                'icon' => 'fa-heartbeat',
                'color' => '#e91e63',
                'keywords' => ['unimed', 'amil', 'sulamerica', 'bradesco saude', 'hapvida', 'plano saude'],
                'type' => 'expense',
                'sort_order' => 18
            ],
            [
                'name' => 'Academia',
                'icon' => 'fa-dumbbell',
                'color' => '#ff5722',
                'keywords' => ['academia', 'smart fit', 'bio ritmo', 'fitness', 'gym', 'personal'],
                'type' => 'expense',
                'sort_order' => 19
            ],

            // Despesas - Educação
            [
                'name' => 'Escola/Faculdade',
                'icon' => 'fa-graduation-cap',
                'color' => '#3f51b5',
                'keywords' => ['escola', 'faculdade', 'universidade', 'mensalidade', 'colegio', 'curso'],
                'type' => 'expense',
                'sort_order' => 20
            ],
            [
                'name' => 'Cursos',
                'icon' => 'fa-book',
                'color' => '#9c27b0',
                'keywords' => ['curso', 'treinamento', 'workshop', 'udemy', 'coursera', 'ingles'],
                'type' => 'expense',
                'sort_order' => 21
            ],
            [
                'name' => 'Material Escolar',
                'icon' => 'fa-pencil-alt',
                'color' => '#673ab7',
                'keywords' => ['papelaria', 'material escolar', 'livro', 'livraria', 'kalunga'],
                'type' => 'expense',
                'sort_order' => 22
            ],

            // Despesas - Lazer
            [
                'name' => 'Cinema/Teatro',
                'icon' => 'fa-film',
                'color' => '#e91e63',
                'keywords' => ['cinema', 'teatro', 'show', 'ingresso', 'cinemark', 'uci'],
                'type' => 'expense',
                'sort_order' => 23
            ],
            [
                'name' => 'Streaming',
                'icon' => 'fa-tv',
                'color' => '#d32f2f',
                'keywords' => ['netflix', 'spotify', 'amazon prime', 'disney', 'hbo', 'globoplay', 'deezer'],
                'type' => 'expense',
                'sort_order' => 24
            ],
            [
                'name' => 'Viagem',
                'icon' => 'fa-plane',
                'color' => '#1976d2',
                'keywords' => ['viagem', 'hotel', 'pousada', 'airbnb', 'booking', 'passagem', 'decolar'],
                'type' => 'expense',
                'sort_order' => 25
            ],
            [
                'name' => 'Bar/Balada',
                'icon' => 'fa-glass-martini',
                'color' => '#7b1fa2',
                'keywords' => ['bar', 'balada', 'cerveja', 'drink', 'pub', 'boate', 'festa'],
                'type' => 'expense',
                'sort_order' => 26
            ],

            // Despesas - Compras
            [
                'name' => 'Roupas/Calçados',
                'icon' => 'fa-tshirt',
                'color' => '#ec407a',
                'keywords' => ['roupa', 'calcado', 'sapato', 'tenis', 'zara', 'renner', 'c&a', 'riachuelo'],
                'type' => 'expense',
                'sort_order' => 27
            ],
            [
                'name' => 'Eletrônicos',
                'icon' => 'fa-laptop',
                'color' => '#5c6bc0',
                'keywords' => ['eletronico', 'celular', 'notebook', 'computador', 'tv', 'fast shop', 'magazine'],
                'type' => 'expense',
                'sort_order' => 28
            ],
            [
                'name' => 'Casa/Decoração',
                'icon' => 'fa-couch',
                'color' => '#8d6e63',
                'keywords' => ['movel', 'decoracao', 'tok stok', 'leroy merlin', 'ikea', 'etna'],
                'type' => 'expense',
                'sort_order' => 29
            ],
            [
                'name' => 'Presentes',
                'icon' => 'fa-gift',
                'color' => '#d81b60',
                'keywords' => ['presente', 'gift', 'aniversario', 'natal'],
                'type' => 'expense',
                'sort_order' => 30
            ],

            // Despesas - Serviços
            [
                'name' => 'Salão/Barbearia',
                'icon' => 'fa-cut',
                'color' => '#ad1457',
                'keywords' => ['salao', 'cabeleireiro', 'barbearia', 'manicure', 'estetica'],
                'type' => 'expense',
                'sort_order' => 31
            ],
            [
                'name' => 'Pet',
                'icon' => 'fa-paw',
                'color' => '#6a4c93',
                'keywords' => ['pet', 'veterinario', 'racao', 'petshop', 'petz', 'cobasi'],
                'type' => 'expense',
                'sort_order' => 32
            ],
            [
                'name' => 'Assinaturas',
                'icon' => 'fa-sync',
                'color' => '#00acc1',
                'keywords' => ['assinatura', 'mensalidade', 'anuidade'],
                'type' => 'expense',
                'sort_order' => 33
            ],

            // Despesas - Financeiro
            [
                'name' => 'Impostos/Taxas',
                'icon' => 'fa-file-invoice-dollar',
                'color' => '#d32f2f',
                'keywords' => ['imposto', 'taxa', 'iptu', 'ipva', 'multa', 'governo', 'tributo'],
                'type' => 'expense',
                'sort_order' => 34
            ],
            [
                'name' => 'Tarifas Bancárias',
                'icon' => 'fa-university',
                'color' => '#c62828',
                'keywords' => ['tarifa', 'anuidade', 'manutencao conta', 'ted', 'doc', 'bancaria'],
                'type' => 'expense',
                'sort_order' => 35
            ],
            [
                'name' => 'Juros',
                'icon' => 'fa-percentage',
                'color' => '#b71c1c',
                'keywords' => ['juros', 'mora', 'encargo', 'rotativo'],
                'type' => 'expense',
                'sort_order' => 36
            ],
            [
                'name' => 'Seguro',
                'icon' => 'fa-shield-alt',
                'color' => '#1a237e',
                'keywords' => ['seguro', 'porto seguro', 'sulamerica', 'bradesco seguro', 'azul', 'prudential'],
                'type' => 'expense',
                'sort_order' => 37
            ],
            [
                'name' => 'Empréstimo',
                'icon' => 'fa-hand-holding-usd',
                'color' => '#b71c1c',
                'keywords' => ['emprestimo', 'financiamento', 'parcela', 'credito'],
                'type' => 'expense',
                'sort_order' => 38
            ],

            // Receitas
            [
                'name' => 'Salário',
                'icon' => 'fa-money-check-alt',
                'color' => '#2e7d32',
                'keywords' => ['salario', 'pagamento', 'holerite', 'vencimento', 'remuneracao'],
                'type' => 'income',
                'sort_order' => 39
            ],
            [
                'name' => 'Freelance',
                'icon' => 'fa-laptop-code',
                'color' => '#388e3c',
                'keywords' => ['freelance', 'freelancer', 'projeto', 'job', 'trabalho'],
                'type' => 'income',
                'sort_order' => 40
            ],
            [
                'name' => 'Vendas',
                'icon' => 'fa-shopping-bag',
                'color' => '#43a047',
                'keywords' => ['venda', 'vendeu', 'mercado livre', 'olx', 'cliente'],
                'type' => 'income',
                'sort_order' => 41
            ],
            [
                'name' => 'Investimentos',
                'icon' => 'fa-chart-line',
                'color' => '#66bb6a',
                'keywords' => ['rendimento', 'dividendo', 'investimento', 'aplicacao', 'resgate'],
                'type' => 'income',
                'sort_order' => 42
            ],
            [
                'name' => 'Reembolso',
                'icon' => 'fa-undo',
                'color' => '#81c784',
                'keywords' => ['reembolso', 'estorno', 'devolucao', 'ressarcimento'],
                'type' => 'income',
                'sort_order' => 43
            ],
            [
                'name' => 'Presente/Doação',
                'icon' => 'fa-gift',
                'color' => '#4caf50',
                'keywords' => ['presente', 'doacao', 'gift', 'mesada'],
                'type' => 'income',
                'sort_order' => 44
            ],

            // Transferências
            [
                'name' => 'Transferência Entre Contas',
                'icon' => 'fa-exchange-alt',
                'color' => '#9e9e9e',
                'keywords' => ['transferencia', 'ted', 'doc', 'pix', 'transf'],
                'type' => 'both',
                'sort_order' => 45
            ],
            [
                'name' => 'Saque',
                'icon' => 'fa-money-bill-wave',
                'color' => '#757575',
                'keywords' => ['saque', 'caixa eletronico', 'atm', '24 horas', 'banco24'],
                'type' => 'both',
                'sort_order' => 46
            ],
            [
                'name' => 'Depósito',
                'icon' => 'fa-piggy-bank',
                'color' => '#616161',
                'keywords' => ['deposito', 'dep', 'envelope'],
                'type' => 'both',
                'sort_order' => 47
            ],

            // Outros
            [
                'name' => 'Outros',
                'icon' => 'fa-ellipsis-h',
                'color' => '#9e9e9e',
                'keywords' => [],
                'type' => 'both',
                'sort_order' => 48
            ],
        ];

        foreach ($categories as $categoryData) {
            $categoryData['slug'] = \Str::slug($categoryData['name']);
            Category::create($categoryData);
        }
    }
}
