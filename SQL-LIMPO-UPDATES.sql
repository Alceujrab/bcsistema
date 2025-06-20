CREATE TABLE IF NOT EXISTS `updates` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `version` varchar(255) NOT NULL,
    `title` varchar(255) NOT NULL,
    `description` text,
    `file_path` varchar(255) DEFAULT NULL,
    `file_hash` varchar(255) DEFAULT NULL,
    `file_size` bigint DEFAULT NULL,
    `status` enum('available','downloading','applying','applied','failed','rolled_back') NOT NULL DEFAULT 'available',
    `applied_at` timestamp NULL DEFAULT NULL,
    `rolled_back_at` timestamp NULL DEFAULT NULL,
    `error_message` text,
    `backup_path` varchar(255) DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `updates_version_unique` (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `updates` (`version`, `title`, `description`, `status`, `created_at`, `updated_at`) VALUES
('1.0.0', 'Sistema Base', 'Instalação inicial do sistema', 'applied', NOW(), NOW()),
('1.1.0', 'Importação de Extratos', 'Adição do sistema de importação de extratos bancários', 'applied', NOW(), NOW()),
('1.2.0', 'Sistema de Updates', 'Implementação do sistema de atualizações automáticas', 'applied', NOW(), NOW()),
('1.3.0', 'Melhorias na Interface', 'Correções e melhorias na interface do usuário', 'available', NOW(), NOW()),
('1.4.0', 'Otimizações de Performance', 'Otimizações gerais do sistema e correção de bugs', 'available', NOW(), NOW());

SELECT * FROM updates;
