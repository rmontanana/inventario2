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
class InformeInventario {

    private $bdd;

    public function __construct($baseDatos)
    {
        $this->bdd = $baseDatos;
    }

    public function ejecuta()
    {
        $opc = $_GET['opc'];
        switch ($opc) {
            case 'Ubicacion':return $this->formularioUbicacion();
            case 'listarUbicacion':return $this->listarUbicacion();
            case 'listarArticulo':return $this->listarArticulo();
            case 'listarTotal': return $this->listarTotal();
            case 'Articulo':return $this->formularioArticulo();
            case 'Total':return $this->inventarioTotal();
            case 'descuadres': return $this->inventarioDescuadres();
        }
    }

    private function inventarioDescuadres()
    {
        $enlace = 'xml/informeDescuadres.xml';
        $informe = new InformePDF($this->bdd, $enlace, true);
        $informe->crea($enlace);
        $informe->cierraPDF();
        return $this->devuelveInforme($informe);
    }

    private function devuelveInforme($informe)
    {
        $letras = "abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
        $nombre = "tmp/informe" . substr(str_shuffle($letras), 0, 10) . ".pdf";
        $informe->guardaArchivo($nombre);
        return '<div class="container">
                    <!--<a href="' . $nombre . '" target="_blank"><span class="glyphicon glyphicon-cloud-download" style="font-size:1.5em;"></span>Descargar Informe</a>--> 
                    <object data="' . $nombre . '" type="application/pdf" width="100%" height="700" style="float:left;">
                    </object>
                </div>';
    }
    
    private function listarUbicacion()
    {
        $salidaInforme = isset($_POST['salida']) ? $_POST['salida'] : 'pantalla';
        switch ($salidaInforme) {
            case "pantalla":
                $fichero = "xml/inventarioUbicacion.xml";
                $salida = "tmp/inventarioUbicacion.xml";
                break;
            case "csv":
                $fichero = "xml/inventarioUbicacionCSV.xml";
                $salida = "tmp/inventarioUbicacionCSV.xml";
                break;
            case "etiquetas":
                $fichero = "xml/inventarioUbicacionEtiquetas.xml";
                $salida = "tmp/inventarioUbicacionEtiquetas.xml";
                break;
        }
        $plantilla = file_get_contents($fichero) or die('Fallo en la apertura de la plantilla ' . $fichero);
        $id = $_POST['id'] == NULL ? $_GET['id'] : $_POST['id'];
        $comando = "select * from Ubicaciones where id='" . $id . "';";
        $resultado = $this->bdd->ejecuta($comando);
        if (!$resultado) {
            return $this->bdd->mensajeError($comando);
        }
        $fila = $this->bdd->procesaResultado();
        $plantilla = str_replace("{id}", $id, $plantilla);
        $plantilla = str_replace("{Descripcion}", $fila['Descripcion'], $plantilla);
        file_put_contents($salida, $plantilla) or die('Fallo en la escritura de la plantilla ' . $salida);
        switch ($salidaInforme) {
            case "pantalla":
                $informe = new InformePDF($this->bdd, $salida, true);
                $informe->crea($salida);
                $informe->cierraPDF();
                return $this->devuelveInforme($informe);
            case "csv":
                //Genera una hoja de cálculo en formato csv
                $nombre = "tmp/Ubicacion" . strftime("%Y%m%d") . rand(100, 999) . ".csv";
                $hoja = new Csv($this->bdd);
                $hoja->crea($nombre);
                $hoja->ejecutaConsulta($salida);
                echo '<script type="text/javascript"> window.open( "' . $nombre . '" ) </script>';
                break;
            case "etiquetas":
                $etiquetas = new EtiquetasPDF($this->bdd, $salida, true);
                $etiquetas->crea($salida);
                $etiquetas->cierraPDF();
                return $this->devuelveInforme($etiquetas);
        }
    }

