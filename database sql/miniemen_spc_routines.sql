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
-- Dumping events for database 'miniemen_spc'
--
/*!50106 SET @save_time_zone= @@TIME_ZONE */ ;
/*!50106 DROP EVENT IF EXISTS `StopUnfinishedWorkHours` */;
DELIMITER ;;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;;
/*!50003 SET character_set_client  = utf8mb4 */ ;;
/*!50003 SET character_set_results = utf8mb4 */ ;;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;;
/*!50003 SET @saved_time_zone      = @@time_zone */ ;;
/*!50003 SET time_zone             = '+00:00' */ ;;
/*!50106 CREATE*/ /*!50117 DEFINER=`miniemen_sep`@`%`*/ /*!50106 EVENT `StopUnfinishedWorkHours` ON SCHEDULE EVERY 1 DAY STARTS '2025-03-31 23:59:59' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    UPDATE werkuren
    SET eindtijd = NOW()
    WHERE eindtijd IS NULL;
END */ ;;
/*!50003 SET time_zone             = @saved_time_zone */ ;;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;;
/*!50003 SET character_set_client  = @saved_cs_client */ ;;
/*!50003 SET character_set_results = @saved_cs_results */ ;;
/*!50003 SET collation_connection  = @saved_col_connection */ ;;
DELIMITER ;
/*!50106 SET TIME_ZONE= @save_time_zone */ ;

