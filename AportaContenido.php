<?php

/**
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
define('PIE', '<center><a target="_blank" href="http://www.gnu.org/licenses/gpl-3.0-standalone.html"><img src="img/gplv3.png" alt="GPL v3"/></a>' .
        '<a target="_blank" href="http://www.apache.org"><img src="img/apache.gif" alt="Sitio web creado con Apache" /></a>' .
        '<a target="_blank" href="http://www.mysql.org"><img src="img/mysql.png" width=125 height=47 alt="Gestor de bases de datos mySQL" /></a>' .
        '<a target="_blank" href="http://www.php.net"><img src="img/php.gif" alt="PHP Language" /></a> </center>');
define('FORMULARIO_ACCESO', '<form name="formulario_acceso" action="index.php?registrarse" method="POST">' .
        'Usuario<br><input type="text" name="usuario" value="" size="8" /><br><br>Clave<br><input type="password" name="clave" value="" size="8" />' .
        '<br><br><button type="submit" name="iniciar" class="btn btn-primary">Iniciar <span class="glyphicon glyphicon-log-in"></span></button></form>');
define('MENSAJE_DEMO', 'Puede Iniciar sesi&oacute;n con<br>usuario <i><b>demo</b></i><br>contrase&ntilde;a <i>demo</i><br>');
define('USUARIO_INCORRECTO', '<label class="bg-danger">Usuario y clave incorrectos!</label><br><br>');
define('CREDITOS', '<div class="modal fade" tabindex="-1" id="creditos" role="dialog" aria-labelledby="modalCreditos" aria-hidden="true">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                              <h4>Créditos</h4>
                            </div>
                            <div class="modal-body">
                                <div class="jumbotron">
                                    <img src="img/logo.png" class="img-responsive img-rounded" style="float:left"> 
                                    <h1>Inventario2</h1>
                                    <p> Aplicación para controlar el inventario de un centro educativo.</p>
                                    <p>En la aplicación se hace uso de los siguientes módulos y/o bibliotecas</p>
                                    <table class="table table-condensed">
                                     <thead><tr><th>Biblioteca/Módulo</th><th>Licencia</th></tr></thead>
                                     <tbody>
                                      <tr><td><a href="http://getbootstrap.com/" target="_blank">Twitter Bootstrap</a></td><td><a target="_blank" href="https://github.com/twbs/bootstrap/blob/master/LICENSE">MIT</a></td>
                                      <tr><td><a href="http://www.fpdf.org/" target="_blank">FPDF</a></td><td>Libre</td>
                                      <tr><td><a href="http://phpqrcode.sourceforge.net/" target="_blank">PHP QR Code Enconder</a></td><td><a target="_blank" href="http://www.gnu.org/licenses/lgpl-3.0.txt">LGPL</a></td>
                                      <tr><td><a href="http://stefangabos.ro/php-libraries/zebra-image/" target="_blank">Zebra_Image</a></td><td><a target="_blank" href="http://www.gnu.org/licenses/lgpl-3.0.txt">LGPL</a></td>
                                      <tr><td><a href="http://jasny.github.io/bootstrap/" target="_blank">Jasny Bootstrap</a></td><td><a target="_blank" href="http://www.apache.org/licenses/LICENSE-2.0">Apache 2.0</a></td>
                                      <tr><td><a href="http://1000hz.github.io/bootstrap-validator/" target="_blank">Bootstrap Validator</a></td><td><a target="_blank" href="https://github.com/1000hz/bootstrap-validator/blob/master/LICENSE">MIT</a></td>
                                      <tr><td><a href="https://github.com/tkrotoff/jquery-simplecolorpicker" target="_blank">jquery-simplecolorpicker</a></td><td><a target="_blank" href="https://github.com/tkrotoff/jquery-simplecolorpicker/blob/master/LICENSE.txt">MIT</a></td>
                                      <tr><td><a href="http://eonasdan.github.io/bootstrap-datetimepicker/" target="_blank">Bootstrap datetimepicker</a></td><td><a target="_blank" href="https://github.com/Eonasdan/bootstrap-datetimepicker/blob/master/src/js/bootstrap-datetimepicker.js">MIT</a></td>
                                      <tr><td><a href="http://silviomoreto.github.io/bootstrap-select/" target="_blank">Bootstrap-select</a></td><td><a target="_blank" href="https://github.com/silviomoreto/bootstrap-select">MIT</a></td>
                                      <tr><td><a href="https://github.com/vitalets/x-editable" target="_blank">X-editable</a></td><td><a target="_blank" href="https://github.com/vitalets/x-editable/blob/master/LICENSE-MIT">MIT</a></td>
                                     </tbody>
                                    </table>
                                    <p><h5>Copyright &copy; 2008-2014 Ricardo Montañana Gómez</h4>
                                    <h5><small>Esta aplicación se distribuye con licencia <a target="_blank" href="http://www.gnu.org/licenses/gpl-3.0.html">GPLv3 </a></small></h5></p>
                                </div>
                            </div>
                        </div>
                      </div>
                    </div>');

// Esta clase aportará el contenido a la plantilla
class AportaContenido {

    /**
     * 
     * @var boolean Aporta información sobre si el usuario está registrado o no.
     */
    private $registrado;

    /**
     * @var string Nombre del usuario
     */
    private $usuario = NULL;

    /**
     * @var Menu Menú de la página.
     */
    private $miMenu;

    /**
     * @var database Controlador de la base de datos
     */
    private $bdd;

    /**
     * @var string Opción elegida por el usuario
     */
    private $opcionActual;

    /**
     * @var boolean Usuario y clave incorrectos?
     */
    private $usuario_inc = false;

    /**
     * @var array Permisos del usuario
     */
    private $perfil;

    /**
     *
     * @var array Datos pasados en la URL
     */
    private $datosURL = array();

    // El constructor necesita saber cuál es la opción actual
    /**
     * Constructor de la clase. 
     * @param BaseDatos $baseDatos Manejador de la base de datos
     * @param boolean $registrado usuario registrado si/no
     * @param String $usuario Nombre del usuario
     * @param array $perfil Permisos de acceso del usuario
     * @param String $opcion Opción elegida por el usuario
     */
    public function __construct($baseDatos, $registrado, $usuario, $perfil, $opcion)
    {
        $this->bdd = $baseDatos;
        $this->miMenu = new Menu('inc/inventario.menu');
        $this->registrado = $registrado;
        $this->usuario = $usuario;
        $this->perfil = $perfil;
        $this->opcionActual = $opcion;
    }

    /**
     * Devuelve la fecha actual
     * @param string $formato formato de devolución de la fecha
     * @param string $idioma idioma para formatear la fecha, p.ej. es_ES
     * @return string
     */
    public function fechaActual($formato = '', $idioma = 'es_ES')
    {
        if ($formato == '')
            $formato = "%d-%b-%y";
        setlocale(LC_TIME, $idioma);
        return strftime($formato);
    }

    /**
     * 
     * @return string Mensaje el usuario debe registrarse.
     */
    private function mensajeRegistro()
    {
        return 'Debe registrarse para acceder a este apartado';
    }

    // Procesaremos todas las invocaciones a métodos en
    // la función __call()
    /**
     * Procesa las peticiones de contenido de la plantilla.
     * @param string $metodo Método a ejecutar
     * @param string $parametros Parámetros del método
     * @return string Contenido devuelto por el método
     */
    public function __call($metodo, $parametros)
    {
        switch ($metodo) { // Dependiendo del método invocado
            case 'usuario':
                if ($this->registrado)
                    return "Usuario=$this->usuario";
                else
                    return '';
            case 'fecha':
                $script = '<script type="text/javascript">
                                $(function () {
                                    $(' . "'#fechaCabecera'" . ").datetimepicker({
                                        pick12HourFormat: false,
                                        language: 'es',
                                        pickTime: false
                                        });
                                });
                                </script>";
                $campo = '<input type="hidden" name="fechaCabecera" id="fechaCabecera" value="' . $this->fechaActual("%d/%m/%Y") . '">';
                $etiqueta = '<label for="fechaCabecera" onClick="$(' . "'#fechaCabecera'" . ").data('DateTimePicker').show();" . '">' . $this->fechaActual() . '</label>';
                return $etiqueta . $campo . $script;
            case 'aplicacion': return PROGRAMA . " v" . VERSION;
            case 'menu': // el menú
                if ($this->registrado) {
                    return $this->miMenu->insertaMenu();
                } else {
                    $salida = FORMULARIO_ACCESO;
                    if ($this->usuario_inc) {
                        $salida.=USUARIO_INCORRECTO;
                    }
                    //$salida.=MENSAJE_DEMO;
                    return $salida;
                }
            case 'opcion':
                list($opcion, $parametro) = explode("&", $this->opcionActual);
                switch ($opcion) {
                    case 'bienvenido':
                        return "Men&uacute; Principal";
                    case 'principal':
                        return "Pantalla Inicial";
                    case 'articulos': $opcion = "art&iacute;culos";
                    case 'elementos':
                    case 'ubicaciones':
                    case 'usuarios':
                    case 'test':
                        return "Mantenimiento " . ucfirst($opcion);
                    case 'configuracion':
                        return 'Configuraci&oacute;n y Preferencias';
                    case 'informeInventario':return "Informe de Inventario";
                    case 'descuadres':return 'Informe de descuadres';
                    case 'importacion': return 'Importaci&oacute;n de datos';
                    case 'copiaseg': return 'Copia de seguridad de datos';
                }
                return '';
            case 'control':
                if ($this->registrado)
                    return '<a href="index.php?cerrarSesion">Cerrar Sesi&oacute;n <span class="glyphicon glyphicon-log-out"></span></a>';
                else
                    return '';
            // Para incluir el contenido central de la página
            case 'contenido':
                // tendremos en cuenta cuál es la opción actual
                /* echo "opcActual=$this->opcActual<br>";
                  echo "Metodo=$Metodo<br>";
                  print_r($Parametros); */
//                if (!$this->registrado) {
//                    return $this->mensajeRegistro();
//                }
                list($opcion, $parametro) = explode("&", $this->opcionActual);
                switch ($opcion) {
                    case 'bienvenido':
                        $mensaje = '<div class="alert alert-success">';
                        $mensaje .= 'Bienvenid@ ' . $this->usuario . '</div>';
                    case 'principal': // contenido inicial

                        $creditos = "$('#creditos').modal({keyboard: false});";
                        $centro = '<div class="well well-sm">' . CENTRO . '</div>';
                        return $mensaje . '<br><br><center><img src="img/logo.png" alt="' . PROGRAMA . '" onClick="' . $creditos . '" >' .
                                '<br><br><label onClick="' . $creditos . '">' . $centro . '</label></center><br><br>' . CREDITOS;
                    case 'articulos':
                    case 'ubicaciones':
                    case 'test':
                    case 'elementos':
                        $this->cargaDatosURL();
                        if ($this->datosURL['opc'] == "informe") {
                            if (!$this->pefil['Informe']) {
                                $this->procesaURL();
                                $fichero = 'xml/informe' . ucfirst($opcion) . '.xml';
                                $salida = TMP.'/informe' . ucfirst($opcion) . '.xml';
                                //Establece los posibles parámetros del listado.
                                $orden = $this->datosURL['orden'];
                                $sentido = $this->datosURL['sentido'] == "asc" ? ' ' : ' desc ';
                                $filtro = isset($this->datosURL['buscar']) ? $this->bdd->filtra($this->datosURL['buscar']) : '';
                                $plantilla = file_get_contents($fichero) or die('Fallo en la apertura de la plantilla ' . $fichero);
                                $plantilla = str_replace("{filtro}", $filtro, $plantilla);
                                $plantilla = str_replace("{orden}", $orden . $sentido, $plantilla);
                                file_put_contents($salida, $plantilla) or die('Fallo en la escritura de la plantilla ' . $salida);
                                $informe = new InformePDF($this->bdd, $salida, $this->registrado);
                                $informe->crea($salida);
                                $informe->cierraPDF();
                                return $this->devuelveInforme($informe);
                            } else {
                                return $this->mensajePermisos("Informes");
                            }
                        }
                        if ($this->perfil['Consulta']) {
                            $ele = new Mantenimiento($this->bdd, $this->perfil, $opcion);
                            return $ele->ejecuta();
                        } else {
                            return $this->mensajePermisos(ucfirst($opcion));
                        }
                    case 'usuarios':
                        if ($this->perfil['Usuarios']) {
                            $this->cargaDatosURL();
                            if ($this->datosURL['opc'] == "informe") {
                                if (!$this->pefil['Informe']) {
                                    $this->procesaURL();
                                    $fichero = 'xml/informe' . ucfirst($opcion) . '.xml';
                                    $salida = TMP.'/informe' . ucfirst($opcion) . '.xml';
                                    //Establece los posibles parámetros del listado.
                                    $orden = $this->datosURL['orden'];
                                    $sentido = $this->datosURL['sentido'] == "asc" ? ' ' : ' desc ';
                                    $filtro = isset($this->datosURL['buscar']) ? $this->bdd->filtra($this->datosURL['buscar']) : '';
                                    $plantilla = file_get_contents($fichero) or die('Fallo en la apertura de la plantilla ' . $fichero);
                                    $plantilla = str_replace("{filtro}", $filtro, $plantilla);
                                    $plantilla = str_replace("{orden}", $orden . $sentido, $plantilla);
                                    file_put_contents($salida, $plantilla) or die('Fallo en la escritura de la plantilla ' . $salida);
                                    $informe = new InformePDF($this->bdd, $salida, $this->registrado);
                                    $informe->crea($salida);
                                    $informe->cierraPDF();
                                    return $this->devuelveInforme($informe);
                                } else {
                                    return $this->mensajePermisos("Informes");
                                }
                            }
                            $ele = new Mantenimiento($this->bdd, $this->perfil, $opcion);
                            return $ele->ejecuta();
                        } else {
                            return $this->mensajePermisos('Usuarios');
                        }
                    case 'configuracion':
                        if ($this->perfil['Config']) {
                            $conf = new Configuracion();
                            return $conf->ejecuta();
                        } else {
                            return $this->mensajePermisos('Configuraci&oacute;n');
                        }
                    case 'informeInventario':
                        if ($this->perfil['Informe']) {
                            $info = new InformeInventario($this->bdd);
                            return $info->ejecuta();
                        } else {
                            return $this->mensajePermisos('Informes');
                        }
                    case 'importacion':
                        if ($this->perfil['Modificacion'] && $this->perfil['Borrado']) {
                            $import = new Importacion($this->bdd, $this->registrado);
                            return $import->ejecuta();
                        } else {
                            return $this->mensajePermisos("Actualizaci&oacute;n, creaci&oacute;n y borrado de elementos");
                        }
                    case 'copiaseg':
                        if ($this->perfil['Config']) {
                            $copia = new CopiaSeguridad();
                            if ($_GET['confirmado'] == "1") {
                                if (!$copia->creaCopia()) {
                                    $tipo = "danger";
                                    $cabecera = "ERROR";
                                } else {
                                    $tipo = "info";
                                    $cabecera = "INFORMACIÓN";
                                }
                                return $this->panel($cabecera, $copia->mensaje(), $tipo);
                            } else {
                                return $copia->dialogo();
                            }
                        } else {
                            return $this->mensajePermisos("Copias de seguridad");
                        }
                } // Fin del contenido
            case 'usuario_incorrecto':
                $this->usuario_inc = true;
                return;
            case 'registro': // Si está registrado mostrar bienvenida
                // si no, un enlace
                if ($this->bEstaRegistrado) {
                    return "Bienvenid@ <b>$this->sUsuario</b><hr />" .
                            '<a href="index.php?cerrarSesion">Cerrar sesi&oacute;n</a>';
                } else {
                    return '';
                }
            default: // Si es cualquier otra marca
                return "Marca {$metodo} queda sin procesar";
        }
    }

    public function cargaDatosURL()
    {
        $this->datosURL['opc'] = isset($_GET['opc']) ? $_GET['opc'] : 'inicial';
        $this->datosURL['orden'] = isset($_GET['orden']) ? $_GET['orden'] : 'id';
        $this->datosURL['sentido'] = isset($_GET['sentido']) ? $_GET['sentido'] : 'asc';
        $this->datosURL['pag'] = isset($_GET['pag']) ? $_GET['pag'] : '0';
        $this->datosURL['buscar'] = isset($_GET['buscar']) ? $_GET['buscar'] : null;
        $this->datosURL['id'] = isset($_GET['id']) ? $_GET['id'] : null;
    }

    /**
     *
     * @param string $tipo
     * @return string
     */
    public function mensajePermisos($tipo)
    {
        return $this->panel("ERROR", "No tiene permiso para acceder a $tipo", "danger");
    }
    
    private function devuelveInforme($informe)
    {
        $letras = "abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
        $nombre = TMP."/informe" . substr(str_shuffle($letras), 0, 10) . ".pdf";
        $informe->guardaArchivo($nombre);
        return '<div class="container">
                    <!--<a href="' . $nombre . '" target="_blank"><span class="glyphicon glyphicon-cloud-download" style="font-size:1.5em;"></span>Descargar Informe</a>--> 
                    <object data="' . $nombre . '" type="application/pdf" width="100%" height="700" style="float:left;">
                    </object>
                </div>';
    }
    
    public function panel($cabecera, $mensaje, $tipo)
    {
        $panel = '<div class="panel panel-' . $tipo . '"><div class="panel-heading">';
        $panel .= '<h3 class="panel-title">' . $cabecera . '</h3></div>';
        $panel .= '<div class="panel-body">';
        $panel .= $mensaje;
        $panel .= '</div>';
        return $panel;
    }
}

?>
