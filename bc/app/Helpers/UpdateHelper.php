<?php

if (!function_exists('formatBytes')) {
    /**
     * Formatar bytes em formato legível
     */
    function formatBytes($size, $precision = 2)
    {
        if ($size == 0) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $base = log($size, 1024);
        
        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $units[floor($base)];
    }
}

if (!function_exists('parseBytes')) {
    /**
     * Converter string de tamanho para bytes
     */
    function parseBytes($size)
    {
        $size = trim($size);
        $last = strtolower($size[strlen($size)-1]);
        $size = (float) $size;
        
        switch($last) {
            case 'g': $size *= 1024;
            case 'm': $size *= 1024;
            case 'k': $size *= 1024;
        }
        
        return (int) $size;
    }
}

if (!function_exists('getCurrentVersion')) {
    /**
     * Obter versão atual do sistema
     */
    function getCurrentVersion()
    {
        return config('app.version', '1.0.0');
    }
}

if (!function_exists('generateUpdateId')) {
    /**
     * Gerar ID único para atualização
     */
    function generateUpdateId($version = null)
    {
        $version = $version ?: time();
        return 'update_' . $version . '_' . substr(md5(uniqid()), 0, 8);
    }
}
