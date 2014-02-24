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
// Esta clase generará el menú de la aplicación.
class Menu {
    private $opciones;
    public function __construct($fichero)
    {
        $contenido=@file_get_contents($fichero) or
            die("<h1>No puedo generar el menú. No puedo acceder al fichero $fichero</h1>");
        // Obtenemos la lista de pares Opción|Enlace
        $elementos=explode("\n", $contenido);
        foreach($elementos as $elemento) {
            list($tipo, $opcion, $enlace, $destino, $titulo)=explode('|', $elemento);
            // Los guardamos en la matriz de opciones
            if ($tipo)
                $this->opciones[]=$tipo.",".$opcion.",".$enlace.",".$destino.",".$titulo;
        }
    }
    public function insertaMenu()
    {
        $salida="";
        reset($this->opciones);
        foreach($this->opciones as $opcion) {
            list($tipo,$opcion,$enlace,$destino,$titulo)=explode(",",$opcion);
            if ($tipo==2)
                $salida.='<li class="active"><a href="'.$enlace.'" target="'.$destino.'" title="'.$titulo.'">'.$opcion.'</a><br /></li>';
            else
                $salida.='<label class="key">'.$opcion.'</label><br/>';
        }
        return $salida;
    }
}
?>
