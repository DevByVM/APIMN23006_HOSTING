-- MySQL dump 10.13  Distrib 8.0.26, for Win64 (x86_64)
--
-- Host: localhost    Database: bd_p3_clave2
-- ------------------------------------------------------
-- Server version	8.0.26

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
-- Table structure for table `doctores`
--

DROP TABLE IF EXISTS `doctores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `doctores` (
  `IdDoctor` varchar(10) NOT NULL,
  `NombresDoctor` varchar(100) NOT NULL,
  `ApellidosDoctor` varchar(100) NOT NULL,
  `Especialidad` varchar(100) NOT NULL,
  `TurnoAtencion` varchar(50) NOT NULL,
  `PacientesMinDiarios` int NOT NULL,
  `Sueldo` double NOT NULL,
  `IdHospital` varchar(10) NOT NULL,
  PRIMARY KEY (`IdDoctor`),
  KEY `fk_doctores_hospitales` (`IdHospital`),
  CONSTRAINT `fk_doctores_hospitales` FOREIGN KEY (`IdHospital`) REFERENCES `hospitales` (`IdHospital`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctores`
--

LOCK TABLES `doctores` WRITE;
/*!40000 ALTER TABLE `doctores` DISABLE KEYS */;
INSERT INTO `doctores` VALUES ('D001','Carlos Eduardo','Ramírez López','Medicina general','Matutino',15,1200.5,'H001'),('D002','Ana María','Gómez Pérez','Cardiología','Vespertino',12,1500.75,'H002'),('D003','Luis Fernando','Martinez Rivas','Emergencias','Nocturno',10,1350.25,'H003'),('D004','José Alejandro','Martínez Rivas','Pediatría','Matutino',14,1400.25,'H003'),('D005','Mario Alberto','López Hernández','Pediatria','Vespertino',11,1450.5,'H004');
/*!40000 ALTER TABLE `doctores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hospitales`
--

DROP TABLE IF EXISTS `hospitales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hospitales` (
  `IdHospital` varchar(10) NOT NULL,
  `NomHospital` varchar(100) NOT NULL,
  `CapacidadAtencion` varchar(50) NOT NULL,
  `Especialidades` varchar(150) NOT NULL,
  PRIMARY KEY (`IdHospital`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hospitales`
--

LOCK TABLES `hospitales` WRITE;
/*!40000 ALTER TABLE `hospitales` DISABLE KEYS */;
INSERT INTO `hospitales` VALUES ('H001','Hospital General San Salvador','250 pacientes','Medicina general, Pediatría'),('H002','Hospital Nacional Central','400 pacientes','Cardiología, Cirugía'),('H003','Hospital San Rafael','300 pacientes','Emergencias, Medicina interna'),('H004','Hospital Nacional Infantil','180 pacientes','Pediatría, Emergencias');
/*!40000 ALTER TABLE `hospitales` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-16 13:31:09
