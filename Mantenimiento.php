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
//Clase que se encargará de manejar los elementos del modelo de datos.
define('EDICION', 'Edici&oacute;n');
define('BORRADO', '<i>Borrado</i>');
define('ANADIR', 'Inserci&oacute;n');

class Mantenimiento {

    private $descripcion;
    protected $bdd;
    protected $url;
    protected $cabecera;
    protected $tabla;
    protected $cadenaBusqueda;
    protected $campos = array();
    protected $foraneas = array();
    protected $campoBusca = "Descripcion";
    protected $comandoConsulta = "";
    protected $perfil;

    public function __construct($baseDatos, $perfil, $nombre) {
        $this->bdd = $baseDatos;
        $this->url = "index.php?$nombre&opc=inicial";
        $this->cabecera = 'Location: ' . $this->url;
        $this->tabla = ucfirst($nombre);
        $this->perfil = $perfil;
    }

    public function ejecuta() {
        $opc = $_GET['opc'];
        $id = $_GET['id'];
        $orden = isset($_GET['orden']) ? $_GET['orden'] : '';
        $sentido = isset($_GET['sentido']) ? $_GET['sentido'] : 'asc';
        //Sólo tiene sentido para las modificaciones.
        //Es la página donde estaba el registro
        $pag = isset($_GET['pag']) ? $_GET['pag'] : '0';
        $this->cadenaBusqueda = $_GET['buscar'];
        $this->obtenerCampos();
        $this->obtieneClavesForaneas();
        switch ($opc) {
            case 'inicial':return $this->consulta($id, $orden, $sentido);
            case 'editar':return $this->muestra($id, EDICION, $pag, $orden, $sentido);
            case 'eliminar':return $this->muestra($id, BORRADO);
            case 'nuevo':return $this->muestra(null, ANADIR);
            case 'insertar':return $this->insertar();
            case 'modificar':return $this->modificar($id, $pag, $orden, $sentido);
            case 'borrar':return $this->borrar($id);
            default:return 'La clase Mantenimiento No entiende lo solicitado.';
        }
    }

    protected function obtieneClavesForaneas() {
        $salida = null;
        foreach ($this->campos as $clave => $valor) {
            $trozos = explode(",", $valor["Comment"]);
            foreach ($trozos as $trozo) {
                if (strstr($trozo, "foreign")) {
                    $temp = substr($trozo, 8, -1);
                    list($tabla, $atributos) = explode("->", $temp);
                    list($clave, $resto) = explode(";", $atributos);
                    //Quita el paréntesis final
                    $atributos = substr($atributos, 0, -1);
                    $salida[$valor['Campo']] = $tabla . "," . $resto;
                }
            }
        }
        $this->foraneas = $salida;
    }

