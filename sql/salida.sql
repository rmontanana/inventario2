-- MySQL dump 10.13  Distrib 5.6.16, for osx10.7 (x86_64)
--
-- Host: localhost    Database: Inventario2
-- ------------------------------------------------------
-- Server version	5.6.16

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Articulos` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(60) NOT NULL COMMENT 'ordenable,link/Articulo',
  `marca` varchar(20) DEFAULT NULL COMMENT 'ordenable',
  `modelo` varchar(20) DEFAULT NULL COMMENT 'ordenable',
  `cantidad` int(11) DEFAULT NULL COMMENT 'ordenable',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=785 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Articulos`
--

LOCK TABLES `Articulos` WRITE;
/*!40000 ALTER TABLE `Articulos` DISABLE KEYS */;
INSERT INTO `Articulos` VALUES (589,'Armario con puertas y cajones','M. E. C.','Dotación Inicial',26),(590,'Armario vitrina con puertas de cristal','M. E. C.','Dotación Inicial',49),(591,'Retrato del Rey','M. E. C.','Dotación Inicial',6),(592,'Encerado para laboratorio','M. E. C.','Dotación Inicial',2),(593,'Estantería de madera con trasera','M. E. C.','Dotación Inicial',61),(594,'Percha de 8 ganchos','M. E. C.','Dotación Inicial',68),(595,'Pupitre M.19','M. E. C.','Dotación Inicial',271),(596,'Tablero de corcho 2,00x1,00','M. E. C.','Dotación Inicial',29),(597,'Estantería metálica','M. E. C.','Dotación Inicial',18),(598,'Encerado P-1','M. E. C.','Dotación Inicial',21),(599,'Mesa de profesor','M. E. C.','Dotación Inicial',19),(600,'Sillón de profesor','M. E. C.','Dotación Inicial',19),(601,'Silla pala plegable (diestros)','M. E. C.','Dotación Inicial',26),(602,'Silla plegable (zurdos)','M. E. C.','Dotación Inicial',4),(603,'Banqueta altura regulable sin respaldo','M. E. C.','Dotación Inicial',95),(604,'Mesa de aula de plástica','M. E. C.','Dotación Inicial',6),(605,'Armario archivador A-2','M. E. C.','Dotación Inicial',4),(606,'Armario metálico','M. E. C.','Dotación Inicial',6),(607,'Armario con puertas ciegas','M. E. C.','Dotación Inicial',9),(608,'Banco de trabajo','M. E. C.','Dotación Inicial',6),(609,'Botiquín','M. E. C.','Dotación Inicial',3),(610,'Mesa de dibujo con banqueta','M. E. C.','Dotación Inicial',2),(611,'Mesa Trabajos teóricos con 5 sillas','M. E. C.','Dotación Inicial',6),(612,'Mesa de reuniones con 6 sillas','M. E. C.','Dotación Inicial',11),(613,'Encuadernadora de canutillo','M. E. C.','Dotación Inicial',1),(614,'Guillotina manual','M. E. C.','Dotación Inicial',1),(615,'Caseta meteorológica','M. E. C.','Dotación Inicial',1),(616,'Carro con ruedas para laboratorio','M. E. C.','Dotación Inicial',1),(617,'Centrifugadora eléctrica','M. E. C.','Dotación Inicial',2),(618,'Colección de rocas y minerales','M. E. C.','Dotación Inicial',6),(619,'Equipo experimentación II M. Preparación','M. E. C.','Dotación Inicial',1),(620,'Equipo multimedia de microscopía','M. E. C.','Dotación Inicial',1),(621,'Equipo de campo','M. E. C.','Dotación Inicial',2),(622,'Equipo de experiencias de mecánica','M. E. C.','Dotación Inicial',7),(623,'Equipo de óptica para alumnos','M. E. C.','Dotación Inicial',6),(624,'Hombre clástico y modelos anatómicos','M. E. C.','Dotación Inicial',1),(625,'Mechero tipo Bunsen','M. E. C.','Dotación Inicial',6),(626,'Mesa de trabajo de alumnos','M. E. C.','Dotación Inicial',6),(627,'Microscopio biológico para profesor','M. E. C.','Dotación Inicial',1),(628,'Mesa de laboratorio tipo B','M. E. C.','Dotación Inicial',8),(629,'Mesa de laboratorio tipo C','M. E. C.','Dotación Inicial',4),(630,'Nevera','M. E. C.','Dotación Inicial',1),(631,'Reactivos para química','M. E. C.','Dotación Inicial',1),(632,'Tabla periódica mural','M. E. C.','Dotación Inicial',1),(633,'Cajón para transparencias','M. E. C.','Dotación Inicial',1),(634,'Clasificador cajones aula plástica','M. E. C.','Dotación Inicial',1),(635,'Colección diapositivas educación plástica y visual','M. E. C.','Dotación Inicial',1),(636,'Equipo de grabado y estampación','M. E. C.','Dotación Inicial',10),(637,'Encerado portatil con trama','M. E. C.','Dotación Inicial',2),(638,'Equipo plantillas y escalas','M. E. C.','Dotación Inicial',1),(639,'Equipo paralex de metacrilato','M. E. C.','Dotación Inicial',15),(640,'Juego de poliedros','M. E. C.','Dotación Inicial',1),(641,'Juesgo de piezas volumétricas seccionadas','M. E. C.','Dotación Inicial',1),(642,'Proyector de diapositivas','M. E. C.','Dotación Inicial',3),(643,'Retroproyector','M. E. C.','Dotación Inicial',3),(644,'Televisor','M. E. C.','Dotación Inicial',4),(645,'Tripode de modelado de mesa','M. E. C.','Dotación Inicial',10),(646,'Colección diapositivas arte España','M. E. C.','Dotación Inicial',1),(647,'Colección diapositivas arte mundial','M. E. C.','Dotación Inicial',1),(648,'Colección diapositivas geografía España','M. E. C.','Dotación Inicial',1),(649,'Mapas históricos murales','M. E. C.','Dotación Inicial',1),(650,'Sistema soporte almacenamiento mapas','M. E. C.','Dotación Inicial',1),(651,'Equipo de fotografía','M. E. C.','Dotación Inicial',1),(652,'Colección diapositivas geografía Mundial','M. E. C.','Dotación Inicial',1),(653,'Equipo didáctico de material cartográfico','M. E. C.','Dotación Inicial',1),(654,'Cassette grabadora','M. E. C.','Dotación Inicial',4),(655,'Calculadora Científica','M. E. C.','Dotación Inicial',33),(656,'Equipo de medidas de campo','M. E. C.','Dotación Inicial',1),(657,'Equipo probabilidad, proc. estocásticos','M. E. C.','Dotación Inicial',1),(658,'Equipo para la construcción de poliedros','M. E. C.','Dotación Inicial',1),(659,'Equipo de geometría del espacio','M. E. C.','Dotación Inicial',1),(660,'Equipo de geometría del plano','M. E. C.','Dotación Inicial',1),(661,'Juego para encerado','M. E. C.','Dotación Inicial',2),(662,'Atril plegable','M. E. C.','Dotación Inicial',10),(663,'Conjunto de instrumentos de percusión de laminas','M. E. C.','Dotación Inicial',2),(664,'Conjunto de instrumentos de pequeña percusión (S.O.)','M. E. C.','Dotación Inicial',1),(665,'Equipo audiovisual aula de música','M. E. C.','Dotación Inicial',1),(666,'Encerado pautado portatil','M. E. C.','Dotación Inicial',1),(667,'Guitarra española','M. E. C.','Dotación Inicial',2),(668,'Piano electrónico','M. E. C.','Dotación Inicial',1),(669,'Clasificador de cajones aula de tecnología','M. E. C.','Dotación Inicial',2),(670,'Conjunto de elementos de contrucción y montaje','M. E. C.','Dotación Inicial',2),(671,'Equipo de dibujo técnico aula de tecnología','M. E. C.','Dotación Inicial',1),(672,'Electroesmeriladora portatil','M. E. C.','Dotación Inicial',1),(673,'Equipo de herramientas con armario (S.O.)','M. E. C.','Dotación Inicial',1),(674,'Equipo de herramientas para alumno','M. E. C.','Dotación Inicial',6),(675,'Equipo de herramientas para madera','M. E. C.','Dotación Inicial',1),(676,'Equipo de operadores tecnológicos-mecánicos','M. E. C.','Dotación Inicial',1),(677,'Equipo de operadores tecnológicos-neumáticos','M. E. C.','Dotación Inicial',1),(678,'Equipo de soldadura eléctrica portatil (S.O.)','M. E. C.','Dotación Inicial',1),(679,'Fuente de alimentación S.O.','M. E. C.','Dotación Inicial',6),(680,'Polímetro analógico didáctico','M. E. C.','Dotación Inicial',6),(681,'Polímetro digital','M. E. C.','Dotación Inicial',1),(682,'Sierra de calar','M. E. C.','Dotación Inicial',3),(683,'Taladradora de sobremesa','M. E. C.','Dotación Inicial',1),(684,'Tornillo de banco de 100 mm.','M. E. C.','Dotación Inicial',6),(685,'Torno de sobremesa','M. E. C.','Dotación Inicial',1),(686,'Mesa de director con sillón','M. E. C.','Dotación Inicial',3),(687,'Silla tapizada','M. E. C.','Dotación Inicial',8),(688,'Fichero F-2','M. E. C.','Dotación Inicial',5),(689,'Banco de pasillo','M. E. C.','Dotación Inicial',6),(690,'Pupitre M.03','M. E. C.','Dotación Inicial',16),(691,'Juego postes y red de voleibol','M. E. C.','Dotación Inicial',1),(692,'Juego de porterías de balonmano','M. E. C.','Dotación Inicial',1),(693,'Cámara de video','M. E. C.','Dotación Inicial',1),(694,'Cassette estero portatil con reproductor de CD','M. E. C.','Dotación Inicial',3),(695,'Mini cadena musical','M. E. C.','Dotación Inicial',1),(696,'Mesa soporte de proyector de diapositivas','M. E. C.','Dotación Inicial',2),(697,'Proyector de cuerpos opacos','M. E. C.','Dotación Inicial',1),(698,'Silla giratoria auxiliar','M. E. C.','Dotación Inicial',1),(699,'Mesa lectura 1,40x75x70 con 6 sillas','M. E. C.','Dotación Inicial',12),(700,'Agitador magnético','M. E. C.','Dotación Inicial',6),(701,'Balanza granatorio electrónica','M. E. C.','Dotación Inicial',2),(702,'Baño maría','M. E. C.','Dotación Inicial',1),(703,'Colección de fósiles','M. E. C.','Dotación Inicial',1),(704,'Equipo de análisis de agua','M. E. C.','Dotación Inicial',1),(705,'Esqueleto humano','M. E. C.','Dotación Inicial',1),(706,'Mapa de fondos oceánicos','M. E. C.','Dotación Inicial',1),(707,'Modelos de organización animal y vegetal','M. E. C.','Dotación Inicial',1),(708,'Modelos geológicos','M. E. C.','Dotación Inicial',1),(709,'Video estacionario','M. E. C.','Dotación Inicial',3),(710,'Planisferio de Peters','M. E. C.','Dotación Inicial',1),(711,'Cizalla-plegadora-punzonadora','M. E. C.','Dotación Inicial',1),(712,'Equipo de operadores tecnológicos electrico-electrónicos','M. E. C.','Dotación Inicial',2),(713,'Pistola decapante','M. E. C.','Dotación Inicial',2),(714,'Mesa auxiliar administrativo','M. E. C.','Dotación Inicial',1),(715,'Equipo básico de alumnos para experimentos de electricidad','M. E. C.','Dotación Inicial',6),(716,'Equipo de ilumniación aula plástica y visual','M. E. C.','Dotación Inicial',1),(717,'Pantalla','M. E. C.','Dotación Inicial',4),(718,'Tórculo pequeño con mesa','M. E. C.','Dotación Inicial',1),(719,'Banco sueco','M. E. C.','Dotación Inicial',8),(720,'Plinto','M. E. C.','Dotación Inicial',1),(721,'Juego Butacas modulares','M. E. C.','Dotación Inicial',2),(722,'Trampolín de tres alturas','M. E. C.','Dotación Inicial',1),(723,'Saltómetro','M. E. C.','Dotación Inicial',1),(724,'Material de gimnasia vario','M. E. C.','Dotación Inicial',1),(725,'Juego esterillas suelo','M. E. C.','Dotación Inicial',1),(726,'Colchonetade 2x1x0,05','M. E. C.','Dotación Inicial',8),(727,'Taladro portatil con soporte y accesorio','M. E. C.','Dotación Inicial',3),(728,'Material de vidrio para laboratorio','M. E. C.','Dotación Inicial',1),(729,'Lupa binocular para profesor','M. E. C.','Dotación Inicial',1),(730,'Lupa binocular para alumnos','M. E. C.','Dotación Inicial',15),(731,'Equipo termología alumnos','M. E. C.','Dotación Inicial',6),(732,'Equipo de Análisis de suelo','M. E. C.','Dotación Inicial',1),(733,'Equipo de Experimentación I.M. Disección','M. E. C.','Dotación Inicial',15),(734,'Juego de banderas','M. E. C.','Dotación Inicial',1),(735,'PC Notes','STI','PCNT_USB',1),(736,'Switch','Hewlett Packard','J4813A',1),(737,'Altavoces Sistema sobwoofer-satélites','','2106',1),(738,'Auriculares con micrófono','Creative','HS300',31),(739,'Impresora multifunción HP Officejet','Hewlett Packard','7205',1),(740,'Teclado','Inves','K366',16),(741,'Teclado y ratón','Toshiba','HCA32602690',1),(742,'Ratón','Inves','MS23',16),(743,'Equipo para el estudio del relieve','M. E. C.','Dotación Inicial',1),(744,'Material General de Física','M. E. C.','Dotación Inicial',1),(745,'Material de Laboratorio','M. E. C.','Dotación Inicial',1),(746,'Microscopio para Alumnos','M. E. C.','Dotación Inicial',15),(747,'Caballete de Pintura','M. E. C.','Dotación Inicial',10),(748,'Juego de Intrumentos para Rotular','M. E. C.','Dotación Inicial',1),(749,'Material General de Dibujo','M. E. C.','Dotación Inicial',1),(750,'Sierra Térmica de Porexpan','M. E. C.','Dotación Inicial',1),(751,'Equipo para la elaboración de circuitos impresos','M. E. C.','Dotación Inicial',1),(752,'Plegadora de plásticos con accesorios','M. E. C.','Dotación Inicial',1),(753,'Servidor Externo','Toshiba','Magnia Z415',1),(754,'Cámara flexo','Avermedia','POB106',1),(755,'Sintonizador externo','Avermedia','Aver TV Box',1),(756,'Monitor CRT 17\"','Inves','C708 Negro CCMM 3P',16),(757,'Ordenador de sobremesa aula Althia','Inves','SIERRA-DMT1400 2.66P',15),(758,'Ordenador de profesor aula Althia','Inves','Sierra DMT 1800 2.66',1),(759,'Balanza Granatorio','M. E. C.','Dotación Inicial',1),(760,'Reproductor DVD','MX-Onda','MX-DVD855NZ',1),(761,'Reproductor DVD con Divx','LG','DVX9743',1),(762,'Ordenador','Airis','dotación inicial',7),(763,'Monitor 17','AOC','FT720',7),(764,'Ordenador Santillana','Regalo Ed.Santillana','',3),(765,'Ordenador Dptos.','Monitor Philips','AMD2800/512/80/17\"',5),(766,'Estufa climatizadora','Ufesa','Climacontrol',3),(767,'Estufa 2000W','Ufesa','TV2610',6),(768,'Switch 8 puertos','Conceptronic','C100S8',2),(769,'Cañón proyector','NEC','VT480G',2),(770,'Protection Center 500 USB DIN','MGE','ProtectionCenter 500',2),(771,'Pantalla COMM-TEC (conectividad)','COMM-TEC','',2),(772,'Monitor CRT 17\" (conectividad)','APD','NH-778',4),(773,'Ordenador fijo conectividad','APD','ALDA CE 915GV',4),(774,'Ordenador portatil conectividad','Toshiba','Tecra S3-290',5),(775,'Bolsa de transporte de portatil','Toshiba','',5),(776,'Cámara de fotos digital','Nikon','Coolpix P2',1),(777,'Conmutador teclado-pantalla-usb','D-Link','DKVM-2KU',1),(778,'Punto de Acceso US-Robotics MAXg','US-Robotics','USR5451',1),(779,'Altavoces estereo','Genius','SP-G06',1),(780,'HP iPAQ rx4240 SP','HP','FA782AA#ABE',20),(781,'Ordenador Proyecto Hermes','Hewlett Packard','Hermes',1),(782,'Monitor 17','Hewlett Packard','Hermes',1),(783,'Ordenador Core2Quad','Packard Bell','Imedia X1610',1),(784,'Monitor 20','LG','Flatron Wide',1);
/*!40000 ALTER TABLE `Articulos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Elementos`
--

DROP TABLE IF EXISTS `Elementos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Elementos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_Articulo` smallint(6) NOT NULL COMMENT 'foreign(Articulos;id),ordenable',
  `id_Ubicacion` smallint(5) unsigned NOT NULL COMMENT 'foreign(Ubicaciones;id),ordenable',
  `numserie` varchar(30) DEFAULT NULL COMMENT 'ordenable',
  `cantidad` int(10) unsigned DEFAULT NULL COMMENT 'ordenable',
  `fechaCompra` date NOT NULL COMMENT 'ordenable',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `id_Articulo` (`id_Articulo`),
  KEY `id_Ubicacion` (`id_Ubicacion`),
  CONSTRAINT `Elementos_ibfk_1` FOREIGN KEY (`id_Articulo`) REFERENCES `Articulos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `Elementos_ibfk_2` FOREIGN KEY (`id_Ubicacion`) REFERENCES `Ubicaciones` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1884 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Elementos`
--

LOCK TABLES `Elementos` WRITE;
/*!40000 ALTER TABLE `Elementos` DISABLE KEYS */;
INSERT INTO `Elementos` VALUES (1414,589,140,'',1,'2004-12-07'),(1415,590,140,'',2,'2004-12-07'),(1416,591,140,'',1,'2004-12-07'),(1417,589,141,'',1,'2004-12-07'),(1418,590,141,'',4,'2004-12-07'),(1419,592,141,'',1,'2004-12-07'),(1420,593,141,'',4,'2004-12-07'),(1421,594,141,'',4,'2004-12-07'),(1422,595,141,'',1,'2004-12-07'),(1423,596,141,'',1,'2004-12-07'),(1424,597,142,'',5,'2004-12-07'),(1425,589,143,'',1,'2004-12-08'),(1426,590,143,'',1,'2004-12-08'),(1427,598,143,'',1,'2004-12-08'),(1428,597,143,'',1,'2004-12-08'),(1429,599,143,'',1,'2004-12-08'),(1430,594,143,'',4,'2004-12-08'),(1431,600,143,'',1,'2004-12-08'),(1432,601,143,'',26,'2004-12-08'),(1433,602,143,'',4,'2004-12-08'),(1434,596,143,'',1,'2004-12-08'),(1435,589,144,'',1,'2004-12-08'),(1436,590,144,'',2,'2004-12-08'),(1437,603,144,'',30,'2004-12-08'),(1438,598,144,'',1,'2004-12-08'),(1439,597,144,'',2,'2004-12-08'),(1440,593,144,'',2,'2004-12-08'),(1441,604,144,'',6,'2004-12-08'),(1442,599,144,'',1,'2004-12-08'),(1443,594,144,'',4,'2004-12-08'),(1444,600,144,'',1,'2004-12-08'),(1445,596,144,'',1,'2004-12-08'),(1446,605,145,'',1,'2004-12-08'),(1447,606,145,'',3,'2004-12-08'),(1448,607,145,'',4,'2004-12-08'),(1449,590,145,'',4,'2004-12-08'),(1450,608,145,'',6,'2004-12-08'),(1451,609,145,'',1,'2004-12-08'),(1452,603,145,'',30,'2004-12-08'),(1453,598,145,'',1,'2004-12-08'),(1454,597,145,'',6,'2004-12-08'),(1455,610,145,'',2,'2004-12-08'),(1456,599,145,'',1,'2004-12-08'),(1457,611,145,'',6,'2004-12-08'),(1458,594,145,'',4,'2004-12-08'),(1459,600,145,'',1,'2004-12-08'),(1460,596,145,'',1,'2004-12-08'),(1461,589,146,'',1,'2004-12-08'),(1462,598,146,'',1,'2004-12-08'),(1463,593,146,'',1,'2004-12-08'),(1464,599,146,'',1,'2004-12-08'),(1465,594,146,'',2,'2004-12-08'),(1466,595,146,'',15,'2004-12-08'),(1467,600,146,'',1,'2004-12-08'),(1468,596,146,'',1,'2004-12-08'),(1469,598,142,'',1,'2004-12-08'),(1470,593,142,'',1,'2004-12-08'),(1471,599,142,'',1,'2004-12-08'),(1472,594,140,'',2,'2004-12-08'),(1473,595,142,'',15,'2004-12-08'),(1474,600,142,'',1,'2004-12-08'),(1475,596,142,'',1,'2004-12-08'),(1476,589,147,'',1,'2004-12-08'),(1477,598,147,'',1,'2004-12-08'),(1478,593,147,'',1,'2004-12-08'),(1479,599,147,'',1,'2004-12-08'),(1480,594,147,'',4,'2004-12-08'),(1481,595,147,'',30,'2004-12-08'),(1482,600,147,'',1,'2004-12-08'),(1483,596,147,'',1,'2004-12-08'),(1484,589,148,'',1,'2004-12-08'),(1485,598,148,'',1,'2004-12-08'),(1486,593,148,'',1,'2004-12-08'),(1487,599,148,'',1,'2004-12-08'),(1488,594,148,'',4,'2004-12-08'),(1489,595,148,'',30,'2004-12-08'),(1490,600,148,'',1,'2004-12-08'),(1491,596,148,'',1,'2004-12-08'),(1492,589,149,'',1,'2004-12-08'),(1493,598,149,'',1,'2004-12-08'),(1494,593,149,'',1,'2004-12-08'),(1495,599,149,'',1,'2004-12-08'),(1496,594,149,'',4,'2004-12-08'),(1497,595,149,'',30,'2004-12-08'),(1498,600,149,'',1,'2004-12-08'),(1499,596,149,'',1,'2004-12-08'),(1500,589,150,'',1,'2004-12-08'),(1501,598,150,'',1,'2004-12-08'),(1502,593,150,'',1,'2004-12-08'),(1503,594,150,'',4,'2004-12-08'),(1504,595,150,'',30,'2004-12-08'),(1505,600,150,'',1,'2004-12-08'),(1506,596,150,'',1,'2004-12-08'),(1507,599,150,'',1,'2004-12-08'),(1508,589,151,'',1,'2004-12-08'),(1509,598,151,'',1,'2004-12-08'),(1510,593,151,'',1,'2004-12-08'),(1511,599,151,'',1,'2004-12-08'),(1512,594,151,'',4,'2004-12-08'),(1513,595,151,'',30,'2004-12-08'),(1514,600,151,'',1,'2004-12-08'),(1515,596,151,'',1,'2004-12-08'),(1516,589,152,'',1,'2004-12-08'),(1517,598,152,'',1,'2004-12-08'),(1518,593,152,'',1,'2004-12-08'),(1519,599,152,'',1,'2004-12-08'),(1520,594,152,'',4,'2004-12-08'),(1521,595,152,'',30,'2004-12-08'),(1522,600,152,'',1,'2004-12-08'),(1523,596,152,'',1,'2004-12-08'),(1524,589,153,'',1,'2004-12-08'),(1525,598,153,'',1,'2004-12-08'),(1526,593,153,'',1,'2004-12-08'),(1527,594,153,'',4,'2004-12-08'),(1528,595,153,'',30,'2004-12-08'),(1529,600,153,'',1,'2004-12-08'),(1530,596,153,'',1,'2004-12-08'),(1531,599,153,'',1,'2004-12-08'),(1532,589,154,'',1,'2004-12-08'),(1533,598,154,'',1,'2004-12-08'),(1534,593,154,'',1,'2004-12-08'),(1535,599,154,'',1,'2004-12-08'),(1536,594,154,'',4,'2004-12-08'),(1537,595,154,'',30,'2004-12-08'),(1538,600,154,'',1,'2004-12-08'),(1539,596,154,'',1,'2004-12-08'),(1540,590,155,'',19,'2004-12-08'),(1541,593,155,'',20,'2004-12-08'),(1542,599,155,'',1,'2004-12-08'),(1543,594,155,'',4,'2004-12-08'),(1544,591,155,'',1,'2004-12-08'),(1545,600,155,'',1,'2004-12-08'),(1546,590,156,'',2,'2004-12-08'),(1547,598,156,'',1,'2004-12-08'),(1548,593,156,'',2,'2004-12-08'),(1549,612,156,'',1,'2004-12-08'),(1550,594,156,'',1,'2004-12-08'),(1551,596,156,'',1,'2004-12-08'),(1552,590,157,'',2,'2004-12-08'),(1553,598,157,'',1,'2004-12-08'),(1554,593,157,'',2,'2004-12-08'),(1555,612,157,'',1,'2004-12-08'),(1556,594,157,'',1,'2004-12-08'),(1557,596,157,'',1,'2004-12-08'),(1558,590,158,'',2,'2004-12-08'),(1559,598,158,'',1,'2004-12-08'),(1560,593,158,'',2,'2004-12-08'),(1561,612,158,'',1,'2004-12-08'),(1562,594,158,'',1,'2004-12-08'),(1563,596,158,'',1,'2004-12-08'),(1564,590,159,'',2,'2004-12-08'),(1565,598,159,'',1,'2004-12-08'),(1566,593,159,'',2,'2004-12-08'),(1567,612,159,'',1,'2004-12-08'),(1568,594,159,'',1,'2004-12-08'),(1569,596,159,'',1,'2004-12-08'),(1570,590,160,'',2,'2004-12-08'),(1571,598,160,'',1,'2004-12-08'),(1572,593,160,'',2,'2004-12-08'),(1573,612,160,'',1,'2004-12-08'),(1574,594,160,'',1,'2004-12-08'),(1575,596,160,'',1,'2004-12-08'),(1576,606,161,'',1,'2004-12-08'),(1577,609,161,'',1,'2004-12-08'),(1578,613,161,'',1,'2004-12-08'),(1579,597,161,'',2,'2004-12-08'),(1580,614,161,'',1,'2004-12-08'),(1581,599,161,'',1,'2004-12-08'),(1582,600,161,'',1,'2004-12-08'),(1583,589,162,'',1,'2004-12-08'),(1584,607,162,'',5,'2004-12-08'),(1585,590,162,'',3,'2004-12-08'),(1586,609,162,'',1,'2004-12-08'),(1587,603,162,'',35,'2004-12-08'),(1588,615,162,'',1,'2004-12-08'),(1589,616,162,'',1,'2004-12-08'),(1590,617,162,'',2,'2004-12-08'),(1591,618,162,'',6,'2004-12-08'),(1592,619,162,'',1,'2004-12-08'),(1593,620,162,'',1,'2004-12-08'),(1594,592,162,'',1,'2004-12-08'),(1595,598,162,'',1,'2004-12-08'),(1596,621,162,'',2,'2004-12-08'),(1597,622,162,'',6,'2004-12-08'),(1598,623,162,'',6,'2004-12-08'),(1599,597,162,'',2,'2004-12-08'),(1600,593,162,'',4,'2004-12-08'),(1601,624,162,'',1,'2004-12-08'),(1602,625,162,'',6,'2004-12-08'),(1603,599,162,'',1,'2004-12-08'),(1604,626,162,'',6,'2004-12-08'),(1605,627,162,'',1,'2004-12-08'),(1606,628,162,'',8,'2004-12-08'),(1607,629,162,'',4,'2004-12-08'),(1608,630,162,'',1,'2004-12-08'),(1609,594,162,'',4,'2004-12-08'),(1610,631,162,'',1,'2004-12-08'),(1611,600,162,'',1,'2004-12-08'),(1612,632,162,'',1,'2004-12-08'),(1613,606,144,'',1,'2004-12-08'),(1614,633,144,'',1,'2004-12-08'),(1615,634,144,'',1,'2004-12-08'),(1616,635,144,'',1,'2004-12-08'),(1617,636,144,'',10,'2004-12-08'),(1618,637,144,'',2,'2004-12-08'),(1619,638,144,'',1,'2004-12-08'),(1620,639,144,'',15,'2004-12-08'),(1621,640,144,'',1,'2004-12-08'),(1622,641,144,'',1,'2004-12-08'),(1623,642,144,'',1,'2004-12-08'),(1624,643,144,'',1,'2004-12-08'),(1625,644,144,'',1,'2004-12-08'),(1626,645,144,'',10,'2004-12-08'),(1627,646,159,'',1,'2004-12-08'),(1628,647,159,'',1,'2004-12-08'),(1629,648,159,'',1,'2004-12-08'),(1630,649,159,'',1,'2004-12-08'),(1631,650,159,'',1,'2004-12-08'),(1632,651,144,'',1,'2004-12-08'),(1633,652,159,'',1,'2004-12-08'),(1634,653,159,'',1,'2004-12-08'),(1635,606,157,'',1,'2004-12-08'),(1636,654,157,'',4,'2004-12-08'),(1637,644,157,'',1,'2004-12-08'),(1638,655,156,'',32,'2004-12-08'),(1639,656,156,'',1,'2004-12-08'),(1640,657,156,'',1,'2004-12-08'),(1641,658,156,'',1,'2004-12-08'),(1642,659,156,'',1,'2004-12-08'),(1643,660,156,'',1,'2004-12-08'),(1644,661,156,'',1,'2004-12-08'),(1645,662,143,'',10,'2004-12-08'),(1646,663,143,'',2,'2004-12-08'),(1647,664,143,'',1,'2004-12-08'),(1648,665,143,'',1,'2004-12-08'),(1649,666,143,'',1,'2004-12-08'),(1650,667,143,'',2,'2004-12-08'),(1651,668,143,'',1,'2004-12-08'),(1652,669,145,'',2,'2004-12-08'),(1653,670,145,'',2,'2004-12-08'),(1654,671,145,'',1,'2004-12-08'),(1655,672,145,'',1,'2004-12-08'),(1656,622,145,'',1,'2004-12-08'),(1657,673,145,'',1,'2004-12-08'),(1658,674,145,'',6,'2004-12-08'),(1659,675,145,'',1,'2004-12-08'),(1660,676,145,'',1,'2004-12-08'),(1661,677,145,'',1,'2004-12-08'),(1662,678,145,'',1,'2004-12-08'),(1663,679,145,'',6,'2004-12-08'),(1664,661,145,'',1,'2004-12-08'),(1665,680,145,'',6,'2004-12-08'),(1666,681,145,'',1,'2004-12-08'),(1667,682,145,'',3,'2004-12-08'),(1668,683,145,'',1,'2004-12-08'),(1669,684,145,'',6,'2004-12-08'),(1670,685,145,'',1,'2004-12-08'),(1671,590,163,'',1,'2004-12-08'),(1672,598,163,'',1,'2004-12-08'),(1673,599,163,'',1,'2004-12-08'),(1674,594,163,'',1,'2004-12-08'),(1675,600,163,'',1,'2004-12-08'),(1676,596,140,'',1,'2004-12-08'),(1677,590,164,'',1,'2004-12-08'),(1678,598,164,'',1,'2004-12-08'),(1679,599,164,'',1,'2004-12-08'),(1680,594,164,'',1,'2004-12-08'),(1681,600,164,'',1,'2004-12-08'),(1682,596,164,'',1,'2004-12-08'),(1683,686,165,'',1,'2004-12-08'),(1684,612,165,'',1,'2004-12-08'),(1685,591,165,'',1,'2004-12-08'),(1686,687,165,'',2,'2004-12-08'),(1687,688,166,'',1,'2004-12-08'),(1688,686,166,'',1,'2004-12-08'),(1689,612,166,'',1,'2004-12-08'),(1690,591,166,'',1,'2004-12-08'),(1691,687,166,'',2,'2004-12-08'),(1692,596,166,'',1,'2004-12-08'),(1693,589,167,'',1,'2004-12-08'),(1694,590,167,'',2,'2004-12-08'),(1695,593,167,'',1,'2004-12-08'),(1696,688,167,'',1,'2004-12-08'),(1697,599,167,'',1,'2004-12-08'),(1698,612,167,'',1,'2004-12-08'),(1699,594,167,'',1,'2004-12-08'),(1700,600,167,'',1,'2004-12-08'),(1701,687,167,'',2,'2004-12-08'),(1702,596,167,'',1,'2004-12-08'),(1703,686,140,'',1,'2004-12-08'),(1704,687,140,'',2,'2004-12-08'),(1705,689,168,'',6,'2004-12-08'),(1706,690,168,'',16,'2004-12-08'),(1707,596,168,'',5,'2004-12-08'),(1708,691,169,'',1,'2004-12-08'),(1709,692,169,'',1,'2004-12-08'),(1710,693,165,'',1,'2004-12-08'),(1711,694,146,'',3,'2004-12-08'),(1712,695,146,'',1,'2004-12-08'),(1713,696,146,'',2,'2004-12-08'),(1714,697,146,'',1,'2004-12-08'),(1715,642,146,'',2,'2004-12-08'),(1716,643,146,'',2,'2004-12-08'),(1717,644,146,'',1,'2004-12-08'),(1718,589,170,'',11,'2004-12-08'),(1719,593,170,'',10,'2004-12-08'),(1720,612,170,'',3,'2004-12-08'),(1721,591,170,'',1,'2004-12-08'),(1722,605,171,'',3,'2004-12-08'),(1723,655,140,'',1,'2004-12-08'),(1724,688,171,'',3,'2004-12-08'),(1725,591,171,'',1,'2004-12-08'),(1726,698,171,'',1,'2004-12-08'),(1727,699,155,'',10,'2004-12-08'),(1728,700,162,'',6,'2004-12-08'),(1729,701,162,'',2,'2004-12-08'),(1730,702,162,'',1,'2004-12-08'),(1731,703,162,'',1,'2004-12-08'),(1732,704,162,'',1,'2004-12-08'),(1733,705,162,'',1,'2004-12-08'),(1734,706,162,'',1,'2004-12-08'),(1735,707,162,'',1,'2004-12-08'),(1736,708,162,'',1,'2004-12-08'),(1737,709,160,'',1,'2004-12-08'),(1738,710,156,'',1,'2004-12-08'),(1739,709,157,'',1,'2004-12-08'),(1740,711,145,'',1,'2004-12-08'),(1741,712,145,'',2,'2004-12-08'),(1742,713,145,'',2,'2004-12-08'),(1743,699,163,'',1,'2004-12-08'),(1744,699,164,'',1,'2004-12-08'),(1745,709,146,'',1,'2004-12-08'),(1746,714,171,'',1,'2004-12-08'),(1747,715,162,'',6,'2004-12-08'),(1748,716,144,'',1,'2004-12-08'),(1749,717,144,'',1,'2004-12-08'),(1750,718,144,'',1,'2004-12-08'),(1751,719,169,'',8,'2004-12-08'),(1752,720,169,'',1,'2004-12-08'),(1753,717,146,'',3,'2004-12-08'),(1754,721,170,'',1,'2004-12-08'),(1755,722,169,'',1,'2004-12-08'),(1756,723,169,'',1,'2004-12-08'),(1757,724,169,'',1,'2004-12-08'),(1758,725,169,'',1,'2004-12-08'),(1759,726,169,'',8,'2004-12-08'),(1760,721,165,'',1,'2004-12-08'),(1761,727,145,'',3,'2004-12-08'),(1762,728,162,'',1,'2004-12-08'),(1763,729,162,'',1,'2004-12-08'),(1764,730,162,'',15,'2004-12-08'),(1765,731,162,'',6,'2004-12-08'),(1766,732,162,'',1,'2004-12-08'),(1767,733,162,'',15,'2004-12-08'),(1768,734,168,'',1,'2004-12-09'),(1769,735,141,'USA311D0950485',1,'2004-12-20'),(1770,736,141,'SG411NV1DD',1,'2004-12-20'),(1771,737,141,'',1,'2004-12-20'),(1772,738,141,'',31,'2004-12-20'),(1773,739,141,'HU3A1FM0NW',1,'2004-12-20'),(1774,740,141,'',16,'2004-12-20'),(1775,741,141,'',1,'2004-12-20'),(1776,742,141,'',16,'2004-12-20'),(1777,743,156,'',1,'2005-02-02'),(1778,744,156,'',1,'2005-02-02'),(1779,745,162,'',1,'2005-02-02'),(1780,746,162,'',15,'2005-02-02'),(1781,747,144,'',10,'2005-02-02'),(1782,748,160,'',1,'2005-02-02'),(1783,749,160,'',1,'2005-02-02'),(1784,750,160,'',1,'2005-02-02'),(1785,751,160,'',1,'2005-02-02'),(1786,752,160,'',1,'2005-02-02'),(1787,753,141,'34029255G',1,'2005-09-29'),(1788,754,141,'402072800098',1,'2005-09-29'),(1789,755,141,'60984020',1,'2005-09-29'),(1790,756,141,'',16,'2005-09-29'),(1791,757,141,'8313252',1,'2005-09-29'),(1792,758,141,'8312296',1,'2005-09-29'),(1793,757,141,'8313295',1,'2005-09-29'),(1794,757,141,'8313255',1,'2005-09-29'),(1795,757,141,'8313262',1,'2005-09-29'),(1796,757,141,'8313289',1,'2005-09-29'),(1797,757,141,'8313287',1,'2005-09-29'),(1798,757,141,'8313259',1,'2005-09-29'),(1799,757,141,'8313283',1,'2005-09-29'),(1800,757,141,'8313280',1,'2005-09-29'),(1801,757,141,'8313278',1,'2005-09-29'),(1802,757,141,'8313281',1,'2005-09-29'),(1803,757,141,'8313274',1,'2005-09-29'),(1804,757,141,'8313242',1,'2005-09-29'),(1805,757,141,'8313290',1,'2005-09-29'),(1806,757,141,'8313258',1,'2005-09-29'),(1807,759,162,'',1,'2004-12-07'),(1808,596,163,'',1,'2005-10-17'),(1809,760,141,'L306777EM',1,'2005-10-19'),(1810,761,146,'5045HSE00126',1,'2005-10-19'),(1811,762,170,'110678317',1,'2005-10-21'),(1812,762,155,'110674918',1,'2005-10-21'),(1813,762,155,'110678354',1,'2005-10-21'),(1814,762,166,'110678347',1,'2005-10-21'),(1815,762,159,'110678315',1,'2005-10-21'),(1816,762,172,'110678344',1,'2005-10-21'),(1817,763,159,'I9CG43A141091',1,'2005-10-21'),(1818,763,165,'I9CG43A141042',1,'2005-10-21'),(1819,763,166,'I9CG43A141023',1,'2005-10-21'),(1820,763,159,'I9CG43A140836',1,'2005-10-21'),(1821,763,155,'I9CG43A140785',1,'2005-10-21'),(1822,763,170,'I9CG43A140824',1,'2005-10-21'),(1823,762,167,'110678310',1,'2005-10-21'),(1824,763,167,'I9CG43A140818',1,'2005-10-21'),(1825,764,170,'1',1,'2005-11-14'),(1826,764,163,'2',1,'2005-11-14'),(1827,764,161,'3',1,'2005-11-14'),(1828,765,160,'2',1,'2005-11-14'),(1829,765,159,'3',1,'2005-11-14'),(1830,765,157,'4',1,'2005-11-14'),(1831,765,158,'5',1,'2005-11-14'),(1832,765,156,'1',1,'2005-12-05'),(1833,766,166,'',1,'2005-12-06'),(1834,766,170,'',1,'2005-12-06'),(1835,766,161,'',1,'2005-12-06'),(1836,767,156,'',1,'2005-12-06'),(1837,767,157,'',1,'2005-12-06'),(1838,767,158,'',1,'2005-12-06'),(1839,767,140,'',1,'2005-12-06'),(1840,767,159,'',1,'2005-12-06'),(1841,767,160,'',1,'2005-12-06'),(1842,768,165,'C100S8 56C0700659',1,'2005-12-07'),(1843,769,146,'01150295 6440784FG',1,'2006-07-07'),(1844,769,161,'01150295 6341033FF',1,'2006-07-07'),(1845,770,165,'1HHG1702R',1,'2006-07-07'),(1846,770,141,'1HHG17034',1,'2006-07-07'),(1847,771,142,'',2,'2006-07-07'),(1848,772,144,'MM73E051137400',1,'2006-07-07'),(1849,772,155,'MM73E051136721',1,'2006-07-07'),(1850,772,155,'MM73E051136716',1,'2006-07-07'),(1851,772,145,'MM73E051137324',1,'2006-07-07'),(1852,773,144,'3260127AG07653',1,'2006-07-07'),(1853,773,155,'3260127AG07691',1,'2006-07-07'),(1854,773,155,'3260127AG07660',1,'2006-07-07'),(1855,773,145,'3260127AG07646',1,'2006-07-07'),(1856,774,146,'S46055407H',1,'2006-07-07'),(1857,774,142,'S46055793H',1,'2006-07-07'),(1858,774,140,'S46063627H',1,'2006-07-07'),(1859,774,165,'S46058827H',1,'2006-07-07'),(1860,775,142,'',5,'2006-07-07'),(1861,774,161,'S46055673H',1,'2006-07-07'),(1862,768,171,'C10058 56C0700218',1,'2006-07-07'),(1863,776,165,'40130911',1,'2006-05-17'),(1864,777,165,'DL17165002775',1,'2006-11-16'),(1865,778,165,'1WBK195F002N',1,'2006-11-16'),(1866,779,165,'JB10211015',1,'2006-11-16'),(1867,780,165,'2CK7190965',1,'2007-11-19'),(1868,780,173,'2CK7190991',1,'2007-11-27'),(1869,780,174,'2CK71909N5',1,'2007-11-27'),(1870,780,175,'2CK7190985',1,'2007-11-27'),(1871,780,176,'2CK71908ZL',1,'2007-11-27'),(1872,780,166,'2CK71908SK',1,'2007-11-27'),(1873,780,177,'2CK71909XN',1,'2007-11-27'),(1874,780,178,'2CK7190983',1,'2007-11-27'),(1875,780,179,'2CK71906FM',1,'2007-12-11'),(1876,780,180,'2CK71908MF',1,'2007-12-11'),(1877,780,181,'2CK71908V5',1,'2007-12-11'),(1878,780,182,'2CK71908N4',1,'2007-12-11'),(1879,780,183,'2CK71906RM',1,'2007-12-11'),(1880,781,140,'CZC7101BCT',1,'2008-02-29'),(1881,782,140,'CNT70205WS',1,'2008-02-29'),(1882,783,165,'100620380374',1,'2008-02-29'),(1883,784,165,'702TEAU3P080',1,'2008-02-29');
/*!40000 ALTER TABLE `Elementos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Ubicaciones`
--

DROP TABLE IF EXISTS `Ubicaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Ubicaciones` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(30) NOT NULL COMMENT 'ordenable,link/Ubicacion',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=184 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Ubicaciones`
--

LOCK TABLES `Ubicaciones` WRITE;
/*!40000 ALTER TABLE `Ubicaciones` DISABLE KEYS */;
INSERT INTO `Ubicaciones` VALUES (140,'Secretario'),(141,'Aula Althia'),(142,'Almacén'),(143,'Aula de Música'),(144,'Aula de Plástica'),(145,'Aula Tecnología'),(146,'Aula Polivalente'),(147,'Aula 01'),(148,'Aula 02'),(149,'Aula 03'),(150,'Aula 04'),(151,'Aula 11'),(152,'Aula 12'),(153,'Aula 13'),(154,'Aula 14'),(155,'Biblioteca'),(156,'Departamento Ciencias'),(157,'Departamento Lenguas Extranjer'),(158,'Departamento Música y E.F.'),(159,'Departamento Socio-Lingüístico'),(160,'Departamento Tecnología-Plást.'),(161,'Conserjería'),(162,'Laboratorio'),(163,'Aula PT'),(164,'AMPA'),(165,'Director'),(166,'Jefa Estudios'),(167,'Orientadora'),(168,'Pasillos y hall'),(169,'Pista deportiva'),(170,'Sala de profesores'),(171,'Secretaría'),(172,'Averías'),(173,'Nicolás Moyano'),(174,'Salvador Pons'),(175,'Loli'),(176,'Noelia'),(177,'Joaquín'),(178,'Dolores'),(179,'José Miguel'),(180,'José García Serrano'),(181,'Eva Sánchez Muñoz'),(182,'Rafael Picazo'),(183,'Celia Martínez');
/*!40000 ALTER TABLE `Ubicaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Usuarios`
--

DROP TABLE IF EXISTS `Usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Usuarios` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(16) NOT NULL DEFAULT '',
  `clave` varchar(32) NOT NULL DEFAULT '',
  `idSesion` varchar(20) NOT NULL DEFAULT '',
  `alta` tinyint(1) NOT NULL DEFAULT '0',
  `modificacion` tinyint(1) NOT NULL DEFAULT '0',
  `borrado` tinyint(1) NOT NULL DEFAULT '0',
  `consulta` tinyint(1) NOT NULL DEFAULT '1',
  `informe` tinyint(1) NOT NULL DEFAULT '1',
  `usuarios` tinyint(1) NOT NULL DEFAULT '0',
  `config` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Usuarios`
--

LOCK TABLES `Usuarios` WRITE;
/*!40000 ALTER TABLE `Usuarios` DISABLE KEYS */;
INSERT INTO `Usuarios` VALUES (2,'admin','galeote','s3LUSqxg{s',1,1,1,1,1,1,1),(3,'demo','demo','NogP_U0Byi',0,0,0,1,1,0,0);
/*!40000 ALTER TABLE `Usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test`
--

DROP TABLE IF EXISTS `test`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test`
--

LOCK TABLES `test` WRITE;
/*!40000 ALTER TABLE `test` DISABLE KEYS */;
/*!40000 ALTER TABLE `test` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-03-02  0:48:18
