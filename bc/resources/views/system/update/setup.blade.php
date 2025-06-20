@extends('layouts.app')

@section('title', 'Configuração do Sistema de Updates')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Sistema de Updates - Configuração Necessária
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning" role="alert">
                        <h6><i class="fas fa-info-circle me-2"></i>Tabela de Updates Não Encontrada</h6>
                        <p class="mb-0">O sistema de updates precisa ser configurado. A tabela <code>updates</code> não existe no banco de dados.</p>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <h6>Instruções para Configuração:</h6>
                            
                            <div class="alert alert-info">
                                <h6><i class="fas fa-database me-2"></i>Passo 1: Executar SQL no Banco</h6>
                                <p>Execute o seguinte SQL no seu painel de controle MySQL (phpMyAdmin, cPanel, etc.):</p>
                                
                                <div class="bg-dark text-light p-3 rounded mb-3">
                                    <pre><code>-- Execute no banco: usadosar_lara962

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

-- Dados iniciais
INSERT IGNORE INTO `updates` (`version`, `title`, `description`, `status`, `created_at`, `updated_at`) VALUES
('1.0.0', 'Sistema Base', 'Instalação inicial do sistema', 'applied', NOW(), NOW()),
('1.1.0', 'Importação de Extratos', 'Adição do sistema de importação de extratos bancários', 'applied', NOW(), NOW()),
('1.2.0', 'Sistema de Updates', 'Implementação do sistema de atualizações automáticas', 'applied', NOW(), NOW()),
('1.3.0', 'Melhorias na Interface', 'Correções e melhorias na interface do usuário', 'available', NOW(), NOW()),
('1.4.0', 'Otimizações de Performance', 'Otimizações gerais do sistema e correção de bugs', 'available', NOW(), NOW());</code></pre>
                                </div>
                                
                                <button class="btn btn-sm btn-outline-primary" onclick="copySQL()">
                                    <i class="fas fa-copy me-1"></i>Copiar SQL
                                </button>
                            </div>

                            <div class="alert alert-success">
                                <h6><i class="fas fa-refresh me-2"></i>Passo 2: Recarregar a Página</h6>
                                <p class="mb-2">Após executar o SQL, recarregue esta página para acessar o sistema de updates.</p>
                                <button class="btn btn-success btn-sm" onclick="location.reload()">
                                    <i class="fas fa-refresh me-1"></i>Recarregar Página
                                </button>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informações do Sistema</h6>
                                </div>
                                <div class="card-body">
                                    <p><strong>Banco:</strong> usadosar_lara962</p>
                                    <p><strong>Host:</strong> localhost</p>
                                    <p><strong>Laravel:</strong> {{ app()->version() }}</p>
                                    <p><strong>PHP:</strong> {{ PHP_VERSION }}</p>
                                </div>
                            </div>

                            <div class="card bg-primary text-white mt-3">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-question-circle me-2"></i>Precisa de Ajuda?</h6>
                                </div>
                                <div class="card-body">
                                    <p class="small mb-2">Se tiver dificuldades:</p>
                                    <ul class="small">
                                        <li>Acesse o phpMyAdmin</li>
                                        <li>Selecione o banco usadosar_lara962</li>
                                        <li>Cole e execute o SQL acima</li>
                                        <li>Recarregue esta página</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copySQL() {
    const sqlText = `-- Execute no banco: usadosar_lara962

CREATE TABLE IF NOT EXISTS \`updates\` (
    \`id\` bigint unsigned NOT NULL AUTO_INCREMENT,
    \`version\` varchar(255) NOT NULL,
    \`title\` varchar(255) NOT NULL,
    \`description\` text,
    \`file_path\` varchar(255) DEFAULT NULL,
    \`file_hash\` varchar(255) DEFAULT NULL,
    \`file_size\` bigint DEFAULT NULL,
    \`status\` enum('available','downloading','applying','applied','failed','rolled_back') NOT NULL DEFAULT 'available',
    \`applied_at\` timestamp NULL DEFAULT NULL,
    \`rolled_back_at\` timestamp NULL DEFAULT NULL,
    \`error_message\` text,
    \`backup_path\` varchar(255) DEFAULT NULL,
    \`created_at\` timestamp NULL DEFAULT NULL,
    \`updated_at\` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (\`id\`),
    UNIQUE KEY \`updates_version_unique\` (\`version\`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dados iniciais
INSERT IGNORE INTO \`updates\` (\`version\`, \`title\`, \`description\`, \`status\`, \`created_at\`, \`updated_at\`) VALUES
('1.0.0', 'Sistema Base', 'Instalação inicial do sistema', 'applied', NOW(), NOW()),
('1.1.0', 'Importação de Extratos', 'Adição do sistema de importação de extratos bancários', 'applied', NOW(), NOW()),
('1.2.0', 'Sistema de Updates', 'Implementação do sistema de atualizações automáticas', 'applied', NOW(), NOW()),
('1.3.0', 'Melhorias na Interface', 'Correções e melhorias na interface do usuário', 'available', NOW(), NOW()),
('1.4.0', 'Otimizações de Performance', 'Otimizações gerais do sistema e correção de bugs', 'available', NOW(), NOW());`;
    
    navigator.clipboard.writeText(sqlText).then(function() {
        alert('SQL copiado para a área de transferência!');
    }, function(err) {
        console.error('Erro ao copiar: ', err);
        // Fallback para browsers antigos
        const textArea = document.createElement('textarea');
        textArea.value = sqlText;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('SQL copiado para a área de transferência!');
    });
}
</script>
@endsection
