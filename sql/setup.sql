-- Creación de tablas
--
-- Host: localhost    Database: Inventario2
-- ------------------------------------------------------

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
-- Table structure for table `Articulos`
--

DROP TABLE IF EXISTS `Articulos`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Articulos` (
  `id` smallint(6) NOT NULL auto_increment COMMENT 'ordenable',
  `descripcion` varchar(60) NOT NULL COMMENT 'ordenable,link/Articulo',
  `marca` varchar(20) default NULL COMMENT 'ordenable',
  `modelo` varchar(20) default NULL COMMENT 'ordenable',
  `cantidad` int(11) default NULL COMMENT 'ordenable',
  `imagen` varchar(45) default NULL COMMENT 'imagen',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=785 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;


--
-- Table structure for table `Elementos`
--

DROP TABLE IF EXISTS `Elementos`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Elementos` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT 'ordenable',
  `id_Articulo` smallint(6) NOT NULL COMMENT 'foreign(Articulos;id),ordenable',
  `id_Ubicacion` smallint(5) unsigned NOT NULL COMMENT 'foreign(Ubicaciones;id),ordenable',
  `numserie` varchar(30) default NULL COMMENT 'ordenable',
  `cantidad` int(10) unsigned default NULL COMMENT 'ordenable',
  `fechaCompra` date NOT NULL COMMENT 'ordenable',
  `imagen` varchar(45) default NULL COMMENT 'imagen',
  PRIMARY KEY  (`id`),
  KEY `id` (`id`),
  KEY `id_Articulo` (`id_Articulo`),
  KEY `id_Ubicacion` (`id_Ubicacion`),
  CONSTRAINT `Elementos_ibfk_1` FOREIGN KEY (`id_Articulo`) REFERENCES `Articulos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `Elementos_ibfk_2` FOREIGN KEY (`id_Ubicacion`) REFERENCES `Ubicaciones` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1884 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;


--
-- Table structure for table `Ubicaciones`
--

DROP TABLE IF EXISTS `Ubicaciones`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Ubicaciones` (
  `id` smallint(5) unsigned NOT NULL auto_increment COMMENT 'ordenable',
  `Descripcion` varchar(50) NOT NULL COMMENT 'ordenable,link/Ubicacion',
  `imagen` varchar(45) DEFAULT NULL COMMENT 'imagen',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=184 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;


--
-- Table structure for table `Usuarios`
--

DROP TABLE IF EXISTS `Usuarios`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Usuarios` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT 'ordenable',
  `nombre` varchar(16) NOT NULL default '',
  `clave` varchar(32) NOT NULL default '',
  `idSesion` varchar(20) NOT NULL default '',
  `alta` tinyint(1) NOT NULL default '0',
  `modificacion` tinyint(1) NOT NULL default '0',
  `borrado` tinyint(1) NOT NULL default '0',
  `consulta` tinyint(1) NOT NULL default '1',
  `informe` tinyint(1) NOT NULL default '1',
  `usuarios` tinyint(1) NOT NULL default '0',
  `config` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Usuarios iniciales
--

LOCK TABLES `Usuarios` WRITE;
/*!40000 ALTER TABLE `Usuarios` DISABLE KEYS */;
INSERT INTO `Usuarios` VALUES (1,'admin','pruebas','s3LUSqxg{s',1,1,1,1,1,1,1),(2,'demo','pruebas','NogP_U0Byi',0,0,0,1,1,0,0);
/*!40000 ALTER TABLE `Usuarios` ENABLE KEYS */;
UNLOCK TABLES;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

