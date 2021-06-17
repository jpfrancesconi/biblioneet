-- MySQL dump 10.18  Distrib 10.3.27-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: 127.0.0.1    Database: biblioneet_dev
-- ------------------------------------------------------
-- Server version	10.3.27-MariaDB-1:10.3.27+maria~bionic-log

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
-- Table structure for table `bn_author`
--

DROP TABLE IF EXISTS `bn_author`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bn_author` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PRIMARY KEY',
  `first_name` varchar(150) NOT NULL COMMENT 'AUTHOR FIRST NAMES',
  `last_name` varchar(150) NOT NULL COMMENT 'AUTHOR LAST NAMES',
  `picture` int(11) DEFAULT NULL COMMENT 'AUTHOR PICTURE',
  `nationality` int(3) DEFAULT NULL COMMENT 'AUTHOR NATIONALITY',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'RECORD IS ENABLE ON SYSTEM',
  `description` longtext DEFAULT NULL COMMENT 'AUTHOR DESCRIPTION',
  `createdby` int(10) unsigned DEFAULT NULL COMMENT 'Record creator user',
  `createdon` datetime NOT NULL COMMENT 'Record creaion date',
  `updatedby` int(10) unsigned DEFAULT NULL COMMENT 'Record last updated user',
  `updatedon` datetime DEFAULT NULL COMMENT 'Record last updated date',
  PRIMARY KEY (`id`),
  KEY `nationality` (`nationality`),
  CONSTRAINT `fk_author_country` FOREIGN KEY (`nationality`) REFERENCES `bn_countries` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COMMENT='Represent authors on the sytem';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bn_author`
--

