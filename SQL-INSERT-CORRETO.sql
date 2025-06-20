INSERT IGNORE INTO `updates` (`version`, `name`, `description`, `status`, `created_at`, `updated_at`) VALUES
('1.0.0', 'Sistema Base', 'Instalação inicial do sistema', 'applied', NOW(), NOW()),
('1.1.0', 'Importação de Extratos', 'Adição do sistema de importação de extratos bancários', 'applied', NOW(), NOW()),
('1.2.0', 'Sistema de Updates', 'Implementação do sistema de atualizações automáticas', 'applied', NOW(), NOW()),
('1.3.0', 'Melhorias na Interface', 'Correções e melhorias na interface do usuário', 'available', NOW(), NOW()),
('1.4.0', 'Otimizações de Performance', 'Otimizações gerais do sistema e correção de bugs', 'available', NOW(), NOW());
