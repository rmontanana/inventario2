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
        private $nombreCentro;
        private $numFilas;
        private $estilo;
        private $servidor;
        private $baseDatos;
        private $usuario;
        private $clave;
        private $configuracion="inc/configuracion.inc";
        private $confNueva="inc/configuracion.new";
        private $confAnterior="inc/configuracion.ant";
        private $plantilla;
        private $colorLateral;
        private $colorFondo;
        
        public function ejecuta()
        {
            $fichero=file_get_contents($this->configuracion,FILE_TEXT);
            $datos=explode("\n",$fichero);
            $grabar=isset($_POST['servidor']);
            if ($grabar) {
                $fsalida=@fopen($this->confNueva,"wb");
            }
            foreach($datos as $linea) {
                if (stripos($linea,"DEFINE")!==false) {
                    $filtro=str_replace("'","",$linea);
                    list($clave,$valor)=explode(",",$filtro);
                    list($resto,$campo)=explode("(",$clave);
                    list($valor,$resto)=explode(")",$valor);
                    //$salida.="[$campo]=[$valor]<br>\n";
                    switch ($campo) {
                        case 'CENTRO':
                            $this->nombreCentro=$valor;
                            if ($grabar) {
                                $linea=str_replace($valor, $_POST['centro'],$linea);
                                $this->nombreCentro=$_POST['centro'];
                            }
                            break;
                        case 'NUMFILAS':
                            $this->numFilas=$valor;
                              if ($grabar) {
                                $linea=str_replace($valor, $_POST['filas'],$linea);
                                $this->numFilas=$_POST['filas'];
                            }
                            break;
                        case 'ESTILO':
                            $this->estilo=$valor;
                              if ($grabar) {
                                $linea=str_replace($valor, $_POST['estilo'],$linea);
                                $this->estilo=$_POST['estilo'];
                            }
                            break;
                        case 'PLANTILLA':
                            $this->plantilla=$valor;
                            if ($grabar) {
                                $linea=str_replace($valor, $_POST['plantilla'],$linea);
                                $this->plantilla=$_POST['plantilla'];
                            }
                            break;
                        case 'SERVIDOR':
                            $this->servidor=$valor;
                              if ($grabar) {
                                $linea=str_replace($valor, $_POST['servidor'],$linea);
                                $this->servidor=$_POST['servidor'];
                            }
                            break;
                        case 'BASEDATOS':
                            $this->baseDatos=$valor;
                              if ($grabar) {
                                $linea=str_replace($valor, $_POST['baseDatos'],$linea);
                                $this->baseDatos=$_POST['baseDatos'];
                            }
                            break;
                        case 'USUARIO':
                            $this->usuario=$valor;
                             if ($grabar) {
                                $linea=str_replace($valor, $_POST['usuario'],$linea);
                                $this->usuario=$_POST['usuario'];
                            } 
                            break;
                        case 'CLAVE':
                            $this->clave=$valor;
                              if ($grabar) {
                                $linea=str_replace($valor, $_POST['clave'],$linea);
                                $this->clave=$_POST['clave'];
                            }
                            break;
                        case 'COLORLAT':
                            $valor = trim($valor);
                            $this->colorLateral = $valor;
                            if ($grabar) {
                                $linea=str_replace($valor,$_POST['colorLat'],$linea);
                                $this->colorLateral=$_POST['colorLat'];
                            }
                            break;
                        case 'COLORFON':
                            $valor = trim($valor);
                            $this->colorFondo = $valor;
                            if ($grabar) {
                                $linea=str_replace($valor,$_POST['colorFon'],$linea);
                                $this->colorFondo=$_POST['colorFon'];
                            }
                            break;
                    }
                }
                if ($grabar) {
                    $registro=substr($linea,0,2)=="?>"?$linea:$linea."\n";
                    fwrite($fsalida,$registro);
                }             
            }
            $salida.=$this->formulario();
            if ($grabar) {
                //$salida.='<label class="warn">Configuraci&oacute;n guardada correctamente</label>';
                $salida.='<p class="bg-primary">Configuraci&oacute;n guardada correctamente</p>';
                fclose($fsalida);
                unlink($this->confAnterior);
                rename($this->configuracion,$this->confAnterior);
                rename($this->confNueva,$this->configuracion);                
            }
            return $salida;
        }
        private function formulario()
        {
            $coloresLateral = array( "Original" => "#C4FAEC", "Verde" => "#7bd148", "Azul marino" => "#5484ed", "Azul" => "#a4bdfc", "Turquesa" => "#46d6db",
                            "Verde claro" => "#7ae7bf", "Verde oscuro" => "#51b749", "Amarillo" => "#fbd75b", "Naranja" => "#ffb878", "Morado" => "#6633FF",
                            "Rojo oscuro" => "#dc2127", "P&uacute;rpura" => "#dbadff", "Gris" => "#e1e1e1");
            $coloresFondo = array( "Verde" => "#7bd148", "Azul marino" => "#5484ed", "Azul" => "#a4bdfc", "Turquesa" => "#46d6db",
                            "Verde claro" => "#7ae7bf", "Verde oscuro" => "#51b749", "Amarillo" => "#fbd75b", "Naranja" => "#ffb878", "Rojo" => "#ff887c",
                            "Rojo oscuro" => "#dc2127", "P&uacute;rpura" => "#dbadff", "Gris" => "#e1e1e1", "Original" => '#F3FEC8');
            $personal=$this->estilo=="personal"?'selected':' ';
            $bluecurve=$this->estilo=="bluecurve"?'selected':' ';
            $cristal=$this->estilo=="cristal"?'selected':' ';
            $normal=$this->plantilla=="normal"? 'selected':' ';
            $bootstrap=$this->plantilla=="bootstrap" ? 'selected':' '; 
            $salida='<center><div class="col-sm-4 col-md-6"><form name="configura" method="post">';
            //$salida.='<p align="center"><table border=1 class="tablaDatos"><tbody>';
            $salida.='<p align="center"><table border=2 class="table table-hover"><tbody>';
            $salida.='<th colspan=2 class="info"><center><b>Preferencias</b></center></th>';
            $salida.='<tr><td>Nombre del Centro</td><td><input type="text" name="centro" value="'.$this->nombreCentro.'" size="30" /></td></tr>';
            $salida.='<tr><td>N&uacute;mero de filas</td><td><input type="text" name="filas" value="'.$this->numFilas.'" size="3" /></td></tr>';
            $salida.='<tr><td  style="vertical-align:middle">Plantilla</td><td><select name="plantilla" class="form-control">';
            $salida.='<option value="normal" '.$normal.'>normal</option>';
            $salida.='<option '.$bootstrap.'>bootstrap</option></select></td></tr>';
            $salida.='<tr><td  style="vertical-align:middle">Estilo</td><td><select name="estilo" class="form-control">';
            $salida.='<option value="personal" '.$personal.'>personal</option>';
            $salida.='<option '.$bluecurve.'>bluecurve</option>';
            $salida.='<option '.$cristal.'>cristal</option></select></td></tr>';
            $salida.='<tr><td style="vertical-align:middle">Color Lateral (bootstrap)</td><td style="vertical-align:middle"><select name="colorLat" id="colorLat" class="form-control">';
            foreach ($coloresLateral as $color => $codigo) {
                $selec = "";
                if ($this->colorLateral == $codigo) {
                    $selec = "selected";
                }
                $salida.='<option value="'.$codigo.'" '.$selec.' >'.$color.'</option>';
            }
            $salida.='</select></td></tr>';
            $salida.='<tr><td style="vertical-align:middle">Color Fondo (bootstrap)</td><td style="vertical-align:middle"><select name="colorFon" id="colorFon" class="form-control">';
            foreach ($coloresFondo as $color => $codigo) {
                $selec = "";
                if ($this->colorFondo == $codigo) {
                    $selec = "selected";
                }
                $salida.='<option value="'.$codigo.'" '.$selec.' >'.$color.'</option>';
            }
            $salida.='</select></td></tr>';
            $salida.='<th colspan=2 class="danger"><center><b>Base de datos</b></center></th>';
            $salida.='<tr><td>Servidor</td><td><input type="text" name="servidor" value="'.$this->servidor.'" size="30" /></td></tr>';
            $salida.='<tr><td>Base de datos</td><td><input type="text" name="baseDatos" value="'.$this->baseDatos.'" size="30" /></td></tr>';
            $salida.='<tr><td>Usuario</td><td><input type="text" name="usuario" value="'.$this->usuario.'" size="30" /></td></tr>';
            $salida.='<tr><td>Clave</td><td><input type="text" name="clave" value="'.$this->clave.'" size="30" /></td></tr>';
            $salida.='<tr align=center><td colspan=2><input type="submit" class="btn btn-primary" align="center" value="Aceptar" name="aceptar" /></td></tr></p>';
            $salida.='</form></div></center>';
            $salida.="<script>
                        $(document).ready(function() {
                            $('select[name=" .'"colorFon"'. "]').on('change', function() {
                               $(document.body).css('background-color', $('select[name=" .'"colorFon"'. "]').val());
                               $('.main').css('background-color', $('select[name=" .'"colorFon"'. "]').val());    
                            });
                            $('select[name=" .'"colorLat"'. "]').on('change', function() {
                               $('.sidebar').css('background-color', $('select[name=" .'"colorLat"'. "]').val());
                            });
                            $('select[name=". '"colorLat"' ."]').simplecolorpicker({theme: 'glyphicons'});
                            $('select[name=" .'"colorFon"' ."]').simplecolorpicker({theme: 'glyphicons'});
                        });
                    </script>";
            //$salida.="<br>fondo=[$this->colorFondo] lateral=[$this->colorLateral]<br>";
            return $salida;
        }
    }
?>