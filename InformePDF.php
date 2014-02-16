
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
class InformePDF {

    /**
     * 
     * @var basedatos Controlador de la base de datos
     */
    private $bdd;
    private $docu;
    private $pdf;
    private $def;

    /**
     * El constructor recibe como argumento el nombre del archivo XML con la definición, encargándose de recuperarla y guardar toda la información localmente
     * @param basedatos $bdd manejador de la base de datos
     * @param string $definicion fichero con la definición del informe en XML
     * @param boolean $registrado usuario registrado si/no
     * @return ficheroPDF
     * todo: cambiar este comentario
     */
    public function __construct($bdd, $definicion, $registrado) {
        if (!$registrado) {
            return 'Debe registrarse para acceder a este apartado';
        }
        // Recuperamos la definición del informe
        $this->def = simplexml_load_file($definicion);
        $this->bdd = $bdd;
        $this->pdf = new Pdf_mysql_table($this->bdd->obtieneManejador(), (string) $this->def->Pagina['Orientacion'], (string) $this->def->Pagina['Formato'], (string) $this->def->Titulo['Texto'], (string) $this->def->Pagina->Cabecera);
        //echo $def->Titulo.$def->Cabecera;
        $this->pdf->Open();
        $this->pdf->setAuthor(utf8_decode(AUTOR));
        $this->pdf->setCreator(html_entity_decode(APLICACION));
        $this->pdf->setSubject(utf8_decode($this->def->Titulo));
        $this->pdf->setAutoPageBreak(true, 10);
    }

    public function crea($definicion) {
        
        //print_r($def);echo $bdd;die();
        // Iniciamos la creación del documento
        $this->def = simplexml_load_file($definicion);
        $this->pdf->setTitulo($this->def->Titulo['Texto']);
        $this->pdf->iniciaTotales();
        $this->bdd->ejecuta(trim($this->def->Datos->Consulta));
        //$filas = $this->bdd->procesaResultado();
        $this->pdf->AddPage();
        // Recuperamos los datos del cuerpo
        foreach ($this->def->Pagina->Cuerpo->Col as $columna) {
            $this->pdf->AddCol((string) $columna['Nombre'], (string) $columna['Ancho'], (string) $columna['Titulo'], (string) $columna['Ajuste'], (string) $columna['Total']);
        }
        $prop = array('HeaderColor' => array(255, 150, 100),
            'color1' => array(210, 245, 255),
            'color2' => array(255, 255, 210),
            'padding' => 2);
        $this->pdf->Table($this->def->Datos->Consulta, $prop);
    }

    public function cierraPDF() {
        $this->pdf->Close();
        $this->docu = $this->pdf->Output('', 'S');
    }

    public function getContenido() {
        return $this->docu;
    }

    public function getCabecera() {
        $cabecera = "Content-type: application/pdf";
        $cabecera = $cabecera . "Content-length: " . strlen($this->docu);
        $cabecera = $cabecera . "Content-Disposition: inline; filename=tmp/Informe.pdf";
        return $cabecera;
    }

    public function guardaArchivo($nombre = "tmp/Informe.pdf") {
        $fichero = fopen($nombre, "w");
        fwrite($fichero, $this->getCabecera());
        fwrite($fichero, $this->getContenido(), strlen($this->getContenido()));
        fclose($fichero);
    }

    public function enviaCabecera() {
        header("Content-type: application/pdf");
        $longitud = strlen($this->docu);
        header("Content-length: $longitud");
        header("Content-Disposition: inline; filename=tmp/Informe.pdf");
    }

    public function imprimeInforme() {
        $this->enviaCabecera();
        echo $this->docu;
    }

}

?>
