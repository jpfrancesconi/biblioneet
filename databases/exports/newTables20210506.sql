-- MariaDB dump 10.19  Distrib 10.5.9-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: 127.0.0.1    Database: biblioneet_dev
-- ------------------------------------------------------
-- Server version	10.5.9-MariaDB-1:10.5.9+maria~stretch

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bn_acquisition_condition`
--

DROP TABLE IF EXISTS `bn_acquisition_condition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bn_acquisition_condition` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK table',
  `condition` varchar(150) NOT NULL COMMENT 'acquisition condiiton name',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'record status',
  `createdby` int(10) unsigned NOT NULL COMMENT 'Record creator user',
  `createdon` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Record creaion date',
  `updatedby` int(10) unsigned DEFAULT NULL COMMENT 'Record last updated user',
  `updatedon` datetime DEFAULT NULL COMMENT 'Record last updated date',
  PRIMARY KEY (`id`),
  KEY `fk_acqcon_createdby` (`createdby`),
  KEY `fk_acqcon_updatedby` (`updatedby`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bn_acquisition_condition`
--

LOCK TABLES `bn_acquisition_condition` WRITE;
/*!40000 ALTER TABLE `bn_acquisition_condition` DISABLE KEYS */;
INSERT INTO `bn_acquisition_condition` VALUES (1,'DONACIÓN',1,1,'2021-05-06 12:00:00',NULL,NULL);
/*!40000 ALTER TABLE `bn_acquisition_condition` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bn_item`
--

DROP TABLE IF EXISTS `bn_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bn_item` (
  `id` bigint(20) NOT NULL COMMENT 'PK Table',
  `title` varchar(255) NOT NULL COMMENT 'Item title',
  `item_type_id` bigint(20) NOT NULL COMMENT 'Item type DGM',
  `parallel_title` varchar(255) DEFAULT NULL COMMENT 'Parallel title (Other language)',
  `edition` varchar(150) DEFAULT NULL,
  `publication_place` varchar(255) DEFAULT NULL COMMENT 'Publication place of item',
  `editorial_id` bigint(20) DEFAULT NULL COMMENT 'Item editorial',
  `publication_year` varchar(60) DEFAULT NULL COMMENT 'Publication date buy just year.',
  `extension` varchar(150) DEFAULT NULL COMMENT 'Item physical description',
  `dimensions` varchar(150) DEFAULT NULL COMMENT 'Item physical dimensions',
  `others_physical_details` varchar(150) DEFAULT NULL COMMENT 'Ithem physical details',
  `complements` varchar(150) DEFAULT NULL COMMENT 'Item materials complement related to',
  `serie_title` varchar(255) DEFAULT NULL COMMENT 'Serie title',
  `serie_number` varchar(60) DEFAULT NULL COMMENT 'Item serie number',
  `notes` longtext DEFAULT NULL COMMENT 'Item notes',
  `isbn` varchar(20) DEFAULT NULL COMMENT 'ISBN item property',
  `issn` varchar(20) DEFAULT NULL COMMENT 'ISSN item property',
  `acquisition_condition_id` bigint(20) NOT NULL COMMENT 'Item acqusition condition',
  `acquisition_condition_notes` longtext DEFAULT NULL COMMENT 'Item acquisition condition notes',
  `cover` int(10) DEFAULT NULL COMMENT 'Item cover image',
  `createdby` int(10) unsigned NOT NULL COMMENT 'Record creator user',
  `createdon` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Record creaion date',
  `updatedby` int(10) unsigned DEFAULT NULL COMMENT 'Record last updated user',
  `updatedon` datetime DEFAULT NULL COMMENT 'Record last updated date',
  PRIMARY KEY (`id`),
  KEY `fk_item_item_type_idx` (`item_type_id`),
  KEY `fk_item_acq_cond_idx` (`acquisition_condition_id`),
  KEY `fk_item_editorial_idx` (`editorial_id`),
  CONSTRAINT `fk_item_acq_cond` FOREIGN KEY (`acquisition_condition_id`) REFERENCES `bn_acquisition_condition` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_item_editorial` FOREIGN KEY (`editorial_id`) REFERENCES `bn_editorial` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_item_item_type` FOREIGN KEY (`item_type_id`) REFERENCES `bn_item_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bn_item`
--

LOCK TABLES `bn_item` WRITE;
/*!40000 ALTER TABLE `bn_item` DISABLE KEYS */;
/*!40000 ALTER TABLE `bn_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bn_item_author`
--

DROP TABLE IF EXISTS `bn_item_author`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bn_item_author` (
  `author_id` bigint(20) NOT NULL COMMENT 'Autor relacionado con el Libro',
  `item_id` bigint(20) NOT NULL COMMENT 'Articulo que se relaciona con el autor',
  `createdby` int(10) unsigned NOT NULL COMMENT 'Record creator user',
  `createdon` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Record creaion date',
  `updatedby` int(10) unsigned DEFAULT NULL COMMENT 'Record last updated user',
  `updatedon` datetime DEFAULT NULL COMMENT 'Record last updated date',
  PRIMARY KEY (`author_id`,`item_id`),
  KEY `fk_bookauth_createdby` (`createdby`),
  KEY `fk_bookauth_updatedby` (`updatedby`),
  KEY `fk_item_author_item_idx` (`item_id`),
  CONSTRAINT `fk_item_author_author` FOREIGN KEY (`author_id`) REFERENCES `bn_author` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bn_item_author`
--

LOCK TABLES `bn_item_author` WRITE;
/*!40000 ALTER TABLE `bn_item_author` DISABLE KEYS */;
INSERT INTO `bn_item_author` VALUES (1,1,1,'2021-02-18 12:00:00',1,'2021-02-18 12:00:00'),(20,13,1,'2021-04-03 09:12:14',NULL,NULL),(21,14,1,'2021-04-03 09:12:14',NULL,NULL),(22,14,1,'2021-04-03 09:12:14',NULL,NULL),(23,15,1,'2021-04-03 09:12:14',NULL,NULL),(23,16,1,'2021-04-18 09:12:12',NULL,NULL),(24,18,1,'2021-04-03 09:12:14',NULL,NULL);
/*!40000 ALTER TABLE `bn_item_author` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bn_item_type`
--

DROP TABLE IF EXISTS `bn_item_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bn_item_type` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Table primary key',
  `type` varchar(150) NOT NULL COMMENT 'Article type name or description',
  `status` tinyint(1) NOT NULL COMMENT 'Article type status',
  `createdby` int(10) unsigned NOT NULL COMMENT 'Record creator user',
  `createdon` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Record creaion date',
  `updatedby` int(10) unsigned DEFAULT NULL COMMENT 'Record last updated user',
  `updatedon` datetime DEFAULT NULL COMMENT 'Record last updated date',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bn_item_type`
--

LOCK TABLES `bn_item_type` WRITE;
/*!40000 ALTER TABLE `bn_item_type` DISABLE KEYS */;
INSERT INTO `bn_item_type` VALUES (1,'LIBRO',1,1,'2021-03-06 14:55:59',NULL,NULL),(2,'REVISTA',1,1,'2021-03-06 14:55:59',NULL,NULL),(3,'MULTIMEDIA',1,1,'2021-03-06 14:55:59',NULL,NULL),(4,'MONOGRAFÍA',1,1,'2021-03-06 14:55:59',NULL,NULL),(5,'DIARIO',1,1,'2021-03-06 14:55:59',NULL,NULL);
/*!40000 ALTER TABLE `bn_item_type` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-05-06 18:16:14
