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
-- Table structure for table `personeel`
--

DROP TABLE IF EXISTS `personeel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personeel` (
  `pk_personeel` int(11) NOT NULL AUTO_INCREMENT,
  `voornaam` varchar(255) NOT NULL,
  `familienaam` varchar(255) NOT NULL,
  `pin` varchar(4) NOT NULL,
  `email` varchar(255) NOT NULL,
  `nfc_id` varchar(255) DEFAULT NULL,
  `gebruikersnaam` varchar(255) DEFAULT NULL,
  `actief` tinyint(1) DEFAULT NULL,
  `job_fk` int(11) DEFAULT NULL,
  PRIMARY KEY (`pk_personeel`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `gebruikersnaam` (`gebruikersnaam`),
  KEY `fk_personeel_job` (`job_fk`),
  CONSTRAINT `fk_personeel_job` FOREIGN KEY (`job_fk`) REFERENCES `job` (`pk_job`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personeel`
--

LOCK TABLES `personeel` WRITE;
/*!40000 ALTER TABLE `personeel` DISABLE KEYS */;
INSERT INTO `personeel` VALUES (1,'Jan','Janssens','1234','jan.janssens@school.be','04680C9A6E1D94','jan.j',0,1),(2,'piet','Pieters','5678','piet.pieters@school.be','0460B29A6E1D90','piet.p',0,2),(3,'marie','maes','9012','marie.maes@school.be','044B079A6E1D94','marie.m',0,3),(4,'Kevin','De Bruyne','3456','kevin.db@school.be','04E4179A6E1D94','kevin.db',0,NULL),(5,'Els','Peeters','7890','els.peeters@school.be','0449619A6E1D91','els.p',0,NULL),(17,'glennick','schouteet','1234','g@gmail','123456789','glenn',0,NULL),(18,'test','spc','1234','test@test.test',NULL,'test',1,1),(19,'sep','emmers','1234','m@gmail','123456789','m@gmail',1,NULL),(21,'Jan','Janssens','1234','a@gmail','123456789','a@gmail',1,NULL),(22,'Jesse','van der Linden','1235','jes.linden@school.be','123456789','Jes.D',1,NULL);
/*!40000 ALTER TABLE `personeel` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-29 14:05:28
