-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 19/06/2025 às 15:38
-- Versão do servidor: 10.6.22-MariaDB-cll-lve
-- Versão do PHP: 8.3.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `usadosar_lara962`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `account_payables`
--

CREATE TABLE `account_payables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `description` varchar(191) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `due_date` date NOT NULL,
  `payment_date` date DEFAULT NULL,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','partial','paid','overdue') NOT NULL DEFAULT 'pending',
  `document_number` varchar(191) DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Despejando dados para a tabela `account_payables`
--

INSERT INTO `account_payables` (`id`, `supplier_id`, `description`, `amount`, `due_date`, `payment_date`, `paid_amount`, `status`, `document_number`, `category`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'ywerre', 1000.00, '2026-08-01', NULL, 0.00, 'pending', NULL, 'Água', NULL, '2025-06-18 00:11:54', '2025-06-18 00:11:54');

-- --------------------------------------------------------

--
-- Estrutura para tabela `account_receivables`
--

CREATE TABLE `account_receivables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `description` varchar(191) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `due_date` date NOT NULL,
  `payment_date` date DEFAULT NULL,
  `received_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','partial','received','overdue') NOT NULL DEFAULT 'pending',
  `invoice_number` varchar(191) DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `bank_accounts`
--

CREATE TABLE `bank_accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `bank_name` varchar(191) NOT NULL,
  `account_number` varchar(191) NOT NULL,
  `agency` varchar(191) DEFAULT NULL,
  `bank_code` varchar(191) DEFAULT NULL,
  `type` enum('checking','savings','investment','credit_card') DEFAULT NULL,
  `balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Despejando dados para a tabela `bank_accounts`
--

INSERT INTO `bank_accounts` (`id`, `name`, `bank_name`, `account_number`, `agency`, `bank_code`, `type`, `balance`, `active`, `created_at`, `updated_at`) VALUES
(6, 'Santander', 'Santander', '1300048-0', '441', '033', 'checking', 0.00, 1, '2025-06-16 21:29:39', '2025-06-18 00:05:08'),
(4, 'Banco do Brasil', 'Banco do Brasil', '262617', '1317x', NULL, 'checking', 0.00, 1, '2025-06-12 19:12:15', '2025-06-12 19:12:15'),
(5, 'Cartao BB Visa Infitini', 'Cartão Visa', '1317', '1317', NULL, 'credit_card', 0.00, 1, '2025-06-16 21:27:29', '2025-06-18 19:22:59'),
(7, 'Cartao C6 pessoal', 'C6', '4414', '0201', '336', 'credit_card', -1000.00, 1, '2025-06-17 13:36:39', '2025-06-17 14:44:38');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cache`
--

