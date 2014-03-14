<?php

/**
 * Fichero de configuración donde se introducirán los valores para
 * la conexión con el servidor MySQL y otros para personalizar la aplicación.
 * @package Inventario
 * @copyright Copyright (c) 2008, Ricardo Montañana Gómez
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * This file is part of Inventario.
 * Inventario is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Inventario is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Inventario.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
require_once 'version.inc';

define('SERVIDOR', 'localhost'); //Ubicación del servidor MySQL
define('BASEDATOS', 'Inventario2'); //Nombre de la base de datos.
define('BASEDATOSTEST', 'Inventario_test'); //Base de datos para los tests.
define('USUARIO', 'test'); //Usuario con permisos de lectura/escritura en la base de datos
define('CLAVE', 'tset'); //contraseña del usuario.
define('PROGRAMA', 'Gesti&oacute;n de Inventario.');
define('CENTRO', 'I.E.S.O. Pascual Serrano');
define('NUMFILAS', '17'); // Número de registros a mostrar en las pantallas de consulta iniciales
define('PAUSA', '2'); //Nº segundos de pausa para mostrar mensaje id insertado
define('ESTILO', 'personal'); //Estilo de los iconos de edición (personal, personal, personal)
define('PLANTILLA', 'bootstrap'); //Estilo de la plantilla y recursos a utilizar
define('COLORLAT', '#46d6db'); //Color de la barra de menú lateral
define('COLORFON', '#a4bdfc'); //Color del fondo de la pantalla
define('MYSQLDUMP', '/usr/local/bin/mysqldump'); //camino a mysqldump
define('GZIP', '/usr/bin/gzip'); //Camino a gzip
?>
