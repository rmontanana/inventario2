<?php
    /** 
     * Genera una instancia de la aplicación y la ejecuta.
     * @author Ricardo Montañana <rmontanana@gmail.com>
     * @version 1.0
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
    //Se incluyen los módulos necesarios
    function __autoload($class_name) {
        require_once $class_name . '.php';
    }
    include('inc/configuracion.inc');

    $aplicacion=new Inventario();
    if ($aplicacion->estado())
        $aplicacion->Ejecuta();
?>
