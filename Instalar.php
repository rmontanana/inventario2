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
include 'inc/configuracion.inc';
define('NUMPASOS', 3);
define('MINBYTES', 4096000); // post_max_size y max_upload van con esto
define('CADENAMINBYTES', '4M');

// Si ya se ha ejecutado con anterioridad esta script no continúa.
if (INSTALADO != 'no') {
    echo "El programa ya está instalado";
    return 1;
}

$instalar = new Instalar();
echo $instalar->ejecuta();

class Instalar {
    private $resultados;
    private $contenido;
    private $plant;
    
    public function __construct()
    {
        //Selecciona la plantilla a utilizar
        $this->plant='plant/';
        $this->plant.=PLANTILLA;
        $this->plant.='.html';;
    }
    
    public function ejecuta()
    {
        $paso = isset($_GET['paso']) ? $_GET['paso'] : 0;
        $paso = $paso > NUMPASOS ? '0' : $paso;
        //Si quiere ir a un determinado paso se asegura que estén completos los anteriores
        for ($i = 0; $i < $paso; $i++) {
            if (!$this->resultado[$i]) {
                //$funcion = "paso" . $i;
                //$this->contenido = $this->$funcion();
                break;
            }
        }
        if ($paso == NUMPASOS) {
            $this->contenido = $this->pasoFinal();
        } else {
            $funcion = "paso" . $i;
            $this->contenido = $this->$funcion();
        }
        $salida = new Distribucion($this->plant, $this);
        return $salida->procesaPlantilla();
    }
    
    private function inicializa()
    {
        for ($i = 0; $i < NUMPASOS; $i++) {
            $funcion = "validaPaso" . $i;
            $resultado[] = $funcion();
        }
    }
        
    // Resumen de cuestiones realizadas
    private function pasoFinal()
    {
        
    }
    
    // Cuestiones relacionadas con el servidor
    private function paso0()
    {
        $displayErr = ini_get('display_errors');
        $info = '<ul class="list-group">';
        $info .= '<li class="list-group-item">';
        $displayErr = $displayErr == "1" || $displayErr == "on" ? "on" : "off";
        $mensaje = $displayErr == "off" ? $this->retornaLabel(false,'Se debe deshabilitar la impresión de errores') :
                                        $this->retornaLabel(true, 'Se debe deshabilitar la impresión de errores', "warning");
        $info .=$mensaje . ' display_errors: <span class="badge">' . $displayErr. '</span> ';
        $info .= '</li>';
        $info .= '<li class="list-group-item">';
        $postMax = ini_get('post_max_size');
        $mensaje = $this->retornaBytes($postMax) >= MINBYTES ? $this->retornaLabel(false, 'Mínimo: ' . CADENAMINBYTES) :
                                                               $this->retornaLabel(true, 'Mínimo: ' . CADENAMINBYTES);
        $info .= $mensaje . ' post_max_size: <span class="badge">' . $postMax . '</span>';
        $info .= '<li class="list-group-item">';
        $uploadMax = ini_get('upload_max_filesize');
        $mensaje = $this->retornaBytes($uploadMax) >= MINBYTES ? $this->retornaLabel(false, 'Mínimo: ' . CADENAMINBYTES) : 
                                                                 $this->retornaLabel(true, 'Mínimo: ' . CADENAMINBYTES);
        $info .= $mensaje . ' upload_max_filesize: <span class="badge">' . $uploadMax . '</span>';
        $info .= '</li>';
        $mysql = extension_loaded('mysqli');
        echo $mysql;
        $mysql = $mysql ? "on" : "off";
        $info .= '<li class="list-group-item">';
        $mensaje = $mysql ? $this->retornaLabel(false, 'Tiene que estar cargada la extensión MySQLi para poder funcionar') :
                            $this->retornaLabel(true, 'Tiene que estar cargada la extensión MySQLi para poder funcionar');
        $info .= $mensaje . ' extensión MySQLi: <span class="badge">' . $mysql . ' ' . '</span>';

        $info .='</li></ul>';
        if ($this->validaPaso0()) {
            $boton = '<button onclick="location.href=' . "'instalar.php?paso=2'". '" type="button" class="btn btn-success btn-lg pull-right">Continuar <span class="glyphicon glyphicon-arrow-right"></span></button>';
        } else {
            $boton = '<button onclick="location.href=' . "'instalar.php'" . '" type="button" class="btn btn-danger btn-lg pull-right">Comprobar de nuevo <span class="glyphicon glyphicon-repeat"></span></button>';
        }
        $info .= $boton;
        $panel = $this->panelMensaje($info, 'info', 'PASO 1: Configuración del servidor');
        return $panel;
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
        if ($this->retornaBytes($postMax) < MINBYTES) 
            $validar = false;
        if ($this->retornaBytes($uploadMax) < MINBYTES)
            $validar = false;
        if (!$mysql) 
            $validar = false;
        return $validar;
    }
    
    // Cuestiones de la base de datos
    private function paso2()
    {
        
    }
    
    private function validaPaso2()
    {
        return false; 
    }
    
    // Usuario administrador
    private function paso3()
    {
        
    }
    
    private function validaPaso3()
    {
        return false;
    }
    
    private function panelMensaje($info, $tipo = "info", $cabecera = "&iexcl;Atenci&oacute;n!") {
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
        if ($formato == '')
            $formato = "%d-%b-%y";
        setlocale(LC_TIME, $idioma);
        return strftime($formato);
    }
}

?>
