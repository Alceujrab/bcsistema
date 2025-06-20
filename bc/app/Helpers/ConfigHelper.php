<?php

namespace App\Helpers;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

class ConfigHelper
{
    /**
     * Obter configuração por chave
     */
    public static function get($key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function() use ($key, $default) {
            $setting = SystemSetting::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Obter todas as configurações de uma categoria
     */
    public static function getByCategory($category)
    {
        return Cache::remember("settings_{$category}", 3600, function() use ($category) {
            return SystemSetting::where('category', $category)
                               ->pluck('value', 'key')
                               ->toArray();
        });
    }

    /**
     * Gerar CSS dinâmico baseado nas configurações
     */
    public static function generateDynamicCSS()
    {
        $appearance = self::getByCategory('appearance');
        
        $css = ':root {';
        
        // Cores principais
        if (isset($appearance['primary_color'])) {
            $css .= '--primary-color: ' . $appearance['primary_color'] . ';';
        }
        
        if (isset($appearance['secondary_color'])) {
            $css .= '--secondary-color: ' . $appearance['secondary_color'] . ';';
        }
        
        if (isset($appearance['accent_color'])) {
            $css .= '--accent-color: ' . $appearance['accent_color'] . ';';
        }
        
        if (isset($appearance['background_color'])) {
            $css .= '--background-color: ' . $appearance['background_color'] . ';';
        }
        
        // Cores do sidebar
        if (isset($appearance['sidebar_bg_color'])) {
            $css .= '--sidebar-bg: ' . $appearance['sidebar_bg_color'] . ';';
        }
        
        if (isset($appearance['sidebar_text_color'])) {
            $css .= '--sidebar-text: ' . $appearance['sidebar_text_color'] . ';';
        }
        
        // Cores dos cards
        if (isset($appearance['card_bg_color'])) {
            $css .= '--card-bg: ' . $appearance['card_bg_color'] . ';';
        }
        
        if (isset($appearance['card_border_color'])) {
            $css .= '--card-border: ' . $appearance['card_border_color'] . ';';
        }
        
        $css .= '}';
        
        // Aplicar cores personalizadas
        if (isset($appearance['primary_color'])) {
            $primaryColor = $appearance['primary_color'];
            $css .= "
                .btn-primary {
                    background-color: {$primaryColor} !important;
                    border-color: {$primaryColor} !important;
                }
                .btn-primary:hover {
                    background-color: " . self::darkenColor($primaryColor, 10) . " !important;
                    border-color: " . self::darkenColor($primaryColor, 10) . " !important;
                }
                .card-header.bg-primary {
                    background-color: {$primaryColor} !important;
                }
            ";
        }
        
        if (isset($appearance['secondary_color'])) {
            $secondaryColor = $appearance['secondary_color'];
            $css .= "
                .btn-secondary {
                    background-color: {$secondaryColor} !important;
                    border-color: {$secondaryColor} !important;
                }
                .btn-secondary:hover {
                    background-color: " . self::darkenColor($secondaryColor, 10) . " !important;
                    border-color: " . self::darkenColor($secondaryColor, 10) . " !important;
                }
            ";
        }
        
        return $css;
    }

    /**
     * Escurecer uma cor hexadecimal
     */
    private static function darkenColor($color, $percent)
    {
        $color = ltrim($color, '#');
        $rgb = array_map('hexdec', str_split($color, 2));
        
        foreach ($rgb as &$value) {
            $value = max(0, min(255, $value - ($value * $percent / 100)));
        }
        
        return '#' . implode('', array_map(function($val) {
            return str_pad(dechex($val), 2, '0', STR_PAD_LEFT);
        }, $rgb));
    }

    /**
     * Obter dados da empresa
     */
    public static function getCompanyData()
    {
        return [
            'name' => self::get('company_name', 'BC Sistema'),
            'logo' => self::get('company_logo', ''),
            'slogan' => self::get('company_slogan', 'Sistema de Gestão Financeira'),
            'address' => self::get('company_address', ''),
            'phone' => self::get('company_phone', ''),
            'email' => self::get('company_email', ''),
            'website' => self::get('company_website', ''),
        ];
    }

    /**
     * Obter configurações do dashboard
     */
    public static function getDashboardConfig()
    {
        return [
            'show_welcome_message' => self::get('dashboard_show_welcome', true),
            'show_quick_stats' => self::get('dashboard_show_stats', true),
            'show_recent_activities' => self::get('dashboard_show_activities', true),
            'show_charts' => self::get('dashboard_show_charts', true),
            'auto_refresh' => self::get('dashboard_auto_refresh', false),
            'refresh_interval' => self::get('dashboard_refresh_interval', 300),
        ];
    }

    /**
     * Limpar cache de configurações
     */
    public static function clearCache($key = null)
    {
        if ($key) {
            Cache::forget("setting_{$key}");
        } else {
            // Limpar todas as configurações em cache
            $keys = SystemSetting::pluck('key');
            foreach ($keys as $key) {
                Cache::forget("setting_{$key}");
            }
            
            // Limpar cache por categoria
            $categories = SystemSetting::distinct('category')->pluck('category');
            foreach ($categories as $category) {
                Cache::forget("settings_{$category}");
            }
        }
    }
}