    private function consulta($pagina, $orden, $sentido) {
        //Calcula los números de página anterior y siguiente.
        $pagina = $pagina + 0;
        $pagSigte = $pagina <= 0 ? 1 : $pagina + 1;
        $pagAnt = $pagSigte - 2;
        $pagFwd = $pagSigte + 3;
        $pagRew = $pagAnt - 3 < 0 ? $pagAnt : $pagAnt - 3;
        //Tengo que procesar la cabecera antes de lo de la cadena de búsqueda por el tema de las búsquedas
        $cabecera = $this->cabeceraTabla();
        //Trata con la cadena de búsqueda
        $this->cadenaBusqueda = isset($_POST['buscar']) ? $_POST['buscar'] : $this->cadenaBusqueda;
        if (isset($this->cadenaBusqueda) && strlen($this->cadenaBusqueda)) {
            $sufijo = " where $this->campoBusca like '%" . $this->bdd->filtra($this->cadenaBusqueda) . "%'";
            $sufijoEnlace = "&buscar=" . $this->cadenaBusqueda;
            $comando = str_replace('{buscar}', $sufijo, $this->comandoConsulta);
        } else {
            $comando = str_replace('{buscar}', '', $this->comandoConsulta);
        }
        //Trata con el orden de mostrar los datos
        if (strlen($orden) > 0) {
            $comando = str_replace('{orden}', "order by " . $orden . " " . $sentido, $comando);
            $sufijoOrden = "&orden=" . $orden . "&sentido=" . $sentido;
        } else {
            $comando = str_replace('{orden}', ' ', $comando);
        }
        //Introduce un botón para hacer búsquedas y el número de la página
        $salida = $this->enlaceBusqueda($pagSigte);
        //Esta orden de centrado se cierra en el pie de la tabla
        //$salida.='<center><h4>P&aacute;gina ' . $pagSigte . '</h4>';
//        $salida .='<div class="nav-bar navbar-fixed"><ul class="nav nav-pills nav-stacked">
//                  <li class="active">
//                    <a href="#">
//                      <span class="badge pull-right">' . $pagSigte . '</span>
//                      P&aacute;gina
//                    </a>
//                  </li>
//                </ul></div>';
        $salida.= $cabecera;
        //Consulta paginada de todas las tuplas
        $comando = str_replace('{inferior}', ($pagAnt + 1) * NUMFILAS, $comando);
        $comando = str_replace('{superior}', NUMFILAS, $comando);
        //$salida.=$comando;
        $tabla = strtolower($this->tabla);
        $this->bdd->ejecuta($comando);
        if ($this->bdd->numeroTuplas() == 0) {
            if ($pagSigte > 1) {
                // Si no hay datos en la consulta y no es la primera página, 
                // carga la página inicial
                header('Location: ' . $this->url);
            } else {
                $salida = "<p align=\"center\"><center><h2>No hay registros</h2></center></p><br>";
            }
        }
        //$salida.=print_r($this->perfil);
        //$salida.=$comando;
        while ($fila = $this->bdd->procesaResultado()) {
            $salida.='<tr align="center" bottom="middle">';
            foreach ($fila as $clave => $valor) {
                if ($clave == "id") {
                    $id = $valor;
                }
                // Comprueba si tiene que añadir el enlace de inventario
                if (strstr($this->campos[$clave]['Comment'], "link")) {
                    $comen = explode(",", $this->campos[$clave]['Comment']);
                    foreach ($comen as $co) {
                        if (strstr($co, "link")) {
                            $tmpco = explode("/", $co);
                            $datoEnlace = $tmpco[1];
                        }
                    }
                    $this->campoBusca = $dato[1];
                    $valor = '<a title="Inventario de ' . $valor . '" $target="_blank" href="index.php?informeInventario&opc=listar' . $datoEnlace . '&id=' . $id . '">' . $valor;
                }
                $salida.="<td>$valor</td>\n";
            }
            //Añade el icono de editar
            if ($this->perfil['Modificacion']) {
                $salida.='<td><a href="index.php?' . $tabla . '&opc=editar&id=' . $id . "&pag=" . $pagina . $sufijoOrden .
                        '"><img title="Editar" src="img/' . ESTILO . '/editar.png" alt="editar"></a>';
            }
            //Añade el icono de eliminar
            if ($this->perfil['Borrado']) {
                $salida.='&nbsp;&nbsp;<a href="index.php?' . $tabla . '&opc=eliminar&id=' . $id .
                        '"><img title="Eliminar" src="img/' . ESTILO . '/eliminar.png" alt="eliminar"></a></td></tr>' . "\n";
            }
        }
        $salida.="</tbody></table></center></p>";
        //Añade botones de comandos
        $enlace = '<a href="' . $this->url . $sufijoOrden . '&id=';
        if ($this->bdd->numeroTuplas()) {
            $anterior = $enlace . $pagAnt . $sufijoEnlace . "\"><img title=\"Pag. Anterior\" alt=\"anterior\" src=\"img/" . ESTILO . "/anterior.png\"></a>\n";
            $siguiente = $enlace . $pagSigte . $sufijoEnlace . "\"><img title=\"Pag. Siguiente\" alt=\"siguiente\" src=\"img/" . ESTILO . "/siguiente.png\"></a>\n";
            $fwd = $enlace . $pagFwd . $sufijoEnlace . "\"><img title=\"+5 Pags.\" alt=\"mas5p\" src=\"img/" . ESTILO . "/fwd.png\"></a>\n";
            $rew = $enlace . $pagRew . $sufijoEnlace . "\"><img title=\"-5 Pags.\" alt=\"menos5p\" src=\"img/" . ESTILO . "/rew.png\"></a>\n";
            if (strlen($orden) > 0) {
                $az = '<a href="' . $this->url . '&orden=' . $orden . '&sentido=asc"><img alt="asc" title="Orden ascendente" src="img/' . ESTILO . '/ascendente.png"></a>';
                $za = '<a href="' . $this->url . '&orden=' . $orden . '&sentido=desc"><img alt="desc" title="Orden descendente" src="img/' . ESTILO . '/descendente.png"></a>';
            } else {
                $az = $za = '';
            }
            if ($this->perfil['Informe']) {
                $informe = '<a href="index.php?' . $tabla . '&opc=informe" target="_blank"><img src="img/' . ESTILO . '/informe.png" alt="informe" title="Informe pdf"></a>';
            } else {
                $informe = "";
            }
        }
        if ($this->perfil['Alta']) {
            $anadir = '<a href="index.php?' . $tabla . '&opc=nuevo">' .
                    '<img title="A&ntilde;adir registro" alt="nuevo" src="img/' . ESTILO . '/nuevo.png"></a>';
        } else {
            $anadir = "";
        }
        $salida.='<p align="center">' .
                "$rew&nbsp&nbsp$anterior&nbsp&nbsp$az&nbsp&nbsp$anadir&nbsp&nbsp$informe&nbsp&nbsp$za&nbsp&nbsp$siguiente&nbsp&nbsp$fwd</p>";
        return $salida;
    }

