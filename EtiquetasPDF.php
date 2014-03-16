
<?php

/**
 * genera un documento PDF a partir de una descripción dada en un archivo XML
 * @author Ricardo Montañana <rmontanana@gmail.com>
 * @version 1.0
 * @package Inventario
 * @copyright Copyright (c) 2008, Ricardo Montañana
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
require_once 'phpqrcode.php';

class EtiquetasPDF {

    /**
     * 
     * @var basedatos Controlador de la base de datos
     */
    private $bdd;
    private $docu;
    private $pdf;
    private $def;
    private $nombreFichero = "tmp/informeEtiquetas.pdf";

    /**
     * El constructor recibe como argumento el nombre del archivo XML con la definición, encargándose de recuperarla y guardar toda la información localmente
     * @param basedatos $bdd manejador de la base de datos
     * @param string $definicion fichero con la definición del informe en XML
     * @param boolean $registrado usuario registrado si/no
     * @return ficheroPDF
     * todo: cambiar este comentario
     */
    public function __construct($bdd, $definicion, $registrado)
    {
        if (!$registrado) {
            return 'Debe registrarse para acceder a este apartado';
        }
        // Recuperamos la definición del informe
        $this->def = simplexml_load_file($definicion);
        $this->bdd = $bdd;
        $this->pdf = new FPDF();
        $this->pdf->SetMargins(0.2, 0.2, 0.2);
        $this->pdf->SetFont('Arial', '', 11);
        $this->pdf->setAutoPageBreak(false);
        //echo $def->Titulo.$def->Cabecera;
        $this->pdf->setAuthor(AUTOR, true);
        $creador = CENTRO . " " . PROGRAMA . VERSION;
        $this->pdf->setCreator(html_entity_decode($creador), true);
        $this->pdf->setSubject($this->def->Titulo, true);
        //$this->pdf->setAutoPageBreak(true, 10);
    }

    public function crea($definicion)
    {
        //print_r($def);echo $bdd;die();
        // Iniciamos la creación del documento
        $this->def = simplexml_load_file($definicion);
        $this->bdd->ejecuta(trim($this->def->Datos->Consulta));
        //Ejecuta la consulta y prepara las variables de la base de datos.
        //$this->bdd->ejecuta(trim($this->def->Datos->Consulta));
        //Inicializa las variables para el control de las etiquetas.
        $this->pdf->AddPage();
        $tamLinea = 5;
        $fila = -1;
        $primero = true; $i = 0;
        $url = explode("/", $_SERVER['SCRIPT_NAME']);
        $aplicacion = $url[1];
        $enlace = "http://".$_SERVER['SERVER_NAME']."/".$aplicacion."/index.php?elementos&opc=editar&id=";
        while($tupla = $this->bdd->procesaResultado()) {            
            if ($i % 2) {
                //Columna 2
                $etiq1 = 136;
                $etiq2 = 105;
            } else {
                //Columna 1
                $etiq1 = 30;
                $etiq2 = 1;
                $fila++;
            }
            if ($i % 14 == 0) {
                if (!$primero) {
                    $this->pdf->AddPage();
                    $fila = 0;
                }
                $primero = false;
            }
            $py = 6 + 41 * $fila;
            $enlace2=$enlace.$tupla['idEl'];
            $fichero = "tmp/etiq".rand(1000,9999).".png";
            QRcode::png($enlace2, $fichero);
            $this->pdf->image($fichero, $etiq2, $py, 30, 30);
            unlink($fichero);
            $this->pdf->setxy($etiq1, $py);
            $this->pdf->Cell(30, 10, utf8_decode($tupla['articulo']));
            $py+=$tamLinea;
            $this->pdf->setxy($etiq1, $py);
            $this->pdf->Cell(30, 10, utf8_decode($tupla['marca']));
            $py+=$tamLinea;
            $this->pdf->setxy($etiq1, $py);
            $this->pdf->Cell(30, 10, utf8_decode($tupla['modelo']));
            $py+=$tamLinea;
            $this->pdf->setxy($etiq1, $py);
            $this->pdf->Cell(30, 10, utf8_decode($tupla['numserie']));
            $py+=$tamLinea;
            $this->pdf->setxy($etiq1, $py);
            $this->pdf->Cell(30, 10, $tupla['fechaCompra']);
            $py+=$tamLinea-1;
            $this->pdf->setxy($etiq2, $py);
            $this->pdf->Cell(30, 10, utf8_decode($tupla['ubicacion']));
            $py+=$tamLinea-1;
            $this->pdf->setxy($etiq2, $py);
            $cadena = "idElemento=" . $tupla['idEl'] . " / idArticulo=" . $tupla['idArt'] . " / idUbicacion=" . $tupla['idUbic'];           
            $this->pdf->Cell(30, 10, $cadena);
            $i++;
        }
        //$this->pdf->MultiCell(0,30,var_export($filas,true));
    }

    public function cierraPDF()
    {
        $this->pdf->Close();
        $this->docu = $this->pdf->Output('', 'S');
    }

    public function getContenido()
    {
        return $this->docu;
    }

    public function getCabecera()
    {
        $cabecera = "Content-type: application/pdf";
        $cabecera = $cabecera . "Content-length: " . strlen($this->docu);
        $cabecera = $cabecera . "Content-Disposition: inline; filename=" . $this->nombreFichero;
        return $cabecera;
    }

    public function guardaArchivo($nombre = "tmp/Informe.pdf")
    {
        $fichero = fopen($nombre, "w");
        fwrite($fichero, $this->getCabecera());
        fwrite($fichero, $this->getContenido(), strlen($this->getContenido()));
        $this->nombreFichero = $nombre;
        fclose($fichero);
    }

    public function enviaCabecera()
    {
        header("Content-type: application/pdf");
        $longitud = strlen($this->docu);
        header("Content-length: $longitud");
        header("Content-Disposition: inline; filename=" . $this->nombreFichero);
    }

    public function imprimeInforme()
    {
        $this->enviaCabecera();
        echo $this->docu;
    }
}

?>