--
-- Dumping routines for database 'miniemen_spc'
--
/*!50003 DROP PROCEDURE IF EXISTS `GetActievePersoneelDetails` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `GetActievePersoneelDetails`()
BEGIN
    SELECT CONCAT(voornaam, ' ', familienaam) AS VolledigeNaam, Email, gebruikersnaam, pk_personeel
    FROM personeel
    WHERE actief = 0;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `GetActievePersoneelDetails_klasgenoot` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `GetActievePersoneelDetails_klasgenoot`()
BEGIN
    SELECT CONCAT(voornaam, ' ', familienaam) AS VolledigeNaam, Email, gebruikersnaam, pk_personeel
    FROM personeel
    WHERE actief = 0;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `GetPersonnelList` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `GetPersonnelList`()
BEGIN
    SELECT pk_personeel, voornaam, familienaam, email FROM personeel;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `GetUserByNfcId` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `GetUserByNfcId`(IN `p_nfc_id` VARCHAR(255))
BEGIN
    SELECT pk_personeel, email
    FROM personeel
    WHERE nfc_id = p_nfc_id;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `SP_FinalizeWorkSessionBreaks` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `SP_FinalizeWorkSessionBreaks`(
    IN IN_pk_werkuren INT,
    IN IN_total_final_break_seconds INT
)
BEGIN
    UPDATE werkuren
    SET 
        final_break_total_seconds = IN_total_final_break_seconds,
        current_break_start_timestamp = NULL,
        current_break_accumulated_seconds_before_pause = 0
    WHERE pk_werkuren = IN_pk_werkuren;
    -- Add other logic for stopping a work session here if this SP handles it all,
    -- or call this SP from your main work session stopping SP.
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `SP_PauseBreak` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `SP_PauseBreak`(
    IN IN_pk_werkuren INT,
    IN IN_accumulated_seconds INT
)
BEGIN
    UPDATE werkuren
    SET 
        current_break_start_timestamp = NULL,
        current_break_accumulated_seconds_before_pause = IN_accumulated_seconds
    WHERE pk_werkuren = IN_pk_werkuren;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_PersoneelLogin` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `sp_PersoneelLogin`(IN `p_email` VARCHAR(255), IN `p_pin` VARCHAR(4))
BEGIN
    SELECT
        pk_personeel, -- De primaire sleutel van de personeel tabel
        CASE
            WHEN LENGTH(p.pin) <> 4 THEN 1 -- Als de lengte van de PIN niet 4 is, dan moet de PIN ingesteld worden
            WHEN p.pin = p_pin THEN 1 -- Als de PIN wel 4 tekens lang is en overeenkomt met de ingevoerde PIN, dan is de login succesvol
            ELSE 0 -- Anders is de login niet succesvol
        END AS LoginSuccessful,
        CASE
            WHEN LENGTH(p.pin) <> 4 THEN 1 -- Als de lengte van de PIN niet 4 is, dan moet de PIN ingesteld worden
            ELSE 0
        END AS NeedsPinSetup
    FROM personeel p
    WHERE p.email = p_email;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `SP_SetBreakStartTimestamp` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `SP_SetBreakStartTimestamp`(
    IN IN_pk_werkuren INT,  -- Assuming pk_werkuren is INT
    IN IN_timestamp DATETIME
)
BEGIN
    UPDATE werkuren
    SET current_break_start_timestamp = IN_timestamp
    WHERE pk_werkuren = IN_pk_werkuren;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_UpdatePersoneelPin` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `sp_UpdatePersoneelPin`(IN `p_personeel_id` INT, IN `p_pin` VARCHAR(4))
BEGIN
    UPDATE personeel
    SET pin = p_pin
    WHERE pk_personeel = p_personeel_id;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `startOphaal` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `startOphaal`()
BEGIN
    SELECT
        DATE_FORMAT(CURDATE(), '%W %e %M') AS HuidigeDatum,
        CONCAT(voornaam, ' ', familienaam) AS VolledigeNaam
    FROM
        personeel;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `StopOldWorkHours` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `StopOldWorkHours`(IN `p_personeel_id` INT)
BEGIN
    DECLARE stopTijdEindeDag DATETIME;
    SET stopTijdEindeDag = TIMESTAMP(DATE(CURDATE() - INTERVAL 1 DAY), '23:59:59');

    UPDATE werkuren
    SET stop_tijd = stopTijdEindeDag
    WHERE personeel_id = p_personeel_id
      AND stop_tijd IS NULL
      AND DATE(start_tijd) <= DATE(CURDATE() - INTERVAL 1 DAY);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `UpdatePersoneel` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `UpdatePersoneel`(IN `p_pk_personeel` INT, IN `p_voornaam` VARCHAR(255), IN `p_familienaam` VARCHAR(255), IN `p_pin` VARCHAR(255), IN `p_email` VARCHAR(255), IN `p_gebruikersnaam` VARCHAR(255), IN `p_actief` BOOLEAN)
BEGIN
    UPDATE personeel
    SET
        voornaam = p_voornaam,
        familienaam = p_familienaam,
        pin = p_pin,
        Email = p_email,
        gebruikersnaam = p_gebruikersnaam,
        actief = p_actief
    WHERE pk_personeel = p_pk_personeel;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `UpdateWerkuren` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `UpdateWerkuren`(IN `p_pk_werkuren` INT, IN `p_personeel_fk` INT, IN `p_starttijd` DATETIME, IN `p_eindtijd` DATETIME, IN `p_datum` DATE, IN `p_school_fk` INT, IN `p_job_fk` INT)
BEGIN
    UPDATE werkuren
    SET
        starttijd = p_starttijd,
        eindtijd = p_eindtijd,
        datum = p_datum,
        school_fk = p_school_fk,
        job_fk = p_job_fk
    WHERE pk_werkuren = p_pk_werkuren;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `usp_AdminLogin` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `usp_AdminLogin`(IN `p_email` VARCHAR(255), IN `p_password` VARCHAR(255))
BEGIN
    SELECT
        pk_admin, -- De primaire sleutel van de admin tabel
        CASE
            WHEN EXISTS (SELECT 1 FROM admin WHERE email = p_email AND paswoord = p_password) THEN 1
            ELSE 0
        END AS LoginSuccessful
    FROM admin
    WHERE email = p_email;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `usp_DeleteWerkuur` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `usp_DeleteWerkuur`(IN `p_werkuren_id` INT)
BEGIN
    DELETE FROM werkuren
    WHERE pk_werkuren = p_werkuren_id;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `usp_GetCurrentTime` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `usp_GetCurrentTime`()
BEGIN
    SELECT DATE_FORMAT(NOW(), '%H:%i:%s') AS HuidigeTijd;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `usp_GetDailyWorkHistory` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `usp_GetDailyWorkHistory`(IN `p_personeel_fk` INT, IN `p_date` DATE)
BEGIN
    SELECT starttijd, eindtijd
    FROM werkuren
    WHERE personeel_fk = p_personeel_fk
      AND DATE(starttijd) = p_date;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `usp_GetEnhancedStartInfo` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `usp_GetEnhancedStartInfo`(IN `in_personeel_id` INT, IN `in_school_fk` INT)
BEGIN
    -- Declareer variabelen
    DECLARE v_personeelsnaam VARCHAR(511);
    DECLARE v_is_gestart BOOLEAN DEFAULT FALSE;
    DECLARE v_is_vandaag_voltooid BOOLEAN DEFAULT FALSE;
    DECLARE v_polygons_json TEXT;

    -- 1. Haal personeelsnaam op
    SELECT CONCAT(p.voornaam, ' ', p.familienaam) INTO v_personeelsnaam
    FROM personeel p
    WHERE p.pk_personeel = in_personeel_id
    LIMIT 1;

    -- 2. Controleer of werkuur VANDAAG gestart is EN actief is
    SELECT EXISTS (
        SELECT 1 FROM werkuren w
        WHERE w.personeel_fk = in_personeel_id AND DATE(w.starttijd) = CURDATE() AND w.eindtijd IS NULL
        LIMIT 1
    ) INTO v_is_gestart;

    -- 3. Controleer of werkuur VANDAAG gestart EN voltooid is
    SELECT EXISTS (
        SELECT 1 FROM werkuren w
        WHERE w.personeel_fk = in_personeel_id AND DATE(w.starttijd) = CURDATE() AND w.eindtijd IS NOT NULL
        LIMIT 1
    ) INTO v_is_vandaag_voltooid;

    -- 4. Verzamel geldige polygon JSON strings met GROUP_CONCAT
    --    en voeg zelf de '[' en ']' toe voor een geldige JSON array.
    SELECT CONCAT('[', -- Openingshaak
                  -- Gebruik IFNULL om lege string te krijgen ipv NULL als er geen rijen zijn, voorkomt CONCAT('[', NULL, ']')
                  IFNULL(GROUP_CONCAT(l.polygon_coordinates SEPARATOR ','), ''),
                  ']'  -- Sluitingshaak
           )
    INTO v_polygons_json
    FROM locatie l
    WHERE l.school_fk = in_school_fk
      AND l.polygon_coordinates IS NOT NULL
      AND JSON_VALID(l.polygon_coordinates); -- Valideer nog steeds de individuele JSON

    -- 5. Selecteer het eindresultaat.
    --    COALESCE is hier niet strikt nodig door de IFNULL in de vorige stap,
    --    maar kan blijven staan voor extra zekerheid.
    SELECT
        TRUE AS success,
        v_personeelsnaam AS personeelsnaam,
        v_is_gestart AS isWerkuurGestart,
        v_is_vandaag_voltooid AS isVandaagAlGewerktofGestopt,
        COALESCE(v_polygons_json, '[]') AS polygons_json -- Stuur '[]' als er echt niks was
    ;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `usp_GetLocationPolygonCoordinatesBySchool` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `usp_GetLocationPolygonCoordinatesBySchool`(IN `in_school_fk` INT)
BEGIN
    SELECT polygon_coordinates
    FROM locatie
    WHERE school_fk = in_school_fk;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `usp_GetMonthlyWorkHistory` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `usp_GetMonthlyWorkHistory`(IN `p_personeel_fk` INT, IN `p_year` INT, IN `p_month` INT)
BEGIN
    SELECT
        w.starttijd,
        IF(w.eindtijd IS NOT NULL, w.eindtijd, NULL) as eindtijd
    FROM
        werkuren w -- Gebruik een alias voor de tabelnaam
    WHERE
        w.personeel_fk = p_personeel_fk
        AND YEAR(w.datum) = p_year
        AND MONTH(w.datum) = p_month
    ORDER BY
        w.starttijd DESC;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `usp_GetMonthlyWorkHistoryadmin` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `usp_GetMonthlyWorkHistoryadmin`(IN `p_year` INT, IN `p_month` INT)
BEGIN
    SELECT
		pk_personeel,
        pk_werkuren,
        w.school_fk,
        w.job_fk,
        REPLACE(ROUND(TIMESTAMPDIFF(MINUTE, starttijd ,eindtijd) / 60,2), '.', ':') AS totaal_tijd,
		CONCAT(p.voornaam, ' ', p.familienaam) AS VolledigeNaam,
        CONCAT(w.datum, ' ', w.starttijd) AS starttijd,
        IF(w.eindtijd IS NOT NULL, CONCAT(w.datum, ' ', w.eindtijd), NULL) AS eindtijd
    FROM
        werkuren w -- Gebruik een alias voor de tabelnaam
	JOIN
        personeel p ON w.personeel_fk = p.pk_personeel
    WHERE
    w.datum >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    ORDER BY
        w.datum desc;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `usp_GetMonthlyWorkHistory_DatumApart` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `usp_GetMonthlyWorkHistory_DatumApart`(IN `p_personeel_fk` INT, IN `p_year` INT, IN `p_month` INT)
BEGIN
    SELECT
        DATE(w.starttijd) AS datum,
        w.starttijd,
        w.eindtijd
    FROM
        werkuren w
    WHERE
        w.personeel_fk = p_personeel_fk
        AND YEAR(w.starttijd) = p_year
        AND MONTH(w.starttijd) = p_month;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `usp_GetPersoneelId` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `usp_GetPersoneelId`(IN `personeel_id` INT)
BEGIN
    SELECT *
    FROM personeel
    WHERE pk_personeel = personeel_id;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `usp_GetPersoneelIdByEmail` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `usp_GetPersoneelIdByEmail`(IN `in_email` VARCHAR(255))
BEGIN
    SELECT pk_personeel
    FROM personeel
    WHERE email = in_email;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `usp_GetSchoolPolygonCoordinates` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `usp_GetSchoolPolygonCoordinates`(IN `in_school_fk` INT)
BEGIN
    SELECT polygon_coordinates
    FROM school
    WHERE pk_school = in_school_fk;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `usp_GetStartInfo` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `usp_GetStartInfo`(IN `p_personeel_fk` INT)
BEGIN
    SELECT
        CASE
            WHEN EXISTS (
                SELECT 1
                FROM werkuren
                WHERE personeel_fk = p_personeel_fk
                    AND DATE(starttijd) = CURDATE()
                    AND eindtijd IS NULL
            ) THEN 1
            ELSE 0
        END AS isWerkuurGestart,
        (SELECT CONCAT(voornaam, ' ', familienaam) FROM personeel WHERE pk_personeel = p_personeel_fk) AS personeelsnaam,
        CURDATE() AS HuidigeDatum;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `usp_GetWerkuren` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `usp_GetWerkuren`()
BEGIN
    SELECT
        CONCAT(p.voornaam, ' ', p.familienaam) AS VolledigeNaam,
        w.starttijd AS Starttijd,
        w.eindtijd AS Eindtijd
    FROM
        werkuren w
    JOIN
        personeel p ON w.personeel_fk = p.pk_personeel
    ORDER BY
        p.Voornaam ASC;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `usp_GetWerkuurByIdAndPersoneelId` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `usp_GetWerkuurByIdAndPersoneelId`(IN `p_werkuur_id` INT, IN `p_personeel_id` INT, IN `p_school_id` INT, IN `p_job_id` INT)
BEGIN
    SELECT
        w.pk_werkuren,
        w.personeel_fk,
		CONCAT(p.voornaam, ' ', p.familienaam) AS VolledigeNaam,
        w.starttijd,
        w.eindtijd,
        w.school_fk,
        w.job_fk,
        w.datum
    FROM
        werkuren w
	JOIN
        personeel p ON w.personeel_fk = p.pk_personeel
    WHERE
        w.pk_werkuren = p_werkuur_id AND w.personeel_fk = p_personeel_id;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `usp_StartWerkuur` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `usp_StartWerkuur`(IN `p_personeel_fk` INT, IN `p_school_fk` INT, IN `p_job_fk` INT)
BEGIN
    -- Controleer of er al een werkuur is gestart voor deze persoon op de huidige datum
    IF EXISTS (SELECT 1 FROM werkuren WHERE personeel_fk = p_personeel_fk AND datum = CURDATE()) THEN
        -- Er is al een werkuur gestart vandaag, geef een foutmelding terug
        SELECT 'error' AS status, 'Je hebt vandaag al een werkuur gestart.' AS message;
    ELSE
        -- Er is nog geen werkuur gestart vandaag, start een nieuwe
        INSERT INTO werkuren (personeel_fk, starttijd, datum, school_fk, job_fk)
        VALUES (p_personeel_fk, NOW(), CURDATE(), p_school_fk, p_job_fk);
        SELECT 'success' AS status;
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `usp_StopWerkuur` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `usp_StopWerkuur`(IN `p_personeel_fk` INT)
BEGIN
    UPDATE werkuren
    SET eindtijd = NOW()
    WHERE personeel_fk = p_personeel_fk
      AND eindtijd IS NULL
    ORDER BY starttijd DESC
    LIMIT 1;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `usp_UpdateWerkuur` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `usp_UpdateWerkuur`(IN `p_pk_werkuren` INT, IN `p_personeel_fk` INT, IN `p_starttijd` TIME, IN `p_eindtijd` TIME, IN `p_datum` DATE, IN `p_school_fk` INT, IN `p_job_fk` INT)
BEGIN
    UPDATE werkuren
    SET
        starttijd = CONCAT(p_datum, ' ', p_starttijd),
        eindtijd = IF(p_eindtijd IS NOT NULL, CONCAT(p_datum, ' ', p_eindtijd), NULL),
        datum = p_datum,
        school_fk = p_school_fk,
        job_fk = p_job_fk
    WHERE
        pk_werkuren = p_pk_werkuren AND personeel_fk = p_personeel_fk;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `usp_VerifyPin` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `usp_VerifyPin`(IN `in_pk_personeel` INT)
BEGIN
    SELECT pin
    FROM personeel
    WHERE pk_personeel = in_pk_personeel;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `VoegNieuweWerkurenToe` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `VoegNieuweWerkurenToe`(IN `p_persoon_id` INT, IN `p_datum` DATE, IN `p_start_tijd` TIME, IN `p_eind_tijd` TIME, IN `p_school_id` INT, IN `p_job_id` INT)
BEGIN
    INSERT INTO werkuren (personeel_fk, datum, starttijd, eindtijd, school_fk, job_fk)
    VALUES (p_persoon_id, p_datum, p_start_tijd, p_eind_tijd, p_school_id, p_job_id);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `VoegPersoneelToe` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`miniemen_sep`@`%` PROCEDURE `VoegPersoneelToe`(IN `p_voornaam` VARCHAR(255), IN `p_familienaam` VARCHAR(255), IN `p_pin` VARCHAR(255), IN `p_email` VARCHAR(255), IN `p_gebruikersnaam` VARCHAR(255), IN `p_nfcid` VARCHAR(255))
BEGIN
    INSERT INTO personeel (voornaam, familienaam, pin, email, gebruikersnaam, nfc_id, actief)
    VALUES (p_voornaam, p_familienaam, p_pin, p_email, p_gebruikersnaam, p_nfcid, "0");
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-29 14:05:39
