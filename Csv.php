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
class Csv {

    /**
     * 
     * @var String Nombre del fichero csv
     */
    private $nombre;

    /**
     * @var FILE manejador del fichero
     */
    private $fichero = NULL;

    /**
     * @var xml conulta asociada a este fichero
     */
    private $consulta;

    /**
     * @var database Controlador de la base de datos
     */
    private $bdd;

    /**
     *
     * @var int Número de registros en el fichero csv
     */
    private $numRegistros;

    /**
     * @var array Cabecera del archivo csv
     */
    private $cabecera;

    /**
     * @var array los datos del fichero csv en memoria
     */
    private $datosFichero;

    /**
      // El constructor necesita saber cuál es la opción actual
      /**
     * Constructor de la clase. 
     * @param BaseDatos $baseDatos Manejador de la base de datos
     */
    public function __construct($baseDatos) {
        $this->bdd = $baseDatos;
    }

    /**
     * Crea un fichero csv con el nombre especificado
     * @param String $fichero Nombre del fichero
     */
    public function crea($fichero) {
        $this->nombre = $fichero;
        $this->fichero = fopen($this->nombre, "w") or die("No puedo abrir " . $this->nombre . " para escritura.");
    }

    /**
     * 
     * @param array $datos escribe la línea en el archivo
     */
    public function escribeLinea($datos) {
        fputcsv($this->fichero, $datos, ',', '"') or die("No puedo escribir en el fichero csv");
    }

    public function __destruct() {
        $this->cierra();
    }

    public function cierra() {
        fclose($this->fichero) or die("No puedo cerrar el archivo csv");
    }

    /**
     * 
     * @param String $fichero Archivo xml que contiene la definición de la consulta
     */
    public function ejecutaConsulta($fichero) {
        $consulta = simplexml_load_file($fichero) or die("No puedo cargar el fichero xml " . $fichero . " al csv");
        // Escribe la cabecera del fichero
        $this->escribeLinea(array($consulta->Pagina->Cabecera, $consulta->Titulo['id'], $consulta->Titulo['Texto']));
        foreach ($consulta->Pagina->Cuerpo->Col as $campo) {
            $campos[] = utf8_decode($campo['Titulo']);
        }
        $this->escribeLinea($campos);
        // Escribe los datos de los campos
        $this->bdd->ejecuta($consulta->Datos->Consulta);
        while ($fila = $this->bdd->procesaResultado()) {
            $campos = array();
            foreach ($consulta->Pagina->Cuerpo->Col as $campo) {
                $campos[] = $fila[(string) $campo['Nombre']];
            }
            $this->escribeLinea($campos);
        }
    }

    /**
     * 
     * @param String $ficheroCSV Nombre del archivo csv
     */
    public function cargaCSV($ficheroCSV) {
        $this->nombre = $ficheroCSV;
        $this->fichero = fopen($this->nombre, "r") or die('No puedo abrir el archivo ' . $this->nombre . " para lectura.");
        list($archivo, $idCabecera, $cabecera) = fgetcsv($this->fichero);
        while ($linea = fgetcsv($this->fichero)) {
            $datosFichero[] = $linea;
        }
        $this->cabecera[] = $archivo;
        $this->cabecera[] = $idCabecera;
        $this->cabecera[] = $cabecera;
        $this->datosFichero = $datosFichero;
    }

    /**
     * Muestra un resumen de los datos del fichero csv cargado por pantalla
     * 
     */
    public function resumen() {
        //$mensaje .=
        $mensaje = "<center><h1>Archivo [inventario" . utf8_decode($this->cabecera[0]) . "]</h1>";
        $mensaje .= "<h2>id=[" . $this->cabecera[1] . "] Descripci&oacute;n=[" . utf8_decode($this->cabecera[2]) . "]</h2><br>";
        $mensaje .= '<table border=1 class="table table-striped table-bordered table-condensed table-hover"><theader>';
        foreach ($this->datosFichero[0] as $campo) {
            $dato = $campo;
            $mensaje .= "<th><b>$dato</b></th>";
        }
        $mensaje .= "<th><b>Acci&oacute;n</b></th>";
        $mensaje .="</theader><tbody>";
        //echo "$mensaje contar Datosfichero=[".count($datosFichero)."]";
        try {
            for ($i = 1; $i < count($this->datosFichero); $i++) {
                $mensaje .= "<tr>";
                $primero = true;
                foreach ($this->datosFichero[$i] as $dato) {
                    if ($primero) {
                        $primero = false;
                        switch ($dato) {
                            case 'S': $estado = "-Baja-";
                                $color = "danger";
                                break;
                            case 'Alta': $estado = "-Alta-";
                                $color = "primary";
                                break;
                            case "N" : $estado = $this->compruebaCantidades($this->datosFichero[$i]);
                                if ($estado != 0) {
                                    $color = "warning";
                                    if ($estado > 0) {
                                        $estado = "+".$estado;
                                    }
                                } else {
                                    $estado = "igual";
                                    $color = "info";
                                }
                                break;
                            default: throw new Exception("El archivo csv tiene un formato incorrecto.<br>Bajas=[$dato]");
                        }
                    }
                    $mensaje .= "<td>" . $dato . "</td>";
                }
                $mensaje .= '<td align="center"><label class="label label-' . $color . '">' . $estado . '</label></td>';
                $mensaje .= "</tr>";
            }
        } catch (Exception $e) {
            $mensaje = "Se ha producido el error [" . $e->getMessage() . "]<br>NO se ha realizado ning&uacute;n cambio en la Base de Datos.";
            return $mensaje;
        }
        $mensaje .= "</tbody></table></p><br>";
        $mensaje .= $this->panelMensaje('Si se produce cualquier error en el procesamiento del fichero, no se aplicar&aacute; ning&uacute;n cambio en la base de datos.');
        
        $mensaje .= '<form method="post" name="Aceptar" action="index.php?importacion&opc=ejecutar">
                <input type="button" name="Cancelar" value="Cancelar" onClick="location.href=' . "'index.php'" . '" class="btn btn-danger">
                <input type="submit" name="Aceptar" value="Aceptar" class="btn btn-primary">
                <input type="hidden" name="ficheroCSV" value="' . $this->nombre . '">
                </form></center>';

        return $mensaje;
    }

