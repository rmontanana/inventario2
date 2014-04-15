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
    private $sql;
    private $tabla;
    
    public function __construct()
    {
        $this->sql = new Sql(SERVIDOR, USUARIO, CLAVE, BASEDATOS);
        if ($this->sql->error()) {
            return $this->respuesta($this->mensaje(false, 'Error conectando con la Base de Datos'));
        };
        $this->tabla = $_GET['tabla'];
    }
    private function respuesta($mensaje)
    {
        header('Content-Type: application/json', true, 200);
        return $mensaje;
    }
    public function procesa()
    {
        $opc = $_GET['opc'];
        switch ($opc) {
            case "get": return $this->obtiene();
            case "put": return $this->actualiza();
        }
    }
    private function mensaje($exito, $texto)
    {
        return json_encode(array("success" => $exito, "msj" => $texto));
    }
    private function actualiza()
    {
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $comando = "update " . mysql_escape_string($this->tabla) . " set " . mysql_escape_string($_POST['name']) . " = '" . mysql_escape_string($_POST['value']) . "' where id = '" . mysql_escape_string($_POST['pk']). "';";
            $this->sql->ejecuta($comando);
            $exito = !$this->sql->error();
            $mensaje = $this->sql->mensajeError();
            $resp = $this->mensaje($exito, $mensaje);  
            return $this->respuesta($resp);
        }
    }
    private function obtiene()
    {
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $comando = "select id, descripcion from " . $this->tabla . " order by descripcion asc;";
            $this->sql->ejecuta($comando);
            $exito = !$this->sql->error();
            $mensaje = $this->sql->mensajeError();
            if (!$exito) {
                return $this->respuesta($this->mensaje($exito, $mensaje));
            }
            $filas = array();
            while($r = $this->sql->procesaResultado()) {
                $filas[] = array($r['id'] => $r['descripcion']);
            }
            $resp = json_encode($filas);
            return $this->respuesta($resp);
        }
    }
}  

?>