LOCK TABLES `bn_author` WRITE;
/*!40000 ALTER TABLE `bn_author` DISABLE KEYS */;
INSERT INTO `bn_author` VALUES (1,'Jorges Luis','Borges',NULL,32,1,'Jorge Francisco Isidoro Luis Borges fue un escritor de cuentos, ensayos y poemas argentino.',2,'2021-01-12 13:10:00',2,'0000-00-00 00:00:00'),(2,'Pablo','Neruda',NULL,152,1,'Pablo Neruda, seudónimo y posterior nombre legal​ de Ricardo Eliécer Neftalí Reyes Basoalto, fue un poeta y político chileno.',2,'0000-00-00 00:00:00',1,'0000-00-00 00:00:00'),(3,'Gabriel','García Márquez',NULL,170,1,NULL,2,'0000-00-00 00:00:00',1,'0000-00-00 00:00:00'),(4,'Roland','Larson',7,840,1,NULL,2,'0000-00-00 00:00:00',2,'0000-00-00 00:00:00'),(12,'Olga','Wornat',14,44,0,NULL,2,'0000-00-00 00:00:00',NULL,'0000-00-00 00:00:00'),(13,'Jose','Larralde',17,32,1,'Gran cantautor de la republica argentina',2,'2020-11-20 03:11:51',NULL,NULL),(16,'Pepe','Argento',19,8,1,'Personaje ficticio representado por Guillermo Francella',2,'2020-12-01 06:12:57',2,'2020-12-01 06:12:43'),(19,'Tom Mike','Apostol',28,840,1,'om Mike Apostol ​ fue un ingeniero y matemático estadounidense, especializado en teoría analítica de los números. Fue profesor del California Institute of Technology.',2,'2021-03-25 04:03:12',NULL,NULL),(20,'John K','Lyon',NULL,NULL,1,'',2,'2021-04-03 10:04:54',NULL,NULL),(21,'Harvey M','Deitel',32,840,1,'',2,'2021-04-03 10:04:28',NULL,NULL),(22,'Paul','Deitel',33,840,1,'',2,'2021-04-03 10:04:48',NULL,NULL),(23,'Andew S','Tanenbaum',35,840,1,'ndrew S. Tanenbaum (nacido el 16 de marzo de 1944 en Nueva York), más conocido como Tanenbaum, es el creador de una versión gratuita del SO (Sistema Operativo) Unix, llamada Minix. El objetivo de esta creación es exclusivamente educativo, por lo que no permitió la modificación de este sistema operativo, ya que pensó que resultaría difícil de entender por sus alumnos.',2,'2021-04-03 11:04:05',NULL,NULL),(24,'Laura','Tam',39,380,1,'',2,'2021-04-03 11:04:34',NULL,NULL);
/*!40000 ALTER TABLE `bn_author` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bn_clasification`
--

LOCK TABLES `bn_clasification` WRITE;
/*!40000 ALTER TABLE `bn_clasification` DISABLE KEYS */;
INSERT INTO `bn_clasification` VALUES (1,'82','LITERATURA',1,1,'2021-05-16 09:00:00',NULL,NULL),(2,'83','MATEMÁTICAS',1,1,'2021-05-24 09:00:00',NULL,NULL);
/*!40000 ALTER TABLE `bn_clasification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bn_countries`
--

DROP TABLE IF EXISTS `bn_countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bn_countries` (
  `id` int(3) NOT NULL DEFAULT 0,
  `alpha_2_code` varchar(2) DEFAULT NULL,
  `alpha_3_code` varchar(3) DEFAULT NULL,
  `en_short_name` varchar(52) DEFAULT NULL,
  `nationality` varchar(39) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alpha_2_code` (`alpha_2_code`),
  UNIQUE KEY `alpha_3_code` (`alpha_3_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bn_countries`
--

LOCK TABLES `bn_countries` WRITE;
/*!40000 ALTER TABLE `bn_countries` DISABLE KEYS */;
INSERT INTO `bn_countries` VALUES (4,'AF','AFG','Afghanistan','Afghan'),(8,'AL','ALB','Albania','Albanian'),(10,'AQ','ATA','Antarctica','Antarctic'),(12,'DZ','DZA','Algeria','Algerian'),(16,'AS','ASM','American Samoa','American Samoan'),(20,'AD','AND','Andorra','Andorran'),(24,'AO','AGO','Angola','Angolan'),(28,'AG','ATG','Antigua and Barbuda','Antiguan or Barbudan'),(31,'AZ','AZE','Azerbaijan','Azerbaijani, Azeri'),(32,'AR','ARG','Argentina','Argentine'),(36,'AU','AUS','Australia','Australian'),(40,'AT','AUT','Austria','Austrian'),(44,'BS','BHS','Bahamas','Bahamian'),(48,'BH','BHR','Bahrain','Bahraini'),(50,'BD','BGD','Bangladesh','Bangladeshi'),(51,'AM','ARM','Armenia','Armenian'),(52,'BB','BRB','Barbados','Barbadian'),(56,'BE','BEL','Belgium','Belgian'),(60,'BM','BMU','Bermuda','Bermudian, Bermudan'),(64,'BT','BTN','Bhutan','Bhutanese'),(68,'BO','BOL','Bolivia (Plurinational State of)','Bolivian'),(70,'BA','BIH','Bosnia and Herzegovina','Bosnian or Herzegovinian'),(72,'BW','BWA','Botswana','Motswana, Botswanan'),(74,'BV','BVT','Bouvet Island','Bouvet Island'),(76,'BR','BRA','Brazil','Brazilian'),(84,'BZ','BLZ','Belize','Belizean'),(86,'IO','IOT','British Indian Ocean Territory','BIOT'),(90,'SB','SLB','Solomon Islands','Solomon Island'),(92,'VG','VGB','Virgin Islands (British)','British Virgin Island'),(96,'BN','BRN','Brunei Darussalam','Bruneian'),(100,'BG','BGR','Bulgaria','Bulgarian'),(104,'MM','MMR','Myanmar','Burmese'),(108,'BI','BDI','Burundi','Burundian'),(112,'BY','BLR','Belarus','Belarusian'),(116,'KH','KHM','Cambodia','Cambodian'),(120,'CM','CMR','Cameroon','Cameroonian'),(124,'CA','CAN','Canada','Canadian'),(132,'CV','CPV','Cabo Verde','Cabo Verdean'),(136,'KY','CYM','Cayman Islands','Caymanian'),(140,'CF','CAF','Central African Republic','Central African'),(144,'LK','LKA','Sri Lanka','Sri Lankan'),(148,'TD','TCD','Chad','Chadian'),(152,'CL','CHL','Chile','Chilean'),(156,'CN','CHN','China','Chinese'),(158,'TW','TWN','Taiwan, Province of China','Chinese, Taiwanese'),(162,'CX','CXR','Christmas Island','Christmas Island'),(166,'CC','CCK','Cocos (Keeling) Islands','Cocos Island'),(170,'CO','COL','Colombia','Colombian'),(174,'KM','COM','Comoros','Comoran, Comorian'),(175,'YT','MYT','Mayotte','Mahoran'),(178,'CG','COG','Congo (Republic of the)','Congolese'),(180,'CD','COD','Congo (Democratic Republic of the)','Congolese'),(184,'CK','COK','Cook Islands','Cook Island'),(188,'CR','CRI','Costa Rica','Costa Rican'),(191,'HR','HRV','Croatia','Croatian'),(192,'CU','CUB','Cuba','Cuban'),(196,'CY','CYP','Cyprus','Cypriot'),(203,'CZ','CZE','Czech Republic','Czech'),(204,'BJ','BEN','Benin','Beninese, Beninois'),(208,'DK','DNK','Denmark','Danish'),(212,'DM','DMA','Dominica','Dominican'),(214,'DO','DOM','Dominican Republic','Dominican'),(218,'EC','ECU','Ecuador','Ecuadorian'),(222,'SV','SLV','El Salvador','Salvadoran'),(226,'GQ','GNQ','Equatorial Guinea','Equatorial Guinean, Equatoguinean'),(231,'ET','ETH','Ethiopia','Ethiopian'),(232,'ER','ERI','Eritrea','Eritrean'),(233,'EE','EST','Estonia','Estonian'),(234,'FO','FRO','Faroe Islands','Faroese'),(238,'FK','FLK','Falkland Islands (Malvinas)','Falkland Island'),(239,'GS','SGS','South Georgia and the South Sandwich Islands','South Georgia or South Sandwich Islands'),(242,'FJ','FJI','Fiji','Fijian'),(246,'FI','FIN','Finland','Finnish'),(248,'AX','ALA','Åland Islands','Åland Island'),(250,'FR','FRA','France','French'),(254,'GF','GUF','French Guiana','French Guianese'),(258,'PF','PYF','French Polynesia','French Polynesian'),(260,'TF','ATF','French Southern Territories','French Southern Territories'),(262,'DJ','DJI','Djibouti','Djiboutian'),(266,'GA','GAB','Gabon','Gabonese'),(268,'GE','GEO','Georgia','Georgian'),(270,'GM','GMB','Gambia','Gambian'),(275,'PS','PSE','Palestine, State of','Palestinian'),(276,'DE','DEU','Germany','German'),(288,'GH','GHA','Ghana','Ghanaian'),(292,'GI','GIB','Gibraltar','Gibraltar'),(296,'KI','KIR','Kiribati','I-Kiribati'),(300,'GR','GRC','Greece','Greek, Hellenic'),(304,'GL','GRL','Greenland','Greenlandic'),(308,'GD','GRD','Grenada','Grenadian'),(312,'GP','GLP','Guadeloupe','Guadeloupe'),(316,'GU','GUM','Guam','Guamanian, Guambat'),(320,'GT','GTM','Guatemala','Guatemalan'),(324,'GN','GIN','Guinea','Guinean'),(328,'GY','GUY','Guyana','Guyanese'),(332,'HT','HTI','Haiti','Haitian'),(334,'HM','HMD','Heard Island and McDonald Islands','Heard Island or McDonald Islands'),(336,'VA','VAT','Vatican City State','Vatican'),(340,'HN','HND','Honduras','Honduran'),(344,'HK','HKG','Hong Kong','Hong Kong, Hong Kongese'),(348,'HU','HUN','Hungary','Hungarian, Magyar'),(352,'IS','ISL','Iceland','Icelandic'),(356,'IN','IND','India','Indian'),(360,'ID','IDN','Indonesia','Indonesian'),(364,'IR','IRN','Iran','Iranian, Persian'),(368,'IQ','IRQ','Iraq','Iraqi'),(372,'IE','IRL','Ireland','Irish'),(376,'IL','ISR','Israel','Israeli'),(380,'IT','ITA','Italy','Italian'),(384,'CI','CIV','Côte d\'Ivoire','Ivorian'),(388,'JM','JAM','Jamaica','Jamaican'),(392,'JP','JPN','Japan','Japanese'),(398,'KZ','KAZ','Kazakhstan','Kazakhstani, Kazakh'),(400,'JO','JOR','Jordan','Jordanian'),(404,'KE','KEN','Kenya','Kenyan'),(408,'KP','PRK','Korea (Democratic People\'s Republic of)','North Korean'),(410,'KR','KOR','Korea (Republic of)','South Korean'),(414,'KW','KWT','Kuwait','Kuwaiti'),(417,'KG','KGZ','Kyrgyzstan','Kyrgyzstani, Kyrgyz, Kirgiz, Kirghiz'),(418,'LA','LAO','Lao People\'s Democratic Republic','Lao, Laotian'),(422,'LB','LBN','Lebanon','Lebanese'),(426,'LS','LSO','Lesotho','Basotho'),(428,'LV','LVA','Latvia','Latvian'),(430,'LR','LBR','Liberia','Liberian'),(434,'LY','LBY','Libya','Libyan'),(438,'LI','LIE','Liechtenstein','Liechtenstein'),(440,'LT','LTU','Lithuania','Lithuanian'),(442,'LU','LUX','Luxembourg','Luxembourg, Luxembourgish'),(446,'MO','MAC','Macao','Macanese, Chinese'),(450,'MG','MDG','Madagascar','Malagasy'),(454,'MW','MWI','Malawi','Malawian'),(458,'MY','MYS','Malaysia','Malaysian'),(462,'MV','MDV','Maldives','Maldivian'),(466,'ML','MLI','Mali','Malian, Malinese'),(470,'MT','MLT','Malta','Maltese'),(474,'MQ','MTQ','Martinique','Martiniquais, Martinican'),(478,'MR','MRT','Mauritania','Mauritanian'),(480,'MU','MUS','Mauritius','Mauritian'),(484,'MX','MEX','Mexico','Mexican'),(492,'MC','MCO','Monaco','Monégasque, Monacan'),(496,'MN','MNG','Mongolia','Mongolian'),(498,'MD','MDA','Moldova (Republic of)','Moldovan'),(499,'ME','MNE','Montenegro','Montenegrin'),(500,'MS','MSR','Montserrat','Montserratian'),(504,'MA','MAR','Morocco','Moroccan'),(508,'MZ','MOZ','Mozambique','Mozambican'),(512,'OM','OMN','Oman','Omani'),(516,'NA','NAM','Namibia','Namibian'),(520,'NR','NRU','Nauru','Nauruan'),(524,'NP','NPL','Nepal','Nepali, Nepalese'),(528,'NL','NLD','Netherlands','Dutch, Netherlandic'),(531,'CW','CUW','Curaçao','Curaçaoan'),(533,'AW','ABW','Aruba','Aruban'),(534,'SX','SXM','Sint Maarten (Dutch part)','Sint Maarten'),(535,'BQ','BES','Bonaire, Sint Eustatius and Saba','Bonaire'),(540,'NC','NCL','New Caledonia','New Caledonian'),(548,'VU','VUT','Vanuatu','Ni-Vanuatu, Vanuatuan'),(554,'NZ','NZL','New Zealand','New Zealand, NZ'),(558,'NI','NIC','Nicaragua','Nicaraguan'),(562,'NE','NER','Niger','Nigerien'),(566,'NG','NGA','Nigeria','Nigerian'),(570,'NU','NIU','Niue','Niuean'),(574,'NF','NFK','Norfolk Island','Norfolk Island'),(578,'NO','NOR','Norway','Norwegian'),(580,'MP','MNP','Northern Mariana Islands','Northern Marianan'),(581,'UM','UMI','United States Minor Outlying Islands','American'),(583,'FM','FSM','Micronesia (Federated States of)','Micronesian'),(584,'MH','MHL','Marshall Islands','Marshallese'),(585,'PW','PLW','Palau','Palauan'),(586,'PK','PAK','Pakistan','Pakistani'),(591,'PA','PAN','Panama','Panamanian'),(598,'PG','PNG','Papua New Guinea','Papua New Guinean, Papuan'),(600,'PY','PRY','Paraguay','Paraguayan'),(604,'PE','PER','Peru','Peruvian'),(608,'PH','PHL','Philippines','Philippine, Filipino'),(612,'PN','PCN','Pitcairn','Pitcairn Island'),(616,'PL','POL','Poland','Polish'),(620,'PT','PRT','Portugal','Portuguese'),(624,'GW','GNB','Guinea-Bissau','Bissau-Guinean'),(626,'TL','TLS','Timor-Leste','Timorese'),(630,'PR','PRI','Puerto Rico','Puerto Rican'),(634,'QA','QAT','Qatar','Qatari'),(638,'RE','REU','Réunion','Réunionese, Réunionnais'),(642,'RO','ROU','Romania','Romanian'),(643,'RU','RUS','Russian Federation','Russian'),(646,'RW','RWA','Rwanda','Rwandan'),(652,'BL','BLM','Saint Barthélemy','Barthélemois'),(654,'SH','SHN','Saint Helena, Ascension and Tristan da Cunha','Saint Helenian'),(659,'KN','KNA','Saint Kitts and Nevis','Kittitian or Nevisian'),(660,'AI','AIA','Anguilla','Anguillan'),(662,'LC','LCA','Saint Lucia','Saint Lucian'),(663,'MF','MAF','Saint Martin (French part)','Saint-Martinoise'),(666,'PM','SPM','Saint Pierre and Miquelon','Saint-Pierrais or Miquelonnais'),(670,'VC','VCT','Saint Vincent and the Grenadines','Saint Vincentian, Vincentian'),(674,'SM','SMR','San Marino','Sammarinese'),(678,'ST','STP','Sao Tome and Principe','São Toméan'),(682,'SA','SAU','Saudi Arabia','Saudi, Saudi Arabian'),(686,'SN','SEN','Senegal','Senegalese'),(688,'RS','SRB','Serbia','Serbian'),(690,'SC','SYC','Seychelles','Seychellois'),(694,'SL','SLE','Sierra Leone','Sierra Leonean'),(702,'SG','SGP','Singapore','Singaporean'),(703,'SK','SVK','Slovakia','Slovak'),(704,'VN','VNM','Vietnam','Vietnamese'),(705,'SI','SVN','Slovenia','Slovenian, Slovene'),(706,'SO','SOM','Somalia','Somali, Somalian'),(710,'ZA','ZAF','South Africa','South African'),(716,'ZW','ZWE','Zimbabwe','Zimbabwean'),(724,'ES','ESP','Spain','Spanish'),(728,'SS','SSD','South Sudan','South Sudanese'),(729,'SD','SDN','Sudan','Sudanese'),(732,'EH','ESH','Western Sahara','Sahrawi, Sahrawian, Sahraouian'),(740,'SR','SUR','Suriname','Surinamese'),(744,'SJ','SJM','Svalbard and Jan Mayen','Svalbard'),(748,'SZ','SWZ','Swaziland','Swazi'),(752,'SE','SWE','Sweden','Swedish'),(756,'CH','CHE','Switzerland','Swiss'),(760,'SY','SYR','Syrian Arab Republic','Syrian'),(762,'TJ','TJK','Tajikistan','Tajikistani'),(764,'TH','THA','Thailand','Thai'),(768,'TG','TGO','Togo','Togolese'),(772,'TK','TKL','Tokelau','Tokelauan'),(776,'TO','TON','Tonga','Tongan'),(780,'TT','TTO','Trinidad and Tobago','Trinidadian or Tobagonian'),(784,'AE','ARE','United Arab Emirates','Emirati, Emirian, Emiri'),(788,'TN','TUN','Tunisia','Tunisian'),(792,'TR','TUR','Turkey','Turkish'),(795,'TM','TKM','Turkmenistan','Turkmen'),(796,'TC','TCA','Turks and Caicos Islands','Turks and Caicos Island'),(798,'TV','TUV','Tuvalu','Tuvaluan'),(800,'UG','UGA','Uganda','Ugandan'),(804,'UA','UKR','Ukraine','Ukrainian'),(807,'MK','MKD','Macedonia (the former Yugoslav Republic of)','Macedonian'),(818,'EG','EGY','Egypt','Egyptian'),(826,'GB','GBR','United Kingdom of Great Britain and Northern Ireland','British, UK'),(831,'GG','GGY','Guernsey','Channel Island'),(832,'JE','JEY','Jersey','Channel Island'),(833,'IM','IMN','Isle of Man','Manx'),(834,'TZ','TZA','Tanzania, United Republic of','Tanzanian'),(840,'US','USA','United States of America','American'),(850,'VI','VIR','Virgin Islands (U.S.)','U.S. Virgin Island'),(854,'BF','BFA','Burkina Faso','Burkinabé'),(858,'UY','URY','Uruguay','Uruguayan'),(860,'UZ','UZB','Uzbekistan','Uzbekistani, Uzbek'),(862,'VE','VEN','Venezuela (Bolivarian Republic of)','Venezuelan'),(876,'WF','WLF','Wallis and Futuna','Wallis and Futuna, Wallisian or Futunan'),(882,'WS','WSM','Samoa','Samoan'),(887,'YE','YEM','Yemen','Yemeni'),(894,'ZM','ZMB','Zambia','Zambian');
/*!40000 ALTER TABLE `bn_countries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bn_editorial`
--

DROP TABLE IF EXISTS `bn_editorial`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bn_editorial` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Clave primaria de la tabla',
  `editorial` varchar(255) NOT NULL COMMENT 'Nombre de la editorial',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Determina si el registro esta activo en el sistema.',
  `createdby` int(10) unsigned NOT NULL COMMENT 'Record creator user',
  `createdon` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Record creaion date',
  `updatedby` int(10) unsigned DEFAULT NULL COMMENT 'Record last updated user',
  `updatedon` datetime DEFAULT NULL COMMENT 'Record last updated date',
  PRIMARY KEY (`id`),
  KEY `fk_editorial_createdby` (`createdby`),
  KEY `fk_editorial_updatedby` (`updatedby`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bn_editorial`
--

LOCK TABLES `bn_editorial` WRITE;
/*!40000 ALTER TABLE `bn_editorial` DISABLE KEYS */;
INSERT INTO `bn_editorial` VALUES (1,'DEBOLSILLO',1,1,'2021-02-18 12:00:00',1,'2021-02-18 12:00:00'),(2,'REVERTÉ',1,1,'2021-03-25 00:00:00',NULL,NULL),(3,'EL ATENEO',1,1,'2021-04-03 12:00:00',NULL,NULL),(4,'PRENTICE HALL',1,1,'2021-04-03 12:00:00',NULL,NULL),(5,'EMECE',1,1,'2021-04-03 12:00:00',NULL,NULL),(6,'Sudamericana',1,2,'2021-05-09 07:05:43',NULL,NULL);
/*!40000 ALTER TABLE `bn_editorial` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bn_index`
--

DROP TABLE IF EXISTS `bn_index`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bn_index` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Primary key',
  `content` varchar(255) NOT NULL COMMENT 'Index text content',
  `number` int(5) DEFAULT NULL COMMENT 'Index page number',
  `item_id` bigint(20) NOT NULL COMMENT 'Item to which index belongs to',
  `index_id` bigint(20) DEFAULT NULL COMMENT 'Index parent',
  `peso` int(3) NOT NULL DEFAULT 0 COMMENT 'Row weight',
  `createdby` int(10) unsigned NOT NULL COMMENT 'User creator ID',
  `createdon` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Record creation date',
  `updatedby` int(10) unsigned DEFAULT NULL COMMENT 'User ID update record',
  `updatedon` datetime DEFAULT NULL COMMENT 'Last update record',
  PRIMARY KEY (`id`),
  KEY `fk_index_createdby_idx` (`createdby`),
  KEY `fk_index_updatedby_idx` (`updatedby`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bn_index`
--

LOCK TABLES `bn_index` WRITE;
/*!40000 ALTER TABLE `bn_index` DISABLE KEYS */;
INSERT INTO `bn_index` VALUES (1,'Series numericas de valores reales',123,4,NULL,0,2,'2021-06-04 08:49:52',NULL,NULL),(2,'Series numericas de valores fijos',128,4,1,1,2,'2021-06-04 08:57:20',NULL,NULL),(3,'Otro contenido',23,4,1,1,2,'2021-06-04 15:44:01',NULL,NULL),(4,'Contenido del indice 432',432,4,6,-9,2,'2021-06-04 15:44:33',NULL,NULL),(5,'Contenido interno 66',66,4,6,-10,2,'2021-06-04 15:46:43',2,'2021-06-04 03:06:58'),(6,'entrada de indice 5',5,4,2,0,2,'2021-06-04 16:50:51',2,'2021-06-04 04:06:50'),(7,'3.3 Pseudocodigo',51,11,NULL,0,2,'2021-06-05 10:26:27',NULL,NULL),(8,'3.4 Estructuras de control',51,11,NULL,0,2,'2021-06-05 10:26:54',NULL,NULL),(9,'3.5 La instruccion de seleccion if',53,11,NULL,0,2,'2021-06-05 10:27:09',2,'2021-06-05 10:06:56'),(10,'3.6 La instruccion de seleccion if...else',54,11,NULL,0,2,'2021-06-05 10:27:23',2,'2021-06-05 10:06:08');
/*!40000 ALTER TABLE `bn_index` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bn_instance`
--

DROP TABLE IF EXISTS `bn_instance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bn_instance` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Primary key',
  `inv_code` varchar(45) DEFAULT NULL COMMENT 'Inventary code',
  `instance_status_id` bigint(20) NOT NULL COMMENT 'Current instance status',
  `signature` varchar(150) DEFAULT NULL COMMENT 'Instance signature',
  `item_id` bigint(20) NOT NULL COMMENT 'Article which this instance belong to',
  `createdby` int(10) unsigned NOT NULL COMMENT 'Record creator user',
  `createdon` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Record creaion date',
  `updatedby` int(10) unsigned DEFAULT NULL COMMENT 'Record last updated user',
  `updatedon` datetime DEFAULT NULL COMMENT 'Record last updated date',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bn_instance`
--

LOCK TABLES `bn_instance` WRITE;
/*!40000 ALTER TABLE `bn_instance` DISABLE KEYS */;
INSERT INTO `bn_instance` VALUES (1,'A2000133',1,'9998881',1,2,'2021-05-08 00:00:00',NULL,NULL),(2,'A2000134',3,'9998882',1,2,'2021-05-08 12:05:53',NULL,NULL),(3,'A2000135',1,'9998883',1,2,'2021-05-08 01:05:39',NULL,NULL),(4,'A2000136',4,'9998884',1,1,'2021-05-08 01:05:56',NULL,NULL),(5,'A2000137',1,'9998885',1,1,'2021-05-08 01:05:30',NULL,NULL),(6,'200-34443',2,'MAT-200-34443',4,2,'2021-05-15 08:05:15',NULL,NULL),(7,'200-99881',1,'MAT-200-99881',4,2,'2021-05-15 08:05:11',NULL,NULL),(8,'200-88991',1,'MAT-200-88991',4,2,'2021-05-15 08:05:56',NULL,NULL),(12,'300-000011',1,'COMP-300-000011',2,2,'2021-05-16 10:05:43',NULL,NULL),(13,'201-34443',1,'PRO-201-34443',11,2,'2021-06-05 09:06:33',NULL,NULL),(14,'123456',1,'9998882',5,2,'2021-06-07 08:06:08',NULL,NULL);
/*!40000 ALTER TABLE `bn_instance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bn_instance_status`
--

DROP TABLE IF EXISTS `bn_instance_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bn_instance_status` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Primary key',
  `status_name` varchar(150) NOT NULL COMMENT 'Status name',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Determine if this record is active.',
  `lendable` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Determines if instance status represent a lendable status.',
  `createdby` int(10) unsigned NOT NULL COMMENT 'Record creator user',
  `createdon` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Record creaion date',
  `updatedby` int(10) unsigned DEFAULT NULL COMMENT 'Record last updated user',
  `updatedon` datetime DEFAULT NULL COMMENT 'Record last updated date',
  PRIMARY KEY (`id`),
  KEY `fk_inssta_createdby` (`createdby`),
  KEY `fk_inssta_updatedby` (`updatedby`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bn_instance_status`
--

LOCK TABLES `bn_instance_status` WRITE;
/*!40000 ALTER TABLE `bn_instance_status` DISABLE KEYS */;
INSERT INTO `bn_instance_status` VALUES (1,'DISPONIBLE',1,1,1,'2021-03-18 21:14:21',NULL,NULL),(2,'PRESTADO',1,0,1,'2021-03-18 21:14:21',NULL,NULL),(3,'SOLO CONSULTA',1,1,1,'2021-03-18 21:14:21',NULL,NULL),(4,'EN REPARACIÓN',1,0,1,'2021-03-18 21:14:21',NULL,NULL),(5,'PERDIDO',1,0,1,'2021-03-18 21:14:21',NULL,NULL),(6,'BAJA POR ROTURA',1,0,1,'2021-03-18 21:14:21',NULL,NULL);
/*!40000 ALTER TABLE `bn_instance_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bn_item`
--

DROP TABLE IF EXISTS `bn_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bn_item` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'PK Table',
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bn_item`
--

LOCK TABLES `bn_item` WRITE;
/*!40000 ALTER TABLE `bn_item` DISABLE KEYS */;
INSERT INTO `bn_item` VALUES (1,'MI PLANTA DE NARANJA LIMA',1,'MY PLANT OF ORANGE LIME','3RA','BUENOS AIRES',4,'1880','GRANDE','23X23X34','TAPA DURA','VIENE CON UN CD: EL MANUAL DEL LECTOR','SERIE PLANTAS','II','QUISQUE VELIT NISI, PRETIUM UT LACINIA IN, ELEMENTUM ID ENIM. SED PORTTITOR LECTUS NIBH.','123123123123','979789789789',1,'QUISQUE VELIT NISI, PRETIUM UT LACINIA IN, ELEMENTUM ID ENIM. SED PORTTITOR LECTUS NIBH.',NULL,2,'2021-05-06 21:38:11',NULL,NULL),(2,'REDES DE COMPUTADORAS',1,'COMPUTERS NETWORKS','4TA','BUENOS AIRES',4,'2003','980 P.','23X23X34','IL., RETRS.','+ 1 CD-ROOM','','','','','',1,'DONADO POR UN EX ALUMNO',41,2,'2021-05-07 07:21:11',NULL,NULL),(4,'SIETE NOCHES / JORGE LUIS BORGES 2.',2,'2','20182','BUENOS AIRES : SUDAMERICANA, 2018.2',3,'20182','2','2','190 P. ; 21 CM.2','2','2','2','CONTIENE: LA DIVINA COMEDIA -- LA PESADILLA -- LAS MIL Y UNA NOCHES -- EL BUDISMO -- LA POESíA -- LA CáBALA -- LA CEGUERA.2\r\nNOTA  	SUMARIO: ESTE VOLUMEN REúNE LOS TEXTOS COMPLETOS DE LAS SIETE CONFERENCIAS QUE BORGES DIO EN EL TEATRO COLISEO DE BUENOS AIRES EN 1977. SE TRATó DE SU CICLO DE EXPOSICIONES PúBLICAS MáS EXTENSO.','978-950-07-6226-2','2',1,'2',NULL,2,'2021-05-09 19:54:43',2,'2021-06-04 09:06:05'),(5,'EL ORO DE LOS TIGRES / JORGE LUIS BORGES.',1,'','1A ED.','BUENOS AIRES : SUDAMERICANA, 2018.',6,'2018','','','109 P. ; 21 CM.','','','','','978-950-07-6191-8','',1,'',NULL,2,'2021-05-09 20:01:52',NULL,NULL),(6,'LA ROSA PROFUNDA / JORGE LUIS BORGES.',1,'','','BUENOS AIRES : SUDAMERICANA, ©2018.',6,'2018','','','70 P. ; 21 CM.','','','','','978-950-07-6212-0','',1,'',NULL,2,'2021-05-09 20:06:07',NULL,NULL),(8,'PRUEBA DE LIBRO 1',1,'','','',NULL,'','','','','','','','','','',1,'',NULL,1,'2021-05-24 19:35:17',NULL,NULL),(9,'PRUEBA DE LIBRO 2',1,'BOOK TEST 2','3RA','BUENOS AIRES',4,'1880','980 P.','23X23X34','156 P. ; 22 CM.','+ 1 CD-ROOM','SERIE PLANTAS','II','THOSE ARE THE NOTES OF THIS BOOK CALLED TEST BOOK 2','978-950-07-5645-7','979789789789',1,'DONAR',NULL,1,'2021-05-24 20:03:14',NULL,NULL),(10,'PRUEBA DE LIBRO 3',1,'','','',NULL,'','','','','','','','','','',1,'',NULL,1,'2021-05-24 20:12:48',NULL,NULL),(11,'COMO PROGRAMAR EN C/C++ Y JAVA',1,'JAVA AND C/C++ HOW TO CODE','4TA','MEXICO',4,'2004','','20X25.5','1152P','CD-ROM','','','LIBRO DE PROGRAMACIóN','970-26-0531-8','',1,'DONADO POR UN ALUMNO.',56,2,'2021-06-05 08:17:25',2,'2021-06-05 09:06:10');
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
  CONSTRAINT `fk_item_author_author` FOREIGN KEY (`author_id`) REFERENCES `bn_author` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_item_author_item` FOREIGN KEY (`item_id`) REFERENCES `bn_item` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bn_item_author`
