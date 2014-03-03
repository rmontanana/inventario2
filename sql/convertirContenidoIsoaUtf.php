<?php
include('../inc/configuracion.inc');
include('../Sql.php');

$bd = new Sql(SERVIDOR, USUARIO, CLAVE, "Inventario2");
$bda = new Sql(SERVIDOR, USUARIO, CLAVE, "Inventario3");
//Convierte Ubicaciones
echo "Actualizando Ubiaciones";
$comando = "select * from Ubicaciones;";
$bd->ejecuta($comando);
while ($fila = $bd->procesaResultado()) {
    //echo $fila['Descripcion'];
    //$descripcion = utf8_encode($fila['Descripcion']);
    $descripcion = $fila['Descripcion'];
    $id = $fila['id'];
    $comando = "insert into Ubicaciones (id,Descripcion) values ($id,'$descripcion');";
    //echo $comando;
    //echo $comando."\n";
    $bda->ejecuta($comando);
}
//Convierte Artículos
echo "Actualizando la tabla de Artículos...";
$comando = "select * from Articulos;";
$bd->ejecuta($comando);
while ($fila = $bd->procesaResultado()) {
    $id = $fila['id'];
    $descripcion = $fila['descripcion'];
    $marca = $fila['marca'];
    $modelo = $fila['modelo'];
    $cantidad = $fila['cantidad'];
    $comando = "insert into Articulos (id,Descripcion,Marca,Modelo,Cantidad) values ($id,'$descripcion','$marca','$modelo',$cantidad);";
    echo $comando."\n";
    $bda->ejecuta($comando);
}
//Convierte Elementos
echo "Actualizando la tabla de Elementos...";
$comando = "select * from Elementos";
$bd->ejecuta($comando);
while ($fila = $bd->procesaResultado()) {
    $id = $fila['id'];
    $id_articulo = $fila['id_Articulo'];
    $id_ubicacion = $fila['id_Ubicacion'];
    $numserie = $fila['numserie'];
    $cantidad = $fila['cantidad'];
    $fechaCompra = $fila['fechaCompra'];
    $comando = "insert into Elementos (id,id_Articulo,id_Ubicacion,numserie,cantidad,fechaCompra) 
        values ($id,$id_articulo,$id_ubicacion,'$numserie',$cantidad,'$fechaCompra');";
    $bda->ejecuta($comando);
}