CREATE TABLE `cache` (
  `key` varchar(191) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Despejando dados para a tabela `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('conciliacao_setting_company_name', 's:10:\"BC Sistema\";', 1750359280),
('conciliacao_setting_company_logo', 's:0:\"\";', 1750359280),
('conciliacao_setting_company_slogan', 's:29:\"Sistema de Gestão Financeira\";', 1750359280),
('conciliacao_setting_company_address', 's:0:\"\";', 1750359280),
('conciliacao_setting_company_phone', 's:0:\"\";', 1750359280),
('conciliacao_setting_company_email', 's:0:\"\";', 1750359280),
('conciliacao_setting_company_website', 's:0:\"\";', 1750359280),
('conciliacao_setting_dashboard_show_welcome', 'b:1;', 1750359280),
('conciliacao_setting_dashboard_show_stats', 'b:1;', 1750359280),
('conciliacao_setting_dashboard_show_activities', 'b:1;', 1750359280),
('conciliacao_setting_dashboard_show_charts', 'b:1;', 1750359280),
('conciliacao_setting_dashboard_auto_refresh', 'b:0;', 1750359280),
('conciliacao_setting_dashboard_refresh_interval', 's:3:\"300\";', 1750359280),
('conciliacao_settings_appearance', 'a:6:{s:13:\"primary_color\";s:7:\"#667eea\";s:15:\"secondary_color\";s:7:\"#764ba2\";s:13:\"success_color\";s:7:\"#28a745\";s:12:\"danger_color\";s:7:\"#dc3545\";s:13:\"warning_color\";s:7:\"#ffc107\";s:10:\"info_color\";s:7:\"#17a2b8\";}', 1750359280),
('conciliacao_settings_general', 'a:4:{s:12:\"company_name\";s:10:\"BC Sistema\";s:12:\"company_logo\";s:0:\"\";s:8:\"timezone\";s:17:\"America/Sao_Paulo\";s:8:\"currency\";s:3:\"BRL\";}', 1750359280);

-- --------------------------------------------------------

--
-- Estrutura para tabela `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(191) NOT NULL,
  `owner` varchar(191) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `icon` varchar(191) DEFAULT NULL,
  `color` varchar(191) NOT NULL DEFAULT '#6c757d',
  `keywords` text DEFAULT NULL,
  `type` enum('income','expense','both') NOT NULL DEFAULT 'both',
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `budget_limit` decimal(15,2) DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `rules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`rules`))
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Despejando dados para a tabela `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `icon`, `color`, `keywords`, `type`, `active`, `sort_order`, `created_at`, `updated_at`, `parent_id`, `budget_limit`, `is_system`, `rules`) VALUES
(1, 'Supermercado', 'supermercado', 'fa-shopping-cart', '#28a745', '[\"mercado\",\"supermercado\",\"hiper\",\"atacadao\",\"carrefour\",\"extra\",\"pao de acucar\",\"assai\"]', 'expense', 1, 1, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(2, 'Restaurante', 'restaurante', 'fa-utensils', '#fd7e14', '[\"restaurante\",\"lanchonete\",\"fast food\",\"mcdonalds\",\"burger king\",\"subway\",\"pizza\"]', 'expense', 1, 2, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(3, 'Padaria/Café', 'padariacafe', 'fa-coffee', '#795548', '[\"padaria\",\"cafe\",\"cafeteria\",\"starbucks\",\"padoca\",\"confeitaria\"]', 'expense', 1, 3, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(4, 'Delivery', 'delivery', 'fa-motorcycle', '#e91e63', '[\"ifood\",\"uber eats\",\"rappi\",\"delivery\",\"entrega\"]', 'expense', 1, 4, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(5, 'Combustível', 'combustivel', 'fa-gas-pump', '#f44336', '[\"posto\",\"gasolina\",\"combustivel\",\"shell\",\"ipiranga\",\"petrobras\",\"alcool\",\"diesel\"]', 'expense', 1, 5, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(6, 'Transporte App', 'transporte-app', 'fa-car', '#9c27b0', '[\"uber\",\"99\",\"cabify\",\"taxi\",\"corrida\"]', 'expense', 1, 6, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(7, 'Estacionamento', 'estacionamento', 'fa-parking', '#3f51b5', '[\"estacionamento\",\"zona azul\",\"estapar\",\"multipark\",\"parking\"]', 'expense', 1, 7, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(8, 'Pedágio', 'pedagio', 'fa-road', '#00bcd4', '[\"pedagio\",\"autoban\",\"ecovias\",\"artesp\",\"sem parar\",\"veloe\"]', 'expense', 1, 8, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(9, 'Manutenção Veículo', 'manutencao-veiculo', 'fa-wrench', '#ff5722', '[\"oficina\",\"mecanica\",\"manutencao\",\"pneu\",\"oleo\",\"revisao\",\"auto\"]', 'expense', 1, 9, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(10, 'Aluguel', 'aluguel', 'fa-home', '#607d8b', '[\"aluguel\",\"locacao\",\"imobiliaria\"]', 'expense', 1, 10, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(11, 'Condomínio', 'condominio', 'fa-building', '#455a64', '[\"condominio\",\"taxa condominial\"]', 'expense', 1, 11, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(12, 'Energia Elétrica', 'energia-eletrica', 'fa-bolt', '#ffc107', '[\"luz\",\"energia\",\"eletrica\",\"cpfl\",\"enel\",\"eletropaulo\",\"cemig\"]', 'expense', 1, 12, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(13, 'Água', 'agua', 'fa-tint', '#03a9f4', '[\"agua\",\"sabesp\",\"saneamento\",\"saae\"]', 'expense', 1, 13, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(14, 'Gás', 'gas', 'fa-fire', '#ff9800', '[\"gas\",\"comgas\",\"ultragaz\",\"liquigas\"]', 'expense', 1, 14, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(15, 'Internet/Telefone', 'internettelefone', 'fa-wifi', '#2196f3', '[\"internet\",\"telefone\",\"vivo\",\"claro\",\"oi\",\"tim\",\"net\",\"banda larga\"]', 'expense', 1, 15, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(16, 'Farmácia', 'farmacia', 'fa-pills', '#4caf50', '[\"farmacia\",\"drogaria\",\"droga raia\",\"drogasil\",\"pague menos\",\"medicamento\",\"remedio\"]', 'expense', 1, 16, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(17, 'Médico/Hospital', 'medicohospital', 'fa-hospital', '#f44336', '[\"medico\",\"hospital\",\"clinica\",\"consulta\",\"exame\",\"laboratorio\",\"saude\"]', 'expense', 1, 17, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(18, 'Plano de Saúde', 'plano-de-saude', 'fa-heartbeat', '#e91e63', '[\"unimed\",\"amil\",\"sulamerica\",\"bradesco saude\",\"hapvida\",\"plano saude\"]', 'expense', 1, 18, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(19, 'Academia', 'academia', 'fa-dumbbell', '#ff5722', '[\"academia\",\"smart fit\",\"bio ritmo\",\"fitness\",\"gym\",\"personal\"]', 'expense', 1, 19, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(20, 'Escola/Faculdade', 'escolafaculdade', 'fa-graduation-cap', '#3f51b5', '[\"escola\",\"faculdade\",\"universidade\",\"mensalidade\",\"colegio\",\"curso\"]', 'expense', 1, 20, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(21, 'Cursos', 'cursos', 'fa-book', '#9c27b0', '[\"curso\",\"treinamento\",\"workshop\",\"udemy\",\"coursera\",\"ingles\"]', 'expense', 1, 21, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(22, 'Material Escolar', 'material-escolar', 'fa-pencil-alt', '#673ab7', '[\"papelaria\",\"material escolar\",\"livro\",\"livraria\",\"kalunga\"]', 'expense', 1, 22, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(23, 'Cinema/Teatro', 'cinemateatro', 'fa-film', '#e91e63', '[\"cinema\",\"teatro\",\"show\",\"ingresso\",\"cinemark\",\"uci\"]', 'expense', 1, 23, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(24, 'Streaming', 'streaming', 'fa-tv', '#d32f2f', '[\"netflix\",\"spotify\",\"amazon prime\",\"disney\",\"hbo\",\"globoplay\",\"deezer\"]', 'expense', 1, 24, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(25, 'Viagem', 'viagem', 'fa-plane', '#1976d2', '[\"viagem\",\"hotel\",\"pousada\",\"airbnb\",\"booking\",\"passagem\",\"decolar\"]', 'expense', 1, 25, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(26, 'Bar/Balada', 'barbalada', 'fa-glass-martini', '#7b1fa2', '[\"bar\",\"balada\",\"cerveja\",\"drink\",\"pub\",\"boate\",\"festa\"]', 'expense', 1, 26, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(27, 'Roupas/Calçados', 'roupascalcados', 'fa-tshirt', '#ec407a', '[\"roupa\",\"calcado\",\"sapato\",\"tenis\",\"zara\",\"renner\",\"c&a\",\"riachuelo\"]', 'expense', 1, 27, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(28, 'Eletrônicos', 'eletronicos', 'fa-laptop', '#5c6bc0', '[\"eletronico\",\"celular\",\"notebook\",\"computador\",\"tv\",\"fast shop\",\"magazine\"]', 'expense', 1, 28, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(29, 'Casa/Decoração', 'casadecoracao', 'fa-couch', '#8d6e63', '[\"movel\",\"decoracao\",\"tok stok\",\"leroy merlin\",\"ikea\",\"etna\"]', 'expense', 1, 29, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(30, 'Presentes', 'presentes', 'fa-gift', '#d81b60', '[\"presente\",\"gift\",\"aniversario\",\"natal\"]', 'expense', 1, 30, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(31, 'Salão/Barbearia', 'salaobarbearia', 'fa-cut', '#ad1457', '[\"salao\",\"cabeleireiro\",\"barbearia\",\"manicure\",\"estetica\"]', 'expense', 1, 31, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(32, 'Pet', 'pet', 'fa-paw', '#6a4c93', '[\"pet\",\"veterinario\",\"racao\",\"petshop\",\"petz\",\"cobasi\"]', 'expense', 1, 32, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(33, 'Assinaturas', 'assinaturas', 'fa-sync', '#00acc1', '[\"assinatura\",\"mensalidade\",\"anuidade\"]', 'expense', 1, 33, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(34, 'Impostos/Taxas', 'impostostaxas', 'fa-file-invoice-dollar', '#d32f2f', '[\"imposto\",\"taxa\",\"iptu\",\"ipva\",\"multa\",\"governo\",\"tributo\"]', 'expense', 1, 34, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(35, 'Tarifas Bancárias', 'tarifas-bancarias', 'fa-university', '#c62828', '[\"tarifa\",\"anuidade\",\"manutencao conta\",\"ted\",\"doc\",\"bancaria\"]', 'expense', 1, 35, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(36, 'Juros', 'juros', 'fa-percentage', '#b71c1c', '[\"juros\",\"mora\",\"encargo\",\"rotativo\"]', 'expense', 1, 36, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(37, 'Seguro', 'seguro', 'fa-shield-alt', '#1a237e', '[\"seguro\",\"porto seguro\",\"sulamerica\",\"bradesco seguro\",\"azul\",\"prudential\"]', 'expense', 1, 37, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(38, 'Empréstimo', 'emprestimo', 'fa-hand-holding-usd', '#b71c1c', '[\"emprestimo\",\"financiamento\",\"parcela\",\"credito\"]', 'expense', 1, 38, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(39, 'Salário', 'salario', 'fa-money-check-alt', '#2e7d32', '[\"salario\",\"pagamento\",\"holerite\",\"vencimento\",\"remuneracao\"]', 'income', 1, 39, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(40, 'Freelance', 'freelance', 'fa-laptop-code', '#388e3c', '[\"freelance\",\"freelancer\",\"projeto\",\"job\",\"trabalho\"]', 'income', 1, 40, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(41, 'Vendas', 'vendas', 'fa-shopping-bag', '#43a047', '[\"venda\",\"vendeu\",\"mercado livre\",\"olx\",\"cliente\"]', 'income', 1, 41, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(42, 'Investimentos', 'investimentos', 'fa-chart-line', '#66bb6a', '[\"rendimento\",\"dividendo\",\"investimento\",\"aplicacao\",\"resgate\"]', 'income', 1, 42, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(43, 'Reembolso', 'reembolso', 'fa-undo', '#81c784', '[\"reembolso\",\"estorno\",\"devolucao\",\"ressarcimento\"]', 'income', 1, 43, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(44, 'Presente/Doação', 'presentedoacao', 'fa-gift', '#4caf50', '[\"presente\",\"doacao\",\"gift\",\"mesada\"]', 'income', 1, 44, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(45, 'Transferência Entre Contas', 'transferencia-entre-contas', 'fa-exchange-alt', '#9e9e9e', '[\"transferencia\",\"ted\",\"doc\",\"pix\",\"transf\"]', 'both', 1, 45, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(46, 'Saque', 'saque', 'fa-money-bill-wave', '#757575', '[\"saque\",\"caixa eletronico\",\"atm\",\"24 horas\",\"banco24\"]', 'both', 1, 46, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(47, 'Depósito', 'deposito', 'fa-piggy-bank', '#616161', '[\"deposito\",\"dep\",\"envelope\"]', 'both', 1, 47, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(48, 'Outros', 'outros', 'fa-ellipsis-h', '#9e9e9e', '[]', 'both', 1, 48, '2025-06-12 18:43:18', '2025-06-12 18:43:18', NULL, NULL, 0, NULL),
(49, 'Pai - Fazenda', 'pai-fazenda', 'tag', '#db24ba', '[\"despesas para fazenda do pai\"]', 'expense', 1, 0, '2025-06-17 14:35:07', '2025-06-17 14:35:07', NULL, NULL, 0, NULL),
(50, 'Pai - Pagamento Rc', 'pai-pagamento-rc', 'tag', '#07579c', '[\"quando o pai passa dinheiro para pagar as despesas\"]', 'income', 1, 0, '2025-06-17 14:35:44', '2025-06-17 14:35:44', NULL, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `clients`
--

CREATE TABLE `clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) DEFAULT NULL,
  `phone` varchar(191) DEFAULT NULL,
  `document` varchar(191) DEFAULT NULL,
  `document_type` enum('cpf','cnpj') DEFAULT NULL,
  `address` varchar(191) DEFAULT NULL,
  `city` varchar(191) DEFAULT NULL,
  `state` varchar(191) DEFAULT NULL,
  `zip_code` varchar(191) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Despejando dados para a tabela `clients`
--

INSERT INTO `clients` (`id`, `name`, `email`, `phone`, `document`, `document_type`, `address`, `city`, `state`, `zip_code`, `notes`, `active`, `created_at`, `updated_at`) VALUES
(1, 'Altamar Sogro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'dados de pagamento', 1, '2025-06-17 21:51:06', '2025-06-17 21:51:06');

-- --------------------------------------------------------

--
-- Estrutura para tabela `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `import_logs`
--

CREATE TABLE `import_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `filename` varchar(191) NOT NULL,
  `path` varchar(191) DEFAULT NULL,
  `bank_account_id` bigint(20) UNSIGNED NOT NULL,
  `import_type` enum('bank_statement','credit_card') NOT NULL DEFAULT 'bank_statement',
  `total_records` int(11) NOT NULL DEFAULT 0,
  `imported_records` int(11) NOT NULL DEFAULT 0,
  `skipped_records` int(11) NOT NULL DEFAULT 0,
  `format` varchar(191) DEFAULT NULL,
  `encoding` varchar(191) DEFAULT NULL,
  `delimiter` varchar(191) DEFAULT NULL,
  `detected_bank` varchar(191) DEFAULT NULL,
  `status` enum('pending','processing','completed','failed') NOT NULL DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(191) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Despejando dados para a tabela `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2024_01_01_000001_create_bank_accounts_table', 1),
(5, '2024_01_01_000002_create_transactions_table', 1),
(6, '2024_01_01_000003_create_reconciliations_table', 1),
(7, '2024_01_01_000004_create_statement_imports_table', 1),
(8, '2024_XX_XX_XXXXXX_create_categories_table', 1),
(9, '2024_01_01_000006_create_categories_table', 2),
(10, '2024_01_01_000006_create_categories_table', 3),
(11, '2024_01_01_000006_create_categories_table', 4),
(12, '2025_06_13_144311_enhance_categories_table', 5),
(13, '2025_06_13_144311_enhance_transactions_table', 5),
(14, '2025_06_13_000003_enhance_transactions_table_safe', 6),
(15, '2025_06_13_150000_safe_enhance_transactions_table', 6),
(16, '2025_06_13_150001_safe_enhance_categories_table', 6),
(17, '2025_06_13_160000_enhance_transactions_table_safe', 6),
(18, '2025_06_16_185037_update_bank_accounts_table_add_fields', 7),
(19, '2025_06_17_165746_create_clients_table', 8),
(20, '2025_06_17_165845_create_suppliers_table', 8),
(21, '2025_06_17_170048_create_account_payables_table', 8),
(22, '2025_06_17_170138_create_account_receivables_table', 8),
(23, '2025_06_17_213000_create_system_settings_table', 9),
(24, '2025_06_17_234036_update_account_payables_category_column', 10),
(25, '2025_06_17_234146_update_account_receivables_category_column', 10),
(26, '2025_06_18_124432_create_import_logs_table', 11),
(27, '2025_06_18_125000_create_updates_table', 11);

-- --------------------------------------------------------

--
-- Estrutura para tabela `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `reconciliations`
--

CREATE TABLE `reconciliations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bank_account_id` bigint(20) UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `starting_balance` decimal(15,2) NOT NULL,
  `ending_balance` decimal(15,2) NOT NULL,
  `calculated_balance` decimal(15,2) DEFAULT NULL,
  `difference` decimal(15,2) DEFAULT NULL,
  `status` enum('draft','completed','approved') NOT NULL DEFAULT 'draft',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Despejando dados para a tabela `reconciliations`
--

INSERT INTO `reconciliations` (`id`, `bank_account_id`, `start_date`, `end_date`, `starting_balance`, `ending_balance`, `calculated_balance`, `difference`, `status`, `created_by`, `approved_by`, `approved_at`, `notes`, `created_at`, `updated_at`) VALUES
(1, 4, '2025-05-01', '2025-06-12', 0.00, 0.00, 0.00, 0.00, 'draft', 1, NULL, NULL, NULL, '2025-06-12 19:14:27', '2025-06-12 19:14:27'),
(2, 4, '2025-05-01', '2025-06-12', 0.00, 0.00, 0.00, 0.00, 'approved', 1, 1, '2025-06-12 21:38:09', NULL, '2025-06-12 20:39:05', '2025-06-12 21:38:09'),
(3, 5, '2025-05-04', '2025-06-18', 0.00, 1.00, 0.00, 1.00, 'completed', 1, NULL, NULL, NULL, '2025-06-18 23:07:20', '2025-06-18 23:07:36');

-- --------------------------------------------------------

--
-- Estrutura para tabela `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(191) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Despejando dados para a tabela `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('rtpo7JyRZLytUDPKa0P8sjWKjayr2r93fmRvLpjq', NULL, '177.130.20.11', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Safari/605.1.15', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiREQzaUtJSzF4MW5LTm0zOWUyR2JjMXU2NEI5QmtqVkxRNTA4WmNSMCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDg6Imh0dHBzOi8vdXNhZG9zYXIuY29tLmJyL2JjL3B1YmxpYy9jbGllbnRzL2NyZWF0ZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1750288640),
('kYx84qL0Vj5acdxnhl0Z5MehKMmilFvD0y9qFEYE', NULL, '177.130.29.162', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Safari/605.1.15', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiT0R6Qlh0VDN0MVdSU2xYNWgyN012MU1TRzd1Q2Y5eEhwYVlvV3BCaSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDg6Imh0dHBzOi8vdXNhZG9zYXIuY29tLmJyL2JjL3B1YmxpYy9jbGllbnRzL2NyZWF0ZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1750328869),
('e8T1L2ePRzwTgLDBRHnsxc9w9mlBjSiMmTclebqK', NULL, '177.130.20.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiM0JpSnFIWTV3Y01nODNnZTdhM2dJdnI0T29UZmI2aFJuU2IwSGIwcyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDM6Imh0dHBzOi8vdXNhZG9zYXIuY29tLmJyL2JjL3B1YmxpYy9kYXNoYm9hcmQiO319', 1750288181),
('YopUwDWBBfFyvzH0z57bhL0ECb1bo7H18PBg2NMd', NULL, '177.130.20.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNFc3bGdYOEpWY0dUVHM4N1BXWjhEZVlnaTZnQ1hIWjdzRW1UQ2tibCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTQ6Imh0dHBzOi8vdXNhZG9zYXIuY29tLmJyL2JjL3B1YmxpYy9zZXR0aW5ncy9keW5hbWljLmNzcyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1750285603),
('FaHPfUIm1ZfpdlDvwVUzyWZDT7HNb3Sn2Qsxa8Nr', NULL, '177.130.20.11', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Safari/605.1.15', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiak1XZ3Vib0Y1ekwxdFllOHNzTXg5YWlZUXlQVmJpMWV4SnNPWTVseiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTQ6Imh0dHBzOi8vdXNhZG9zYXIuY29tLmJyL2JjL3B1YmxpYy9zZXR0aW5ncy9keW5hbWljLmNzcyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1750277733),
('IVKD6eXF0H2V7cYpVmb2BrSl38HPlnUhMqhGF3p2', NULL, '177.130.20.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZEdnZE5tY1dyV0ZhZW5DSG9uUlY2SHpKOHVSYkU5b3N0NkQxa0h4WiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDI6Imh0dHBzOi8vdXNhZG9zYXIuY29tLmJyL2JjL3JlY29uY2lsaWF0aW9ucyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1750274728),
('XHu5qHv6v7nk0IkirzoSByV5KOirSxare4y0rzIX', NULL, '177.130.20.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVm1kektINkJXUHh0RWEzVTlUUVIxNVVlQlgwTm43S25Ib0c0c3BpNyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDc6Imh0dHBzOi8vdXNhZG9zYXIuY29tLmJyL2JjL3B1YmxpYy9iYW5rLWFjY291bnRzIjt9fQ==', 1750249395),
('dpqFXjeMonUduuUMQ1weH7Y1EAkPO3lC01AyiN1P', NULL, '177.130.20.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZDE0enVTMm5iSWlCQWZNMVNvQWowZmhmNG9ZejQ0U2YzUUNkVEJmTSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDc6Imh0dHBzOi8vdXNhZG9zYXIuY29tLmJyL2JjL3NldHRpbmdzL2R5bmFtaWMuY3NzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1750271154),
('eq5IPTltHglU5opaUDYaF7WwAY6ZeFVsiF3fGc08', NULL, '177.130.20.11', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Safari/605.1.15', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiT2NpbEE1NjRjOGZ1anphTFFqcUtvSUphb1k5NEE3MjZpcUJLdmFMUiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDg6Imh0dHBzOi8vdXNhZG9zYXIuY29tLmJyL2JjL3B1YmxpYy9jbGllbnRzL2NyZWF0ZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1750266348),
('pjynQQFkJfEXpFlPLz0LgqvKccv2ELcKIoGq1oM0', NULL, '177.130.29.162', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRU50QTJWVEpERUo0NUpLZEJxWkZIWmFxcWhXRmQxQ2dHSFhTUTBRYSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDM6Imh0dHBzOi8vdXNhZG9zYXIuY29tLmJyL2JjL3B1YmxpYy9kYXNoYm9hcmQiO319', 1750339979),
('fTBnv0ptRXrgTYE3TmakwbjwFYjJYYlLMtOnUX7M', NULL, '177.130.29.162', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiM1o0MW1yeGRrQUdmSnZZZlhtb1l4VWZZZU04MEJSSmZ6N1JwSFhoaSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDk6Imh0dHBzOi8vdXNhZG9zYXIuY29tLmJyL2JjL3B1YmxpYy9yZWNvbmNpbGlhdGlvbnMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1750355873);

-- --------------------------------------------------------

--
-- Estrutura para tabela `statement_imports`
--

CREATE TABLE `statement_imports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bank_account_id` bigint(20) UNSIGNED NOT NULL,
  `filename` varchar(191) NOT NULL,
  `file_type` varchar(191) NOT NULL,
  `total_transactions` int(11) NOT NULL,
  `imported_transactions` int(11) NOT NULL,
  `duplicate_transactions` int(11) NOT NULL,
  `error_transactions` int(11) NOT NULL,
  `status` enum('processing','completed','failed') NOT NULL,
  `import_log` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`import_log`)),
  `imported_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Despejando dados para a tabela `statement_imports`
--

INSERT INTO `statement_imports` (`id`, `bank_account_id`, `filename`, `file_type`, `total_transactions`, `imported_transactions`, `duplicate_transactions`, `error_transactions`, `status`, `import_log`, `imported_by`, `created_at`, `updated_at`) VALUES
(2, 4, 'extrato.csv', 'csv', 43, 43, 0, 0, 'completed', '[]', 1, '2025-06-12 20:38:06', '2025-06-12 20:38:06'),
(10, 6, 'extratoCC.ofx', 'ofx', 0, 0, 0, 0, 'completed', '[]', 1, '2025-06-18 00:12:31', '2025-06-18 00:12:31'),
(11, 6, 'extratoCC.ofx', 'ofx', 0, 0, 0, 0, 'completed', '[]', 1, '2025-06-18 12:01:16', '2025-06-18 12:01:16'),
(12, 6, 'OUROCARD_VISA_INFINITE-Mai_25.ofx', 'ofx', 14, 2, 0, 12, 'completed', '[\"Linha 2: Erro - SQLSTATE[01000]: Warning: 1265 Data truncated for column \'type\' at row 1 (Connection: mysql, SQL: insert into `transactions` (`bank_account_id`, `transaction_date`, `description`, `amount`, `type`, `reference_number`, `status`, `import_hash`, `updated_at`, `created_at`) values (6, 2025-04-23 00:00:00, TIM*66984447866        RIO DE JANEIR BR, 54.99, PAYMENT, 2025042349840000000060890000000002, pending, 62b2f66fc0f2e7e3b0ee12f7339cd2e7, 2025-06-18 13:35:03, 2025-06-18 13:35:03))\",\"Linha 3: Erro - SQLSTATE[01000]: Warning: 1265 Data truncated for column \'type\' at row 1 (Connection: mysql, SQL: insert into `transactions` (`bank_account_id`, `transaction_date`, `description`, `amount`, `type`, `reference_number`, `status`, `import_hash`, `updated_at`, `created_at`) values (6, 2025-05-05 00:00:00, IFD*IFOOD CLUB         Osasco        BR, 5.95, PAYMENT, 2025050549840000000060890000000003, pending, ef2b02331632f9af9d698aed3041af2e, 2025-06-18 13:35:03, 2025-06-18 13:35:03))\",\"Linha 4: Erro - SQLSTATE[01000]: Warning: 1265 Data truncated for column \'type\' at row 1 (Connection: mysql, SQL: insert into `transactions` (`bank_account_id`, `transaction_date`, `description`, `amount`, `type`, `reference_number`, `status`, `import_hash`, `updated_at`, `created_at`) values (6, 2025-05-09 00:00:00, AVUS                   PORTO ALEGRE  BR, 4.9, PAYMENT, 2025050949840000000060890000000004, pending, 35baf9fca535abbc535beaa4308f8c54, 2025-06-18 13:35:03, 2025-06-18 13:35:03))\",\"Linha 5: Erro - SQLSTATE[01000]: Warning: 1265 Data truncated for column \'type\' at row 1 (Connection: mysql, SQL: insert into `transactions` (`bank_account_id`, `transaction_date`, `description`, `amount`, `type`, `reference_number`, `status`, `import_hash`, `updated_at`, `created_at`) values (6, 2025-05-10 00:00:00, PICPAY*ASSISTENCIARE   Sao Paulo     BR, 13.9, PAYMENT, 2025051049840000000060890000000005, pending, 7250f89d27e8e59cda02dbaf3c61f1a6, 2025-06-18 13:35:03, 2025-06-18 13:35:03))\",\"Linha 6: Erro - SQLSTATE[01000]: Warning: 1265 Data truncated for column \'type\' at row 1 (Connection: mysql, SQL: insert into `transactions` (`bank_account_id`, `transaction_date`, `description`, `amount`, `type`, `reference_number`, `status`, `import_hash`, `updated_at`, `created_at`) values (6, 2025-05-13 00:00:00, ANUIDADE DIFERENCIADA TIT-PARC 08\\/12 BR, 83, PAYMENT, 2025051349840000000060890000000006, pending, 6c7f1cb66f21a77137a3af44dc427e63, 2025-06-18 13:35:03, 2025-06-18 13:35:03))\",\"Linha 7: Erro - SQLSTATE[01000]: Warning: 1265 Data truncated for column \'type\' at row 1 (Connection: mysql, SQL: insert into `transactions` (`bank_account_id`, `transaction_date`, `description`, `amount`, `type`, `reference_number`, `status`, `import_hash`, `updated_at`, `created_at`) values (6, 2025-04-26 00:00:00, DL *GOOGLE YouTubePrem SAO PAULO     BR, 41.9, PAYMENT, 2025042649840000000060890000000007, pending, d66504e8da76c7781c3b65f20e8ade91, 2025-06-18 13:35:03, 2025-06-18 13:35:03))\",\"Linha 8: Erro - SQLSTATE[01000]: Warning: 1265 Data truncated for column \'type\' at row 1 (Connection: mysql, SQL: insert into `transactions` (`bank_account_id`, `transaction_date`, `description`, `amount`, `type`, `reference_number`, `status`, `import_hash`, `updated_at`, `created_at`) values (6, 2025-05-06 00:00:00, DL *GOOGLE Google One  SAO PAULO     BR, 14.99, PAYMENT, 2025050649840000000060890000000008, pending, ad08f54278540e04d72c7275dd302131, 2025-06-18 13:35:03, 2025-06-18 13:35:03))\",\"Linha 9: Erro - SQLSTATE[01000]: Warning: 1265 Data truncated for column \'type\' at row 1 (Connection: mysql, SQL: insert into `transactions` (`bank_account_id`, `transaction_date`, `description`, `amount`, `type`, `reference_number`, `status`, `import_hash`, `updated_at`, `created_at`) values (6, 2024-12-22 00:00:00, EC *ALLIGHT   PARC 05\\/10 RIBEIRO PRETBR, 69.45, PAYMENT, 2024122249840000000060890000000009, pending, 09f861852d8d8a16ccfb72e2af5a12a6, 2025-06-18 13:35:03, 2025-06-18 13:35:03))\",\"Linha 10: Erro - SQLSTATE[01000]: Warning: 1265 Data truncated for column \'type\' at row 1 (Connection: mysql, SQL: insert into `transactions` (`bank_account_id`, `transaction_date`, `description`, `amount`, `type`, `reference_number`, `status`, `import_hash`, `updated_at`, `created_at`) values (6, 2024-12-22 00:00:00, MP*MOVEISBARB PARC 05\\/09 OSASCO      BR, 22.34, PAYMENT, 2024122249840000000060890000000010, pending, 9db64ad5e5a8e368c426f1f68013ddfd, 2025-06-18 13:35:03, 2025-06-18 13:35:03))\",\"Linha 11: Erro - SQLSTATE[01000]: Warning: 1265 Data truncated for column \'type\' at row 1 (Connection: mysql, SQL: insert into `transactions` (`bank_account_id`, `transaction_date`, `description`, `amount`, `type`, `reference_number`, `status`, `import_hash`, `updated_at`, `created_at`) values (6, 2024-12-23 00:00:00, FUJIOKA SITE  PARC 05\\/10 GOIANIA     BR, 136.13, PAYMENT, 2024122349840000000060890000000011, pending, e4876fd975eb7691a9b9885a7cedeb6d, 2025-06-18 13:35:03, 2025-06-18 13:35:03))\",\"Linha 12: Erro - SQLSTATE[01000]: Warning: 1265 Data truncated for column \'type\' at row 1 (Connection: mysql, SQL: insert into `transactions` (`bank_account_id`, `transaction_date`, `description`, `amount`, `type`, `reference_number`, `status`, `import_hash`, `updated_at`, `created_at`) values (6, 2025-04-25 00:00:00, MOVIDA PARTIC PARC 01\\/12 VARZEA GRANDBR, 2000, PAYMENT, 2025042549840000000060890000000012, pending, 89f94e2a812041f7e5d27828e32c96c5, 2025-06-18 13:35:03, 2025-06-18 13:35:03))\",\"Linha 13: Erro - SQLSTATE[01000]: Warning: 1265 Data truncated for column \'type\' at row 1 (Connection: mysql, SQL: insert into `transactions` (`bank_account_id`, `transaction_date`, `description`, `amount`, `type`, `reference_number`, `status`, `import_hash`, `updated_at`, `created_at`) values (6, 2024-12-30 00:00:00, FUJIOKA SITE  PARC 05\\/10 GOIANIA     BR, 597.55, PAYMENT, 2024123049840000000060890000000013, pending, 657d27fa4e54c82d9968acd7224f81e0, 2025-06-18 13:35:03, 2025-06-18 13:35:03))\"]', 1, '2025-06-18 16:35:03', '2025-06-18 16:35:03');

-- --------------------------------------------------------

--
-- Estrutura para tabela `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) DEFAULT NULL,
  `phone` varchar(191) DEFAULT NULL,
  `document` varchar(191) DEFAULT NULL,
  `document_type` enum('cpf','cnpj') DEFAULT NULL,
  `address` varchar(191) DEFAULT NULL,
  `city` varchar(191) DEFAULT NULL,
  `state` varchar(191) DEFAULT NULL,
  `zip_code` varchar(191) DEFAULT NULL,
  `contact_person` varchar(191) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Despejando dados para a tabela `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `email`, `phone`, `document`, `document_type`, `address`, `city`, `state`, `zip_code`, `contact_person`, `notes`, `active`, `created_at`, `updated_at`) VALUES
(1, 'Altamar Sogro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-17 21:51:55', '2025-06-17 21:51:55');

-- --------------------------------------------------------

--
-- Estrutura para tabela `system_settings`
--

CREATE TABLE `system_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(191) NOT NULL,
  `value` text DEFAULT NULL,
  `type` varchar(191) NOT NULL DEFAULT 'string',
  `category` varchar(191) NOT NULL DEFAULT 'general',
  `label` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options`)),
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Despejando dados para a tabela `system_settings`
--

INSERT INTO `system_settings` (`id`, `key`, `value`, `type`, `category`, `label`, `description`, `options`, `is_public`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'primary_color', '#667eea', 'color', 'appearance', 'Cor Primária', 'Cor principal do sistema, usada em botões e destaques', NULL, 1, 1, '2025-06-17 23:35:16', '2025-06-17 23:35:16'),
(2, 'secondary_color', '#764ba2', 'color', 'appearance', 'Cor Secundária', 'Cor secundária para gradientes e elementos complementares', NULL, 1, 2, '2025-06-17 23:35:16', '2025-06-17 23:35:16'),
(3, 'success_color', '#28a745', 'color', 'appearance', 'Cor de Sucesso', 'Cor para indicar sucesso e valores positivos', NULL, 1, 3, '2025-06-17 23:35:16', '2025-06-17 23:35:16'),
(4, 'danger_color', '#dc3545', 'color', 'appearance', 'Cor de Alerta', 'Cor para alertas e valores negativos', NULL, 1, 4, '2025-06-17 23:35:16', '2025-06-17 23:35:16'),
(5, 'warning_color', '#ffc107', 'color', 'appearance', 'Cor de Aviso', 'Cor para avisos e atenção', NULL, 1, 5, '2025-06-17 23:35:16', '2025-06-17 23:35:16'),
(6, 'info_color', '#17a2b8', 'color', 'appearance', 'Cor de Informação', 'Cor para informações e dados neutros', NULL, 1, 6, '2025-06-17 23:35:16', '2025-06-17 23:35:16'),
(7, 'company_name', 'BC Sistema', 'string', 'general', 'Nome da Empresa', 'Nome da empresa exibido no sistema', NULL, 1, 1, '2025-06-17 23:35:16', '2025-06-17 23:35:16'),
(8, 'company_logo', '', 'file', 'general', 'Logo da Empresa', 'Logo exibido no cabeçalho do sistema', NULL, 1, 2, '2025-06-17 23:35:16', '2025-06-17 23:35:16'),
(9, 'timezone', 'America/Sao_Paulo', 'select', 'general', 'Fuso Horário', 'Fuso horário padrão do sistema', '{\"America\\/Sao_Paulo\":\"S\\u00e3o Paulo (GMT-3)\",\"America\\/New_York\":\"Nova York (GMT-5)\",\"Europe\\/London\":\"Londres (GMT+0)\",\"Europe\\/Berlin\":\"Berlim (GMT+1)\"}', 0, 3, '2025-06-17 23:35:16', '2025-06-17 23:35:16'),
(10, 'currency', 'BRL', 'select', 'general', 'Moeda Padrão', 'Moeda usada nos cálculos e exibições', '{\"BRL\":\"Real Brasileiro (R$)\",\"USD\":\"D\\u00f3lar Americano ($)\",\"EUR\":\"Euro (\\u20ac)\",\"GBP\":\"Libra Esterlina (\\u00a3)\"}', 1, 4, '2025-06-17 23:35:16', '2025-06-17 23:35:16'),
(11, 'dashboard_cards_per_row', '3', 'select', 'dashboard', 'Cards por Linha', 'Número de cards por linha no dashboard', '{\"2\":\"2 Cards\",\"3\":\"3 Cards\",\"4\":\"4 Cards\",\"6\":\"6 Cards\"}', 1, 1, '2025-06-17 23:35:16', '2025-06-17 23:35:16'),
(12, 'dashboard_refresh_interval', '300', 'integer', 'dashboard', 'Intervalo de Atualização (segundos)', 'Tempo para atualização automática dos dados', NULL, 0, 2, '2025-06-17 23:35:16', '2025-06-17 23:35:16'),
(13, 'show_welcome_banner', 'true', 'boolean', 'dashboard', 'Mostrar Banner de Boas-vindas', 'Exibir banner de boas-vindas no dashboard', NULL, 1, 3, '2025-06-17 23:35:16', '2025-06-17 23:35:16'),
(14, 'enable_clients_module', 'true', 'boolean', 'modules', 'Módulo de Clientes', 'Habilitar módulo de gestão de clientes', NULL, 0, 1, '2025-06-17 23:35:16', '2025-06-17 23:35:16'),
(15, 'enable_suppliers_module', 'true', 'boolean', 'modules', 'Módulo de Fornecedores', 'Habilitar módulo de gestão de fornecedores', NULL, 0, 2, '2025-06-17 23:35:16', '2025-06-17 23:35:16'),
(16, 'enable_reports_module', 'true', 'boolean', 'modules', 'Módulo de Relatórios', 'Habilitar módulo de relatórios avançados', NULL, 0, 3, '2025-06-17 23:35:16', '2025-06-17 23:35:16');

-- --------------------------------------------------------

--
-- Estrutura para tabela `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bank_account_id` bigint(20) UNSIGNED NOT NULL,
  `transaction_date` date NOT NULL,
  `description` varchar(191) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `type` enum('credit','debit') NOT NULL,
  `reference_number` varchar(191) DEFAULT NULL,
  `category` varchar(191) DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `attachment` varchar(500) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `recurring_type` enum('none','daily','weekly','monthly','yearly') NOT NULL DEFAULT 'none',
  `recurring_until` date DEFAULT NULL,
  `is_transfer` tinyint(1) NOT NULL DEFAULT 0,
  `transfer_transaction_id` bigint(20) UNSIGNED DEFAULT NULL,
  `priority` enum('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
  `location` varchar(191) DEFAULT NULL,
  `status` enum('pending','reconciled','error') NOT NULL DEFAULT 'pending',
  `reconciliation_id` bigint(20) UNSIGNED DEFAULT NULL,
  `import_hash` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `processed_at` timestamp NULL DEFAULT NULL,
  `verification_status` enum('pending','verified','rejected') NOT NULL DEFAULT 'pending',
  `external_id` varchar(191) DEFAULT NULL,
  `merchant_category` varchar(191) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Despejando dados para a tabela `transactions`
--

INSERT INTO `transactions` (`id`, `bank_account_id`, `transaction_date`, `description`, `amount`, `type`, `reference_number`, `category`, `category_id`, `tags`, `attachment`, `notes`, `recurring_type`, `recurring_until`, `is_transfer`, `transfer_transaction_id`, `priority`, `location`, `status`, `reconciliation_id`, `import_hash`, `created_at`, `updated_at`, `metadata`, `processed_at`, `verification_status`, `external_id`, `merchant_category`) VALUES
(96, 4, '2025-05-26', 'Tarifa MSG - Mês Anterior - Cobrança referente 12/05/2025', 5.00, 'debit', '999616110', 'Outros', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, 'afe31aa326a89428dd232dc8858ebddf', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(91, 4, '2025-05-22', 'Pix - Enviado - 22/05 18:52 Robson Severino Duarte', 100.00, 'debit', '52207', 'Transferência', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, '836f435cfab5c559360850fbad06634b', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(92, 4, '2025-05-22', 'Pix - Enviado - 22/05 18:59 Anna Luiza Moreira Pentead', 50.00, 'debit', '52208', 'Transferência', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, '2a53e8f668a3672a8951ceca93871dc7', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(95, 4, '2025-05-22', 'BB Rende Fácil - Rende Facil', 4602.56, 'credit', '9903', 'Outros', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, 'cbc0a941eec04a77079c9463763b3df1', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(94, 4, '2025-05-22', 'Pix - Enviado - 22/05 19:16 Alceu Penteado Junior', 1800.00, 'debit', '52210', 'Transferência', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, 'a2e0f6729effee4b19584233ce378386', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(93, 4, '2025-05-22', 'Pix - Enviado - 22/05 19:00 Niceia Batista Do Prado', 60.00, 'debit', '52209', 'Transferência', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, 'ea3565f0d2d8eed3abc9750ed497a42f', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(89, 4, '2025-05-22', 'Pix - Enviado - 22/05 17:09 Gleison Santos Pantaleao', 70.00, 'debit', '52205', 'Transferência', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, '98ca382132338ea1fb71ecbf0fd3bf2d', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(90, 4, '2025-05-22', 'Pix - Enviado - 22/05 17:59 Marcelo Silva Santos', 100.00, 'debit', '52206', 'Transferência', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, 'dbb3c20e60a9fc31f46a3409fac7e194', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(88, 4, '2025-05-22', 'Pix - Enviado - 22/05 16:57 Francimar Da Conceicao Sil', 500.00, 'debit', '52204', 'Transferência', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, '26ec0f0d8c59b861895020705066817f', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(87, 4, '2025-05-22', 'Pix - Enviado - 22/05 15:35 Claudio Auto Pecas Do Vale', 640.00, 'debit', '52203', 'Transferência', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, 'a192c6f9f54469458e641adb1560c4d7', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(86, 4, '2025-05-22', 'Pagamento de Boleto - BANCO DO BRASIL S A AGENCIA CO', 982.56, 'debit', '52202', 'Outros', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, 'f5d8edbff4eca7b98503f9ba6459cb75', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(85, 4, '2025-05-22', 'Pix - Enviado - 22/05 14:19 Ronaldo Fernandes', 300.00, 'debit', '52201', 'Transferência', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, 'c13b4d719c0dd0f88f3f6ec7f0910ba9', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(84, 4, '2025-05-20', 'BB Rende Fácil - Rende Facil', 3384.39, 'credit', '9903', 'Outros', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, '5bba3328ce12e29ad1d614a5acc00172', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(83, 4, '2025-05-20', 'Pix - Enviado - 20/05 17:40 Wino Interativa Ltda', 130.00, 'debit', '52002', 'Transferência', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, 'c6712d8753bf400b346b1420e6363b74', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(82, 4, '2025-05-20', 'Pix - Enviado - 20/05 16:46 C6 Bank', 3254.39, 'debit', '52001', 'Transferência', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, 'dc89fd34fa5b15aefec1b4917735ce2a', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(81, 4, '2025-05-16', 'BB Rende Fácil - Rende Facil', 8000.00, 'debit', '9903', 'Outros', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, '3692bdf56beb8d345e7ba620b59bd6eb', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(76, 4, '2025-05-13', 'Compra com Cartão - 13/05 08:10 MERC. BAHIA RODOVIAR', 10.00, 'debit', '229400', 'Outros', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, '5b50f623066455b5e25ad70ad916d5fe', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(75, 4, '2025-05-13', 'Compra com Cartão - 13/05 07:57 PANIFICADORA ARAGUAI', 10.00, 'debit', '128626', 'Alimentação', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, 'cfe3332343451cc736f7da97b4a2c5ef', '2025-06-12 20:38:06', '2025-06-12 22:05:34', NULL, NULL, 'pending', NULL, NULL),
(74, 4, '2025-05-07', 'BB Rende Fácil - Rende Facil', 504.58, 'credit', '9903', 'Outros', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, '5079039d0947f92af5b0cbfb3de7cca2', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(73, 4, '2025-05-07', 'Pagamento de Impostos - SEFAZ - MT - ICMS', 1286.60, 'debit', '50704', 'Transporte', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, '47470aff36d3010d42f1580116c18cd6', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(72, 4, '2025-05-07', 'Pix - Enviado - 07/05 14:50 Estado De Mato Grosso', 197.18, 'debit', '50703', 'Transferência', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, 'ea4c65b53f366b9eea223a0ed8714263', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(70, 4, '2025-05-07', 'Pix - Enviado - 07/05 14:49 Estado De Mato Grosso', 189.34, 'debit', '50701', 'Transferência', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, 'd23bd788f8c06cc6087d1a0c3f71e7c6', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(69, 4, '2025-05-07', 'Pix - Recebido - 07/05 14:52 88733904120 ALCEU PENTEADO', 1300.00, 'credit', '466403500318711', 'Transferência', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, '79aec208cb832acf705c36086087ed49', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(68, 4, '2025-05-06', 'BB Rende Fácil - Rende Facil', 610.13, 'credit', '9903', 'Outros', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, '766ff9099f07b6846f90133d17f788e9', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(67, 4, '2025-05-06', 'Pgto BB Créd Veículo - 984219489-BB CRÉD VEÍCULO USADO-VAREJO', 1223.30, 'debit', '881261000280044', 'Outros', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, '108367c22feb6734f46b00ab8e24906b', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(66, 4, '2025-05-06', 'Pix - Enviado - 06/05 17:16 Usados Araguaia Veiculos', 500.00, 'debit', '50603', 'Moradia', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, 'd173f490c47e4a8e5e0e95a5f5e71da9', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(65, 4, '2025-05-06', 'Pix - Enviado - 06/05 16:35 Usados Araguaia Veiculos', 1900.00, 'debit', '50602', 'Moradia', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, '81796deb6ac436876ebd1c7f3362e11d', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(64, 4, '2025-05-06', 'Pix - Enviado - 06/05 16:06 Ailton Rosa De Araujo', 580.00, 'debit', '50601', 'Transferência', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, '5247851390fe1ff56bcaedd11043a81a', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(63, 4, '2025-05-06', 'Contr CDC Antecipação', 3593.17, 'credit', '101261000076890', 'Outros', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, '1be5e8121fb0afe47ea9e4a02ef35ad6', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(62, 4, '2025-05-05', 'BB Rende Fácil - Rende Facil', 1222.13, 'debit', '9903', 'Outros', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, 'c10c12ccc19aea1b1a9dc69ae2d44724', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(60, 4, '2025-05-02', 'BB Rende Fácil - Rende Facil', 133.80, 'credit', '9903', 'Outros', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, 'b774cce3eae694c3df9c82df7d14eed9', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(80, 4, '2025-05-16', 'Pix - Recebido - 16/05 18:23 04634709155 SCHIRLEI NAIAR', 8000.00, 'credit', '474305951364151', 'Transferência', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, '2eb8eec58b27c274f6a5c00b84da407a', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(79, 4, '2025-05-13', 'BB Rende Fácil - Rende Facil', 98.29, 'credit', '9903', 'Outros', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, 'd68b51f81c4a2ad3b5c2b6cf81081c4a', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(78, 4, '2025-05-13', 'Compra com Cartão - 13/05 15:17 DROGARIA ULTRA POP 1', 34.96, 'debit', '655032', 'Outros', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, 'b76ad7e2e483a07aef1f3fe8981b935a', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(77, 4, '2025-05-13', 'Compra com Cartão - 13/05 11:55 30.398.022 OSMERI', 43.33, 'debit', '342905', 'Outros', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, 'e64faff0bb560f8de7878ada5ae0842d', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(71, 4, '2025-05-07', 'Pix - Enviado - 07/05 14:49 Estado De Mato Grosso', 131.46, 'debit', '50702', 'Transferência', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, 'f6c388aecefd4441b299002bdf9cbe3e', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(61, 4, '2025-05-05', 'Pix - Recebido - 05/05 11:10 00002862596132 WEBERTY JOS', 1224.00, 'credit', '51110495080482', 'Transferência', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, '36222c8a4cae2bcead57d0100b270f5a', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(59, 4, '2025-05-02', 'Cobrança de I.O.F. - IOF Saldo Devedor Conta', 0.79, 'debit', '391100701', 'Outros', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, '8c682a58a415f760c7bda4d07f25c716', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(58, 4, '2025-05-02', 'Cobrança de Juros - Juros Saldo Devedor Conta', 1.03, 'debit', '511058916', 'Outros', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, '1ea3249af17ef411b4a4fe0a296ec93c', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(57, 4, '2025-05-02', 'Brasilprev - BRASILPREV SEGUROS', 133.85, 'debit', '4154', 'Outros', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, 'cc6ed50d8a2d46a02d6d0bf462ec6a13', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(97, 4, '2025-05-26', 'BB Rende Fácil - Rende Facil', 5.00, 'credit', '9903', 'Outros', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, '00a1e363b3adba2baed262f4cb8a942b', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(98, 4, '2025-05-27', 'Pagto cartão crédito - CARTAO PETROBRAS', 3.00, 'debit', '91595059', 'Outros', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, 'aba50a99061d855ea582b70fa28ca17b', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(99, 4, '2025-05-27', 'BB Rende Fácil', 3.00, 'credit', '9903', 'Outros', NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'reconciled', 2, '901bdaaa5129912102f3ef15384267a9', '2025-06-12 20:38:06', '2025-06-12 21:33:20', NULL, NULL, 'pending', NULL, NULL),
(104, 6, '2025-04-24', 'PGTO. CASH AG.    1317 000131700  200100', 23760.50, 'credit', '2025042449840000000060890000000001', NULL, NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'pending', NULL, '06e345bdcc9bc536c755d829192652be', '2025-06-18 16:35:03', '2025-06-18 16:35:03', NULL, NULL, 'pending', NULL, NULL),
(103, 6, '2025-05-13', 'DESC AUTOMATICO ANUD. TIT-PARC 08/12 BR', 83.00, 'credit', '2025051349840000000060890000000000', NULL, NULL, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'pending', NULL, 'c293faa7f238d05c4aae6a12c27ac842', '2025-06-18 16:35:03', '2025-06-18 16:35:03', NULL, NULL, 'pending', NULL, NULL),
(102, 7, '2025-06-17', 'pai fazenda teste', -1000.00, 'debit', NULL, NULL, 49, NULL, NULL, NULL, 'none', NULL, 0, NULL, 'normal', NULL, 'pending', NULL, NULL, '2025-06-17 14:44:38', '2025-06-17 14:44:38', NULL, NULL, 'pending', NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `updates`
--

CREATE TABLE `updates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` text NOT NULL,
  `release_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `download_url` varchar(191) DEFAULT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `file_hash` varchar(191) DEFAULT NULL,
  `changes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`changes`)),
  `requirements` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`requirements`)),
  `status` enum('available','downloading','ready','applying','applied','failed') NOT NULL DEFAULT 'available',
  `applied_at` timestamp NULL DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `updates`
--

INSERT INTO `updates` (`id`, `version`, `name`, `description`, `release_date`, `download_url`, `file_size`, `file_hash`, `changes`, `requirements`, `status`, `applied_at`, `error_message`, `created_at`, `updated_at`) VALUES
(1, '1.1.0', 'Sistema de Importação de Extratos Aprimorado', 'Melhoria completa no sistema de importação com suporte a múltiplos formatos e detecção automática de bancos brasileiros.', '2025-06-11 15:48:43', 'https://releases.bcsistema.com/updates/v1.1.0.zip', 2457600, 'sha256:abc123...', '[\"Suporte completo a CSV, OFX, QIF, PDF e Excel\",\"Detec\\u00e7\\u00e3o autom\\u00e1tica de bancos brasileiros (Ita\\u00fa, Bradesco, Santander, BB, Caixa)\",\"Interface melhorada para upload de arquivos\",\"Valida\\u00e7\\u00e3o avan\\u00e7ada de formatos\",\"Sistema de logs detalhado\"]', '[\"PHP >= 8.2\",\"Laravel >= 11.0\",\"Extens\\u00e3o ZIP habilitada\",\"M\\u00ednimo 100MB espa\\u00e7o livre\"]', 'available', NULL, NULL, '2025-06-18 15:48:43', '2025-06-18 15:48:43'),
(2, '1.2.0', 'Sistema de Atualizações Automáticas', 'Implementação do sistema de atualizações automáticas para facilitar deployments e manutenção.', '2025-06-17 15:48:43', 'https://releases.bcsistema.com/updates/v1.2.0.zip', 1843200, 'sha256:def456...', '[\"Sistema completo de atualiza\\u00e7\\u00f5es autom\\u00e1ticas\",\"Interface web para gerenciar updates\",\"Backup autom\\u00e1tico antes das atualiza\\u00e7\\u00f5es\",\"Verifica\\u00e7\\u00e3o de integridade dos arquivos\",\"Rollback autom\\u00e1tico em caso de falha\",\"Logs detalhados de todo o processo\"]', '[\"PHP >= 8.2\",\"Laravel >= 11.0\",\"Extens\\u00e3o ZIP habilitada\",\"Permiss\\u00f5es de escrita no diret\\u00f3rio raiz\",\"M\\u00ednimo 200MB espa\\u00e7o livre\"]', 'available', NULL, NULL, '2025-06-18 15:48:43', '2025-06-18 15:48:43'),
(3, '1.3.0', 'Dashboard Profissional e Relatórios Avançados', 'Nova versão do dashboard com gráficos interativos e sistema de relatórios profissional.', '2025-06-21 15:48:43', 'https://releases.bcsistema.com/updates/v1.3.0.zip', 3276800, 'sha256:ghi789...', '[\"Dashboard completamente redesenhado\",\"Gr\\u00e1ficos interativos com Chart.js\",\"Relat\\u00f3rios em PDF\\/Excel profissionais\",\"Filtros avan\\u00e7ados por per\\u00edodo\",\"Widgets customiz\\u00e1veis\",\"Tema escuro\\/claro\",\"Performance otimizada\"]', '[\"PHP >= 8.2\",\"Laravel >= 11.0\",\"Node.js >= 18 (para assets)\",\"M\\u00ednimo 150MB espa\\u00e7o livre\"]', 'available', NULL, NULL, '2025-06-18 15:48:43', '2025-06-18 15:48:43'),
(4, '1.0.0', 'Sistema Base', 'Instalação inicial do sistema', '2025-06-18 16:23:12', NULL, NULL, NULL, NULL, NULL, 'applied', NULL, NULL, '2025-06-18 16:23:12', '2025-06-18 16:23:12'),
(5, '1.4.0', 'Otimizações de Performance', 'Otimizações gerais do sistema e correção de bugs', '2025-06-18 16:23:12', NULL, NULL, NULL, NULL, NULL, 'available', NULL, NULL, '2025-06-18 16:23:12', '2025-06-18 16:23:12');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Administrador', 'admin@usadosar.com.br', NULL, '$2y$12$V9Wk7NFbR38nXl3to81yWOEh/Qhfo0D14sP.v3Qh639v68S6gff/K', NULL, '2025-06-12 18:43:18', '2025-06-12 18:43:18');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `account_payables`
--
ALTER TABLE `account_payables`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_payables_supplier_id_foreign` (`supplier_id`);

--
-- Índices de tabela `account_receivables`
--
ALTER TABLE `account_receivables`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_receivables_client_id_foreign` (`client_id`);

--
-- Índices de tabela `bank_accounts`
--
ALTER TABLE `bank_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Índices de tabela `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Índices de tabela `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`),
  ADD KEY `categories_parent_id_index` (`parent_id`),
  ADD KEY `categories_sort_order_index` (`sort_order`),
  ADD KEY `categories_is_system_index` (`is_system`);

--
-- Índices de tabela `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Índices de tabela `import_logs`
--
ALTER TABLE `import_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `import_logs_bank_account_id_status_index` (`bank_account_id`,`status`),
  ADD KEY `import_logs_import_type_created_at_index` (`import_type`,`created_at`);

--
-- Índices de tabela `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Índices de tabela `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Índices de tabela `reconciliations`
--
ALTER TABLE `reconciliations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reconciliations_bank_account_id_foreign` (`bank_account_id`),
  ADD KEY `reconciliations_created_by_foreign` (`created_by`),
  ADD KEY `reconciliations_approved_by_foreign` (`approved_by`);

--
-- Índices de tabela `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Índices de tabela `statement_imports`
--
ALTER TABLE `statement_imports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `statement_imports_bank_account_id_foreign` (`bank_account_id`),
  ADD KEY `statement_imports_imported_by_foreign` (`imported_by`);

--
-- Índices de tabela `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `system_settings_key_unique` (`key`);

--
-- Índices de tabela `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transactions_bank_account_id_import_hash_unique` (`bank_account_id`,`import_hash`),
  ADD KEY `transactions_reconciliation_id_foreign` (`reconciliation_id`),
  ADD KEY `transactions_bank_account_id_transaction_date_index` (`bank_account_id`,`transaction_date`),
  ADD KEY `transactions_import_hash_index` (`import_hash`),
  ADD KEY `transactions_transfer_transaction_id_foreign` (`transfer_transaction_id`),
  ADD KEY `transactions_transaction_date_status_index` (`transaction_date`,`status`),
  ADD KEY `transactions_category_id_type_index` (`category_id`,`type`),
  ADD KEY `idx_transactions_date` (`transaction_date`),
  ADD KEY `idx_transactions_bank_account` (`bank_account_id`),
  ADD KEY `idx_transactions_category` (`category_id`),
  ADD KEY `idx_transactions_status` (`status`),
  ADD KEY `transactions_status_verification_status_index` (`status`,`verification_status`),
  ADD KEY `transactions_external_id_index` (`external_id`),
  ADD KEY `transactions_merchant_category_index` (`merchant_category`),
  ADD KEY `transactions_amount_type_index` (`amount`,`type`),
  ADD KEY `transactions_status_processed_at_index` (`status`,`processed_at`),
  ADD KEY `transactions_created_at_index` (`created_at`);

--
-- Índices de tabela `updates`
--
ALTER TABLE `updates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `updates_version_unique` (`version`),
  ADD KEY `updates_status_release_date_index` (`status`,`release_date`),
  ADD KEY `updates_version_index` (`version`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `account_payables`
--
ALTER TABLE `account_payables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `account_receivables`
--
ALTER TABLE `account_receivables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `bank_accounts`
--
ALTER TABLE `bank_accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de tabela `clients`
--
ALTER TABLE `clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `import_logs`
--
ALTER TABLE `import_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de tabela `reconciliations`
--
ALTER TABLE `reconciliations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `statement_imports`
--
ALTER TABLE `statement_imports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT de tabela `updates`
--
ALTER TABLE `updates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
