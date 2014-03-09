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
        '<br><br><input type="submit" value="Iniciar" name="iniciar" /></form>');
define('MENSAJE_DEMO', 'Puede Iniciar sesi&oacute;n con<br>usuario <i><b>demo</b></i><br>contrase&ntilde;a <i>demo</i><br>');
define('USUARIO_INCORRECTO', '<label class="error">Usuario y clave incorrectos!</label><br><br>');

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

    // El constructor necesita saber cuál es la opción actual
    /**
     * Constructor de la clase. 
     * @param BaseDatos $baseDatos Manejador de la base de datos
     * @param boolean $registrado usuario registrado si/no
     * @param String $usuario Nombre del usuario
     * @param array $perfil Permisos de acceso del usuario
     * @param String $opcion Opción elegida por el usuario
     */
    public function __construct($baseDatos, $registrado, $usuario, $perfil, $opcion) {
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
    public function fechaActual($formato = '', $idioma = 'es_ES') {
        if ($formato == '')
            $formato = "%d-%b-%y";
        setlocale(LC_TIME, $idioma);
        return strftime($formato);
    }

    /**
     * 
     * @return string Mensaje el usuario debe registrarse.
     */
    private function mensajeRegistro() {
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
    public function __call($metodo, $parametros) {
        switch ($metodo) { // Dependiendo del método invocado
            case 'titulo': // devolvemos el título
                return PROGRAMA.VERSION;
            case 'usuario':
                if ($this->registrado)
                    return "Usuario=$this->usuario";
                else
                    return '';
            case 'fecha': return $this->fechaActual();
            case 'aplicacion': return PROGRAMA.VERSION;
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
                    case 'elementos':
                    case 'articulos': $opcion = "art&iacute;culos";
                    case 'ubicaciones':
                    case 'usuarios':
                    case 'test':
                        return "Mantenimiento de " . ucfirst($opcion) . ".";
                    case 'configuracion':
                        return 'Configuraci&oacute;n y Preferencias.';
                    case 'informeInventario':return "Informe de Inventario";
                    case 'descuadres':return 'Informe de descuadres';
                    case 'importacion': return 'Importaci&oacute;n de datos';
                    case 'copiaseg': return 'Copia de seguridad de datos';
                }
                return '';
            case 'control':
                if ($this->registrado)
                    return '<a href="index.php?cerrarSesion">Cerrar Sesi&oacute;n</a>';
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
                    case 'principal': // contenido inicial
                        return '<br><br><center><img src="img/logo.png" alt="' . PROGRAMA . '">' .
                                '<br><label>' . CENTRO . '</label></center><br><br>' . PIE;
                    case 'articulos':
                    case 'ubicaciones':
                    case 'test':
                    case 'elementos':
                        if ($this->perfil['Consulta']) {
                            $ele = new Mantenimiento($this->bdd, $this->perfil, $opcion);
                            return $ele->ejecuta();
                        } else {
                            return $this->mensajePermisos(ucfirst($opcion));
                        }
                    case 'usuarios':
                        if ($this->perfil['Usuarios']) {
                            $ele = new Mantenimiento($this->bdd, $this->perfil, $opcion);
                            return $ele->ejecuta();
                        } else {
                            return $this->mensajePermisos('Usuarios');
                        }

                    case 'bienvenido': // El usuario quiere iniciar sesión
                        return 'Bienvenid@ ' . $this->usuario . '<br><br><center><img src="img/codigoBarras.png" alt="' . PROGRAMA . '">' .
                                '<br><label>' . CENTRO . '</label></center><br><br>' . PIE;
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
                    case 'descuadres':
                        if ($this->perfil['Informe']) {
                            $enlace = 'xml/informe' . ucfirst($opcion) . '.xml';
                            $informe = new InformePDF($this->bdd, $enlace, $this->registrado);
                            $informe->crea($enlace);
                            $informe->cierraPDF();
                            $informe->imprimeInforme();
                            return;
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
                            $archivo_sql = "tmp/copiaseg.sql";
                            $archivo = $archivo_sql . ".gz";
                            if (file_exists($archivo)) {
                                unlink($archivo);
                            }
                            $comando = escapeshellcmd(MYSQLDUMP . ' -u ' . USUARIO . ' --password=' . CLAVE . ' --result-file=' . $archivo_sql . ' ' . BASEDATOS);                                                       
                            $comando2 = escapeshellcmd(GZIP . ' -9f ' . $archivo_sql);                                    
                            exec($comando);
                            exec($comando2);
                            if (filesize($archivo) < 1024) {
                                //No se ha realizado la copia de seguridad
                                $mensaje = "La copia de seguridad no se ha realizado correctamente.<br><br>";
                                $mensaje .= "Compruebe que las rutas a los programas mysqldump y gzip en configuraci&oacute;n est&aacute;n correctamente establecidas ";
                                $mensaje .= "y que los datos de acceso a la base de datos sean correctos.<br>";
                                $mensaje .= "mysqldump=[" . MYSQLDUMP . "]<br>";
                                $mensaje .= "gzip=[" . GZIP . "]";
                                $cabecera = "ERROR";
                                $tipo = "danger";
                            } else {
                                $mensaje .= 'Copia de seguridad realizada con &eacute;xito.<br><br>Pulse sobre el siguiente enlace para descargar:<br><br>';
                                $mensaje .= '<a href="' . $archivo . '">Descargar Copia de Seguridad de Datos</a><br>';
                                $cabecera = "Informaci&oacute;n";
                                $tipo = "success";
                            }
                            return $this->panel($cabecera,$mensaje,$tipo);
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

    /**
     *
     * @param string $tipo
     * @return string
     */
    public function mensajePermisos($tipo) {
        return $this->panel("ERROR", "No tiene permiso para acceder a $tipo", "danger");
    }

    public function panel($cabecera, $mensaje, $tipo) {
        $panel = '<div class="panel panel-' . $tipo . '"><div class="panel-heading">';
        $panel .= '<h3 class="panel-title">' . $cabecera . '</h3></div>';
        $panel .= '<div class="panel-body">';
        $panel .= $mensaje;
        $panel .= '</div>';
        return $panel;
    }

}

?>
