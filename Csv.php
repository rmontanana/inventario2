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
     * @param String $linea escribe la línea en el archivo, la línea debe estar formateada
     */
    public function escribeLinea($linea) {
        fwrite($this->fichero, $linea . "\n") or die("No puedo escribir en el fichero xml");
    }

    /**
     * 
     * @param array $campos array de campos a escribir en el fichero
     */
    public function escribeCampos($campos) {
        $linea = "";
        foreach ($campos as $campo) {
            $linea .= '"' . $campo . '",';
        }
        $this->escribeLinea($linea);
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
        $this->escribeLinea('"' . utf8_decode($consulta->Titulo['Texto']) . '","' . $consulta->Titulo['id'] . '"');
        $this->escribeLinea("");
        $this->escribeLinea('"' . $consulta->Pagina->Cabecera . '"');
        $this->escribeLinea("");
        $campos = array("Baja");
        foreach ($consulta->Pagina->Cuerpo->Col as $campo) {
            $campos[] = utf8_decode($campo['Titulo']);
        }
        $campos[] = "Cantidad Real";
        $this->escribeCampos($campos);
        // Escribe los datos de los campos
        $this->bdd->ejecuta($consulta->Datos->Consulta);
        while ($fila = $this->bdd->procesaResultado()) {
            $campos = array("");
            foreach ($consulta->Pagina->Cuerpo->Col as $campo) {
                $campos[] = $fila[(string) $campo['Nombre']];
            }
            //$campos = $fila;
            //array_unshift($campos, "");
            $this->escribeCampos($campos);
        }
    }

    private function quitaComillas($dato) {
        //return substr($dato, 1, -1);
        return str_replace("\"", "", $dato);
    }

    /**
     * 
     * @param String $ficheroCSV Nombre del archivo csv
     * @param String $ficheroXML Nombre del archivo que contiene la consulta xml
     */
    public function cargaCSV($ficheroCSV) {
        $this->nombre = $ficheroCSV;
        $this->fichero = fopen($this->nombre, "r") or die('No puedo abrir el archivo ' . $this->nombre . " para lectura.");
        $linea = fgets($this->fichero);
        $datos = explode(",", $linea);
        $cabecera = $this->quitaComillas($datos[0]);
        $idCabecera = $this->quitaComillas($datos[1]);
        \fgets($this->fichero);
        $linea = fgets($this->fichero);
        $linea = $this->quitaComillas($linea);
        $linea = explode(",", $linea);
        $linea = explode(" ", $linea[0]);
        $archivo = trim($linea[2]);
        $ficheroXML = "xml/inventario" . ucfirst($archivo) . "CSV.xml";
        $consulta = simplexml_load_file($ficheroXML) or die("No puedo cargar el fichero xml " . $ficheroXML . " al cargar csv");
        \fgets($this->fichero);
        $lineas = 0; $datosFichero = array();
        //var_dump($consulta);
        while ($linea = fgets($this->fichero)) {
            $datos = explode(",", $linea);
            $i = 1;
            $lineas++;
            $datosFichero["Baja"][] = $this->quitaComillas($linea[0]);
            foreach ($consulta->Pagina->Cuerpo->Col as $campo) {
                $datosFichero[utf8_decode($campo['Titulo'])][] = $this->quitaComillas($datos[$i++]);
                //echo "datosfichero"; var_dump($datosFichero);
                //echo "campo=[$campo]";
            }
            $datosFichero["Cant Real"] = $linea[$i];
        }
        $this->numRegistros = $lineas;
        return $this->Resumen($cabecera, $idCabecera, $archivo, $datosFichero, $consulta);
    }

    public function Resumen($cabecera, $idCabecera, $archivo, $datosFichero, $consulta) {
        $mensaje = "<center><h1>Archivo [inventario".utf8_decode($archivo)."]</h1>";
        $mensaje .= "<h2>id=[$idCabecera] Descripci&oacute;n=[".$cabecera."]</h2><br>";
        $mensaje .= '<p align="center"><table border=1 class="tablaDatos"><tbody>';
        $mensaje .= "<th><b>Baja</b></th>";
        $campos = array("Baja");
        foreach ($consulta->Pagina->Cuerpo->Col as $campo) {
            $dato = utf8_decode($campo["Titulo"]);
            $mensaje .= "<th><b>$dato</b></th>";
            $campos[] = $dato;
        }
        $campos[] = "Cant Real";
        $mensaje .= "<th><b>Cant. Real</b></th>";
        $mensaje .= '<tr align="center" bottom="middle">';
        for ($i=0; $i < count($datosFichero['Baja']); $i++) {
            $mensaje .= "<tr>";
            foreach($campos as $campo) {
                $mensaje .= "<td>".$datosFichero[$campo][$i]."</td>";
            }
            $mensaje .= "</tr>";
        }
        foreach ($datosFichero as $clave => $dato) {
            $mensaje .= "<td>$dato[$i]</td>";
        }
        $mensaje .= "</tr>";
        $mensaje .= "</tbody></table></p><br>";
        $mensaje .= '<form method="post" name="Aceptar" action="index.php?Importacion&opc=Ejecutar">
                <input type="button" name="Cancelar" value="Cancelar" onClick="location.href=' .  "'index.php'" .'">
                <input type="submit" name="Aceptar" value="Aceptar"></form></center>';
        ob_start();
        var_dump($datosFichero);
        $mensaje .= ob_get_clean();
        return $mensaje;
    }

}

?>
