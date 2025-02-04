-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: localhost    Database: backend_club
-- ------------------------------------------------------
-- Server version	8.0.41

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `club`
--

DROP TABLE IF EXISTS `club`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `club` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `budget` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `club`
--

LOCK TABLES `club` WRITE;
/*!40000 ALTER TABLE `club` DISABLE KEYS */;
INSERT INTO `club` VALUES (1,'Real Madrid',8650000);
INSERT INTO `club` VALUES (2,'Atlético de Madrid',3729000);
INSERT INTO `club` VALUES (3,'F.C. Barcelona',4630000);
INSERT INTO `club` VALUES (4,'Athletic Club',1749000);
INSERT INTO `club` VALUES (5,'Valencia C.F.',801000);
/*!40000 ALTER TABLE `club` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coach`
--

DROP TABLE IF EXISTS `coach`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coach` (
  `id` int NOT NULL AUTO_INCREMENT,
  `club_id` int DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `age` int NOT NULL,
  `salary` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3F596DCC61190A32` (`club_id`),
  CONSTRAINT `FK_3F596DCC61190A32` FOREIGN KEY (`club_id`) REFERENCES `club` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coach`
--

LOCK TABLES `coach` WRITE;
/*!40000 ALTER TABLE `coach` DISABLE KEYS */;
INSERT INTO `coach` VALUES (1,1,'Carlo Ancelotti',0,500000);
INSERT INTO `coach` VALUES (2,2,'Diego Simeone',55,500000);
INSERT INTO `coach` VALUES (3,3,'Hansi Flick',63,500000);
INSERT INTO `coach` VALUES (4,5,'Rubén Baraja',44,200000);
INSERT INTO `coach` VALUES (5,4,'Ernesto Valverde',58,300000);
/*!40000 ALTER TABLE `coach` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctrine_migration_versions`
--

LOCK TABLES `doctrine_migration_versions` WRITE;
/*!40000 ALTER TABLE `doctrine_migration_versions` DISABLE KEYS */;
INSERT INTO `doctrine_migration_versions` VALUES ('DoctrineMigrations\\Version20250123121448','2025-01-25 16:51:07',669);
INSERT INTO `doctrine_migration_versions` VALUES ('DoctrineMigrations\\Version20250126134338','2025-01-26 13:48:57',699);
INSERT INTO `doctrine_migration_versions` VALUES ('DoctrineMigrations\\Version20250127113755','2025-01-27 11:38:22',764);
INSERT INTO `doctrine_migration_versions` VALUES ('DoctrineMigrations\\Version20250127160641','2025-01-27 16:06:50',1247);
INSERT INTO `doctrine_migration_versions` VALUES ('DoctrineMigrations\\Version20250127161844','2025-01-27 16:18:51',288);
INSERT INTO `doctrine_migration_versions` VALUES ('DoctrineMigrations\\Version20250127175139','2025-01-27 17:51:47',475);
/*!40000 ALTER TABLE `doctrine_migration_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `player`
--

DROP TABLE IF EXISTS `player`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `player` (
  `id` int NOT NULL AUTO_INCREMENT,
  `club_id` int DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `age` int NOT NULL,
  `salary` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_98197A6561190A32` (`club_id`),
  CONSTRAINT `FK_98197A6561190A32` FOREIGN KEY (`club_id`) REFERENCES `club` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `player`
--

LOCK TABLES `player` WRITE;
/*!40000 ALTER TABLE `player` DISABLE KEYS */;
INSERT INTO `player` VALUES (1,1,'CR7',26,960000);
INSERT INTO `player` VALUES (2,3,'Messi',23,990000);
INSERT INTO `player` VALUES (3,2,'Griezmann',25,770000);
INSERT INTO `player` VALUES (4,4,'Nico Williams',19,680000);
INSERT INTO `player` VALUES (5,5,'David Villa',24,810000);
INSERT INTO `player` VALUES (6,1,'Kroos',26,790000);
INSERT INTO `player` VALUES (7,2,'Jan Oblak',27,430000);
INSERT INTO `player` VALUES (8,4,'Iñaki Williams',23,530000);
INSERT INTO `player` VALUES (9,5,'Rubén Baraja',22,330000);
INSERT INTO `player` VALUES (10,3,'Sergio Busquets',20,440000);
INSERT INTO `player` VALUES (11,1,'Karim Benzema',24,880000);
INSERT INTO `player` VALUES (12,3,'Andrés Iniesta',20,940000);
INSERT INTO `player` VALUES (13,2,'Fernando Torres',18,71000);
INSERT INTO `player` VALUES (14,4,'Oihan Sancet',22,41000);
INSERT INTO `player` VALUES (15,5,'Gaizka Mendieta',22,59000);
INSERT INTO `player` VALUES (17,1,'Luka Modric',26,770000);
INSERT INTO `player` VALUES (18,1,'Dani Carvajal',21,470000);
INSERT INTO `player` VALUES (19,1,'Vinicius JR',18,870000);
INSERT INTO `player` VALUES (20,1,'Bellingham',22,840000);
INSERT INTO `player` VALUES (21,1,'Fede Valverde',23,710000);
INSERT INTO `player` VALUES (22,1,'Rodrygo',20,760000);
INSERT INTO `player` VALUES (23,1,'Curtois',27,730000);
INSERT INTO `player` VALUES (24,1,'Antonio Rudiger',25,380000);
INSERT INTO `player` VALUES (25,1,'Eder Militao',25,330000);
INSERT INTO `player` VALUES (26,1,'Camavinga',21,410000);
INSERT INTO `player` VALUES (27,1,'Morientes',26,250000);
/*!40000 ALTER TABLE `player` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-01-29 11:47:28
