<?php
/**
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
 */
class Configuracion
{
    private $configuracion = 'inc/configuracion.inc';
    private $confNueva = 'inc/configuracion.new';
    private $confAnterior = 'inc/configuracion.ant';
    private $datosConf;
    //Campos del fichero de configuración que se van a editar.
    private $lista = ['SERVIDOR', 'PUERTO', 'BASEDATOS', 'BASEDATOSTEST', 'USUARIO', 'CLAVE', 'CENTRO', 'NUMFILAS', 'ESTILO', 'PLANTILLA', 'COLORLAT', 'COLORFON', 'MYSQLDUMP', 'GZIP', 'TMP'];
    private $campos;

    public function __construct()
    {
        $this->campos = implode(',', $this->lista);
    }

    //Hecho público para poder efectuar los tests correspondientes.
    public function obtieneFichero()
    {
        return file_get_contents($this->configuracion, FILE_TEXT);
    }

    public function obtieneLista()
    {
        return $this->lista;
    }

    public function obtieneDatos($linea, &$clave, &$valor)
    {
        $filtro = str_replace("'", '', $linea);
        list($clave, $valor) = explode(',', $filtro);
        list($resto, $campo) = explode('(', $clave);
        list($valor, $resto) = explode(')', $valor);
        list($resto, $clave) = explode('(', $clave);
        $valor = trim($valor);
    }

    private function creaTitulo($titulo, $ayuda)
    {
        return '<td style="vertical-align:middle"><a class="dato" href="#" data-placement="right" data-content="'.$ayuda.'">'.$titulo.'</a></td>';
    }

    public function ejecuta()
    {
        $fichero = $this->obtieneFichero();
        $datos = explode("\n", $fichero);
        $grabar = isset($_POST['SERVIDOR']);
        if ($grabar) {
            $fsalida = @fopen($this->confNueva, 'wb');
        }
        foreach ($datos as $linea) {
            if (stripos($linea, 'DEFINE') !== false) {
                //Comprueba que tenga una definición correcta
                $this->obtieneDatos($linea, $clave, $valor);
                $this->datosConf[$clave] = $valor;
                if ($grabar && stripos($this->campos, $clave) !== false) {
                    $linea = str_replace($valor, $_POST[$clave], $linea);
                    $this->datosConf[$clave] = $_POST[$clave];
                }
                //$salida = "DatosConf=".var_export($this->datosConf, true) . "stripos = " . stripos($campos, "GZIP");
                //$salida .= "Post=" . var_export($_POST, true);
            }
            if ($grabar) {
                $registro = substr($linea, 0, 2) == '?>' ? $linea : $linea."\n";
                fwrite($fsalida, $registro);
            }
        }
        $salida = $this->formulario();
        if ($grabar) {
            $salida .= '<div class="alert alert-success">Configuraci&oacute;n guardada correctamente</div>';
            fclose($fsalida);
            //unlink($this->confAnterior);
            rename($this->configuracion, $this->confAnterior);
            rename($this->confNueva, $this->configuracion);
            unlink($this->confAnterior);
        }

        return $salida;
    }

