/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.6.21-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: usadosar_lara962
-- ------------------------------------------------------
-- Server version	10.6.21-MariaDB-cll-lve

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bank_accounts`
--

DROP TABLE IF EXISTS `bank_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bank_accounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `bank_name` varchar(191) NOT NULL,
  `account_number` varchar(191) NOT NULL,
  `agency` varchar(191) DEFAULT NULL,
  `type` enum('checking','savings','credit_card') NOT NULL,
  `balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bank_accounts`
--

LOCK TABLES `bank_accounts` WRITE;
/*!40000 ALTER TABLE `bank_accounts` DISABLE KEYS */;
INSERT INTO `bank_accounts` VALUES (4,'Banco do Brasil','Banco do Brasil','262617','1317x','checking',0.00,1,'2025-06-12 19:12:15','2025-06-12 19:12:15');
/*!40000 ALTER TABLE `bank_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(191) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(191) NOT NULL,
  `owner` varchar(191) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
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
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `budget_limit` decimal(15,2) DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `rules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`rules`)),
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_parent_id_index` (`parent_id`),
  KEY `categories_sort_order_index` (`sort_order`),
  KEY `categories_is_system_index` (`is_system`)
) ENGINE=MyISAM AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Supermercado','supermercado','fa-shopping-cart','#28a745','[\"mercado\",\"supermercado\",\"hiper\",\"atacadao\",\"carrefour\",\"extra\",\"pao de acucar\",\"assai\"]','expense',1,1,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(2,'Restaurante','restaurante','fa-utensils','#fd7e14','[\"restaurante\",\"lanchonete\",\"fast food\",\"mcdonalds\",\"burger king\",\"subway\",\"pizza\"]','expense',1,2,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(3,'Padaria/Café','padariacafe','fa-coffee','#795548','[\"padaria\",\"cafe\",\"cafeteria\",\"starbucks\",\"padoca\",\"confeitaria\"]','expense',1,3,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(4,'Delivery','delivery','fa-motorcycle','#e91e63','[\"ifood\",\"uber eats\",\"rappi\",\"delivery\",\"entrega\"]','expense',1,4,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(5,'Combustível','combustivel','fa-gas-pump','#f44336','[\"posto\",\"gasolina\",\"combustivel\",\"shell\",\"ipiranga\",\"petrobras\",\"alcool\",\"diesel\"]','expense',1,5,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(6,'Transporte App','transporte-app','fa-car','#9c27b0','[\"uber\",\"99\",\"cabify\",\"taxi\",\"corrida\"]','expense',1,6,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(7,'Estacionamento','estacionamento','fa-parking','#3f51b5','[\"estacionamento\",\"zona azul\",\"estapar\",\"multipark\",\"parking\"]','expense',1,7,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(8,'Pedágio','pedagio','fa-road','#00bcd4','[\"pedagio\",\"autoban\",\"ecovias\",\"artesp\",\"sem parar\",\"veloe\"]','expense',1,8,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(9,'Manutenção Veículo','manutencao-veiculo','fa-wrench','#ff5722','[\"oficina\",\"mecanica\",\"manutencao\",\"pneu\",\"oleo\",\"revisao\",\"auto\"]','expense',1,9,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(10,'Aluguel','aluguel','fa-home','#607d8b','[\"aluguel\",\"locacao\",\"imobiliaria\"]','expense',1,10,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(11,'Condomínio','condominio','fa-building','#455a64','[\"condominio\",\"taxa condominial\"]','expense',1,11,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(12,'Energia Elétrica','energia-eletrica','fa-bolt','#ffc107','[\"luz\",\"energia\",\"eletrica\",\"cpfl\",\"enel\",\"eletropaulo\",\"cemig\"]','expense',1,12,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(13,'Água','agua','fa-tint','#03a9f4','[\"agua\",\"sabesp\",\"saneamento\",\"saae\"]','expense',1,13,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(14,'Gás','gas','fa-fire','#ff9800','[\"gas\",\"comgas\",\"ultragaz\",\"liquigas\"]','expense',1,14,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(15,'Internet/Telefone','internettelefone','fa-wifi','#2196f3','[\"internet\",\"telefone\",\"vivo\",\"claro\",\"oi\",\"tim\",\"net\",\"banda larga\"]','expense',1,15,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(16,'Farmácia','farmacia','fa-pills','#4caf50','[\"farmacia\",\"drogaria\",\"droga raia\",\"drogasil\",\"pague menos\",\"medicamento\",\"remedio\"]','expense',1,16,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(17,'Médico/Hospital','medicohospital','fa-hospital','#f44336','[\"medico\",\"hospital\",\"clinica\",\"consulta\",\"exame\",\"laboratorio\",\"saude\"]','expense',1,17,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(18,'Plano de Saúde','plano-de-saude','fa-heartbeat','#e91e63','[\"unimed\",\"amil\",\"sulamerica\",\"bradesco saude\",\"hapvida\",\"plano saude\"]','expense',1,18,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(19,'Academia','academia','fa-dumbbell','#ff5722','[\"academia\",\"smart fit\",\"bio ritmo\",\"fitness\",\"gym\",\"personal\"]','expense',1,19,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(20,'Escola/Faculdade','escolafaculdade','fa-graduation-cap','#3f51b5','[\"escola\",\"faculdade\",\"universidade\",\"mensalidade\",\"colegio\",\"curso\"]','expense',1,20,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(21,'Cursos','cursos','fa-book','#9c27b0','[\"curso\",\"treinamento\",\"workshop\",\"udemy\",\"coursera\",\"ingles\"]','expense',1,21,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(22,'Material Escolar','material-escolar','fa-pencil-alt','#673ab7','[\"papelaria\",\"material escolar\",\"livro\",\"livraria\",\"kalunga\"]','expense',1,22,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(23,'Cinema/Teatro','cinemateatro','fa-film','#e91e63','[\"cinema\",\"teatro\",\"show\",\"ingresso\",\"cinemark\",\"uci\"]','expense',1,23,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(24,'Streaming','streaming','fa-tv','#d32f2f','[\"netflix\",\"spotify\",\"amazon prime\",\"disney\",\"hbo\",\"globoplay\",\"deezer\"]','expense',1,24,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(25,'Viagem','viagem','fa-plane','#1976d2','[\"viagem\",\"hotel\",\"pousada\",\"airbnb\",\"booking\",\"passagem\",\"decolar\"]','expense',1,25,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(26,'Bar/Balada','barbalada','fa-glass-martini','#7b1fa2','[\"bar\",\"balada\",\"cerveja\",\"drink\",\"pub\",\"boate\",\"festa\"]','expense',1,26,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(27,'Roupas/Calçados','roupascalcados','fa-tshirt','#ec407a','[\"roupa\",\"calcado\",\"sapato\",\"tenis\",\"zara\",\"renner\",\"c&a\",\"riachuelo\"]','expense',1,27,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(28,'Eletrônicos','eletronicos','fa-laptop','#5c6bc0','[\"eletronico\",\"celular\",\"notebook\",\"computador\",\"tv\",\"fast shop\",\"magazine\"]','expense',1,28,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(29,'Casa/Decoração','casadecoracao','fa-couch','#8d6e63','[\"movel\",\"decoracao\",\"tok stok\",\"leroy merlin\",\"ikea\",\"etna\"]','expense',1,29,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(30,'Presentes','presentes','fa-gift','#d81b60','[\"presente\",\"gift\",\"aniversario\",\"natal\"]','expense',1,30,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(31,'Salão/Barbearia','salaobarbearia','fa-cut','#ad1457','[\"salao\",\"cabeleireiro\",\"barbearia\",\"manicure\",\"estetica\"]','expense',1,31,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(32,'Pet','pet','fa-paw','#6a4c93','[\"pet\",\"veterinario\",\"racao\",\"petshop\",\"petz\",\"cobasi\"]','expense',1,32,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(33,'Assinaturas','assinaturas','fa-sync','#00acc1','[\"assinatura\",\"mensalidade\",\"anuidade\"]','expense',1,33,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(34,'Impostos/Taxas','impostostaxas','fa-file-invoice-dollar','#d32f2f','[\"imposto\",\"taxa\",\"iptu\",\"ipva\",\"multa\",\"governo\",\"tributo\"]','expense',1,34,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(35,'Tarifas Bancárias','tarifas-bancarias','fa-university','#c62828','[\"tarifa\",\"anuidade\",\"manutencao conta\",\"ted\",\"doc\",\"bancaria\"]','expense',1,35,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(36,'Juros','juros','fa-percentage','#b71c1c','[\"juros\",\"mora\",\"encargo\",\"rotativo\"]','expense',1,36,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(37,'Seguro','seguro','fa-shield-alt','#1a237e','[\"seguro\",\"porto seguro\",\"sulamerica\",\"bradesco seguro\",\"azul\",\"prudential\"]','expense',1,37,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(38,'Empréstimo','emprestimo','fa-hand-holding-usd','#b71c1c','[\"emprestimo\",\"financiamento\",\"parcela\",\"credito\"]','expense',1,38,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(39,'Salário','salario','fa-money-check-alt','#2e7d32','[\"salario\",\"pagamento\",\"holerite\",\"vencimento\",\"remuneracao\"]','income',1,39,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(40,'Freelance','freelance','fa-laptop-code','#388e3c','[\"freelance\",\"freelancer\",\"projeto\",\"job\",\"trabalho\"]','income',1,40,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(41,'Vendas','vendas','fa-shopping-bag','#43a047','[\"venda\",\"vendeu\",\"mercado livre\",\"olx\",\"cliente\"]','income',1,41,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(42,'Investimentos','investimentos','fa-chart-line','#66bb6a','[\"rendimento\",\"dividendo\",\"investimento\",\"aplicacao\",\"resgate\"]','income',1,42,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(43,'Reembolso','reembolso','fa-undo','#81c784','[\"reembolso\",\"estorno\",\"devolucao\",\"ressarcimento\"]','income',1,43,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(44,'Presente/Doação','presentedoacao','fa-gift','#4caf50','[\"presente\",\"doacao\",\"gift\",\"mesada\"]','income',1,44,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(45,'Transferência Entre Contas','transferencia-entre-contas','fa-exchange-alt','#9e9e9e','[\"transferencia\",\"ted\",\"doc\",\"pix\",\"transf\"]','both',1,45,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(46,'Saque','saque','fa-money-bill-wave','#757575','[\"saque\",\"caixa eletronico\",\"atm\",\"24 horas\",\"banco24\"]','both',1,46,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(47,'Depósito','deposito','fa-piggy-bank','#616161','[\"deposito\",\"dep\",\"envelope\"]','both',1,47,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL),(48,'Outros','outros','fa-ellipsis-h','#9e9e9e','[]','both',1,48,'2025-06-12 18:43:18','2025-06-12 18:43:18',NULL,NULL,0,NULL);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2024_01_01_000001_create_bank_accounts_table',1),(5,'2024_01_01_000002_create_transactions_table',1),(6,'2024_01_01_000003_create_reconciliations_table',1),(7,'2024_01_01_000004_create_statement_imports_table',1),(8,'2024_XX_XX_XXXXXX_create_categories_table',1),(9,'2024_01_01_000006_create_categories_table',2),(10,'2024_01_01_000006_create_categories_table',3),(11,'2024_01_01_000006_create_categories_table',4),(12,'2025_06_13_144311_enhance_categories_table',5),(13,'2025_06_13_144311_enhance_transactions_table',5),(14,'2025_06_13_000003_enhance_transactions_table_safe',6),(15,'2025_06_13_150000_safe_enhance_transactions_table',6),(16,'2025_06_13_150001_safe_enhance_categories_table',6),(17,'2025_06_13_160000_enhance_transactions_table_safe',6);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reconciliations`
--

DROP TABLE IF EXISTS `reconciliations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reconciliations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `bank_account_id` bigint(20) unsigned NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `starting_balance` decimal(15,2) NOT NULL,
  `ending_balance` decimal(15,2) NOT NULL,
  `calculated_balance` decimal(15,2) DEFAULT NULL,
  `difference` decimal(15,2) DEFAULT NULL,
  `status` enum('draft','completed','approved') NOT NULL DEFAULT 'draft',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reconciliations_bank_account_id_foreign` (`bank_account_id`),
  KEY `reconciliations_created_by_foreign` (`created_by`),
  KEY `reconciliations_approved_by_foreign` (`approved_by`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reconciliations`
--

LOCK TABLES `reconciliations` WRITE;
/*!40000 ALTER TABLE `reconciliations` DISABLE KEYS */;
INSERT INTO `reconciliations` VALUES (1,4,'2025-05-01','2025-06-12',0.00,0.00,0.00,0.00,'draft',1,NULL,NULL,NULL,'2025-06-12 19:14:27','2025-06-12 19:14:27'),(2,4,'2025-05-01','2025-06-12',0.00,0.00,0.00,0.00,'approved',1,1,'2025-06-12 21:38:09',NULL,'2025-06-12 20:39:05','2025-06-12 21:38:09');
/*!40000 ALTER TABLE `reconciliations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(191) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('S2IMiiv51J8oaBMGdlOulRYanzz0vOpQFZx9L23u',NULL,'177.130.20.98','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQzlZcGlqWDVONGVObmE5Tk9LbjljbE5LOVNueFlubWtGWTRhb3VsSSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTM6Imh0dHBzOi8vdXNhZG9zYXIuY29tLmJyL2JjL3B1YmxpYy90cmFuc2FjdGlvbnM/cGFnZT0yIjt9fQ==',1749904704);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `statement_imports`
--

DROP TABLE IF EXISTS `statement_imports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `statement_imports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `bank_account_id` bigint(20) unsigned NOT NULL,
  `filename` varchar(191) NOT NULL,
  `file_type` varchar(191) NOT NULL,
  `total_transactions` int(11) NOT NULL,
  `imported_transactions` int(11) NOT NULL,
  `duplicate_transactions` int(11) NOT NULL,
  `error_transactions` int(11) NOT NULL,
  `status` enum('processing','completed','failed') NOT NULL,
  `import_log` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`import_log`)),
  `imported_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `statement_imports_bank_account_id_foreign` (`bank_account_id`),
  KEY `statement_imports_imported_by_foreign` (`imported_by`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `statement_imports`
--

LOCK TABLES `statement_imports` WRITE;
/*!40000 ALTER TABLE `statement_imports` DISABLE KEYS */;
INSERT INTO `statement_imports` VALUES (2,4,'extrato.csv','csv',43,43,0,0,'completed','[]',1,'2025-06-12 20:38:06','2025-06-12 20:38:06');
/*!40000 ALTER TABLE `statement_imports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `bank_account_id` bigint(20) unsigned NOT NULL,
  `transaction_date` date NOT NULL,
  `description` varchar(191) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `type` enum('credit','debit') NOT NULL,
  `reference_number` varchar(191) DEFAULT NULL,
  `category` varchar(191) DEFAULT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `attachment` varchar(500) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `recurring_type` enum('none','daily','weekly','monthly','yearly') NOT NULL DEFAULT 'none',
  `recurring_until` date DEFAULT NULL,
  `is_transfer` tinyint(1) NOT NULL DEFAULT 0,
  `transfer_transaction_id` bigint(20) unsigned DEFAULT NULL,
  `priority` enum('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
  `location` varchar(191) DEFAULT NULL,
  `status` enum('pending','reconciled','error') NOT NULL DEFAULT 'pending',
  `reconciliation_id` bigint(20) unsigned DEFAULT NULL,
  `import_hash` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `processed_at` timestamp NULL DEFAULT NULL,
  `verification_status` enum('pending','verified','rejected') NOT NULL DEFAULT 'pending',
  `external_id` varchar(191) DEFAULT NULL,
  `merchant_category` varchar(191) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transactions_bank_account_id_import_hash_unique` (`bank_account_id`,`import_hash`),
  KEY `transactions_reconciliation_id_foreign` (`reconciliation_id`),
  KEY `transactions_bank_account_id_transaction_date_index` (`bank_account_id`,`transaction_date`),
  KEY `transactions_import_hash_index` (`import_hash`),
  KEY `transactions_transfer_transaction_id_foreign` (`transfer_transaction_id`),
  KEY `transactions_transaction_date_status_index` (`transaction_date`,`status`),
  KEY `transactions_category_id_type_index` (`category_id`,`type`),
  KEY `idx_transactions_date` (`transaction_date`),
  KEY `idx_transactions_bank_account` (`bank_account_id`),
  KEY `idx_transactions_category` (`category_id`),
  KEY `idx_transactions_status` (`status`),
  KEY `transactions_status_verification_status_index` (`status`,`verification_status`),
  KEY `transactions_external_id_index` (`external_id`),
  KEY `transactions_merchant_category_index` (`merchant_category`),
  KEY `transactions_amount_type_index` (`amount`,`type`),
  KEY `transactions_status_processed_at_index` (`status`,`processed_at`),
  KEY `transactions_created_at_index` (`created_at`)
) ENGINE=MyISAM AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
INSERT INTO `transactions` VALUES (96,4,'2025-05-26','Tarifa MSG - Mês Anterior - Cobrança referente 12/05/2025',5.00,'debit','999616110','Outros',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'afe31aa326a89428dd232dc8858ebddf','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(91,4,'2025-05-22','Pix - Enviado - 22/05 18:52 Robson Severino Duarte',100.00,'debit','52207','Transferência',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'836f435cfab5c559360850fbad06634b','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(92,4,'2025-05-22','Pix - Enviado - 22/05 18:59 Anna Luiza Moreira Pentead',50.00,'debit','52208','Transferência',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'2a53e8f668a3672a8951ceca93871dc7','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(95,4,'2025-05-22','BB Rende Fácil - Rende Facil',4602.56,'credit','9903','Outros',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'cbc0a941eec04a77079c9463763b3df1','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(94,4,'2025-05-22','Pix - Enviado - 22/05 19:16 Alceu Penteado Junior',1800.00,'debit','52210','Transferência',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'a2e0f6729effee4b19584233ce378386','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(93,4,'2025-05-22','Pix - Enviado - 22/05 19:00 Niceia Batista Do Prado',60.00,'debit','52209','Transferência',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'ea3565f0d2d8eed3abc9750ed497a42f','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(89,4,'2025-05-22','Pix - Enviado - 22/05 17:09 Gleison Santos Pantaleao',70.00,'debit','52205','Transferência',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'98ca382132338ea1fb71ecbf0fd3bf2d','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(90,4,'2025-05-22','Pix - Enviado - 22/05 17:59 Marcelo Silva Santos',100.00,'debit','52206','Transferência',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'dbb3c20e60a9fc31f46a3409fac7e194','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(88,4,'2025-05-22','Pix - Enviado - 22/05 16:57 Francimar Da Conceicao Sil',500.00,'debit','52204','Transferência',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'26ec0f0d8c59b861895020705066817f','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(87,4,'2025-05-22','Pix - Enviado - 22/05 15:35 Claudio Auto Pecas Do Vale',640.00,'debit','52203','Transferência',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'a192c6f9f54469458e641adb1560c4d7','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(86,4,'2025-05-22','Pagamento de Boleto - BANCO DO BRASIL S A AGENCIA CO',982.56,'debit','52202','Outros',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'f5d8edbff4eca7b98503f9ba6459cb75','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(85,4,'2025-05-22','Pix - Enviado - 22/05 14:19 Ronaldo Fernandes',300.00,'debit','52201','Transferência',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'c13b4d719c0dd0f88f3f6ec7f0910ba9','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(84,4,'2025-05-20','BB Rende Fácil - Rende Facil',3384.39,'credit','9903','Outros',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'5bba3328ce12e29ad1d614a5acc00172','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(83,4,'2025-05-20','Pix - Enviado - 20/05 17:40 Wino Interativa Ltda',130.00,'debit','52002','Transferência',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'c6712d8753bf400b346b1420e6363b74','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(82,4,'2025-05-20','Pix - Enviado - 20/05 16:46 C6 Bank',3254.39,'debit','52001','Transferência',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'dc89fd34fa5b15aefec1b4917735ce2a','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(81,4,'2025-05-16','BB Rende Fácil - Rende Facil',8000.00,'debit','9903','Outros',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'3692bdf56beb8d345e7ba620b59bd6eb','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(76,4,'2025-05-13','Compra com Cartão - 13/05 08:10 MERC. BAHIA RODOVIAR',10.00,'debit','229400','Outros',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'5b50f623066455b5e25ad70ad916d5fe','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(75,4,'2025-05-13','Compra com Cartão - 13/05 07:57 PANIFICADORA ARAGUAI',10.00,'debit','128626','Alimentação',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'cfe3332343451cc736f7da97b4a2c5ef','2025-06-12 20:38:06','2025-06-12 22:05:34',NULL,NULL,'pending',NULL,NULL),(74,4,'2025-05-07','BB Rende Fácil - Rende Facil',504.58,'credit','9903','Outros',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'5079039d0947f92af5b0cbfb3de7cca2','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(73,4,'2025-05-07','Pagamento de Impostos - SEFAZ - MT - ICMS',1286.60,'debit','50704','Transporte',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'47470aff36d3010d42f1580116c18cd6','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(72,4,'2025-05-07','Pix - Enviado - 07/05 14:50 Estado De Mato Grosso',197.18,'debit','50703','Transferência',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'ea4c65b53f366b9eea223a0ed8714263','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(70,4,'2025-05-07','Pix - Enviado - 07/05 14:49 Estado De Mato Grosso',189.34,'debit','50701','Transferência',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'d23bd788f8c06cc6087d1a0c3f71e7c6','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(69,4,'2025-05-07','Pix - Recebido - 07/05 14:52 88733904120 ALCEU PENTEADO',1300.00,'credit','466403500318711','Transferência',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'79aec208cb832acf705c36086087ed49','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(68,4,'2025-05-06','BB Rende Fácil - Rende Facil',610.13,'credit','9903','Outros',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'766ff9099f07b6846f90133d17f788e9','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(67,4,'2025-05-06','Pgto BB Créd Veículo - 984219489-BB CRÉD VEÍCULO USADO-VAREJO',1223.30,'debit','881261000280044','Outros',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'108367c22feb6734f46b00ab8e24906b','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(66,4,'2025-05-06','Pix - Enviado - 06/05 17:16 Usados Araguaia Veiculos',500.00,'debit','50603','Moradia',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'d173f490c47e4a8e5e0e95a5f5e71da9','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(65,4,'2025-05-06','Pix - Enviado - 06/05 16:35 Usados Araguaia Veiculos',1900.00,'debit','50602','Moradia',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'81796deb6ac436876ebd1c7f3362e11d','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(64,4,'2025-05-06','Pix - Enviado - 06/05 16:06 Ailton Rosa De Araujo',580.00,'debit','50601','Transferência',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'5247851390fe1ff56bcaedd11043a81a','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(63,4,'2025-05-06','Contr CDC Antecipação',3593.17,'credit','101261000076890','Outros',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'1be5e8121fb0afe47ea9e4a02ef35ad6','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(62,4,'2025-05-05','BB Rende Fácil - Rende Facil',1222.13,'debit','9903','Outros',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'c10c12ccc19aea1b1a9dc69ae2d44724','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(60,4,'2025-05-02','BB Rende Fácil - Rende Facil',133.80,'credit','9903','Outros',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'b774cce3eae694c3df9c82df7d14eed9','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(80,4,'2025-05-16','Pix - Recebido - 16/05 18:23 04634709155 SCHIRLEI NAIAR',8000.00,'credit','474305951364151','Transferência',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'2eb8eec58b27c274f6a5c00b84da407a','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(79,4,'2025-05-13','BB Rende Fácil - Rende Facil',98.29,'credit','9903','Outros',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'d68b51f81c4a2ad3b5c2b6cf81081c4a','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(78,4,'2025-05-13','Compra com Cartão - 13/05 15:17 DROGARIA ULTRA POP 1',34.96,'debit','655032','Outros',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'b76ad7e2e483a07aef1f3fe8981b935a','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(77,4,'2025-05-13','Compra com Cartão - 13/05 11:55 30.398.022 OSMERI',43.33,'debit','342905','Outros',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'e64faff0bb560f8de7878ada5ae0842d','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(71,4,'2025-05-07','Pix - Enviado - 07/05 14:49 Estado De Mato Grosso',131.46,'debit','50702','Transferência',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'f6c388aecefd4441b299002bdf9cbe3e','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(61,4,'2025-05-05','Pix - Recebido - 05/05 11:10 00002862596132 WEBERTY JOS',1224.00,'credit','51110495080482','Transferência',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'36222c8a4cae2bcead57d0100b270f5a','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(59,4,'2025-05-02','Cobrança de I.O.F. - IOF Saldo Devedor Conta',0.79,'debit','391100701','Outros',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'8c682a58a415f760c7bda4d07f25c716','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(58,4,'2025-05-02','Cobrança de Juros - Juros Saldo Devedor Conta',1.03,'debit','511058916','Outros',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'1ea3249af17ef411b4a4fe0a296ec93c','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(57,4,'2025-05-02','Brasilprev - BRASILPREV SEGUROS',133.85,'debit','4154','Outros',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'cc6ed50d8a2d46a02d6d0bf462ec6a13','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(97,4,'2025-05-26','BB Rende Fácil - Rende Facil',5.00,'credit','9903','Outros',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'00a1e363b3adba2baed262f4cb8a942b','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(98,4,'2025-05-27','Pagto cartão crédito - CARTAO PETROBRAS',3.00,'debit','91595059','Outros',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'aba50a99061d855ea582b70fa28ca17b','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL),(99,4,'2025-05-27','BB Rende Fácil',3.00,'credit','9903','Outros',NULL,NULL,NULL,NULL,'none',NULL,0,NULL,'normal',NULL,'reconciled',2,'901bdaaa5129912102f3ef15384267a9','2025-06-12 20:38:06','2025-06-12 21:33:20',NULL,NULL,'pending',NULL,NULL);
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrador','admin@usadosar.com.br',NULL,'$2y$12$V9Wk7NFbR38nXl3to81yWOEh/Qhfo0D14sP.v3Qh639v68S6gff/K',NULL,'2025-06-12 18:43:18','2025-06-12 18:43:18');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-14 10:14:36