    private function enlaceBusqueda($pagina) {
        //$salida = '<p align="center">';
        //$salida .='<center><form name="busqueda" method="POST"><input type="text" class="form-control" name="buscar"';
        //$salida .='value="' . $this->cadenaBusqueda . '" size="40" /><input type="submit" class="btn btn-primary" value="Buscar" name=';
        //$salida .='"Buscar" />';
        //$salida .= '</form></center>';
        //$salida.='</p>';
        $salida = '<form name="busqueda" method="POST"><div class="col-lg-6"><div class="input-group">
                <input type="text" name="buscar" placeholder="Descripci&oacute;n" class="form-control">
                <span class="input-group-btn"><button class="btn btn-primary" type="button">Buscar</button>
                </span></div></div></form>';
        //$salida .= '<div class="col-lg-1 pull-right"><ul class="nav nav-pills nav-stacked "><li class="active">
        //           <a href="#"><span class="badge pull-right">'.$pagina.'</span>P&aacute;gina</a></li></ul></div>';
        $salida .= '<button class="btn btn-info pull-right" type="button">P&aacute;gina <span class="badge">'
                . $pagina . '</span></button>';
//        $salida .= '<div class="progress progress-striped">
//  <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%">
//    P&aacute;gina 5 de 6<span class="sr-only">20% Complete</span>
//  </div>
//</div>';
        return $salida;
    }

    protected function borrar($id) {
        $comando = "delete from " . $this->tabla . " where id=\"$id\"";
        if (!$this->bdd->ejecuta($comando)) {
            return $this->errorBD($comando);
        }
        header('Location: ' . $this->url);
        return;
    }

