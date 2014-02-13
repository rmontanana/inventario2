
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

    class InformePDF
    {
    /**
    * 
    * @var basedatos Controlador de la base de datos
    */
        private $bdd;
        private $docu;
    /**
    * El constructor recibe como argumento el nombre del archivo XML con la definición, encargándose de recuperarla y guardar toda la información localmente
    * @param basedatos $bdd manejador de la base de datos
    * @param string $definicion fichero con la definición del informe en XML
    * @param boolean $registrado usuario registrado si/no
    * @return ficheroPDF
    */
        public function __construct($bdd,$definicion,$registrado)
        {
            if (!$registrado) {
                return 'Debe registrarse para acceder a este apartado';
            }      
            $this->bdd=$bdd;
            // Recuperamos la definición del informe
            $def=simplexml_load_file($definicion);
            //print_r($def);echo $bdd;die();
            // Iniciamos la creación del documento
            $pdf=new Pdf_mysql_table($this->bdd->obtieneManejador(),(string)$def->Pagina['Orientacion'],
                (string)$def->Pagina['Formato'],
                (string)$def->Titulo['Texto'],(string)$def->Pagina->Cabecera);
            echo $def->Titulo.$def->Cabecera;
            $pdf->Open();
            $pdf->setAuthor(utf8_decode(AUTOR));
            $pdf->setCreator(html_entity_decode(APLICACION));
            $pdf->setSubject(utf8_decode($def->Titulo));
            $pdf->setAutoPageBreak(true,10);
            $this->bdd->ejecuta(trim($def->Datos->Consulta));
            $filas=$this->bdd->procesaResultado();
            $pdf->AddPage();
            // Recuperamos los datos del cuerpo
            foreach($def->Pagina->Cuerpo->Col as $columna) {
                $pdf->AddCol((string)$columna['Nombre'],(string)$columna['Ancho'],
                    (string)$columna['Titulo'],(string)$columna['Ajuste'],
                    (string)$columna['Total']);
            }
            $prop=array('HeaderColor'=>array(255,150,100),
                'color1'=>array(210,245,255),
                'color2'=>array(255,255,210),
                'padding'=>2);
            $pdf->Table($def->Datos->Consulta,$prop);
            $pdf->Close();
            // Obtenemos el documento y su longitud
            $documento=$pdf->Output('','S');
            $this->docu=$documento;
        }
        public function getContenido()
        {
            return $this->docu;
        }
        public function getCabecera()
        {
            $cabecera = "Content-type: application/pdf";
            $cabecera = $cabecera . "Content-length: " . strlen($this->docu);
            $cabecera = $cabecera . "Content-Disposition: inline; filename=Informe.pdf";
            return $cabecera;
        }
        public function guardaArchivo($nombre = "Informe.pdf")
        {
            $fichero = fopen($nombre, "w");
            fwrite($fichero,$this->getCabecera());
            fwrite($fichero,$this->getContenido(), strlen($this->getContenido()));
            fclose($fichero);
        }
        public function enviaCabecera()
        {
            header("Content-type: application/pdf");
            $longitud=strlen($this->docu);
            header("Content-length: $longitud");
            header("Content-Disposition: inline; filename=Informe.pdf");
        }
        public function imprimeInforme()
        {
            $this->enviaCabecera();
            echo $this->docu;
        }
    }
?>
