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
//
// Esta clase procesará una página sustituyendo
// las marcas {} por el contenido que proceda.
//
// El constructor recibe el nombre del archivo que
// actúa como plantilla de distribución del contenido
// y una referencia al objeto cuyos métodos deberán
// aportar los contenidos.
//
class Distribucion {
	// Variable para conservar la plantilla
	private $plantilla;
	// Matriz que contendrá los nombres de elementos
	private $elementos;
	// Referencia al objeto cuyos métodos serán
	// invocados para aportar el contenido
	private $objeto;
	// Constructor de la clase
	public function __construct($archivo, $objeto)
	{
            // Recuperamos el contenido del archivo
            $this->plantilla=file_get_contents($archivo)
                or die('Fallo en la apertura de la plantilla '.$archivo);
            // y guardamos la referencia al objeto
            $this->objeto=$objeto;
            // Extraemos todas las marcas de contenido
            preg_match_all('/\{[A-Za-z]+\}/', $this->plantilla,$el, PREG_PATTERN_ORDER);
            // Nos quedamos con la matriz de resultados
            $this->elementos=$el[0];
	}
	// Este método es el encargado de procesar la plantilla
	public function procesaPlantilla()
	{
            // Tomamos la plantilla en una variable local, para
            // así no modificar el contenido original
            $pagina=$this->plantilla;
            // Recorremos la matriz de marcas de contenido
            foreach($this->elementos as $el) {
                // Eliminamos los delimitadores { y }
                $el=substr($el,1,strlen($el)-2);
                // invocamos a la función del objeto
                $resultado=$this->objeto->$el();
                // e introducimos su contenido en lugar de la marca
                $pagina=str_replace('{'.$el.'}',$resultado,$pagina);
            }
            /**
             *  @todo Tratar de activar la compresión.
             */
            // Si es posible comprimir
//            if(strstr($_SERVER['HTTP_ACCEPT_ENCODING'],'gzip')) {
//                // introducimos la cabecera que indica que el contenido está comprimido
//                header('Content-Encoding: gzip');
//                // y comprime al m�ximo la información antes de enviarla
//                return gzencode($pagina, 9);
//            }
            return $pagina; // enviamos sin comprimir
	}
}
?>