    protected function insertar() {
        $comando = "insert into " . $this->tabla . " (";
        $lista = explode("&", $_POST['listacampos']);
        $primero = true;
        //Añade la lista de campos
        foreach ($lista as $campo) {
            if ($campo == "") {
                continue;
            }
            if ($primero) {
                $primero = false;
                $coma = " ";
            } else {
                $coma = ",";
            }
            $comando.="$coma $campo";
        }
        $comando.=") values (";
        //Añade la lista de valores
        $primero = true;
        foreach ($lista as $campo) {
            if ($campo == "")
                continue;
            if ($primero) {
                $primero = false;
                $coma = " ";
            } else {
                $coma = ",";
            }
            $valor = $_POST[$campo] == "" ? "null" : '"' . $_POST[$campo] . '"';
            $comando.="$coma " . $valor;
        }
        $comando.=")";
        if (!$this->bdd->ejecuta($comando)) {
            return $this->errorBD($comando);
        }
        list($enlace, $resto) = explode("&", $this->url);
        $enlace.="&opc=inicial";
        return "<h1><a href=\"$enlace\">Se ha insertado el registro con la clave " . $this->bdd->ultimoId() . "</a></h1>";
    }

    protected function modificar($id, $pag, $orden, $sentido) {
        //Los datos a utilizar para actualizar la tupla vienen en $_POST.
        //La lista de atributos de la tupla viene en el campo oculto listacampos
        //print_r($_GET);
        //echo "id=$id pag=$pag orden=$orden sentido=$sentido";die();
        $comando = "update " . $this->tabla . " set ";
        $lista = explode("&", $_POST['listacampos']);
        $primero = true;
        foreach ($lista as $campo) {
            if ($campo == "id" || $campo == "")
                continue;
            if ($primero) {
                $primero = false;
                $coma = " ";
            }
            else
                $coma = ",";
            if (strlen(trim($_POST[$campo])) == 0)
                $comando.="$coma $campo=null";
            else
                $comando.=$coma . ' ' . $campo . '="' . $_POST[$campo] . '"';
        }
        $comando.=" where id=\"$id\"";
        if (!$this->bdd->ejecuta($comando)) {
            return $this->errorBD($comando);
        }

        list($enlace, $resto) = explode("&", $this->url);
        $enlace.="&opc=inicial&orden=" . $orden . "&sentido=" . $sentido . "&id=" . $pag;
        header('Location: ' . $enlace);
        return;
    }

    protected function muestra($id, $tipoAccion, $pag = "", $orden = "", $sentido = "") {
        if (isset($id)) {
            $comando = "select * from " . $this->tabla . " where id='$id'";
            $resultado = $this->bdd->ejecuta($comando);
            if (!$resultado) {
                return $this->errorBD("", "No se han podido encontrar datos del identificador $id");
            }
            $fila = $this->bdd->procesaResultado();
        } else {
            $fila = null;
        }
        //list($tipo,$valor)=explode($columna["Type"]);
        $accion = "index.php?" . strtolower($this->tabla) . "&id=$id&opc=";
        switch ($tipoAccion) {
            case EDICION:
                $accion.="modificar";
                $accion.=isset($pag) ? "&pag=$pag" : '';
                $accion.=isset($orden) ? "&orden=$orden" : '';
                $accion.=isset($sentido) ? "&sentido=$sentido" : '';
                break;
            case BORRADO:
                $accion.="borrar";
                break;
            case ANADIR:
                $accion.="insertar";
                break;
        }
        //Genera un formulario con los datos de la tupla seleccionada.
        return $this->formularioCampos($accion, $tipoAccion, $fila);
    }

    //Función que genera un campo de lista con los valores de descripción de la
    //tabla a la cual pertenece la clave foránea.
    protected function generaLista($datos, $campo, $valorInicial, $modo) {
        $salida = "<select class=\"form-control\" name=\"$campo\">\n";
        list($tabla, $atributos) = explode(",", $datos);
        $atributos = str_replace("/", ",", $atributos);
        // Elimina las llaves
        $atributos = substr($atributos, 1, -1);
        $comando = "select id,$atributos from $tabla order by $atributos";
        $resultado = $this->bdd->ejecuta($comando);
        if (!$resultado) {
            return $this->errorBD($comando);
        }
        $modoEfectivo = $modo == "readonly" ? "disabled" : "";
        $primero = true;
        while ($fila = $this->bdd->procesaResultado()) {
            foreach ($fila as $clave => $valor) {
                if ($clave == "id") {
                    if ($primero) {
                        $primero = false;
                    } else {
                        $salida = substr($salida, 0, -1);
                        $salida.="</option>\n";
                    }
                    $seleccionado = $valor == $valorInicial ? " selected " : "";
                    $salida.='<option value="' . $valor . '" ' . $seleccionado . $modoEfectivo . ' >';
                } else {
                    $salida.=$valor . "-";
                }
            }
        }
        $salida.="</select>\n<br><br>";
        return $salida;
    }

