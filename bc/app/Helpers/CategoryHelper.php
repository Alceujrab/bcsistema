<?php

namespace App\Helpers;

use App\Models\SystemSetting;

class CategoryHelper
{
    /**
     * Obter categorias para contas a pagar
     */
    public static function getPayableCategories(): array
    {
        $setting = SystemSetting::where('key', 'payable_categories')->first();
        
        if ($setting && $setting->value) {
            return json_decode($setting->value, true) ?? [];
        }

        // Categorias padrão se não houver configuração
        return [
            'services' => 'Serviços',
            'products' => 'Produtos',
            'utilities' => 'Utilidades',
            'rent' => 'Aluguel',
            'taxes' => 'Impostos',
            'other' => 'Outros'
        ];
    }

    /**
     * Obter categorias para contas a receber
     */
    public static function getReceivableCategories(): array
    {
        $setting = SystemSetting::where('key', 'receivable_categories')->first();
        
        if ($setting && $setting->value) {
            return json_decode($setting->value, true) ?? [];
        }

        // Categorias padrão se não houver configuração
        return [
            'sales' => 'Vendas',
            'services' => 'Serviços', 
            'rent' => 'Aluguel',
            'other' => 'Outros'
        ];
    }

    /**
     * Adicionar nova categoria para contas a pagar
     */
    public static function addPayableCategory(string $key, string $label): bool
    {
        try {
            $categories = self::getPayableCategories();
            $categories[$key] = $label;
            
            SystemSetting::updateOrCreate(
                ['key' => 'payable_categories'],
                [
                    'value' => json_encode($categories),
                    'description' => 'Categorias disponíveis para Contas a Pagar',
                    'type' => 'json'
                ]
            );
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Adicionar nova categoria para contas a receber
     */
    public static function addReceivableCategory(string $key, string $label): bool
    {
        try {
            $categories = self::getReceivableCategories();
            $categories[$key] = $label;
            
            SystemSetting::updateOrCreate(
                ['key' => 'receivable_categories'],
                [
                    'value' => json_encode($categories),
                    'description' => 'Categorias disponíveis para Contas a Receber',
                    'type' => 'json'
                ]
            );
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Remover categoria para contas a pagar
     */
    public static function removePayableCategory(string $key): bool
    {
        try {
            $categories = self::getPayableCategories();
            unset($categories[$key]);
            
            SystemSetting::updateOrCreate(
                ['key' => 'payable_categories'],
                [
                    'value' => json_encode($categories),
                    'description' => 'Categorias disponíveis para Contas a Pagar',
                    'type' => 'json'
                ]
            );
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Remover categoria para contas a receber
     */
    public static function removeReceivableCategory(string $key): bool
    {
        try {
            $categories = self::getReceivableCategories();
            unset($categories[$key]);
            
            SystemSetting::updateOrCreate(
                ['key' => 'receivable_categories'],
                [
                    'value' => json_encode($categories),
                    'description' => 'Categorias disponíveis para Contas a Receber',
                    'type' => 'json'
                ]
            );
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Obter nome da categoria por chave
     */
    public static function getCategoryName(string $type, string $key): string
    {
        if ($type === 'payable') {
            $categories = self::getPayableCategories();
        } else {
            $categories = self::getReceivableCategories();
        }

        return $categories[$key] ?? ucfirst($key);
    }
}
