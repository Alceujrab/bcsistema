# INSTRUÇÕES PARA CORRIGIR O ERRO DA TABELA UPDATES

## 🔴 ERRO ATUAL
```
SQLSTATE[42S02]: Base table or view not found: 1146 Table 'usadosar_lara962.updates' doesn't exist
```

## 🔧 SOLUÇÃO RÁPIDA

### Passo 1: Acessar o phpMyAdmin
1. Faça login no seu painel de controle do hosting
2. Localize e clique em "phpMyAdmin" ou "Gerenciador de Banco de Dados"
3. Selecione o banco de dados: `usadosar_lara962`

### Passo 2: Executar o SQL
1. Clique na aba "SQL" no phpMyAdmin
2. Cole o código SQL abaixo:

```sql
-- Criar tabela updates
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

-- Inserir dados iniciais
INSERT IGNORE INTO `updates` (`version`, `title`, `description`, `status`, `created_at`, `updated_at`) VALUES
('1.0.0', 'Sistema Base', 'Instalação inicial do sistema', 'applied', NOW(), NOW()),
('1.1.0', 'Importação de Extratos', 'Adição do sistema de importação de extratos bancários', 'applied', NOW(), NOW()),
('1.2.2', 'Sistema de Updates', 'Implementação do sistema de atualizações automáticas', 'applied', NOW(), NOW()),
('1.3.0', 'Melhorias na Interface', 'Correções e melhorias na interface do usuário', 'available', NOW(), NOW()),
('1.4.0', 'Otimizações de Performance', 'Otimizações gerais do sistema e correção de bugs', 'available', NOW(), NOW());
```

3. Clique em "Executar" ou "Go"

### Passo 3: Verificar
1. Após executar o SQL, você verá uma mensagem de sucesso
2. Acesse novamente: https://usadosar.com.br/bc/system/update
3. O sistema de updates deve funcionar normalmente

## 🎯 RESULTADO ESPERADO
- Tabela `updates` criada com sucesso
- 5 registros de exemplo inseridos
- Sistema de updates funcionando
- Sem mais erros de "table not found"

## 📱 ALTERNATIVA PELO SISTEMA
Se preferir, você também pode:
1. Acessar: https://usadosar.com.br/bc/system/update
2. O sistema detectará automaticamente que a tabela não existe
3. Exibirá uma página com instruções e o SQL para copiar

## ⚠️ IMPORTANTE
- Execute o SQL apenas uma vez
- O comando `CREATE TABLE IF NOT EXISTS` é seguro para re-executar
- Os dados são inseridos com `INSERT IGNORE` para evitar duplicatas

## 🔍 VERIFICAÇÃO
Para confirmar que funcionou:
```sql
SELECT COUNT(*) FROM updates;
```
Deve retornar: 5 registros

## 🆘 PROBLEMAS?
Se ainda tiver problemas:
1. Verifique se está no banco correto: `usadosar_lara962`
2. Confirme se o usuário tem permissão para criar tabelas
3. Verifique se não há erros de sintaxe no SQL

---
**Criado em:** 18 de Junho de 2025
**Sistema:** BC Sistema de Gestão Financeira
**Versão:** 1.2.2