    private function obtenerCampos() {
        //Si hay un fichero de descripción xml lo utiliza.
        $nombre = "xml/mantenimiento" . $this->tabla . ".xml";
        if (file_exists($nombre)) {
            $def = simplexml_load_file($nombre);
            foreach ($def->Campos->Col as $columna) {
                $this->campos[(string) $columna['Nombre']] = array("Field" => (string) $columna['Titulo'], "Comment" => (string) $columna['Varios'],
                    "Type" => (string) $columna['Tipo'] . "(" . $columna['Ancho'] . ")", "Editable" => (string) $columna['Editable'], "Campo" => (string) $columna['Campo']);
            }
            $this->comandoConsulta = $def->Consulta;
        } else {
            //Toma los datos de la tabla.
            $datos = $this->bdd->estructura($this->tabla);
            for ($i = 0; $i < count($datos); $i++) {
                $this->campos[$datos[$i]["Field"]][] = $datos[$i];
                $this->campos[$datos[$i]["Field"]] = $this->campos[$datos[$i]["Field"]][0];
                $this->campos[$datos[$i]["Field"]]["Campo"] = $datos[$i]["Field"];
                $this->campos[$datos[$i]["Field"]]["Editable"] = "si";
            }
            $this->comandoConsulta = "select * from " . $this->tabla . " {buscar} {orden} limit {inferior},{superior}";
        }
    }

    private function cabeceraTabla() {
        //$salida = '<p align="center"><table border=1 class="tablaDatos"><tbody>';
        $salida = '<p align="center"><table border=1 class="table table-striped table-bordered table-condensed table-hover"><tbody>';
        foreach ($this->campos as $clave => $datos) {
            $comen = explode(",", $datos["Comment"]);
            $ordenable = false;
            foreach ($comen as $co) {
                if (strstr($co, "ordenable")) {
                    $ordenable = true;
                }
                if (strstr($co, "buscable")) {
                    $dato = explode("/", $co);
                    $this->campoBusca = $dato[1];
                }
            }
            $clave2 = $clave;
            $clave = str_ireplace("descripcion", "Descripci&oacute;n", $clave);
            $clave = str_ireplace("ubicacion", "Ubicaci&oacute;n", $clave);
            $clave = str_ireplace("articulo", "Art&iacute;culo", $clave);
            if ($ordenable) {
                $salida.="<th><b><a title=\"Establece orden por $clave \" href=\"$this->url&orden=" . strtolower($clave2) . "\"> " . ucfirst($clave) . " </a></b></th>\n";
            } else {
                $salida.='<th><b>' . ucfirst($clave) . '</b></th>' . "\n";
            }
        }

        $salida.="<th><b>Acci&oacute;n</b></th>\n";
        return $salida;
    }

