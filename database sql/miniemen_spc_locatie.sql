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
-- Table structure for table `locatie`
--

DROP TABLE IF EXISTS `locatie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `locatie` (
  `pk_locatie` int(11) NOT NULL AUTO_INCREMENT,
  `naam` varchar(255) DEFAULT NULL,
  `adres` varchar(255) DEFAULT NULL,
  `school_fk` int(11) DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `latitude` float DEFAULT NULL,
  `straal` float DEFAULT NULL,
  `polygon_coordinates` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`polygon_coordinates`)),
  PRIMARY KEY (`pk_locatie`),
  KEY `fk_locatie_school` (`school_fk`),
  CONSTRAINT `fk_locatie_school` FOREIGN KEY (`school_fk`) REFERENCES `school` (`pk_school`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `locatie`
--

LOCK TABLES `locatie` WRITE;
/*!40000 ALTER TABLE `locatie` DISABLE KEYS */;
INSERT INTO `locatie` VALUES (1,'Hoofdgebouw','Schoolstraat 10, 3000 Leuven',101,4.7,50.88,100,'[\r   {\"latitude\": 51.5, \"longitude\": 2.5},\r \r   {\"latitude\": 51.5, \"longitude\": 6.4},\r \r   {\"latitude\": 49.5, \"longitude\": 6.4},\r \r{\"latitude\": 49.5, \"longitude\": 2.5}\r \r ]'),(2,'Bijgebouw Noord','Kerkplein 5, 3001 Heverlee',102,4.71,50.87,75,NULL),(3,'Sportzaal','Parklaan 12, 3000 Leuven',101,4.69,50.89,50,NULL);
/*!40000 ALTER TABLE `locatie` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-29 14:05:29