    /**
     * 
     * @param $array línea de datos del fichero csv para comprobar las cantidades si se han modificado o no 
     * @return string
     */
    private function compruebaCantidades($datos) {
        $ultimo = count($datos);
        return $datos[$ultimo - 2] - $datos[$ultimo - 1];
    }

    private function panelMensaje($info) {
        $mensaje = '<div class="panel panel-danger"><div class="panel-heading">';
        $mensaje .= '<h3 class="panel-title">ATENCI&Oacute;N</h3></div>';
        $mensaje .= '<div class="panel-body">';
        $mensaje .= $info;
        $mensaje .= '</div>';
        $mensaje .= '</div>';
        return $mensaje;
    }
    
    private function escribeFic($comando) 
    {
        $fp = fopen("tmp/comandos","a");
        fputs($fp,$comando);
        fclose($fp);
    }
    
    private function bajaElemento($i)
    {
        $id = $this->datosFichero['idElem'][$i];
        $comando = 'delete from Elementos where id="'.$id.'";';
        $this->escribeFic($comando);
    }
    
    private function modificaElemento($i) 
    {
        $id = $this->datosFichero['idElem'][$i];
        $comando = 'update Elementos set Cantidad="'.$datosFichero['cantidadReal'][$i].'" where id="'.$id.'";';
        $this->escribeFic($comando);
    }
    
    private function altaElemento($i)
    {
        if ($this->cabecera[0] == "Articulo") {
            $idUbicacion = $this->datosFichero['idUbic'][$i];
            $idArticulo = $this->cabecera[1];
        } else {
            $idUbicacion = $this->cabecera[1];
            $idArticulo = $this->datosFichero['idArt'][$i];
        }    
        $idArt = $datosFichero['idArt'][$i];
        $comando = 'insert into Elementos () values (null,"'.$idArticulo.'","'.$idUbicacion.'","'.$this->datosFichero['N Serie'][$i]
                .'",'.$this->datosFichero['Cant. Real'][$i].',"'.$this->datosFichero['Fecha C.'].'");';
        $this->escribeFic($comando);
    }

    /**
     * Procesa contra la base de datos todas las acciones del archivo
     */
    public function ejecutaFichero() {
        //Realiza una transacción para que no se ejecute parcialmente una actualización
        try {
            $this->bdd->comienzaTransaccion();
            $campos = $this->datosFichero[0];
            for ($i = 1; $i < count($this->datosFichero); $i++) {
                switch ($this->datosFichero[$i][0]) {
                    case 'S':
                        $this->bajaElemento($i);
                        break;
                    case 'Alta':
                        $this->altaElemento($this->datosFichero[$i]);
                        break;
                    case 'N':
                        if ($this->compruebaCantidades($this->datosFichero[$i]) != 0) {
                            $this->modificaElemento($this->datosFichero[$i]);
                        }
                        break;
                    default: throw new Exception("Acci&oacute;n no reconocida en la importacion [" . $this->datosFichero[0] . "]");
                }
            }
        } catch (Exception $e) {
            $this->bdd->abortaTransaccion();
            $mensaje = "Se ha producido el error [" . $e->getMessage() . "]<br>NO se ha realizado ning&uacute;n cambio en la Base de Datos.";
            return $this->panelMensaje($mensaje);
        }
    }

    public function ejecutaFichero2() {
        echo '<script>visualizaProgreso();</script>';
        for ($i = 1; $i < 80; $i++) {
            //sleep(1);
            $progreso = $i;
            echo '<script>actProgreso('.$progreso.');</script>';
            //echo str_repeat(' ',1024*64);
            flush();
            //echo '$(".bar").css("width", "'.$progreso.'");';
        }
    }
}

?>
