<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Sistema de Atualizações - Configurações
    |--------------------------------------------------------------------------
    |
    | Configurações para o sistema de atualizações automáticas
    |
    */

    // IPs autorizados a acessar o sistema de update (vazio = todos)
    'allowed_ips' => [
        // '127.0.0.1',
        // '192.168.1.100',
    ],

    // Token de segurança para acesso ao sistema
    'security_token' => env('UPDATE_SECURITY_TOKEN', null),

    // Diretórios a serem incluídos no backup
    'backup_directories' => [
        'app',
        'config', 
        'database',
        'public',
        'resources',
        'routes',
    ],

    // Arquivos específicos a serem incluídos no backup
    'backup_files' => [
        'composer.json',
        'composer.lock',
        '.env.example',
        'artisan',
    ],

    // Diretórios a serem excluídos do backup
    'backup_exclude' => [
        'storage/logs',
        'storage/framework/cache',
        'storage/framework/sessions',
        'storage/framework/views',
        'vendor',
        'node_modules',
        '.git',
    ],

    // Número máximo de backups a manter
    'max_backups' => 10,

    // Tempo limite para operações de update (em segundos)
    'timeout' => 300,

    // Verificar requirements antes do update
    'check_requirements' => true,

    // Requirements mínimos
    'minimum_requirements' => [
        'php' => '8.2.0',
        'laravel' => '11.0.0',
        'disk_space' => '500MB', // Espaço mínimo em disco
        'memory' => '256MB',     // Memória mínima
    ],

    // Comandos a executar após o update
    'post_update_commands' => [
        'migrate' => true,
        'cache_clear' => true,
        'config_cache' => true,
        'route_cache' => true,
        'view_cache' => true,
    ],

    // Notificações
    'notifications' => [
        'enabled' => true,
        'channels' => ['log'], // log, mail, slack, etc.
        'events' => [
            'update_started',
            'update_completed', 
            'update_failed',
            'backup_created',
            'backup_restored',
        ],
    ],

    // Modos de update
    'modes' => [
        'safe' => [
            'description' => 'Modo seguro - cria backup antes de aplicar',
            'create_backup' => true,
            'rollback_on_failure' => true,
            'test_after_update' => true,
        ],
        'fast' => [
            'description' => 'Modo rápido - aplica diretamente',
            'create_backup' => false,
            'rollback_on_failure' => false,
            'test_after_update' => false,
        ],
        'maintenance' => [
            'description' => 'Modo manutenção - coloca site em manutenção',
            'enable_maintenance' => true,
            'create_backup' => true,
            'rollback_on_failure' => true,
            'test_after_update' => true,
        ],
    ],

    // Modo padrão
    'default_mode' => 'safe',

    // URL de verificação de atualizações remotas (opcional)
    'remote_update_url' => env('REMOTE_UPDATE_URL', null),

    // Chave de API para atualizações remotas
    'remote_api_key' => env('REMOTE_UPDATE_API_KEY', null),

    // Verificação automática de updates
    'auto_check' => [
        'enabled' => false,
        'frequency' => 'daily', // hourly, daily, weekly
        'notify_only' => true,   // apenas notificar, não aplicar
    ],

    // Logs detalhados
    'detailed_logging' => true,

    // Validação de integridade dos arquivos
    'integrity_check' => [
        'enabled' => true,
        'algorithms' => ['sha256', 'md5'],
    ],

    // Rollback automático em caso de falha
    'auto_rollback' => [
        'enabled' => true,
        'timeout' => 30, // segundos para detectar falha
        'health_check_url' => '/health', // endpoint para verificar saúde
    ],
];
