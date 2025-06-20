<?php
/**
 * Script para criar a tabela updates no MySQL
 * Execute este script via browser ou linha de comando
 */

// Configurações do banco de dados
$host = 'localhost';
$port = 3306;
$database = 'usadosar_lara962';
$username = 'usadosar_lara962';
$password = '[17pvS1-16';

try {
    // Conectar ao MySQL usando MySQLi
    $mysqli = new mysqli($host, $username, $password, $database, $port);
    
    if ($mysqli->connect_error) {
        die("Erro de conexão: " . $mysqli->connect_error);
    }
    
    echo "Conexão com MySQL estabelecida com sucesso!\n";
    
    // SQL para criar a tabela updates
    $createTableSQL = "
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
    ";
    
    if ($mysqli->query($createTableSQL)) {
        echo "Tabela 'updates' criada com sucesso!\n";
    } else {
        echo "Erro ao criar tabela: " . $mysqli->error . "\n";
    }
    
    // SQL para inserir dados de exemplo
    $insertDataSQL = "
    INSERT IGNORE INTO `updates` (`version`, `title`, `description`, `status`, `created_at`, `updated_at`) VALUES
    ('1.0.0', 'Sistema Base', 'Instalação inicial do sistema', 'applied', NOW(), NOW()),
    ('1.1.0', 'Importação de Extratos', 'Adição do sistema de importação de extratos bancários', 'applied', NOW(), NOW()),
    ('1.2.0', 'Sistema de Updates', 'Implementação do sistema de atualizações automáticas', 'applied', NOW(), NOW()),
    ('1.3.0', 'Melhorias na Interface', 'Correções e melhorias na interface do usuário', 'available', NOW(), NOW()),
    ('1.4.0', 'Otimizações de Performance', 'Otimizações gerais do sistema e correção de bugs', 'available', NOW(), NOW());
    ";
    
    if ($mysqli->query($insertDataSQL)) {
        echo "Dados de exemplo inseridos com sucesso!\n";
    } else {
        echo "Erro ao inserir dados: " . $mysqli->error . "\n";
    }
    
    // Verificar se a tabela foi criada
    $result = $mysqli->query("SHOW TABLES LIKE 'updates'");
    if ($result && $result->num_rows > 0) {
        echo "Tabela 'updates' existe no banco de dados!\n";
        
        // Contar registros
        $count = $mysqli->query("SELECT COUNT(*) as total FROM updates");
        $row = $count->fetch_assoc();
        echo "Total de registros na tabela updates: " . $row['total'] . "\n";
    } else {
        echo "Tabela 'updates' não foi encontrada!\n";
    }
    
    $mysqli->close();
    echo "Script executado com sucesso!\n";
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?>
