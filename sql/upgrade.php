<?php
/**
 * Migra los datos de la versión anterior de Inventario a la actual.
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
$host="localhost";
$baseAnt="Inventario";
$baseNueva="Inventario2";
$usuario="test";
$claveUsuario="tset";
$probar=false;


// No se debería modificar nada después de este comentario
function creaUbicacion($bd1,$bd2,$clave)
{
    global $probar;
    $comando="select nombre from Ubicaciones where codigo=".$clave.";";
    $resultado=$bd1->query($comando);
    if ($bd1->affected_rows==0) {
        echo $comando;
        die("No encontró la ubicación ".$clave);
    }
    $dato=$resultado->fetch_assoc();
    $valor=$dato['nombre'];
    $comando="insert into Ubicaciones values (NULL,'".$valor."');";
    if ($probar) {
        echo $comando;
        return 1;
    } else {
        $test=$bd2->query($comando);
        if (!$test) {
            die("**No pudo insertar ubicacion.".$comando);
        }
        return $bd2->insert_id;
    } 
}
function creaArticulo($bd1,$bd2,$clave)
{
    global $probar;
    $comando="select * from Articulos where codigo='".$clave."';";
    $resultado=$bd1->query($comando);
    if ($bd1->affected_rows==0) {
        echo $comando;
        die("No encontró el artículo ".$clave);
    }
    $dato=$resultado->fetch_assoc();
    $valor1=$dato['descripcion'];
    $valor2=$dato['marca'];
    $valor3=$dato['modelo'];
    $valor4=$dato['cantidad'];
    $comando="insert into Articulos values (NULL,'".$valor1."','".$valor2."','".$valor3."',".$valor4.");";
    if ($probar) {
        echo $comando;
        return 1;
    } else {
        $test=$bd2->query($comando);
        if (!$test) {
            die("**No pudo insertar artículo.".$comando);
        }
        return $bd2->insert_id;
    }
}
function generaSesion()
{
    $long=10;
    $cadena="";
    for ($i=0;$i<$long;$i++) {
        $cadena.=chr(rand(40,126));
    }
    return $cadena;
}
/*
 *
 * Comienzo del programa principal.
 *
 */
$bd1=new mysqli($host,$usuario,$claveUsuario,$baseAnt);
if(mysqli_connect_errno()) {
  die("**Error conectando a la base de datos antigua.".$bd1->error);
}
$bd2=new mysqli($host,$usuario,$claveUsuario,$baseNueva);
if(mysqli_connect_errno()) {
  die("**Error conectando a la base de datos nueva.".$bd2->error);
}
$bd2->autocommit(false);
$datos=$bd1->query("select * from Elementos;");
if (!$datos) {
    die("**No encontró datos en la tabla de elementos.");
}
$numRegistros=$bd1->affected_rows;
$contador=0;
$ubicaciones=array();
$articulos=array();
echo "++Comenzando proceso de actualización de Elementos con ".$numRegistros." registros por procesar.<br>\n";
while($fila=$datos->fetch_assoc()) {
    $contador++;
    echo "Procesando registro ".$contador." de ".$numRegistros."<br>\n";
    if (!isset($ubicaciones[$fila['codUbicacion']])) {        
        $ubicaciones[$fila['codUbicacion']]=creaUbicacion($bd1,$bd2,$fila['codUbicacion']);
    }
    if (!isset($articulos[$fila['codArticulo']])) {
        $articulos[$fila['codArticulo']]=creaArticulo($bd1,$bd2,$fila['codArticulo']);
    }
    $comando="insert into Elementos values (NULL,".$articulos[$fila['codArticulo']].",".$ubicaciones[$fila['codUbicacion']];
    $comando.=",'".$fila['numserie']."',".$fila['cantidad'].",'".$fila['fechaCompra']."');";
    if ($probar) {
        echo $comando."<br>";
    } else {
        $res=$bd2->query($comando);
        if (!$res) {
            die("**Error ejecutando el comando de actualización. ".$comando." ".$bd2->error);
        }
    }
}
//Traspasa los usuarios
$datos=$bd1->query("select * from Usuarios;");
if (!$datos) {
    die("**No encontró datos en la tabla de Usuarios.");
}
$numRegistros=$bd1->affected_rows;
$contador=0;
while ($fila=$datos->fetch_assoc()) {
    $contador++;
    echo "Procesando registro ".$contador." de ".$numRegistros."<br>\n";
    $sesion=generaSesion();
    $comando="insert into Usuarios values (NULL,'".$fila['usuario']."','".$fila['usuario']."','".$sesion;
    $comando.="',".$fila['altas'].",".$fila['modificaciones'].",".$fila['bajas'].",".$fila['consultas'].",";
    $comando.=$fila['informes'].",".$fila['usuarios'].",1);";
    if ($probar) {
        echo $comando."<br>";
    } else {
        $res=$bd2->query($comando);
        if (!$res) {
            die("**Error ejecutando el comando de actualización. ".$comando." ".$bd2->error);
        }
    }
}
echo "++Fin del proceso.<br>\n";
$bd2->commit();
$bd1->close();
$bd2->close();
?>