--

LOCK TABLES `bn_item_author` WRITE;
/*!40000 ALTER TABLE `bn_item_author` DISABLE KEYS */;
INSERT INTO `bn_item_author` VALUES (1,4,2,'2021-06-04 09:06:05',NULL,NULL),(1,5,2,'2021-05-09 08:05:52',NULL,NULL),(1,6,2,'2021-05-09 08:05:07',NULL,NULL),(1,8,1,'2021-05-24 07:05:17',NULL,NULL),(2,1,2,'2021-05-06 09:05:11',NULL,NULL),(3,4,2,'2021-06-04 09:06:05',NULL,NULL),(3,10,1,'2021-05-24 08:05:48',NULL,NULL),(20,9,1,'2021-05-24 08:05:14',NULL,NULL),(21,4,2,'2021-06-04 09:06:05',NULL,NULL),(21,11,2,'2021-06-05 09:06:10',NULL,NULL),(22,9,1,'2021-05-24 08:05:14',NULL,NULL),(22,11,2,'2021-06-05 09:06:10',NULL,NULL),(23,2,2,'2021-05-07 07:05:11',NULL,NULL);
/*!40000 ALTER TABLE `bn_item_author` ENABLE KEYS */;
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
INSERT INTO `bn_item_clasification` VALUES (4,1,2,'2021-06-04 09:06:05',NULL,NULL),(8,1,1,'2021-05-24 07:05:17',NULL,NULL),(9,1,1,'2021-05-24 08:05:14',NULL,NULL),(9,2,1,'2021-05-24 08:05:14',NULL,NULL),(10,1,1,'2021-05-24 08:05:48',NULL,NULL),(11,1,2,'2021-06-05 09:06:10',NULL,NULL);
/*!40000 ALTER TABLE `bn_item_clasification` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bn_item_type`
--

LOCK TABLES `bn_item_type` WRITE;
/*!40000 ALTER TABLE `bn_item_type` DISABLE KEYS */;
INSERT INTO `bn_item_type` VALUES (1,'LIBRO',1,1,'2021-03-06 14:55:59',NULL,NULL),(2,'REVISTA',1,1,'2021-03-06 14:55:59',NULL,NULL),(3,'MULTIMEDIA',1,1,'2021-03-06 14:55:59',NULL,NULL),(4,'MONOGRAFÍA',1,1,'2021-03-06 14:55:59',NULL,NULL),(5,'DIARIO',1,1,'2021-03-06 14:55:59',NULL,NULL),(6,'LÁMINA',1,1,'2021-05-14 12:00:00',NULL,NULL);
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

-- Dump completed on 2021-06-17  9:34:56
