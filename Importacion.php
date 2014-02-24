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
class Importacion {

    private $bdd;

    public function __construct($baseDatos, $registrado) {
         if (!$registrado) {
            return 'Debe registrarse para acceder a este apartado';
        }
        $this->bdd = $baseDatos;
    }

    public function ejecuta() {
        $opc = '';
        if (isset($_GET['opc'])) {
            $opc = $_GET['opc'];
        }
        switch ($opc) {
            case 'form':return $this->formulario();
            case 'importar':return $this->importarFichero();
            case 'ejecutar':return $this->ejecutaFichero();
            default: return "Importacion: No entiendo qué me has pedido.";
        }
    }

    private function importarFichero() {
        $uploadfile = "tmp/" . basename($_FILES['fichero']['name']);
        if (!move_uploaded_file($_FILES['fichero']['tmp_name'], $uploadfile)) {
            die('No se pudo subir el fichero ' . $_FILES['userfile']['tmp_name']);
        }
        $csv = new Csv($this->bdd);
        $csv->cargaCSV($uploadfile);
        return $csv->resumen();
    }

    private function formulario() {
        $accion = "index.php?importacion&opc=importar";
        $salida = '<form  enctype="multipart/form-data" name="importacion.form" method="post" action="' . $accion . '">' . "\n";
        $salida .= "<fieldset style=\"width: 96%;\"><p><legend style=\"color: red;\"><b>Elige Archivo</b></legend>\n";
        $salida .= '<input type="file" name="fichero" id="fichero" onChange="seleccionFichero(this);">';
        $salida .= '<p align="center"><button class="btn btn-primary" type=submit>Aceptar</button></p><br>' . "\n";
        $mensaje = utf8_decode('Sólo se permiten archivos con extensión CSV');
        $salida .= "<script type='text/javascript'>
                function seleccionFichero(obj) {
                var filePath = obj.value;

                var ext = filePath.substring(filePath.lastIndexOf('.') + 1).toLowerCase();
                if(ext != 'csv') {
                    alert('".$mensaje."');
                    location.reload();
                }}
           </script>";
        return $salida;
    }
    private function ejecutaFichero() {
        $archivo = $_POST['ficheroCSV'];
        $csv = new Csv($this->bdd);
        $csv->cargaCSV($archivo);
        return $csv->ejecutaFichero();
    }
}

?>