    private function formulario()
    {
        $coloresLateral = ['Original' => '#C4FAEC', 'Verde' => '#7bd148', 'Azul marino' => '#5484ed', 'Azul' => '#a4bdfc', 'Turquesa' => '#46d6db',
            'Verde claro'             => '#7ae7bf', 'Verde oscuro' => '#51b749', 'Amarillo' => '#fbd75b', 'Naranja' => '#ffb878', 'Morado' => '#6633FF',
            'Rojo oscuro'             => '#dc2127', 'P&uacute;rpura' => '#dbadff', 'Gris' => '#e1e1e1'];
        $coloresFondo = ['Verde' => '#7bd148', 'Azul marino' => '#5484ed', 'Azul' => '#a4bdfc', 'Turquesa' => '#46d6db',
            'Verde claro'        => '#7ae7bf', 'Verde oscuro' => '#51b749', 'Amarillo' => '#fbd75b', 'Naranja' => '#ffb878', 'Rojo' => '#ff887c',
            'Rojo oscuro'        => '#dc2127', 'P&uacute;rpura' => '#dbadff', 'Gris' => '#e1e1e1', 'Original' => '#F3FEC8'];
        $personal = $this->datosConf['ESTILO'] == 'personal' ? 'selected' : ' ';
        $bluecurve = $this->datosConf['ESTILO'] == 'bluecurve' ? 'selected' : ' ';
        $cristal = $this->datosConf['ESTILO'] == 'cristal' ? 'selected' : ' ';
        $bootst = $this->datosConf['ESTILO'] == 'bootstrap' ? 'selected' : ' ';
        $normal = $this->datosConf['PLANTILLA'] == 'normal' ? 'selected' : ' ';
        $bootstrap = $this->datosConf['PLANTILLA'] == 'bootstrap' ? 'selected' : ' ';
        $salida = '<center><div class="col-sm-4 col-md-8"><form name="configura" method="post">';
        //$salida.='<p align="center"><table border=1 class="tablaDatos"><tbody>';
        $salida .= '<p align="center"><table border=2 class="table table-hover"><tbody>';
        $salida .= '<th colspan=2 class="info"><center><b>Preferencias</b></center></th>';
        $salida .= '<tr>'.$this->creaTitulo('Nombre del Centro', 'Nombre que aparecerá en los informes y en la página principal de la aplicación').'<td><input type="text" name="CENTRO" value="'.$this->datosConf['CENTRO'].'" maxlength="35" size="35" /></td></tr>';
        $salida .= '<tr>'.$this->creaTitulo('Número de filas', 'Número de filas que aparecerán en la pantalla de consulta de los maestros. Valor entre 10 y 25.').'<td><input type="number" max="25" min="10" name="NUMFILAS" value="'.$this->datosConf['NUMFILAS'].'" size="3" /></td></tr>';
        $salida .= '<tr>'.$this->creaTitulo('Plantilla', 'Plantilla html utilizada para mostrar el contenido de la aplicación.').'<td><select name="PLANTILLA" class="form-control">';
        $salida .= '<option value="normal" '.$normal.'>normal</option>';
        $salida .= '<option '.$bootstrap.'>bootstrap</option></select></td></tr>';
        $salida .= '<tr>'.$this->creaTitulo('Estilo', 'Estilo de los botones de control en los mantenimientos de los maestros').'<td><select name="ESTILO" class="form-control">';
        $salida .= '<option value="personal" '.$personal.'>personal</option>';
        $salida .= '<option '.$bluecurve.'>bluecurve</option>';
        $salida .= '<option '.$bootst.'>bootstrap</option>';
        $salida .= '<option '.$cristal.'>cristal</option></select></td></tr>';
        $salida .= '<tr>'.$this->creaTitulo('Color Lateral', 'Color que se aplicará a la parte izquierda de la aplicación donde aparece el menú').'<td style="vertical-align:middle"><select name="COLORLAT" id="COLORLAT" class="form-control">';
        foreach ($coloresLateral as $color => $codigo) {
            $selec = '';
            if (trim($this->datosConf['COLORLAT']) == $codigo) {
                $selec = 'selected';
            }
            $salida .= '<option value="'.$codigo.'" '.$selec.' >'.$color.'</option>';
        }
        $salida .= '</select></td></tr>';
        $salida .= '<tr>'.$this->creaTitulo('Color Fondo', 'Color que aparecerá como fondo en todas las pantallas de la aplicación').'<td style="vertical-align:middle"><select name="COLORFON" id="COLORFON" class="form-control">';
        foreach ($coloresFondo as $color => $codigo) {
            $selec = '';
            if (trim($this->datosConf['COLORFON']) == $codigo) {
                $selec = 'selected';
            }
            $salida .= '<option value="'.$codigo.'" '.$selec.' >'.$color.'</option>';
        }
        $salida .= '</select></td></tr>';
        $salida .= '<tr>'.$this->creaTitulo('Directorio tmp', 'Directorio donde se almacenarán los archivos temporales de la aplicación y también los archivos e informes que genera').'<td><input type="text" name="TMP" value="'.$this->datosConf['TMP'].'" maxlength="35" size="35" /></td></tr>';
        $salida .= '<th colspan=2 class="danger"><center><b>Base de datos</b></center></th>';
        $salida .= '<tr>'.$this->creaTitulo('Servidor', 'Nombre o dirección IP del servidor MySQL. Normalmente localhost').'<td><input type="text" name="SERVIDOR" value="'.$this->datosConf['SERVIDOR'].'" maxlength="35" size="35" /></td></tr>';
        $salida .= '<tr>'.$this->creaTitulo('Puerto', 'Número de puerto donde el servidor admite conexiones MySQL. Normalmente 3306').'<td><input type="text" name="PUERTO" value="'.$this->datosConf['PUERTO'].'" maxlength="35" size="35" /></td></tr>';
        $salida .= '<tr>'.$this->creaTitulo('Base de datos', 'Nombre de la base de datos donde se almacenarán los datos de la aplicación').'<td><input type="text" name="BASEDATOS" value="'.$this->datosConf['BASEDATOS'].'" maxlength="35" size="35" /></td></tr>';
        $salida .= '<tr>'.$this->creaTitulo('Base de datos Tests', 'Nombre de la base de datos donde se almacenarán los datos de prueba de la aplicación').'<td><input type="text" name="BASEDATOSTEST" value="'.$this->datosConf['BASEDATOSTEST'].'" maxlength="35" size="35" /></td></tr>';
        $salida .= '<tr>'.$this->creaTitulo('Usuario', 'Usuario con permisos de lectura/escritura en la base de datos').'<td><input type="text" name="USUARIO" value="'.$this->datosConf['USUARIO'].'" maxlength="35" size="35" /></td></tr>';
        $salida .= '<tr>'.$this->creaTitulo('Clave', 'Contraseña del usuario con permisos sobre la base de datos').'<td><input type="text" name="CLAVE" value="'.$this->datosConf['CLAVE'].'" maxlength="35" size="35" /></td></tr>';
        $salida .= '<tr>'.$this->creaTitulo('mysqldump', 'Ruta completa a la utilidad mysqldump. Este programa es necesario para que se puedan hacer las copias de seguridad de la aplicación').'<td><input type="text" name="MYSQLDUMP" value="'.$this->datosConf['MYSQLDUMP'].'" maxlength="35" size="35" /></td></tr>';
        $salida .= '<tr>'.$this->creaTitulo('gzip', 'Ruta completa a la utilidad gzip. Este programa es necesario para que se puedan comprimir las copias de seguridad de la aplicación').'<td><input type="text" name="GZIP" value="'.$this->datosConf['GZIP'].'" maxlength="35" size="35" /></td></tr>';
        $salida .= '<tr align=center><td colspan=2>
            <a class="btn btn-info" role="button" onClick="location.href='."'index.php'".'"><span class="glyphicon glyphicon-arrow-left"></span> Volver</a>
            <button type="submit" class="btn btn-primary" name="aceptar"><span class="glyphicon glyphicon-ok"></span> Aceptar</td></tr></p>';
        $salida .= '</form></div></center>';
        $salida .= "<script>
                        $(document).ready(function() {
                            $('select[name=".'"COLORFON"'."]').on('change', function() {
                               $(document.body).css('background-color', $('select[name=".'"COLORFON"'."]').val());
                               $('.main').css('background-color', $('select[name=".'"COLORFON"'."]').val());    
                            });
                            $('select[name=".'"COLORLAT"'."]').on('change', function() {
                               $('.sidebar').css('background-color', $('select[name=".'"COLORLAT"'."]').val());
                            });
                            $('select[name=".'"COLORLAT"'."]').simplecolorpicker({theme: 'glyphicons'});
                            $('select[name=".'"COLORFON"'."]').simplecolorpicker({theme: 'glyphicons'});
                            $('.dato').popover({trigger: 'hover'});
                        });
                    </script>";

        return $salida;
    }
}
