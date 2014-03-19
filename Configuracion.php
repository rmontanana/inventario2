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
class Configuracion {
    private $configuracion = "inc/configuracion.inc";
    private $confNueva = "inc/configuracion.new";
    private $confAnterior = "inc/configuracion.ant";
    private $datosConf;
    //Campos del fichero de configuración que se van a editar.
    private $lista = array('SERVIDOR', 'PUERTO', 'BASEDATOS', 'BASEDATOSTEST', 'USUARIO', 'CLAVE', 'CENTRO', 'NUMFILAS', 'ESTILO', 'PLANTILLA', 'COLORLAT', 'COLORFON', 'MYSQLDUMP', 'GZIP');
    private $campos;
    
    public function __construct()
    {
        $this->campos = implode(",", $this->lista);
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
        $filtro = str_replace("'", "", $linea);
        list($clave, $valor) = explode(",", $filtro);
        list($resto, $campo) = explode("(", $clave);
        list($valor, $resto) = explode(")", $valor);
        list($resto, $clave) = explode("(", $clave);
        $valor = trim($valor);
    }
    
    public function ejecuta() {      
        $fichero = $this->obtieneFichero();
        $datos = explode("\n", $fichero);
        $grabar = isset($_POST['SERVIDOR']);
        if ($grabar) {
            $fsalida = @fopen($this->confNueva, "wb");
        }
        foreach ($datos as $linea) {
            if (stripos($linea, "DEFINE") !== false) {
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
                $registro = substr($linea, 0, 2) == "?>" ? $linea : $linea . "\n";
                fwrite($fsalida, $registro);
            }
        }
        $salida.=$this->formulario();
        if ($grabar) {
            $salida.='<p class="bg-primary">Configuraci&oacute;n guardada correctamente</p>';
            fclose($fsalida);
            //unlink($this->confAnterior);
            rename($this->configuracion, $this->confAnterior);
            rename($this->confNueva, $this->configuracion);
            unlink($this->confAnterior);
        }
        return $salida;
    }

