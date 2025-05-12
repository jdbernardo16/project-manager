/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.6.2-MariaDB, for osx10.20 (arm64)
--
-- Host: localhost    Database: project-management
-- ------------------------------------------------------
-- Server version	11.6.2-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES
('laravel_cache_admin@praxxys.ph|127.0.0.1','i:1;',1746839491),
('laravel_cache_admin@praxxys.ph|127.0.0.1:timer','i:1746839491;',1746839491),
('laravel_cache_development@praxxys.ph|127.0.0.1','i:1;',1746601909),
('laravel_cache_development@praxxys.ph|127.0.0.1:timer','i:1746601909;',1746601909);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calendar_events`
--

DROP TABLE IF EXISTS `calendar_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calendar_events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned DEFAULT NULL,
  `resource_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `calendar_events_project_id_foreign` (`project_id`),
  KEY `calendar_events_resource_id_foreign` (`resource_id`),
  CONSTRAINT `calendar_events_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `calendar_events_resource_id_foreign` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calendar_events`
--

LOCK TABLES `calendar_events` WRITE;
/*!40000 ALTER TABLE `calendar_events` DISABLE KEYS */;
/*!40000 ALTER TABLE `calendar_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1),
(4,'2025_04_02_125158_create_projects_table',1),
(5,'2025_04_02_125158_create_resources_table',1),
(6,'2025_04_02_125159_create_calendar_events_table',1),
(7,'2025_04_02_125159_create_project_resource_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project_resource`
--

DROP TABLE IF EXISTS `project_resource`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_resource` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `resource_id` bigint(20) unsigned NOT NULL,
  `assigned_hours` float NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_resource_project_id_foreign` (`project_id`),
  KEY `project_resource_resource_id_foreign` (`resource_id`),
  CONSTRAINT `project_resource_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_resource_resource_id_foreign` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_resource`
--

LOCK TABLES `project_resource` WRITE;
/*!40000 ALTER TABLE `project_resource` DISABLE KEYS */;
INSERT INTO `project_resource` VALUES
(5,7,13,100,'2025-05-04',NULL,'2025-05-07 07:43:56','2025-05-07 07:54:21'),
(6,8,15,10,'2025-05-07',NULL,'2025-05-07 07:58:46','2025-05-07 08:12:20'),
(7,8,8,10,'2025-05-07',NULL,'2025-05-07 07:58:46','2025-05-07 08:12:20'),
(8,8,22,10,'2025-05-07',NULL,'2025-05-07 07:58:46','2025-05-07 08:12:20'),
(9,9,15,400,'2025-05-07',NULL,'2025-05-07 08:00:11','2025-05-07 08:00:11'),
(10,10,18,100,'2025-05-07',NULL,'2025-05-07 08:00:47','2025-05-07 08:00:47'),
(11,11,10,16,'2025-05-07',NULL,'2025-05-07 08:01:20','2025-05-07 08:01:30'),
(12,12,16,800,'2025-05-07',NULL,'2025-05-07 08:02:27','2025-05-07 08:02:27'),
(13,12,9,50,'2025-05-07',NULL,'2025-05-07 08:02:27','2025-05-07 08:02:27'),
(14,12,10,50,'2025-05-07',NULL,'2025-05-07 08:02:27','2025-05-07 08:02:27'),
(15,13,14,24,'2025-05-07',NULL,'2025-05-07 08:03:27','2025-05-10 09:15:44'),
(16,13,7,8,'2025-05-07',NULL,'2025-05-07 08:03:27','2025-05-10 09:15:44'),
(17,13,19,50,'2025-05-07',NULL,'2025-05-07 08:03:27','2025-05-10 09:15:44'),
(18,14,12,50,'2025-05-07',NULL,'2025-05-07 08:04:18','2025-05-07 08:04:27'),
(19,15,12,100,'2025-05-07',NULL,'2025-05-07 08:05:01','2025-05-07 08:05:01'),
(20,16,12,100,'2025-05-07',NULL,'2025-05-07 08:05:29','2025-05-07 08:05:29'),
(23,19,14,20,'2025-05-07',NULL,'2025-05-07 08:08:12','2025-05-07 08:08:22'),
(24,20,17,20,'2025-05-07',NULL,'2025-05-07 08:09:52','2025-05-07 08:11:46'),
(25,8,21,50,'2025-05-07','2025-05-14','2025-05-07 08:12:20','2025-05-07 08:12:20'),
(26,21,19,50,'2025-05-05',NULL,'2025-05-10 01:14:12','2025-05-10 01:14:12'),
(27,21,10,50,'2025-05-05',NULL,'2025-05-10 01:14:12','2025-05-10 01:14:12'),
(28,18,7,12,'2025-05-05','2025-05-07','2025-05-10 01:23:53','2025-05-10 01:23:53');
/*!40000 ALTER TABLE `project_resource` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `estimate_hours` float NOT NULL,
  `hours_remaining` float DEFAULT NULL,
  `deadline` date NOT NULL,
  `status` enum('upcoming','ongoing','completed') NOT NULL DEFAULT 'upcoming',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES
(7,'Vault',NULL,100,8,'2025-05-09','ongoing','2025-05-07 07:28:08','2025-05-07 07:54:21'),
(8,'Intelo',NULL,100,0,'2025-05-07','ongoing','2025-05-07 07:58:46','2025-05-07 07:59:33'),
(9,'Singa Ship',NULL,400,400,'2025-06-20','upcoming','2025-05-07 08:00:11','2025-05-07 08:00:11'),
(10,'Save Plus',NULL,100,100,'2025-05-30','upcoming','2025-05-07 08:00:47','2025-05-07 08:00:47'),
(11,'Alpha IBF',NULL,16,16,'2025-05-09','ongoing','2025-05-07 08:01:20','2025-05-07 08:01:30'),
(12,'Global Pacific',NULL,1000,1000,'2025-05-30','upcoming','2025-05-07 08:02:27','2025-05-07 08:02:27'),
(13,'PNB',NULL,91,91,'2025-05-14','ongoing','2025-05-07 08:03:27','2025-05-10 09:15:44'),
(14,'4A\'s Cargo',NULL,50,50,'2025-05-16','ongoing','2025-05-07 08:04:18','2025-05-07 08:04:27'),
(15,'Italpinas',NULL,100,100,'2025-05-30','upcoming','2025-05-07 08:05:01','2025-05-07 08:05:01'),
(16,'Goldrank',NULL,100,100,'2025-05-30','upcoming','2025-05-07 08:05:29','2025-05-07 08:05:29'),
(18,'PCCI',NULL,12,0,'2025-05-09','completed','2025-05-07 08:07:01','2025-05-10 01:21:54'),
(19,'Univers',NULL,20,0,'2025-05-07','ongoing','2025-05-07 08:08:12','2025-05-07 08:08:22'),
(20,'Timson',NULL,40,20,'2025-05-09','ongoing','2025-05-07 08:09:52','2025-05-07 08:11:46'),
(21,'Lyric',NULL,100,100,'2025-05-16','upcoming','2025-05-10 01:14:12','2025-05-10 01:14:12');
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resources`
--

DROP TABLE IF EXISTS `resources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resources` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `capacity` float NOT NULL DEFAULT 7,
  `availability_status` enum('available','assigned','unavailable') NOT NULL DEFAULT 'available',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `resources_user_id_foreign` (`user_id`),
  CONSTRAINT `resources_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resources`
--

LOCK TABLES `resources` WRITE;
/*!40000 ALTER TABLE `resources` DISABLE KEYS */;
INSERT INTO `resources` VALUES
(7,9,5,'assigned','FE','2025-05-07 07:15:32','2025-05-10 01:23:53'),
(8,10,7,'assigned','FE Dev','2025-05-07 07:15:44','2025-05-07 07:58:46'),
(9,11,7,'assigned','FE','2025-05-07 07:15:59','2025-05-07 08:02:27'),
(10,12,7,'assigned','FE','2025-05-07 07:16:22','2025-05-07 08:01:20'),
(11,13,7,'available','FE','2025-05-07 07:16:37','2025-05-07 07:16:37'),
(12,14,7,'assigned','FE','2025-05-07 07:16:51','2025-05-07 08:04:18'),
(13,15,7,'assigned','Fullstack','2025-05-07 07:17:08','2025-05-07 07:43:56'),
(14,16,7,'assigned','Fullstack','2025-05-07 07:17:19','2025-05-07 08:03:27'),
(15,17,7,'assigned','Fullstack','2025-05-07 07:17:36','2025-05-07 07:58:46'),
(16,18,7,'assigned','BE','2025-05-07 07:17:48','2025-05-07 08:02:27'),
(17,19,7,'assigned','BE','2025-05-07 07:18:00','2025-05-07 08:09:52'),
(18,20,7,'assigned','BE','2025-05-07 07:18:13','2025-05-07 08:00:47'),
(19,21,7,'assigned','BE','2025-05-07 07:18:23','2025-05-07 08:03:27'),
(20,22,7,'available','BE','2025-05-07 07:18:32','2025-05-10 01:20:32'),
(21,23,7,'assigned','BE','2025-05-07 07:18:46','2025-05-07 08:12:20'),
(22,24,7,'assigned','BE','2025-05-07 07:18:57','2025-05-07 07:58:46');
/*!40000 ALTER TABLE `resources` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES
('1uHjHz3aCbKpcAn4eoOb685TTRfkuMkNesNnXLyM',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiak1sZ2ZPZGRGd1IyTWlWS2RjaHV5a1VOYXV3aVlOdTY2VzRuTVl3eCI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjMwOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvcHJvamVjdHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=',1746869677),
('6eiDklWwDU12Iwl5R6ftXRZkUDfaYH5HQ5y0qRXD',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiQ1U4UGlqdmxXT2pNTDdZYWY2VjNLdVVaMGZlQXV3Vk1YYUNpNFVnViI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjIxOiJodHRwOi8vbG9jYWxob3N0OjgwMDAiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=',1746872236),
('es4rvELb1hcJeT6u8oXFx35KlLmDhoo24XseFVTZ',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiSzFObVFuSXFLTXYxRmhJOHQycGxnOVQ2YklqTkFtRFVENHJyZXAwRiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjIxOiJodHRwOi8vbG9jYWxob3N0OjgwMDAiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=',1746871119);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','project_manager','resource') NOT NULL DEFAULT 'resource',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'Admin User','admin@example.com','2025-05-07 07:10:28','$2y$12$tLJtOwYlaBUH3diDAYpxP.HxfsOo7eQBP0tiaoyaXOeTCaYq48mtS','admin','anZgxXY8li','2025-05-07 07:10:29','2025-05-07 07:10:29'),
(2,'Project Manager','pm@example.com','2025-05-07 07:10:29','$2y$12$MjShQgs7I3EYtCeGzi3r4etWLmsncGJi4WuytKUNuV54d6e2NhVBS','project_manager','o4w0P0hkoh','2025-05-07 07:10:29','2025-05-07 07:10:29'),
(3,'Madaline Bednar','usmith@example.com','2025-05-07 07:10:29','$2y$12$R2NKVmx0nlyrUT4.ZOD8COInxUPLjtce5QpZUCtqR5FKuOzz//wku','resource','RZh8da6xtr','2025-05-07 07:10:29','2025-05-07 07:10:29'),
(4,'Zoie Harber','jakubowski.marion@example.com','2025-05-07 07:10:29','$2y$12$R2NKVmx0nlyrUT4.ZOD8COInxUPLjtce5QpZUCtqR5FKuOzz//wku','resource','QKa0sAVkTS','2025-05-07 07:10:29','2025-05-07 07:10:29'),
(5,'Dr. Telly Carroll','buckridge.lane@example.net','2025-05-07 07:10:29','$2y$12$R2NKVmx0nlyrUT4.ZOD8COInxUPLjtce5QpZUCtqR5FKuOzz//wku','resource','8gs4dMo5Z5','2025-05-07 07:10:29','2025-05-07 07:10:29'),
(6,'Marlee Collins','russel.balistreri@example.com','2025-05-07 07:10:29','$2y$12$R2NKVmx0nlyrUT4.ZOD8COInxUPLjtce5QpZUCtqR5FKuOzz//wku','resource','V6ViRu9ajJ','2025-05-07 07:10:29','2025-05-07 07:10:29'),
(7,'Miss Verona Goldner','dan19@example.com','2025-05-07 07:10:29','$2y$12$R2NKVmx0nlyrUT4.ZOD8COInxUPLjtce5QpZUCtqR5FKuOzz//wku','resource','qWapZZkDNW','2025-05-07 07:10:29','2025-05-07 07:10:29'),
(8,'Dev Resource 1','dev1@example.com','2025-05-07 07:10:29','$2y$12$3iHcSEh4.tKmXvK3.wBJRenbCDoUbCSJty37V9fsGAyGohg1AR3Ki','resource','VjRo0jd7jG','2025-05-07 07:10:29','2025-05-07 07:10:29'),
(9,'JB','jb@praxxys.ph',NULL,'$2y$12$uNNH6L682Ve2ThZ8reC3JOhDi8t3cNPoJzNQauul7KLyrWJU1v9fW','resource',NULL,'2025-05-07 07:15:32','2025-05-07 07:15:32'),
(10,'SD','sd@praxxys.ph',NULL,'$2y$12$jw/NWsNFd/xF3mX75UnCD.tQ.9fHS7DcTN1Zwp/o3l1l2KWgp5s/a','resource',NULL,'2025-05-07 07:15:44','2025-05-07 07:15:44'),
(11,'NB','nb@praxxys.ph',NULL,'$2y$12$5H7zrKr.CCTN17ey3h5iJOX/rQv3rWChkQgZ3ZdzL/My6FheZE5fS','resource',NULL,'2025-05-07 07:15:59','2025-05-07 07:16:06'),
(12,'PAG','pag@praxxys.ph',NULL,'$2y$12$N5rxKMsPUQkAA2N1zWJ8WeYWsRCDObFo6W.M1nCC1wjAN60UByRaa','resource',NULL,'2025-05-07 07:16:22','2025-05-07 07:16:22'),
(13,'WL','wl@praxxys.ph',NULL,'$2y$12$.rAk/gUTWEerab9qMYIXweeZxe4iMlYXX./Dd.wo5rZBh/vXu90NW','resource',NULL,'2025-05-07 07:16:37','2025-05-07 07:16:37'),
(14,'KBP','kbp@praxxys.ph',NULL,'$2y$12$zL9Y4jqYkx7qoGdAyBPKoOtaqZIsI.Sbejud8aOqJMEra/2ZTfzUm','resource',NULL,'2025-05-07 07:16:51','2025-05-07 07:16:51'),
(15,'CBA','cba@praxxys.ph',NULL,'$2y$12$x0nV3joACqAHegAPJYfmROVKBzSLIk1S67Q/C16v3/lAwI3a5vaFm','resource',NULL,'2025-05-07 07:17:08','2025-05-07 07:17:08'),
(16,'SE','se@praxxys.ph',NULL,'$2y$12$iMAhPdFni0jPsc2bUnxpXuZ0yqHk4EqTkt.yWCZvV5lgYTbUZX0.S','resource',NULL,'2025-05-07 07:17:19','2025-05-07 07:17:19'),
(17,'RJN','rjn@praxxys.ph',NULL,'$2y$12$fWH7pAcfyRJYo15Qam6i.udQNRMWVUEy7ewuinXG8Z/Xs/eeyAYg6','resource',NULL,'2025-05-07 07:17:36','2025-05-07 07:17:36'),
(18,'JRM','jrm@praxxys.ph',NULL,'$2y$12$h.YI1BfG0PG6JvRJ1PIhV.n0zLl8xW.9c8KpBTVAji9RFqrHJbT66','resource',NULL,'2025-05-07 07:17:48','2025-05-07 07:17:48'),
(19,'LF','lf@praxxys.ph',NULL,'$2y$12$2n7dCP181D.gNdP.3LjcUeghEIqY.peHutgok5p2ZbrYkZkfvVHLW','resource',NULL,'2025-05-07 07:18:00','2025-05-07 07:18:00'),
(20,'AD','ad@praxxys.ph',NULL,'$2y$12$if/1MmicB7.93hkU6qKS/.nDKoR1ITZHGL8JrUgtUPU5JlCCfELw6','resource',NULL,'2025-05-07 07:18:13','2025-05-07 07:18:13'),
(21,'RV','rv@praxxys.ph',NULL,'$2y$12$b2X5dSdGXPd9sBE9DNCRjegYDG2qLRofh0o016FKEl.XjlDD5fHWW','resource',NULL,'2025-05-07 07:18:23','2025-05-07 07:18:23'),
(22,'RM','rm@praxxys.ph',NULL,'$2y$12$08FK3qZ/QCmyhYaFQjRgL.uKxGbzxUKVIzvMJ/fGt20rGYwejl0Sy','resource',NULL,'2025-05-07 07:18:32','2025-05-07 07:18:32'),
(23,'PC','pc@praxxys.ph',NULL,'$2y$12$HX7riLUIO959kNU63DLEiOykXJ.mEEY5xF3jnuT3jqwjy/xOVkIAS','resource',NULL,'2025-05-07 07:18:46','2025-05-07 07:18:46'),
(24,'RF','rf@praxxys.ph',NULL,'$2y$12$Mg4jjGpO1tI/97KK/EkgmO4rffULpWD1zWytC9FUb5tA7sFVy7kcC','resource',NULL,'2025-05-07 07:18:57','2025-05-07 07:18:57');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2025-05-12 20:56:55
