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
-- Table structure for table `werkuren`
--

DROP TABLE IF EXISTS `werkuren`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `werkuren` (
  `pk_werkuren` int(11) NOT NULL AUTO_INCREMENT,
  `personeel_fk` int(11) DEFAULT NULL,
  `starttijd` datetime DEFAULT NULL,
  `eindtijd` datetime DEFAULT NULL,
  `school_fk` int(11) DEFAULT NULL,
  `job_fk` int(11) DEFAULT NULL,
  `datum` date DEFAULT NULL,
  `current_break_start_timestamp` datetime DEFAULT NULL COMMENT 'Timestamp when the current break segment started or resumed (UTC recommended)',
  `current_break_accumulated_seconds_before_pause` int(11) NOT NULL DEFAULT 0 COMMENT 'Accumulated seconds for the current break segment before it was last paused',
  `final_break_total_seconds` int(11) NOT NULL DEFAULT 0 COMMENT 'Total seconds of all breaks for this work session, finalized on work stop',
  PRIMARY KEY (`pk_werkuren`),
  KEY `fk_werkuren_personeel` (`personeel_fk`),
  KEY `fk_werkuren_school` (`school_fk`),
  KEY `fk_werkuren_job` (`job_fk`),
  CONSTRAINT `fk_werkuren_job` FOREIGN KEY (`job_fk`) REFERENCES `job` (`pk_job`),
  CONSTRAINT `fk_werkuren_personeel` FOREIGN KEY (`personeel_fk`) REFERENCES `personeel` (`pk_personeel`),
  CONSTRAINT `fk_werkuren_school` FOREIGN KEY (`school_fk`) REFERENCES `school` (`pk_school`)
) ENGINE=InnoDB AUTO_INCREMENT=128 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `werkuren`
--

LOCK TABLES `werkuren` WRITE;
/*!40000 ALTER TABLE `werkuren` DISABLE KEYS */;
INSERT INTO `werkuren` VALUES (125,5,'2025-05-21 11:50:00','2025-05-21 15:50:00',102,2,'2025-05-29',NULL,0,0),(126,1,'2025-05-21 11:53:00','2025-05-21 11:55:00',103,1,'2025-05-21',NULL,0,0),(127,17,'2025-05-13 11:29:00','2025-05-13 23:29:00',103,1,'2025-05-13',NULL,0,0);
/*!40000 ALTER TABLE `werkuren` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-29 14:05:31