    private function formulario() {
        $coloresLateral = array("Original" => "#C4FAEC", "Verde" => "#7bd148", "Azul marino" => "#5484ed", "Azul" => "#a4bdfc", "Turquesa" => "#46d6db",
            "Verde claro" => "#7ae7bf", "Verde oscuro" => "#51b749", "Amarillo" => "#fbd75b", "Naranja" => "#ffb878", "Morado" => "#6633FF",
            "Rojo oscuro" => "#dc2127", "P&uacute;rpura" => "#dbadff", "Gris" => "#e1e1e1");
        $coloresFondo = array("Verde" => "#7bd148", "Azul marino" => "#5484ed", "Azul" => "#a4bdfc", "Turquesa" => "#46d6db",
            "Verde claro" => "#7ae7bf", "Verde oscuro" => "#51b749", "Amarillo" => "#fbd75b", "Naranja" => "#ffb878", "Rojo" => "#ff887c",
            "Rojo oscuro" => "#dc2127", "P&uacute;rpura" => "#dbadff", "Gris" => "#e1e1e1", "Original" => '#F3FEC8');
        $personal = $this->datosConf['ESTILO'] == "personal" ? 'selected' : ' ';
        $bluecurve = $this->datosConf['ESTILO'] == "bluecurve" ? 'selected' : ' ';
        $cristal = $this->datosConf['ESTILO'] == "cristal" ? 'selected' : ' ';
        $normal = $this->datosConf['PLANTILLA'] == "normal" ? 'selected' : ' ';
        $bootstrap = $this->datosConf['PLANTILLA'] == "bootstrap" ? 'selected' : ' ';
        $salida = '<center><div class="col-sm-4 col-md-6"><form name="configura" method="post">';
        //$salida.='<p align="center"><table border=1 class="tablaDatos"><tbody>';
        $salida.='<p align="center"><table border=2 class="table table-hover"><tbody>';
        $salida.='<th colspan=2 class="info"><center><b>Preferencias</b></center></th>';
        $salida.='<tr><td>Nombre del Centro</td><td><input type="text" name="CENTRO" value="' . $this->datosConf['CENTRO'] . '" size="30" /></td></tr>';
        $salida.='<tr><td>N&uacute;mero de filas</td><td><input type="text" name="NUMFILAS" value="' . $this->datosConf['NUMFILAS'] . '" size="3" /></td></tr>';
        $salida.='<tr><td  style="vertical-align:middle">Plantilla</td><td><select name="PLANTILLA" class="form-control">';
        $salida.='<option value="normal" ' . $normal . '>normal</option>';
        $salida.='<option ' . $bootstrap . '>bootstrap</option></select></td></tr>';
        $salida.='<tr><td  style="vertical-align:middle">Estilo</td><td><select name="ESTILO" class="form-control">';
        $salida.='<option value="personal" ' . $personal . '>personal</option>';
        $salida.='<option ' . $bluecurve . '>bluecurve</option>';
        $salida.='<option ' . $cristal . '>cristal</option></select></td></tr>';
        $salida.='<tr><td style="vertical-align:middle">Color Lateral (bootstrap)</td><td style="vertical-align:middle"><select name="COLORLAT" id="COLORLAT" class="form-control">';
        foreach ($coloresLateral as $color => $codigo) {
            $selec = "";
            if (trim($this->datosConf['COLORLAT']) == $codigo) {
                $selec = "selected";
            }
            $salida.='<option value="' . $codigo . '" ' . $selec . ' >' . $color . '</option>';
        }
        $salida.='</select></td></tr>';
        $salida.='<tr><td style="vertical-align:middle">Color Fondo (bootstrap)</td><td style="vertical-align:middle"><select name="COLORFON" id="COLORFON" class="form-control">';
        foreach ($coloresFondo as $color => $codigo) {
            $selec = "";
            if (trim($this->datosConf['COLORFON']) == $codigo) {
                $selec = "selected";
            }
            $salida.='<option value="' . $codigo . '" ' . $selec . ' >' . $color . '</option>';
        }
        $salida.='</select></td></tr>';
        $salida.='<th colspan=2 class="danger"><center><b>Base de datos</b></center></th>';
        $salida.='<tr><td>Servidor</td><td><input type="text" name="SERVIDOR" value="' . $this->datosConf['SERVIDOR'] . '" size="30" /></td></tr>';
        $salida.='<tr><td>Puerto</td><td><input type="text" name="PUERTO" value="' . $this->datosConf['PUERTO'] . '" size="30" /></td></tr>';
        $salida.='<tr><td>Base de datos</td><td><input type="text" name="BASEDATOS" value="' . $this->datosConf['BASEDATOS'] . '" size="30" /></td></tr>';
        $salida.='<tr><td>Base de datos Tests</td><td><input type="text" name="BASEDATOSTEST" value="' . $this->datosConf['BASEDATOSTEST'] . '" size="30" /></td></tr>';
        $salida.='<tr><td>Usuario</td><td><input type="text" name="USUARIO" value="' . $this->datosConf['USUARIO'] . '" size="30" /></td></tr>';
        $salida.='<tr><td>Clave</td><td><input type="text" name="CLAVE" value="' . $this->datosConf['CLAVE'] . '" size="30" /></td></tr>';
        $salida.='<tr><td>mysqldump</td><td><input type="text" name="MYSQLDUMP" value="' . $this->datosConf['MYSQLDUMP'] . '" size="30" /></td></tr>';
        $salida.='<tr><td>gzip</td><td><input type="text" name="GZIP" value="' . $this->datosConf['GZIP'] . '" size="30" /></td></tr>';
        $salida.='<tr align=center><td colspan=2><input type="submit" class="btn btn-primary" align="center" value="Aceptar" name="aceptar" /></td></tr></p>';
        $salida.='</form></div></center>';
        $salida.="<script>
                        $(document).ready(function() {
                            $('select[name=" . '"COLORFON"' . "]').on('change', function() {
                               $(document.body).css('background-color', $('select[name=" . '"COLORFON"' . "]').val());
                               $('.main').css('background-color', $('select[name=" . '"COLORFON"' . "]').val());    
                            });
                            $('select[name=" . '"COLORLAT"' . "]').on('change', function() {
                               $('.sidebar').css('background-color', $('select[name=" . '"COLORLAT"' . "]').val());
                            });
                            $('select[name=" . '"COLORLAT"' . "]').simplecolorpicker({theme: 'glyphicons'});
                            $('select[name=" . '"COLORFON"' . "]').simplecolorpicker({theme: 'glyphicons'});
                        });
                    </script>";
        return $salida;
    }

}

?>