    /**
     * 
     * @param string $accion URL de la acción del POST
     * @param string $tipo ANADIR,EDITAR,BORRADO
     * @param array $datos Vector con los datos del registro
     * @return array lista de campos y formulario de entrada
     */
    private function formularioCampos($accion, $tipo, $datos) {
        $modo = $tipo == BORRADO ? "readonly" : "";
        $nfechas = 0;
        $salida.='<div class="col-sm-8"><form name="mantenimiento.form" class="form-horizontal" role="form" method="post" action="' . $accion . '">' . "\n";
        $salida.="<fieldset style=\"width: 96%;\"><p><legend style=\"color: red;\"><b>$tipo</b></legend>\n";
        foreach ($this->campos as $clave => $valor) {
            if ($valor["Editable"] == "no") {
                //Se salta los campos que no deben aparecer
                continue;
            }
            $salida .='<div class="form-group">';
            $campo = $valor['Campo'];
            $salida.='<label class="col-sm-2 control-label" for="' . $campo . '">' . ucfirst($clave) . "</label> ";
            $salida.='<div class="col-sm-5">';
            //Se asegura que el id no se pueda modificar.
            $modoEfectivo = $clave == 'id' ? "readonly" : $modo;
            $valorDato = $datos == null ? "" : $datos[$campo];
            if ($clave == 'id' && $tipo == ANADIR) {
                $valorDato = null;
            }
            if (!isset($this->foraneas[$valor['Campo']])) {
                $tipoCampo = $valor['Type'];
                //Si es un campo fecha u hora y está insertando pone la fecha actual o la hora actual
                if ($tipo == ANADIR) {
                    if (stripos($tipoCampo, "echa") || stripos($tipoCampo, "ate")) {
                        $valorDato = strftime("%d/%m/%Y");
                    }
                }
                // Calcula el tamaño y el tipo
                $tipo_campo = "text";
                if (stripos($tipoCampo, "echa") || stripos($tipoCampo, "ate")) {
                    $tamano = "19";
                    $tipo_campo = "datetime";
                    $nfechas++;
                    //
                    //Prueba
                    //
                    $salida .= '<div class="input-group date" id="datetimepicker'.$nfechas.'">
                    <input type="text" $name ="'.$campo.'" data-format="YYYY/MM/DD" value="'.$valorDato.'" '. $modoEfectivo. ' class="form-control" />
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                    </div>';
                    $salida .= '<script type="text/javascript">
                            $(function () {
                                $('."'#datetimepicker".$nfechas."').datetimepicker({
                                    pick12HourFormat: false,
                                    language: 'es',
                                    pickTime: false
                                    });
                            });
                            </script>";
                    $salida .= "</div></div>";
                    continue;
                } else {
                    list($resto, $tamano) = explode("(", $tipoCampo);
                    $tamano = substr($tamano, 0, -1);
                }
                if ($tipoCampo == "Password") {
                    $tipo_campo = "password";
                }
                //Si no es una clave foránea añade un campo de texto normal
                $salida.='<input class="form-control" type="' . $tipo_campo . '" name="' . $campo . '" value="' . $valorDato .
                        '" maxlength="' . $tamano . '" size="' . (string) (intval($tamano) + 5) . '" ' . $modoEfectivo . " ><br><br>\n";
                $salida.='</div></div>';
            } else {
                $salida.=$this->generaLista($this->foraneas[$campo], $campo, $valorDato, $modoEfectivo);
                $salida.="</div></div>";
            }
            //Genera una lista con los campos que intervienen en el formulario.
            $campos.="$campo&";
        }
        //genera un campo oculto con la lista de campos a modificar.
        $salida .= '<input name="listacampos" type="hidden" value="' . $campos . "\">\n";
        $salida .= "</fieldset><p>";
        $salida .= '<center>';
        $salida .= '<button type="button" onClick="location.href=' . "'$this->url'" . '" class="btn btn-info">Volver</button>';
        $salida .= '&nbsp;&nbsp;<button type="reset" class="btn btn-danger">Cancelar</button>';
        $salida .= '&nbsp;&nbsp;<button type=submit class="btn btn-primary">Aceptar</button>';
        $salida .= '<br></center></div>';
        return $salida;
    }

    protected function errorBD($comando, $mensaje = "") {
        if (!$mensaje) {
            return "<h1>No pudo ejecutar correctamente el comando $comando error=" . $this->bdd->mensajeError() . " </h1>";
        } else {
            return "<h1>$mensaje error=" . $this->bdd->mensajeError() . "</h1>";
        }
    }

}

?>