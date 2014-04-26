<?php
/**
 * Programa de instalación que genera el entorno de ejecución
 * tanto el fichero de configuración como la base de datos
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
//Se incluyen los módulos necesarios
function __autoload($class_name) {
    require_once $class_name . '.php';
}

require_once 'inc/configuracion.inc';
define('NUMPASOS', 3);
//Para el Paso 1
define('MINBYTES', 4096000); // post_max_size y max_upload van con esto
define('CADENAMINBYTES', '4M');
define('CONFIGURACION', 'inc/configuracion.inc');
define('CONFIGTMP', 'tmp/config.tmp');
define('TMP', './tmp');
define('INC', './inc');

$instalar = new Instalar();
if ($instalar->error) {
    echo $instalar->panelError();
    return;
}
echo $instalar->ejecuta();

class Instalar {
    private $contenido;
    private $plant;
    public $error;
    public $error_msj; 
    
    public function __construct()
    {
        //Selecciona la plantilla a utilizar
        $this->plant='plant/';
        $this->plant.=PLANTILLA;
        $this->plant.='.html';
        $this->error = false;
        $this->eror_msj = '';
        if (INSTALADO != 'no') {
            $this->error = true;
            $this->error_msj = 'El programa ya está instalado';
        }
        /*if ($this->existenDatos()) {
            $this->error = true;
            $this->error_msj = "El indicador de instalación tiene 'no' pero la base de datos " . BASEDATOS . " contiene la tabla Articulos.";
        }*/
    }
    
    private function existenDatos()
    {
        //Comprueba si existe la tabla Articulos
        $sql = new Sql(SERVIDOR, USUARIO, CLAVE, BASEDATOS);
        if ($sql->error())
            return false;
        $sql->ejecuta('select * from Articulos;');
        if ($sql->error())
            return false;
        return true;
    }
    
    public function ejecuta()
    {
        $paso = isset($_GET['paso']) ? $_GET['paso'] : 0;
        $paso = $paso > NUMPASOS ? '0' : $paso;
        $i=0;
        //Si quiere ir a un determinado paso se asegura que estén completos los anteriores
        for ($i = 0; $i < $paso; $i++) {
            $funcion = "validaPaso" . $i;
            if (!$this->$funcion()) {
                break;
            }
        }
        $funcion = "paso" . $i;
        $this->contenido = $this->$funcion();
        $salida = new Distribucion($this->plant, $this);
        return $salida->procesaPlantilla();
    }

    // Cuestiones relacionadas con el servidor
    private function paso0()
    {
        $info = '<ul class="list-group">';
        $info .= '<li class="list-group-item list-group-item-info">Configuración de PHP (php.ini)</li>';
        // display_errors
        $displayErr = ini_get('display_errors');
        $displayErr = $displayErr == "1" || $displayErr == "on" ? "on" : "off";
        $mensaje = $displayErr == "off" ? $this->retornaLabel(false,'Se debe deshabilitar la impresión de errores') :
                                          $this->retornaLabel(true, 'Se debe deshabilitar la impresión de errores', "warning");
        $info .= $this->retornaElemento($mensaje, 'display_errors', $displayErr);
        // post_max_size
        $postMax = ini_get('post_max_size');
        $mensaje = $this->retornaBytes($postMax) >= MINBYTES ? $this->retornaLabel(false, 'Mínimo: ' . CADENAMINBYTES) :
                                                               $this->retornaLabel(true, 'Mínimo: ' . CADENAMINBYTES);
        $info .= $this->retornaElemento($mensaje, 'post_max_size', $postMax);
        // upload_max_filesize
        $uploadMax = ini_get('upload_max_filesize');
        $mensaje = $this->retornaBytes($uploadMax) >= MINBYTES ? $this->retornaLabel(false, 'Mínimo: ' . CADENAMINBYTES) : 
                                                                 $this->retornaLabel(true, 'Mínimo: ' . CADENAMINBYTES);
        $info .= $this->retornaElemento($mensaje, 'upload_max_filesize', $uploadMax);
        // mysqli
        $mysql = extension_loaded('mysqli');
        $mysql = $mysql ? "on" : "off";
        $mensaje = $mysql ? $this->retornaLabel(false, 'Tiene que estar cargada la extensión MySQLi para poder funcionar') :
                            $this->retornaLabel(true, 'Tiene que estar cargada la extensión MySQLi para poder funcionar');
        $info .= $this->retornaElemento($mensaje, 'extensión MySQLi', $mysql);
        $info .= '<li class="list-group-item list-group-item-info">Configuración de la Aplicación</li>';
        // img.dat
        $mensaje = is_writable(IMAGEDATA) ? $this->retornaLabel(false, "Se debe poder escribir en el directorio " . IMAGEDATA) : 
                                      $this->retornaLabel(true, "Se debe poder escribir en el directorio " . IMAGEDATA);
        $valor = is_writable(IMAGEDATA) ? "Sí" : "No";
        $info .= $this->retornaElemento($mensaje, 'Se puede escribir en ' . IMAGEDATA, $valor);      
        
        // tmp
        $mensaje = is_writable(TMP) ? $this->retornaLabel(false, "Se debe poder escribir en el directorio " . TMP) : 
                                      $this->retornaLabel(true, "Se debe poder escribir en el directorio " . TMP);
        $valor = is_writable(TMP) ? "Sí" : "No";
        $info .= $this->retornaElemento($mensaje, 'Se puede escribir en ' . TMP, $valor);
        
        // inc
        $mensaje = is_writable(INC) ? $this->retornaLabel(false, "Se debe poder escribir en el directorio " . INC) : 
                                      $this->retornaLabel(true, "Se debe poder escribir en el directorio " . INC);
        $valor = is_writable(INC) ? "Sí" : "No";
        $info .= $this->retornaElemento($mensaje, 'Se puede escribir en ' . INC, $valor);
        
        // configuracion.inc
        $mensaje = is_writable(CONFIGURACION) ? $this->retornaLabel(false, "Se debe poder escribir en el fichero de configuración ". CONFIGURACION) :
                                                $this->retornaLabel(true, "Se debe poder escribir en el fichero de configuración ". CONFIGURACION);
        $valor = is_writable(CONFIGURACION) ? "Sí" : "No";
        $info .= $this->retornaElemento($mensaje, 'Se puede escribir en ' . CONFIGURACION, $valor);
        
        // Final del paso
        $info .='</ul>';
        $info .= $this->validaPaso0() ? $this->retornaBoton(false, "instalar.php?paso=1") : $this->retornaBoton(true, "instalar.php");
        $panel = $this->panelMensaje($info, 'primary', 'PASO 1: Configuración del servidor y la aplicación');
        return $panel;
    }
    private function retornaElemento($validacion, $mensaje, $valor)
    {
        $info = '<li class="list-group-item">';
        $info .= $validacion . ' ' . $mensaje . ': <span class="badge">' . $valor . '</span>';
        $info .= '</li>';
        return $info;
    }
    
    private function retornaBoton($error, $paso, $javascript = true)
    {
        $anadido = $javascript ? 'onclick="location.href=' . "'" . $paso . "'". '"' : '';
        if (!$error) {
            return '<button ' . $anadido . ' type="submit" class="btn btn-success btn-lg pull-right">Continuar <span class="glyphicon glyphicon-arrow-right"></span></button>';
        } else {
            return '<button ' . $anadido . ' type="submit" class="btn btn-danger btn-lg pull-right">Comprobar de nuevo <span class="glyphicon glyphicon-repeat"></span></button>';
        }
    }
    
    private function botonVolver($enlace)
    {
        $boton = '<button type="button" onClick="location.href=' . "'$enlace'" . '" class="btn btn-success btn-lg pull-left">Paso anterior <span class="glyphicon glyphicon-arrow-left"></span></button>';
        return $boton;
    }
    
    private function retornaLabel($error, $mensaje, $tipo = "danger")
    {
        if ($error) {
            $nombre1 = $tipo; $nombre2 = "remove";
        } else {
            $nombre1 = "success"; $nombre2 = "ok";
        }
        $mensaje = '<a href="#" data-placement="right" data-toggle="popover" data-content="' . $mensaje . 
                   '"><span class="label label-' . $nombre1 . '"><span class="glyphicon glyphicon-' . $nombre2 . 
                   '"></span></a>';
        $mensaje .='<script>$(function () { $("[data-toggle=\'popover\']").popover(); });</script>';
        return $mensaje;
    }
    
    private function retornaBytes($val) 
    {
        $val = trim($val);
        $last = strtolower($val[strlen($val)-1]);
        switch($last) {
            // El modificador 'G' está disponble desde PHP 5.1.0
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }
        return $val;
    }
    
    private function validaPaso0()
    {
        $validar = true; 
        $postMax = ini_get('post_max_size');
        $uploadMax = ini_get('upload_max_filesize');
        $mysql = extension_loaded('mysqli');
        $escConfig = is_writable(CONFIGURACION);
        $escInc = is_writable(INC);
        $escTMP = is_writable(TMP);
        $escIMG = is_writable(IMAGEDATA);
        if ($this->retornaBytes($postMax) < MINBYTES) 
            $validar = false;
        if ($this->retornaBytes($uploadMax) < MINBYTES)
            $validar = false;
        if (!$mysql) 
            $validar = false;
        if (!$escConfig) 
            $validar = false;
        if (!$escTMP)
            $validar = false;
        if (!$escIMG)
            $validar = false;
        if (!$escInc)
            $validar = false;
        return $validar;
    }
    
    private function actualizaConfiguracion($grabar, $campos, &$datos)
    {
        $conf = new Configuracion();
        $fichero = $conf->obtieneFichero();
        $datosFichero = explode("\n", $fichero);
        if ($grabar) {
            $fsalida = @fopen(CONFIGTMP, "wb");
        }
        foreach ($datosFichero as $linea) {
            if (stripos($linea, "DEFINE") !== false) {
                $conf->obtieneDatos($linea, $clave, $valor);
                if (stripos($campos, $clave) !== false) {
                    if ($grabar) {
                        $linea = str_replace($valor, $datos[$clave], $linea);
                        $valor = $datos[$clave];
                    }
                }
                $datos[$clave] = $valor;
            }
            $registro = substr($linea, 0, 2) == "?>" ? $linea : $linea . "\n";
            if ($grabar) {
                fwrite($fsalida, $registro);
            }
        }
        if ($grabar) {
            fclose($fsalida);
            unlink(CONFIGURACION);
            rename(CONFIGTMP, CONFIGURACION);        
        }
    }
    
    // Cuestiones de la base de datos
    private function paso1()
    {
        $grabar = isset($_POST['SERVIDOR']);
        $campos = 'SERVIDOR,PUERTO,BASEDATOS,USUARIO,CLAVE';
        //Lee y si hace falta actualiza los datos del formulario en el fichero de configuración
        if ($grabar) {
            foreach ($_POST as $clave => $valor) {
                $datos[$clave] = $valor;
            }
        } else {
            $datos = array();
        }
        $this->actualizaConfiguracion($grabar, $campos, $datos);
        if ($grabar && $this->validaPaso1()) {
                //Pasa al paso siguiente
                return $this->paso2();
        }
        
        $info  = '<form method="post" name="conf" action="instalar.php?paso=1">';
        $info .= '<ul class="list-group">';
        $info .= '<li class="list-group-item list-group-item-info">Datos de configuración</li>';
        $info .= '<li class="list-group-item">Servidor <input type="text" name="SERVIDOR" class="form-control" placeholder="Nombre del servidor o dirección IP" value="'. $datos['SERVIDOR'] .'"></li>';
        $info .= '<li class="list-group-item">Puerto <input type="text" name="PUERTO" class="form-control" placeholder="Puerto de conexión" value="'. $datos['PUERTO'] .'"></li>';
        $info .= '<li class="list-group-item">Base de Datos <input type="text" name="BASEDATOS" class="form-control" placeholder="Nombre de la Base de Datos" value="'. $datos['BASEDATOS'] .'"></li>';
        $info .= '<li class="list-group-item">Usuario <input type="text" name="USUARIO" class="form-control" placeholder="Usuario" value="'. $datos['USUARIO'] .'"></li>';
        $info .= '<li class="list-group-item">Contraseña <input type="text" name="CLAVE" class="form-control" placeholder="Contraseña" value="'. $datos['CLAVE'] .'"></li>';
        $info .= '</ul>';
        $info .= $this->botonVolver("instalar.php");
        $info .= $this->validaPaso1() ? $this->retornaBoton(false, "instalar.php?paso=1", false) : $this->retornaBoton(true, "instalar.php?paso=1", false);
        $info .= '</form>';
        $panel = $this->panelMensaje($info, 'primary', 'PASO 2: Configuración de la Base de Datos.');
        return $panel;
    }
    
    private function validaPaso1()
    {
        $sql = new Sql(SERVIDOR, USUARIO, CLAVE, '');
        if ($sql->error())
            return false;
        $sql = new Sql(SERVIDOR, USUARIO, CLAVE, BASEDATOS);
        if ($sql->error()) {
            return false;
        }
        $comando = 'create table test2 (id int(10));';
        $sql->ejecuta($comando);
        if ($sql->error()) {
            return false;
        }
        $comando = 'drop table test2;';
        $sql->ejecuta($comando);
        if ($sql->error()) {
            return false;
        }
        return true; 
    }
    
    // Usuario administrador
    private function paso2()
    {
        if (isset($_POST['usuario'])) {
            //ha enviado el formulario.
            //Crea la base de datos
            $borra_database = "DROP DATABASE " . BASEDATOS . " ;";
            $database = "CREATE DATABASE " . BASEDATOS . " DEFAULT CHARACTER SET utf8;";
            $articulos = "CREATE TABLE `Articulos` (
                              `id` smallint(6) NOT NULL auto_increment COMMENT 'ordenable,link/Articulo',
                              `descripcion` varchar(60) NOT NULL COMMENT 'ordenable,ajax/text',
                              `marca` varchar(20) default NULL COMMENT 'ordenable,ajax/text',
                              `modelo` varchar(20) default NULL COMMENT 'ordenable,ajax/text',
                              `cantidad` int(11) default NULL COMMENT 'ordenable,ajax/number',
                              `imagen` varchar(45) default NULL COMMENT 'imagen',
                              PRIMARY KEY  (`id`)
                            ) ENGINE=InnoDB AUTO_INCREMENT=769 DEFAULT CHARSET=utf8;
                            ";
            $ubicaciones = "CREATE TABLE `Ubicaciones` (
                              `id` smallint(5) unsigned NOT NULL auto_increment COMMENT 'ordenable,link/Ubicacion',
                              `Descripcion` varchar(50) NOT NULL COMMENT 'ordenable,ajax/text',
                              `imagen` varchar(45) DEFAULT NULL COMMENT 'imagen',
                              PRIMARY KEY  (`id`)
                            ) ENGINE=InnoDB AUTO_INCREMENT=179 DEFAULT CHARSET=utf8;
                            ";
            $elementos = "CREATE TABLE `Elementos` (
                              `id` int(10) unsigned NOT NULL auto_increment COMMENT 'ordenable',
                              `id_Articulo` smallint(6) NOT NULL COMMENT 'foreign(Articulos;id),ordenable',
                              `id_Ubicacion` smallint(5) unsigned NOT NULL COMMENT 'foreign(Ubicaciones;id),ordenable',
                              `numserie` varchar(30) default NULL COMMENT 'ordenable,ajax/text',
                              `cantidad` int(10) unsigned default NULL COMMENT 'ordenable,ajax/number',
                              `fechaCompra` date NOT NULL COMMENT 'ordenable,ajax/combodate',
                              `imagen` varchar(45) default NULL COMMENT 'imagen',
                              PRIMARY KEY  (`id`),
                              KEY `id` (`id`),
                              KEY `id_Articulo` (`id_Articulo`),
                              KEY `id_Ubicacion` (`id_Ubicacion`),
                              CONSTRAINT `Elementos_ibfk_1` FOREIGN KEY (`id_Articulo`) REFERENCES `Articulos` (`id`) ON DELETE CASCADE,
                              CONSTRAINT `Elementos_ibfk_2` FOREIGN KEY (`id_Ubicacion`) REFERENCES `Ubicaciones` (`id`) ON UPDATE CASCADE
                            ) ENGINE=InnoDB AUTO_INCREMENT=1789 DEFAULT CHARSET=utf8;
                            ";
            $usuarios = "CREATE TABLE `Usuarios` (
                              `id` int(10) unsigned NOT NULL auto_increment COMMENT 'ordenable',
                              `nombre` varchar(16) NOT NULL default '' COMMENT 'ajax/text',
                              `clave` varchar(32) NOT NULL default '' COMMENT 'ajax/text',
                              `idSesion` varchar(20) NOT NULL default '' COMMENT 'ajax/text',
                              `alta` tinyint(1) NOT NULL default '0',
                              `modificacion` tinyint(1) NOT NULL default '0',
                              `borrado` tinyint(1) NOT NULL default '0',
                              `consulta` tinyint(1) NOT NULL default '1',
                              `informe` tinyint(1) NOT NULL default '1',
                              `usuarios` tinyint(1) NOT NULL default '0',
                              `config` tinyint(1) NOT NULL default '0',
                              PRIMARY KEY  (`id`),
                              KEY `nombre` (`nombre`)
                            ) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
                            ";
            $letras = "abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
            $sesion = substr(str_shuffle($letras), 0, 8);
            $usuario = $_POST['usuario'];
            $clave = $_POST['clave'];
            $administrador = "insert into Usuarios values (null,'$usuario','$clave','$sesion','1','1','1','1','1','1','1');";
            
            @mysqli_query($borra_database);
            @mysqli_query($database);
            $sql = new Sql(SERVIDOR, USUARIO, CLAVE, BASEDATOS);
            $sql->ejecuta($ubicaciones);
            if ($sql->error()) {
                return $this->panelMensaje($sql->mensajeError(), "danger", "ERROR");
            }
            $sql->ejecuta($articulos);
            if ($sql->error()) {
                return $this->panelMensaje($sql->mensajeError(), "danger", "ERROR");
            }
            $sql->ejecuta($elementos);
            if ($sql->error()) {
                return $this->panelMensaje($sql->mensajeError(), "danger", "ERROR");
            }
            $sql->ejecuta($usuarios);
            if ($sql->error()) {
                return $this->panelMensaje($sql->mensajeError(), "danger", "ERROR");
            }
            $sql->ejecuta($administrador);
                        if ($sql->error()) {
                return $this->panelMensaje($sql->mensajeError(), "danger", "ERROR");
            }
            $campos="INSTALADO";
            $datos['INSTALADO'] = "sí";
            $this->actualizaConfiguracion(true, $campos, $datos);
            return $this->resumen();
        }
        
        $info = '      
        <form data-toggle="validator" role="form" class="form-horizontal" method="post" action="instalar.php?paso=2">
                <div class="form-group">
                    <label for="usuario" class="control-label col-sm-2">Usuario</label>
                    <div class="form-group col-sm-10">
                        <input type="text" class="form-control" id="usuario" name="usuario" placeholder="nombre de usuario" data-minlength="5" data-minlength-error="Mínimo 5 caracteres" required>
                        <div class="help-block with-errors">Mínimo 5 caracteres</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="clave" class="control-label col-sm-2">Contraseña</label>
                    <div class="form-group col-sm-10">
                        <input type="password" data-toggle="validator" data-minlength="6" class="form-control" id="clave" name="clave" placeholder="Contraseña" data-minlength-error="Mínimo 6 caracteres" required>
                        <span class="help-block with-errors">Mínimo 6 caracteres</span>
                        <input type="password" class="form-control" id="claverepetida" data-match="#clave" data-match-error="No coincide" data-minlength-error="Mínimo 6 caracteres" placeholder="Confirmar contraseña" required>
                        <div class="help-block with-errors">Mínimo 6 caracteres</div>
                    </div>
                </div>

                <div class="form-group col-sm-12">
                ' . $this->botonVolver("instalar.php?paso=1") . '
                    <button type="submit" class="btn btn-primary pull-right btn-lg" disabled="disabled">Crear base de datos y usuario <span class="glyphicon glyphicon-arrow-right"></button>
                </div>
            </div>
        </form>
        <script type="text/javascript" src="./css/validator.min.js"></script>';
        $panel = $this->panelMensaje($info, 'primary', 'PASO 3: Creación de la base de datos y el usuario administrador.');
        return $panel;        
    }
    
    private function validaPaso2()
    {
        //La validación de este paso se hace con la del formulario en javascript
        return true;
    }
    
    public function panelMensaje($info, $tipo = "info", $cabecera = "&iexcl;Atenci&oacute;n!") {
        $mensaje = '<div class="panel panel-' . $tipo . ' col-sm-6"><div class="panel-heading">';
        $mensaje .= '<h3 class="panel-title">' . $cabecera . '</h3></div>';
        $mensaje .= '<div class="panel-body">';
        $mensaje .= $info;
        $mensaje .= '</div>';
        $mensaje .= '</div>';
        return $mensaje;
    }
    
    public function contenido()
    {
        return $this->contenido;
    }
    
    public function menu()
    {
        return '';
    }
    
    public function opcion()
    {
        return 'INSTALACI&Oacute;N';
    }
    
    public function control()
    {
        return '';
    }
    
    public function aplicacion()
    {
        return PROGRAMA . ' v' . VERSION;
    }
    
    public function usuario()
    {
        return '';
    }
    
    public function fecha()
    {
        $idioma = 'es_ES';
        $formato = "%d-%b-%y";
        setlocale(LC_TIME, $idioma);
        return strftime($formato);
    }
    public function cabecera()
    {
        return '<!DOCTYPE html>
                <html lang="es">
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                        <meta charset="utf-8">
                        <meta http-equiv="X-UA-Compatible" content="IE=edge">
                        <meta name="viewport" content="width=device-width, initial-scale=1">
                        <meta name="description" content="">
                        <meta name="author" content="Ricardo Montañana">
                        <link rel="shortcut icon" href="img/tux.ico">
                        <title>Inventario</title>
                        <!-- Bootstrap core CSS -->
                        <link href="css/bootstrap/css/bootstrap.min.css" rel="stylesheet">
                        <!-- Custom styles for this template -->
                        <link href="css/dashboard.php" rel="stylesheet">
                        <link href="css/bootstrap-datetimepicker.min.css" rel="stylesheet">
                        <link rel="stylesheet" href="css/jquery.simplecolorpicker.css">
                        <link rel="stylesheet" href="css/jquery.simplecolorpicker-glyphicons.css">
                        <link rel="stylesheet" href="css/jasny-bootstrap.min.css">
                        <link rel="stylesheet" href="css/bootstrap-select/bootstrap-select.min.css">
                        <style type="text/css"></style>

                        <script type="text/javascript" src="./css/jquery.min.js"></script>
                        <script type="text/javascript" src="./css/bootstrap-select/bootstrap-select.min.js"></script>
                    </head>
                    <body>';
    }
    
    public function panelError()
    {
        $mensaje = $this->cabecera();
        $mensaje .= $this->panelMensaje($this->error_msj, "danger", "&iexcl;ERROR!");
        $mensaje .= "</body></html>";
        return $mensaje;
    }
    
    private function resumen()
    {
        $info = '<ul class="list-group">';
        $info .= '<li class="list-group-item list-group-item-info">Paso 1</li>';
        $info .= $this->retornaElemento($this->retornaLabel(false, ""), "Configuración de PHP");
        $info .= $this->retornaElemento($this->retornaLabel(false, ""), "Configuración de la aplicación");
        $info .= '<li class="list-group-item list-group-item-info">Paso 2</li>';
        $info .= $this->retornaElemento($this->retornaLabel(false, ""), "Configuración de la base de datos");
        $info .= '<li class="list-group-item list-group-item-info">Paso 3</li>';
        $info .= $this->retornaElemento($this->retornaLabel(false, ""), "Creación de Base de datos");
        $info .= $this->retornaElemento($this->retornaLabel(false, ""), "Creación del usuario administrador"); 
        $info .= '</ul>';
        $info .= $this->retornaBoton(false, "index.php", true);
        $panel = $this->panelMensaje($info, 'success', 'Instalación finalizada.');
        return $panel;
        
    }
}

?>