<?php
/**
 * Test de la clase Sql
 */
    include 'Sql.php';
    $bd=new Sql("localhost","test","tset","Inventario2");
    if ($bd->error()) {
        die("Error al conectar\n");
    }
    if (!$bd->ejecuta("select * from Articulos limit 0,10")) {
        die("No pudo ejecutar consulta. ".$bd->mensajeError()."\n");
        
    }
    echo "Hay ".$bd->numeroTuplas()." registros.<br>\n";
    while ($datos=$bd->procesaResultado()) {
        
        foreach($datos as $clave => $valor) {
            echo "[$clave]=[$valor] ";
        }
        echo "<br>\n";
        
    }
    $datos=$bd->estructura("Elementos");
    for ($i=0;$i<count($datos);$i++) {
        $campos[$datos[$i]["Field"]]=$datos[$i];
    }
    //print_r($datos);
    echo "Hay ".count($campos)." tuplas.";
    foreach($campos as $clave => $valor) {
        $trozos=explode(",",$valor["Comment"]);
        //echo "Trozos=";print_r($trozos);//print_r($campos);
        foreach($trozos as $trozo) {
            if (strstr($trozo,"foreign")) {
                $temp=substr($trozo,8,-1);
                list($tabla,$atributo)=explode(";",$temp);
                $salida[$clave]=$tabla.",".$atributo;
                echo "[$clave],[$tabla],[$atributo]<br>\n";
                $existen=true;
            }
        }
    }
    /*for ($i=0;$i<count($datos);$i++) {
        echo $datos[$i]["Field"]."<br>";
        
        /*foreach($datos[$i] as $clave => $valor) {
                echo "[$clave]=[$valor] ";
        }
        echo "<br>\n";
    }*/
    if ($bd->error()) {
        echo $bd->mensajeError();
    }
    

?>
