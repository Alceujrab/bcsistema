<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Categorias para Contas a Pagar
        $payableCategories = [
            'services' => 'Serviços',
            'products' => 'Produtos',
            'utilities' => 'Utilidades',
            'rent' => 'Aluguel',
            'taxes' => 'Impostos', 
            'maintenance' => 'Manutenção',
            'marketing' => 'Marketing',
            'office_supplies' => 'Material de Escritório',
            'fuel' => 'Combustível',
            'insurance' => 'Seguro',
            'legal' => 'Jurídico',
            'accounting' => 'Contabilidade',
            'other' => 'Outros'
        ];

        // Categorias para Contas a Receber
        $receivableCategories = [
            'sales' => 'Vendas',
            'services' => 'Serviços',
            'rent' => 'Aluguel',
            'consultancy' => 'Consultoria',
            'commission' => 'Comissão',
            'interest' => 'Juros',
            'dividend' => 'Dividendos',
            'royalty' => 'Royalty',
            'license' => 'Licenciamento',
            'subscription' => 'Assinatura',
            'other' => 'Outros'
        ];

        // Salvar categorias no sistema de configurações
        SystemSetting::updateOrCreate(
            ['key' => 'payable_categories'],
            [
                'value' => json_encode($payableCategories),
                'description' => 'Categorias disponíveis para Contas a Pagar',
                'type' => 'json'
            ]
        );

        SystemSetting::updateOrCreate(
            ['key' => 'receivable_categories'],
            [
                'value' => json_encode($receivableCategories),
                'description' => 'Categorias disponíveis para Contas a Receber',
                'type' => 'json'
            ]
        );

        echo "✓ Categorias dinâmicas criadas com sucesso!\n";
    }
}
