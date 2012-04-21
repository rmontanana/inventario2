<?php
    /** 
     * Mantenimiento de Elementos
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
//Es una especialización de Mantenimiento.
class Elementos extends Mantenimiento{
	
    public function __construct($baseDatos,$nombre)
    {
        parent::__construct($baseDatos,$nombre);
    }
    public function ejecuta()
    {
        $opc=$_GET['opc'];
        $id=$_GET['id'];
        //sig o ant
        $op=$_GET['op'];
        //campo por el que ordenar la consulta
        $orden=isset($_GET['orden'])?$_GET['orden']:'ubicacion';
        //ascendente o descendente
        $sentido=isset($_GET['sentido'])?$_GET['sentido']:'asc';
        switch ($opc) {
            case 'inicial':
                return $this->consulta($id,$op,$orden,$sentido);
            case 'editar':
                return $this->muestra($id,EDICION);
            case 'eliminar':
                return $this->muestra($id,BORRADO);
            case 'nuevo':
                return $this->muestra(null,ANADIR);
            case 'insertar':
                return $this->insertar();
            case 'modificar':
                return $this->modificar($id);
            case 'borrar':
                return $this->borrar($id);
            case 'informe':
                return $this->informe();
            case 'baja':
                return $this->baja($id);
            default:
                return 'La clase gestion No entiende lo solicitado.';
        }
    }
    //La consulta es distinta de la general ya que va ordenada por fechas y
    //no mostramos el id, además hay más botones de opciones.
    private function consulta($fec,$op,$orden,$sentido)
    {
        //Esto es necesario ya que no se puede poner un alias en la clausula where
        switch ($orden) {
            case "ubicacion":$campoWhere="U.Descripcion";break;
            case "articulo":$campoWhere="A.Descripcion";break;
            case "marca":$campoWhere="A.Marca";break;
            case "modelo":$campoWhere="A.Modelo";break;
            case "numserie":$campoWhere="E.numserie";break;
            case "fecha":$campoWhere="fecha";break;
        }
        $opSig=$sentido=='desc'?'<=':'>=';
        $opAnt=$sentido=='desc'?'>=':'<=';
        switch ($op) {
            case "sig":$sufijo="and $campoWhere $opSig\"$fec\"";break;
            case "ant":$ix=$fec;$sufijo="and $campoWhere $opAnt\"$ix\"";break;
            default:$sufijo="";
        }
        //Tratamiento de las claves foráneas
        list($existen,$respuesta)=$this->obtieneClavesForaneas();
        if ($existen) {
            foreach ($respuesta as $linea) {
                list($campo,$tabla,$atributo)=explode(",",$linea);
                $foraneas[$campo]=$tabla;
            }
        }
        //Consulta paginada de las incidencias abiertas.
        //$comando="select id,fecha,id_elemento,id_ubicacion,descripcion,id_proveedor ".
        //			"from Incidencias where isnull(fechaResolucion) $sufijo order by fecha desc limit ".NUMFILAS;
        //$comando="select I.id,fecha,E.Descripcion as elemento,U.Descripcion as ubicacion,I.descripcion,P.Descripcion as proveedor ".
        //			"from Incidencias I inner join Elementos E on id_elemento=E.id inner join Ubicaciones U on id_ubicacion=U.id ".
        //			"inner join Proveedores P on id_proveedor=P.id where (isnull(fechaResolucion) or fechaResolucion='00-00-000') $sufijo order by ".$orden." ".$sentido." limit ".NUMFILAS;
        $comando="SELECT E.id,U.Descripcion as ubicacion,A.Descripcion as articulo,A.Marca as marca,A.Modelo as modelo,E.numserie as numserie,E.cantidad,".
            " DATE_FORMAT(E.fechacompra, '%d/%m/%Y') as fechaCompra ".
            "FROM Elementos E inner join Articulos A on E.id_articulo=A.id inner join ".
            "Ubicaciones U on E.id_ubicacion=U.id $sufijo order by ".$orden." ".$sentido." limit ".NUMFILAS;
        //echo $comando;exit;
        $resultado=$this->bdd->query($comando);
        if (!$resultado)
            return $this->errorBD("","No se pudo ejecutar la consulta $comando en la base de datos");
        if ($resultado->num_rows==0) {
            //Evita un bucle infinito
            if ($fec!="" && $op!="")
                return $this->consulta("","",$orden,$sentido);
            else
                return "<h1>No se pudo ejecutar la consulta $comando.</h1>";
        }
        //Prepara la salida de datos en una tabla.
        //En la cabecera los nombres de los campos
        $salida='<p align="center"><table border=1 class="tablaDatos"><tbody>';
        $i=0;
        $primero=true;
        while ($campo=$resultado->fetch_field()) {
            if($primero) {
                //Saltamos el id que no lo queremos en pantalla
                $primero=false;
                continue;
            }
            //Si es una clave foránea pondrá el nombre de la tabla
            if ($foraneas[$campo->name]) {
                $listaClaves[$i]=$foraneas[$campo->name];
                $dato=$foraneas[$campo->name];
            } else
                $dato=ucfirst($campo->name);
            $i+=1;
            $salida.="<th><b><a title=\"Establece orden por $dato\" href=\"$this->url&orden=".strtolower($dato)."\"> $dato </a></b></th>\n";
        }
        $salida.="<th><b> Acci&oacute;n </b></th>";
        //En el cuerpo los datos
        $primero=true;
        while($fila=$resultado->fetch_assoc()) {
            $salida.="<tr>";
            $resultado->field_seek(0);
            $idSig=$fila[$orden];
            $id=$fila['id'];
            //Se queda con la fecha mayor
            if ($primero) {
                $primero=false;
                $idAnt=$idSig;
            }
            $i=0;
            $primer=true;
            while($campo=$resultado->fetch_field()) {
                if ($primer) {
                    //Se debe saltar el primer campo que es el id y no lo queremos en pantalla.
                    $primer=false;
                    continue;
                }
                if ($listaClaves[$i])
                    $dato=$this->obtenerDescripcion($listaClaves[$i],$fila[$campo->name]);
                else
                    $dato=$fila[$campo->name];	
                $salida.="<td>".$dato."</td>";
                $i+=1;
            }
            //Icono de editar
            $iconoEditar='<a href="index.php?elementos&opc=editar&id='.$id.
                '"><img title="Editar" src="img/editar.png" alt="editar"></a>';
            //Icono de eliminar
            $iconoEliminar='<a href="index.php?elementos&opc=eliminar&id='.$id.
                '"><img title="Eliminar" src="img/eliminar.png" alt="eliminar"></a>';
            $iconoBaja='<a href="index.php?incidencias&opc=baja&id='.$id.
                '"><img title="Baja" src="img/cerrar.png" alt="Baja"></a>';
            $salida.="<td>$iconoAvisos&nbsp;$iconoEditar&nbsp;&nbsp;$iconoBaja&nbsp;&nbsp;&nbsp;$iconoEliminar</td></tr>\n";
        }
        $salida.="</tbody></table></p>";
        //Añade botones de comandos
        $enlace='<a href="'.$this->url.'&orden='.$orden.'&sentido='.$sentido.'&id=';
        $anterior=$enlace.$idAnt."&op=ant\"><img title=\"Pag. Anterior\" alt=\"anterior\" src=\"img/anterior.png\"></a>\n";
        $siguiente=$enlace.$idSig."&op=sig\"><img title=\"Pag. Siguiente\" alt=\"siguiente\" src=\"img/siguiente.png\"></a>\n";
        $anadir='<a href="index.php?elementos&opc=nuevo">'.
            '<img title="A&ntilde;adir registro" alt="nuevo" src="img/nuevo.png"></a>';		
        $az='<a href="'.$this->url.'&orden='.$orden.'&sentido=asc"><img alt="asc" title="Orden ascendente" src="img/ascendente.png"></a>';
        $za='<a href="'.$this->url.'&orden='.$orden.'&sentido=desc"><img alt="desc" title="Orden descendente" src="img/descendente.png"></a>';
        $informe='<a href="index.php?'.$this->tabla.'&opc=informe" target="_blank"><img src="img/informe.png" alt="informe" title="Informe pdf"></a>';
        $salida.='<p align="center">'.
                        "$anterior&nbsp&nbsp$az&nbsp&nbsp$anadir&nbsp&nbsp$informe&nbsp&nbsp$za&nbsp&nbsp$siguiente</p>";
        $resultado->close();
        return $salida;
    }
    //Función que genera un campo de lista con los valores de descripción de la
    //tabla a la cual pertenece la clave foránea.
    protected function generaLista($tabla,$campo,$valor,$modo)
    {
        //La tabla debe tener un campo Descripción
        $comando="select id,Descripcion from $tabla order by Descripcion";
        if ($tabla=="Articulos")
            $comando="select id,Descripcion,Marca,Modelo from $tabla order by Descripcion";
        $resultado=$this->bdd->query($comando);
        if (!$resultado)
            return $this->errorBD($comando);
        $modoEfectivo=$modo=="readonly" ? "disabled" : "";
        $salida="<select name=\"$campo\">\n";
        while($fila=$resultado->fetch_assoc()) {
            $dato=$fila['id'];
            $seleccionado=$dato==$valor ? " selected " : "";
            $salida.='<option value="'.$dato.'" '.$seleccionado.$modoEfectivo.' >'.
                $fila['Descripcion'];
                if ($tabla=="Articulos") {
                    $salida.="-".$fila['Marca']."-".$fila['Modelo'];
                }
            $salida.="</option>\n";
        }
        $salida.="</select>\n<br><br>";
        $resultado->close();
        return $salida;
    }
    private function baja($id)
    {
        //Baja del elemento de inventario
        /*
        $fecha=strftime("%Y-%m-%d");
        $comando="update Incidencias set fechaResolucion='$fecha' where id='$id';";
        $resultado=$this->bdd->query($comando);
        if (!$resultado)
                return $this->errorBDD($comando);*/
        header('location: '.$this->url);
    }
}