    private function listarArticulo()
    {
        $salidaInforme = isset($_POST['salida']) ? $_POST['salida'] : 'pantalla';
        switch ($salidaInforme) {
            case "pantalla":
                $fichero = "xml/inventarioArticulo.xml";
                $salida = "tmp/inventarioArticulo.xml";
                break;
            case "csv":
                $fichero = "xml/inventarioArticuloCSV.xml";
                $salida = "tmp/inventarioArticuloCSV.xml";
                break;
            case "etiquetas":
                $fichero = "xml/inventarioArticuloEtiquetas.xml";
                $salida = "tmp/inventarioArticuloEtiquetas.xml";
                break;
        }
        $plantilla = file_get_contents($fichero) or die('Fallo en la apertura de la plantilla ' . $fichero);
        $id = $_POST['id'] == NULL ? $_GET['id'] : $_POST['id'];
        $comando = "select * from Articulos where id='" . $id . "';";
        $resultado = $this->bdd->ejecuta($comando);
        if (!$resultado) {
            return $this->bdd->mensajeError($comando);
        }
        $fila = $this->bdd->procesaResultado();
        $plantilla = str_replace("{id}", $id, $plantilla);
        $plantilla = str_replace("{Descripcion}", $fila['descripcion'], $plantilla);
        $plantilla = str_replace("{Marca}", $fila['marca'], $plantilla);
        $plantilla = str_replace("{Modelo}", $fila['modelo'], $plantilla);
        file_put_contents($salida, $plantilla) or die('Fallo en la escritura de la plantilla ' . $salida);
        switch ($salidaInforme) {
            case "pantalla":
                $informe = new InformePDF($this->bdd, $salida, true);
                $informe->crea($salida);
                $informe->cierraPDF();
                return $this->devuelveInforme($informe);
            case "csv":
                //Genera una hoja de cálculo en formato csv
                $nombre = "tmp/Articulo" . strftime("%Y%m%d") . rand(100, 999) . ".csv";
                $hoja = new Csv($this->bdd);
                $hoja->crea($nombre);
                $hoja->ejecutaConsulta($salida);
                echo '<script type="text/javascript"> window.open( "' . $nombre . '" ) </script>';
                break;
            case "etiquetas":
                $etiquetas = new EtiquetasPDF($this->bdd, $salida, true);
                $etiquetas->crea($salida);
                $etiquetas->cierraPDF();
                return $this->devuelveInforme($etiquetas);
        }
    }

    private function listaUbicaciones()
    {
        $salida = "<select class=\"selectpicker show-tick\" name=\"id\" data-live-search=\"true\" data-width=\"auto\">\n";
        $comando = "select * from Ubicaciones order by Descripcion";
        $resultado = $this->bdd->ejecuta($comando);
        if (!$resultado) {
            return $this->bdd->mensajeError($comando);
        }
        while ($fila = $this->bdd->procesaResultado()) {
            $salida.="<option value=" . $fila['id'] . ">" . $fila['Descripcion'] . "</option><br>\n";
        }
        $salida.="</select>\n";
        return $salida;
    }

    private function listaArticulos()
    {
        $salida = "<select class=\"selectpicker show-tick\" name=\"id\" data-live-search=\"true\" data-width=\"auto\">\n";
        $comando = "select * from Articulos order by descripcion, marca, modelo";
        $resultado = $this->bdd->ejecuta($comando);
        if (!$resultado) {
            return $this->bdd->mensajeError($comando);
        }
        while ($fila = $this->bdd->procesaResultado()) {
            $salida.="<option value=" . $fila['id'] . ">" . $fila['descripcion'] . "-" . $fila['marca'] . "-" . $fila['modelo'] . "</option><br>\n";
        }
        $salida.="</select>\n";
        return $salida;
    }

