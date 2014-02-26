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
        private $colorLat;
        private $colorCen;
        
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
                            $this->colorLat=$valor;
                            if ($grabar) {
                                $linea=str_replace($valor,$POST['COLORLAT'],$linea);
                                $this->colorLat=$_POST['COLORLAT'];
                            }
                        case 'COLORCEN':
                            $this->colorCen=$valor;
                            if ($grabar) {
                                $linea=str_replace($valor,$POST['COLORCEN'],$linea);
                                $this->colorCen=$_POST['COLORCEN'];
                            }
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
            $personal=$this->estilo=="personal"?'selected':' ';
            $bluecurve=$this->estilo=="bluecurve"?'selected':' ';
            $cristal=$this->estilo=="cristal"?'selected':' ';
            $normal=$this->plantilla=="normal"? 'selected':' ';
            $bootstrap=$this->plantilla=="bootstrap" ? 'selected':' '; 
            $salida='<center><div class="col-sm-2 col-md-6"><form name="configura" method="post">';
            //$salida.='<p align="center"><table border=1 class="tablaDatos"><tbody>';
            $salida.='<p align="center"><table border=2 class="table table-hover"><tbody>';
            $salida.='<th colspan=2 class="info"><center><b>Preferencias</b></center></th>';
            $salida.='<tr><td>Nombre del Centro</td><td><input type="text" name="centro" value="'.$this->nombreCentro.'" size="30" /></td></tr>';
            $salida.='<tr><td>N&uacute;mero de filas</td><td><input type="text" name="filas" value="'.$this->numFilas.'" size="3" /></td></tr>';
            $salida.='<tr><td>Plantilla</td><td><select name="plantilla">';
            $salida.='<option value="normal" '.$normal.'>normal</option>';
            $salida.='<option '.$bootstrap.'>bootstrap</option></select></td></tr>';
            $salida.='<tr><td>Estilo</td><td><select name="estilo">';
            $salida.='<option value="personal" '.$personal.'>personal</option>';
            $salida.='<option '.$bluecurve.'>bluecurve</option>';
            $salida.='<option '.$cristal.'>cristal</option></select></td></tr>';
            $salida.='<select name="colorpicker-bootstrap3-form" id="colorpicker-bootstrap3-form" class="form-control">
        <option value="#7bd148">Green</option>
        <option value="#5484ed">Bold blue</option>
        <option value="#a4bdfc">Blue</option>
        <option value="#46d6db">Turquoise</option>
        <option value="#7ae7bf">Light green</option>
        <option value="#51b749">Bold green</option>
        <option value="#fbd75b">Yellow</option>
        <option value="#ffb878">Orange</option>
        <option value="#ff887c">Red</option>
        <option value="#dc2127">Bold red</option>
        <option value="#dbadff">Purple</option>
        <option value="#e1e1e1">Gray</option>
      </select>'
            $salida.='<th colspan=2 class="danger"><center><b>Base de datos</b></center></th>';
            $salida.='<tr><td>Servidor</td><td><input type="text" name="servidor" value="'.$this->servidor.'" size="30" /></td></tr>';
            $salida.='<tr><td>Base de datos</td><td><input type="text" name="baseDatos" value="'.$this->baseDatos.'" size="30" /></td></tr>';
            $salida.='<tr><td>Usuario</td><td><input type="text" name="usuario" value="'.$this->usuario.'" size="30" /></td></tr>';
            $salida.='<tr><td>Clave</td><td><input type="text" name="clave" value="'.$this->clave.'" size="30" /></td></tr>';
            $salida.='<tr align=center><td colspan=2><input type="submit" class="btn btn-primary" align="center" value="Aceptar" name="aceptar" /></td></tr></p>';
            $salida.='</form></div></center>';
            return $salida;
        }
    }
?>
<h3>Bootstrap 3 form</h3>
<form class="form-horizontal">
  <div class="form-group">
    <label class="col-sm-4 control-label" for="input01">Text input</label>
    <div class="col-sm-6">
      <input type="text" class="form-control" id="input01">
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-4 col-sm-6">
      <div class="checkbox">
        <label>
          <input type="checkbox"> Option one
        </label>
      </div>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-4 control-label" for="colorpicker-bootstrap3-form">colorpicker-bootstrap3-form</label>
    <div class="col-sm-6">
      <select name="colorLat" id="colorLat" class="form-control">
        <option value="#7bd148">Green</option>
        <option value="#5484ed">Bold blue</option>
        <option value="#a4bdfc">Blue</option>
        <option value="#46d6db">Turquoise</option>
        <option value="#7ae7bf">Light green</option>
        <option value="#51b749">Bold green</option>
        <option value="#fbd75b">Yellow</option>
        <option value="#ffb878">Orange</option>
        <option value="#ff887c">Red</option>
        <option value="#dc2127">Bold red</option>
        <option value="#dbadff">Purple</option>
        <option value="#e1e1e1">Gray</option>
      </select>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-4 control-label" for="multiSelect">Multicon-select</label>
    <div class="col-sm-6">
      <select multiple id="multiSelect" class="form-control">
        <option>1</option>
        <option>2</option>
        <option>3</option>
        <option>4</option>
        <option>5</option>
      </select>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-4 control-label" for="fileInput">File input</label>
    <div class="col-sm-6">
      <input id="fileInput" type="file">
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-4 col-sm-6">
      <button type="submit" class="btn btn-primary">Save changes</button>
      <button class="btn btn-default">Cancel</button>
    </div>
  </div>
</form>



<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.js"></script>
<script src="http://netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.js"></script>
<script src="jquery.simplecolorpicker.js"></script>

<script>
$(document).ready(function() {
  $('select[name="colorLat"]').on('change', function() {
    $(document.body).css('background-color', $('select[name="colorpicker-change-background-color"]').val());
  });

  
 

  $('#init').on('click', function() {
    
    $('select[name="colorLat"]').simplecolorpicker({theme: 'glyphicons'});
    
  });

  $('#destroy').on('click', function() {
    $('select').simplecolorpicker('destroy');
  });

  // By default, activate simplecolorpicker plugin on HTML selects
  $('#init').trigger('click');
});
</script>

</body>
</html>
