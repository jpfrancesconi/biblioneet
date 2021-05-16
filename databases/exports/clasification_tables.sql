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
-- Table structure for table `bn_clasification`
--

DROP TABLE IF EXISTS `bn_clasification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bn_clasification` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `code` varchar(45) NOT NULL COMMENT 'Clasification code',
  `materia` varchar(255) NOT NULL COMMENT 'Materia',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Status record',
  `createdby` int(10) unsigned NOT NULL COMMENT 'Record creator user',
  `createdon` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Record creaion date',
  `updatedby` int(10) unsigned DEFAULT NULL COMMENT 'Record last updated user',
  `updatedon` datetime DEFAULT NULL COMMENT 'Record last updated date',
  PRIMARY KEY (`id`),
  KEY `fk_clasif_createdby` (`createdby`),
  KEY `fk_clasif_updatedby` (`updatedby`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bn_clasification`
--

LOCK TABLES `bn_clasification` WRITE;
/*!40000 ALTER TABLE `bn_clasification` DISABLE KEYS */;
INSERT INTO `bn_clasification` VALUES (1,'82','LITERATURA',1,1,'2021-05-16 09:00:00',NULL,NULL);
/*!40000 ALTER TABLE `bn_clasification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bn_item_clasification`
--

DROP TABLE IF EXISTS `bn_item_clasification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bn_item_clasification` (
  `item_id` bigint(20) NOT NULL COMMENT 'Item record',
  `clasification_id` bigint(20) NOT NULL COMMENT 'Clasification subject from item',
  `createdby` int(10) unsigned NOT NULL COMMENT 'Record creator user',
  `createdon` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Record creaion date',
  `updatedby` int(10) unsigned DEFAULT NULL COMMENT 'Record last updated user',
  `updatedon` datetime DEFAULT NULL COMMENT 'Record last updated date',
  PRIMARY KEY (`item_id`,`clasification_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bn_item_clasification`
--

LOCK TABLES `bn_item_clasification` WRITE;
/*!40000 ALTER TABLE `bn_item_clasification` DISABLE KEYS */;
/*!40000 ALTER TABLE `bn_item_clasification` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-05-16 12:13:37
