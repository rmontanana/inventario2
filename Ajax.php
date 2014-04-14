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
//Clase encargada de procesar las peticiones ajax
require_once 'inc/configuracion.inc';
require_once 'Sql.php';

$ajax = new Ajax();
echo $ajax->procesa();

class Ajax {
    private function respuesta($exito, $mensaje)
    {
        $resp = json_encode(array("success" => $exito, "msj" => $mensaje));
        header('Content-Type: application/json', true, 200);
        return $resp;
    }
    public function procesa()
    {
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $tabla = $_GET['tabla'];
            $sql = new Sql(SERVIDOR, USUARIO, CLAVE, BASEDATOS);
            if ($sql->error()) {
                return $this->respuesta(false, 'Error conectando con la Base de Datos');
            }
            $comando = "update " . mysql_escape_string($tabla) . " set " . mysql_escape_string($_POST['name']) . " = '" . mysql_escape_string($_POST['value']) . "' where id = '" . mysql_escape_string($_POST['pk']). "';";
            $sql->ejecuta($comando);
            $exito = !$sql->error();
            $mensaje = $sql->mensajeError();
            return $this->respuesta($exito, $mensaje);
        }
    }
}  

?>