    private function formulario($accion, $etiqueta, $lista)
    {
        $salida = '<div class="col-sm-6 col-md-6"><form name="informeInventario.form" method="post" action="' . $accion . '">' . "\n";
        $salida.="<fieldset style=\"width: 96%;\"><p><legend style=\"color: red;\"><b>Elige $etiqueta</b></legend>\n";
        $salida.="<br><br><label>$etiqueta </label>";
        $salida.=$lista;
        $salida.="<br><br>
            <label for='salida'>Salida del informe por:</label>";
        $salida.='<div class="radio"><label><input type="radio" name="salida" value="pantalla" checked><span class="glyphicon glyphicon-list-alt"></span> Pantalla</label></div>';
        $salida.='<div class="radio"><label><input type="radio" name="salida" value="csv"><span class="glyphicon glyphicon-cloud-download"></span> Archivo CSV</label></div>';
        $salida.='<div class="radio"><label><input type="radio" name="salida" value="etiquetas"><span class="glyphicon glyphicon-qrcode"></span> Etiquetas (<a target="_new" href="http://www.apli.es/producto/ficha_producto.aspx?referencia=01275&stype=referencia&referenciaValue=01275&q=01275">Apli 1275</a>)</label></div>';
        $salida.="<br><br></fieldset><p>";
        $salida.='<p align="center"><button type=submit class="btn btn-primary"><span class="glyphicon glyphicon-ok"></span> Aceptar</button></p><br></div>' . "\n";
        $salida.="<script>$('.selectpicker').selectpicker();</script>";
        return $salida;
    }

    private function formularioUbicacion()
    {
        //Genera un formulario con las ubicaciones disponibles.
        $accion = "index.php?informeInventario&opc=listarUbicacion";
        return $this->formulario($accion, 'Ubicaci&oacute;n', $this->listaUbicaciones());
    }

    private function formularioArticulo()
    {
        $accion = "index.php?informeInventario&opc=listarArticulo";
        return $this->formulario($accion, 'Art&iacute;culo', $this->listaArticulos());
    }
    
    private function inventarioTotal()
    {
        return $this->dialogo();
    }

    private function dialogo()
    {
        $dialogo = '<div class="container col-5"><div class="jumbotron">
                      <h1>Inventario Total</h1>
                      <p>¿Desea obtener el inventario de todo el centro?</p>
                      <p><a class="btn btn-primary btn-lg" role="button" onClick="location.href=' . "'index.php'" . '"><span class="glyphicon glyphicon-arrow-left"></span> Volver</a>
                         <a class="btn btn-success btn-lg" role="button" onClick="location.href=' . "'index.php?informeInventario&opc=listarTotal'" . '">
                             <span class="glyphicon glyphicon-cloud-download"></span> Continuar</a></p>
                    </div></div>';
        return $dialogo;
    }
    
    private function listarTotal()
    {
        $fichero = "xml/inventarioUbicacion.xml";
        $salida = "tmp/inventarioUbicacion.xml";
        $comando = "select * from Ubicaciones ;";
        $resultado = $this->bdd->ejecuta($comando);
        if (!$resultado) {
            return $this->bdd->mensajeError($comando);
        }
        //Utiliza un nuevo manejador de base de datos para poder hacer una consulta en los informes
        $bdatos = new Sql(SERVIDOR, USUARIO, CLAVE, BASEDATOS);
        $primero = true;
        while ($fila = $this->bdd->procesaResultado()) {
            $plantilla = file_get_contents($fichero) or die('Fallo en la apertura de la plantilla ' . $fichero);
            $plantilla = str_replace("{id}", $fila['id'], $plantilla);
            $plantilla = str_replace("{Descripcion}", $fila['Descripcion'], $plantilla);
            file_put_contents($salida, $plantilla) or die('Fallo en la escritura de la plantilla ' . $salida);
            if ($primero) {
                $primero = false;
                $informe = new InformePDF($bdatos, $salida, true);
            }
            $informe->crea($salida);
        }
        $informe->cierraPDF();
        return $this->devuelveInforme($informe);
    }

}

?>
