-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: spc.minoffice.be    Database: miniemen_spc
-- ------------------------------------------------------
-- Server version	5.5.5-10.5.22-MariaDB-1:10.5.22+maria~ubu1804

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `vakantie_dagen`
--

DROP TABLE IF EXISTS `vakantie_dagen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vakantie_dagen` (
  `pk_vakantie_dagen` int(11) NOT NULL AUTO_INCREMENT,
  `start_datum` date DEFAULT NULL,
  `eind_datum` date DEFAULT NULL,
  `aanvraag_datum` date DEFAULT NULL,
  `goedgekeurd` tinyint(1) DEFAULT NULL,
  `personeel_fk` int(11) DEFAULT NULL,
  PRIMARY KEY (`pk_vakantie_dagen`),
  KEY `fk_vakantie_dagen_personeel` (`personeel_fk`),
  CONSTRAINT `fk_vakantie_dagen_personeel` FOREIGN KEY (`personeel_fk`) REFERENCES `personeel` (`pk_personeel`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vakantie_dagen`
--

LOCK TABLES `vakantie_dagen` WRITE;
/*!40000 ALTER TABLE `vakantie_dagen` DISABLE KEYS */;
INSERT INTO `vakantie_dagen` VALUES (1,'2025-07-01','2025-07-15','2025-06-10',1,1),(2,'2025-08-05','2025-08-09','2025-07-20',1,2),(3,'2025-10-28','2025-10-28','2025-10-20',0,3),(4,'2025-12-26','2025-12-31','2025-11-15',1,1);
/*!40000 ALTER TABLE `vakantie_dagen` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-29 14:05:30
