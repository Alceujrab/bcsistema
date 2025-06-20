<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'category',
        'label',
        'description',
        'options',
        'is_public',
        'sort_order'
    ];

    protected $casts = [
        'options' => 'json',
        'is_public' => 'boolean'
    ];

    /**
     * Obter valor de uma configuração
     */
    public static function get($key, $default = null)
    {
        $cacheKey = "system_setting_{$key}";
        
        return Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }
            
            return self::castValue($setting->value, $setting->type);
        });
    }

    /**
     * Definir valor de uma configuração
     */
    public static function set($key, $value)
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        // Limpar cache
        Cache::forget("system_setting_{$key}");
        Cache::forget('all_system_settings');

        return $setting;
    }

    /**
     * Obter todas as configurações por categoria
     */
    public static function getByCategory($category)
    {
        $cacheKey = "system_settings_category_{$category}";
        
        return Cache::remember($cacheKey, 3600, function () use ($category) {
            return self::where('category', $category)
                      ->orderBy('sort_order')
                      ->get()
                      ->mapWithKeys(function ($setting) {
                          return [$setting->key => self::castValue($setting->value, $setting->type)];
                      });
        });
    }

    /**
     * Obter todas as configurações públicas
     */
    public static function getPublicSettings()
    {
        $cacheKey = 'public_system_settings';
        
        return Cache::remember($cacheKey, 3600, function () {
            return self::where('is_public', true)
                      ->get()
                      ->mapWithKeys(function ($setting) {
                          return [$setting->key => self::castValue($setting->value, $setting->type)];
                      });
        });
    }

    /**
     * Converter valor baseado no tipo
     */
    private static function castValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $value;
            case 'json':
                return json_decode($value, true);
            case 'array':
                return is_array($value) ? $value : json_decode($value, true);
            default:
                return $value;
        }
    }

    /**
     * Limpar todos os caches de configurações
     */
    public static function clearCache()
    {
        $settings = self::all();
        
        foreach ($settings as $setting) {
            Cache::forget("system_setting_{$setting->key}");
        }
        
        Cache::forget('all_system_settings');
        Cache::forget('public_system_settings');
        
        // Limpar cache por categoria
        $categories = self::distinct('category')->pluck('category');
        foreach ($categories as $category) {
            Cache::forget("system_settings_category_{$category}");
        }
    }

    /**
     * Obter configurações para CSS/JavaScript
     */
    public static function getThemeConfig()
    {
        return [
            'primary_color' => self::get('primary_color', '#667eea'),
            'secondary_color' => self::get('secondary_color', '#764ba2'),
            'success_color' => self::get('success_color', '#28a745'),
            'danger_color' => self::get('danger_color', '#dc3545'),
            'warning_color' => self::get('warning_color', '#ffc107'),
            'info_color' => self::get('info_color', '#17a2b8'),
        ];
    }

    /**
     * Scope para configurações públicas
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope por categoria
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
