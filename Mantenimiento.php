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
    protected $tabla;
    protected $cadenaBusqueda;
    protected $campos = array();
    protected $foraneas = array();
    protected $campoBusca = "Descripcion";
    protected $comandoConsulta = "";
    protected $perfil;
    protected $datosURL = array();
    protected $datosURLb = array(); //para hacer una copia

    public function __construct($baseDatos, $perfil, $nombre)
    {
        $this->bdd = $baseDatos;
        $this->url = "index.php?$nombre";
        //$this->datosURL['']
        $this->tabla = ucfirst($nombre);
        $this->perfil = $perfil;
        $this->cargaDatosURL();
    }

    /**
     * Carga en los atributos de la clase los datos de la URL
     * Los datos constantes en la URL son:
     *  - opc = {inicial, editar, eliminar, nuevo, insertar, modificar, borrar}
     *  - orden = {id, ... } nombre del campo por el que se ordena la visualización
     *  - sentido = {asc, desc}
     *  - pag = nº página 0, 1, 2, ...
     * Los datos opcionales de la URL son:
     *  - buscar = cadena de búsqueda
     *  - id = nº de la clave necesario para la edición o el borrado
     */
    public function cargaDatosURL()
    {
        $this->datosURL['opc'] = isset($_GET['opc']) ? $_GET['opc'] : 'inicial';
        $this->datosURL['orden'] = isset($_GET['orden']) ? $_GET['orden'] : 'id';
        $this->datosURL['sentido'] = isset($_GET['sentido']) ? $_GET['sentido'] : 'asc';
        $this->datosURL['pag'] = isset($_GET['pag']) ? $_GET['pag'] : '0';
        $this->cadenaBusqueda = isset($_GET['buscar']) ? $_GET['buscar'] : null;
        $this->cadenaBusqueda = isset($_POST['buscar']) ? $_POST['buscar'] : $this->cadenaBusqueda;
        $this->datosURL['buscar'] = $this->cadenaBusqueda;
        $this->datosURL['id'] = isset($_GET['id']) ? $_GET['id'] : null;
    }
    
    public function backupURL()
    {
        $this->datosURLb = $this->datosURL;
    }
    
    public function restoreURL()
    {
        $this->datosURL = $this->datosURLb;
    }
    
    //Monta una URL con los datos cargados en los atributos de la clase
    private function montaURL()
    {
        //Primero los datos obligatorios
        $opc = "&opc=" . $this->datosURL['opc'];
        $orden = "&orden=" . $this->datosURL['orden'];
        $sentido = "&sentido=" . $this->datosURL['sentido'];
        $pag = "&pag=" . $this->datosURL['pag'];
        //Ahora los datos opcionales
        $buscar = isset($this->cadenaBusqueda) ? "&buscar=$this->cadenaBusqueda" : null;
        $id = isset($this->datosURL['id']) ? "&id=" . $this->datosURL['id'] : null;
        $enlace = $this->url . $opc . $orden . $sentido . $pag . $buscar . $id;
        return $enlace;
    }

    public function ejecuta()
    {
        $this->obtenerCampos();
        $this->obtieneClavesForaneas();
        switch ($this->datosURL['opc']) {
            case 'inicial':return $this->consulta();
            case 'editar':return $this->muestra(EDICION);
            case 'eliminar':return $this->muestra(BORRADO);
            case 'nuevo':return $this->muestra(ANADIR);
            case 'insertar':return $this->insertar();
            case 'modificar':return $this->modificar();
            case 'borrar':return $this->borrar();
            default: return "La clase Mantenimiento No entiende lo solicitado [" . $this->datosURL['opc'] . "]";
        }
    }

    protected function obtieneClavesForaneas()
    {
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

    private function consulta()
    {
        $orden = $this->datosURL['orden'];
        $sentido = $this->datosURL['sentido'];
        //Calcula los números de página anterior y siguiente.
        $pagina = $this->datosURL['pag'];
        $pagSigte = $pagina <= 0 ? 1 : $pagina + 1;
        $pagAnt = $pagSigte - 2 < 0 ? 0 : $pagSigte -2;
        $pagFwd = $pagSigte + 3;
        $pagRew = $pagAnt - 3 < 0 ? 0 : $pagAnt - 3;
        //Tengo que procesar la cabecera antes de lo de la cadena de búsqueda por el tema de las búsquedas
        $cabecera = $this->cabeceraTabla();
        //Trata con la cadena de búsqueda si viene del post debe quedarse con ella sino con la del get y si no está definida => vacía
        if (isset($this->cadenaBusqueda) && strlen($this->cadenaBusqueda)) {
            $sufijo = " where $this->campoBusca like '%" . $this->bdd->filtra($this->cadenaBusqueda) . "%'";
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
        $salida.= $cabecera;
        //Consulta paginada de todas las tuplas
        $comando = str_replace('{inferior}', $pagina * NUMFILAS, $comando);
        $comando = str_replace('{superior}', NUMFILAS, $comando);
        $tabla = strtolower($this->tabla);
        $this->bdd->ejecuta($comando);
        $numRegistros = $this->bdd->numeroTotalTuplas();
        //Si el número de la página fwd es mayor que el total de páginas lo establece a éste
        if (NUMFILAS > 0) {
            $totalPags = (int) ($numRegistros / NUMFILAS) - 1;
            if ($numRegistros % NUMFILAS) {
                $totalPags++;
            }
        } else {
            $totalPags = 0;
        }
        $pagFwd = $pagFwd > $totalPags ? $totalPags : $pagFwd;
        if ($this->bdd->numeroTuplas() == 0) {
            if ($pagSigte > 1) {
                // Si no hay datos en la consulta y no es la primera página, 
                // carga la página final
                $this->datosURL['pag'] = $totalPags;
                header('Location: ' . $this->montaURL());
            } else {
                $salida = "<p align=\"center\"><center><h2>No hay registros</h2></center></p><br>";
            }
        }
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
                if (strstr($this->campos[$clave]['Comment'], "imagen") && isset($valor)) {
                    $msj = '<button class="btn btn-info btn-xs" type="button" data-toggle="modal" data-target="#mensajeModal' . $id .'">Imagen</button>';
                    $msj .= $this->creaModal($valor, $id);
                    $valor = $msj;
                }
                if ($this->campos[$clave]['Type'] == "Boolean(1)") {
                    $checked = $valor == '1' ? 'checked' : '';
                    $valor = '<input type="checkbox" disabled ' . $checked . '>';
                }
                $salida.="<td>$valor</td>\n";
            }
            //Añade el icono de editar
            if ($this->perfil['Modificacion']) {
                //$salida.='<td><a href="index.php?' . $tabla . '&opc=editar&id=' . $id . "&pag=" . $pagina . $sufijoOrden . $sufijoEnlace .
                $this->backupURL(); $this->datosURL['opc'] = "editar"; $this->datosURL['id'] = $id;
                $salida.='<td><a href="' . $this->montaURL() .
                        '"><img title="Editar" src="img/' . ESTILO . '/editar.png" alt="editar"></a>';
                $this->restoreURL();
            }
            //Añade el icono de eliminar
            if ($this->perfil['Borrado']) {
                //$salida.='&nbsp;&nbsp;<a href="index.php?' . $tabla . '&opc=eliminar&id=' . $id . $sufijoEnlace .
                $this->backupURL(); $this->datosURL['opc'] = "eliminar"; $this->datosURL['id'] = $id;
                $salida.='&nbsp;&nbsp;<a href="' . $this->montaURL() .
                        '"><img title="Eliminar" src="img/' . ESTILO . '/eliminar.png" alt="eliminar"></a></td></tr>' . "\n";
                $this->restoreURL();
            }
        }
        $salida.="</tbody></table></center></p>";
        //Añade botones de comandos
        
        if ($numRegistros) {
            $this->backupURL();
            $this->datosURL['pag'] = $pagAnt;
            $anterior = $this->montaURL();
            $this->datosURL['pag'] = $pagSigte;
            $siguiente = $this->montaURL();
            $this->datosURL['pag'] = $pagFwd;
            $fwd = $this->montaURL();
            $this->datosURL['pag'] = $pagRew;
            $rew = $this->montaURL();
            $anterior = '<a href="' . $anterior . "\"><img title=\"Pag. Anterior\" alt=\"anterior\" src=\"img/" . ESTILO . "/anterior.png\"></a>\n";
            $siguiente = '<a href="' . $siguiente . "\"><img title=\"Pag. Siguiente\" alt=\"siguiente\" src=\"img/" . ESTILO . "/siguiente.png\"></a>\n";
            $fwd = '<a href="' . $fwd . "\"><img title=\"+4 Pags.\" alt=\"mas4pags\" src=\"img/" . ESTILO . "/fwd.png\"></a>\n";
            $rew = '<a href="' . $rew . "\"><img title=\"-4 Pags.\" alt=\"menos4pags\" src=\"img/" . ESTILO . "/rew.png\"></a>\n";
            $this->restoreURL();
            $this->datosURL['sentido'] = "asc";
            $az = $this->montaURL();
            $az = '<a href="' . $az . '"><img alt="asc" title="Orden ascendente" src="img/' . ESTILO . '/ascendente.png"></a>';
            $this->datosURL['sentido'] = "desc";
            $za = $this->montaURL();
            $za = '<a href="' . $za . '"><img alt="desc" title="Orden descendente" src="img/' . ESTILO . '/descendente.png"></a>';
            $this->restoreURL();
            if ($this->perfil['Informe']) {
                $this->datosURL['opc'] = "informe";
                $inf = $this->montaURL();
                $informe = '<a href="' . $inf . '" target="_blank"><img src="img/' . ESTILO . '/informe.png" alt="informe" title="Informe pdf"></a>';
            } else {
                $informe = "";
            }
            $this->restoreURL();
        }
        if ($this->perfil['Alta']) {
            $this->datosURL['opc'] = 'nuevo';
            $anadir = '<a href="' . $this->montaURL() . '">' .
                    '<img title="A&ntilde;adir registro" alt="nuevo" src="img/' . ESTILO . '/nuevo.png"></a>';
        } else {
            $anadir = "";
        }
        $salida.='<p align="center">' .
                "$rew&nbsp&nbsp$anterior&nbsp&nbsp$az&nbsp&nbsp$anadir&nbsp&nbsp$informe&nbsp&nbsp$za&nbsp&nbsp$siguiente&nbsp&nbsp$fwd</p>";
        return $salida;
    }

    private function enlaceBusqueda($pagina)
    {
        $valor = isset($this->cadenaBusqueda) ? 'value="' . $this->cadenaBusqueda . '"' : '';
        $salida = '<form name="busqueda" method="POST"><div class="col-xs-6 col-sm-4 col-md-6 col-lg-6"><div class="input-group">
                <input type="text" name="buscar" placeholder="Descripci&oacute;n" class="form-control" ' . $valor . '>
                <span class="input-group-btn"><button class="btn btn-primary" type="button">Buscar</button>
                </span></div></div></form>';
        $salida .= '<button class="btn btn-info pull-right" type="button">P&aacute;gina <span class="badge">'
                . $pagina . '</span></button>';
        return $salida;
    }

    protected function borrar()
    {
        //@todo hay que tener en cuenta aquí la cadena de búsqueda y la página en la url
        $id = $this->datosURL['id'];
        $comando = "delete from " . $this->tabla . " where id=\"$id\"";
        if (!$this->bdd->ejecuta($comando)) {
            return $this->errorBD($comando);
        }
        $this->datosURL['opc'] = 'inicial';
        $this->datosURL['id'] = null;
        //Comprueba si existe la imagen en datos para borrarla.
        Imagen::borraImagenId($this->tabla, $id);
        $url = $this->montaURL();
        header('Location: ' . $url);
        return;
    }

    protected function insertar()
    {
        $comando = "insert into " . $this->tabla . " (";
        $lista = explode("&", $_POST['listacampos']);
        $primero = true;
        $hayImagen = false;
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
            if ($this->campos[$campo]['Type'] == 'Boolean(1)') {
                $valor = "";
                if (empty($_POST[$campo])) {
                    $valor = "0";
                }
                $valor = $_POST[$campo] == "on" ? '1' : $valor;
            } else {
                if (stristr($this->campos[$campo]['Comment'], "imagen")) {
                    //procesa el envío de la imagen
                    $imagen = new Imagen();
                    $accion = $imagen->determinaAccion($campo);
                    if ($accion != NOHACERNADA) {
                        $mensaje = "";
                        if (!$imagen->procesaEnvio($campo, $mensaje)) {
                            return $this->panelMensaje($mensaje, "danger", "ERROR PROCESANDO IMAGEN");
                        }
                        $hayImagen = true;
                        $campoImagen = $campo;
                    } else {
                        $valor = "null";
                    }
                } else {
                    $valor = $_POST[$campo] == "" ? "null" : '"' . $_POST[$campo] . '"';
                }
            }
            $comando.="$coma " . $valor;
        }
        $comando.=")";
        if (!$this->bdd->ejecuta($comando)) {
            return $this->errorBD($comando);
        }
        $id = $this->bdd->ultimoId();
        if ($hayImagen) {
            //Tiene que recuperar el id del registro insertado y actualizar el archivo de imagen
            if (!$imagen->mueveImagenId($this->tabla, $id, $mensaje)) {
                return $this->panelMensaje($mensaje, "danger", "ERROR COMPRIMIENDO IMAGEN");
            }
            $comando = "update " . $this->tabla . " set " . $campoImagen . "='" . $imagen->archivoComprimido . "' where id='" . $id ."';";
            if (!$this->bdd->ejecuta($comando)) {
                return $this->errorBD($comando);
            }
        }
        $this->datosURL['opc'] = 'inicial';
        $this->datosURL['id'] = null;
        $cabecera = "refresh:".PAUSA.";url=".$this->montaURL();
        header($cabecera);
        return $this->panelMensaje("Se ha insertado el registro con la clave " . $id, "info", "Informaci&oacute;n");
        //return "<h1><a href=\"".$this->montaURL()."\">Se ha insertado el registro con la clave " . $this->bdd->ultimoId() . "</a></h1>";
    }
    
    protected function modificar()
    {
        //Los datos a utilizar para actualizar la tupla vienen en $_POST.
        //La lista de atributos de la tupla viene en el campo oculto listacampos
        //print_r($_GET);
        //echo "id=$id pag=$pag orden=$orden sentido=$sentido";die();
        //@todo hay que tener en cuenta aquí la página en la que se encuentra y la cadena de búsqueda
        $comando = "update " . $this->tabla . " set ";
        $lista = explode("&", $_POST['listacampos']);
        $primero = true;
        foreach ($lista as $campo) {
            if ($campo == "id" || $campo == "")
                continue;
            if ($primero) {
                $primero = false;
                $coma = " ";
            } else {
                $coma = ",";
            }
            if ($this->campos[$campo]['Type'] == 'Boolean(1)') {
                $valor = "";
                if (empty($_POST[$campo])) {
                    $valor = "0";
                }
                $valor = $_POST[$campo] == "on" ? '1' : $valor;
                $comando.=$coma . ' ' . $campo . '="' . $valor . '"';
            } else {
                if (stristr($this->campos[$campo]['Comment'], "imagen")) {
                    $valor = $_POST[$campo];
                    $imagen = new Imagen();
                    $accion = $imagen->determinaAccion($campo);
                    if ($accion != NOHACERNADA) {
                        if ($accion == HAYQUEGRABAR) {
                            $mensaje = "";
                            if (!$imagen->procesaEnvio($campo, $mensaje)) {
                                return $this->panelMensaje($mensaje, "danger", "ERROR PROCESANDO IMAGEN");
                            }
                            $mensaje = "";
                            if (!$imagen->mueveImagenId($this->tabla, $this->datosURL['id'], $mensaje)) {
                                return $this->panelMensaje($mensaje, "danger", "ERROR COMPRIMIENDO IMAGEN");
                            }                                    
                            $comando .= "$coma $campo='" . $imagen->archivoComprimido . "'";
                        } else {
                            //Hay que borrar
                            Imagen::borraImagenId($this->tabla, $this->datosURL['id']);
                            $extensiones = array("png", "jpg", "gif");
                            $comando .= "$coma $campo=null";
                        }
                    }
                } else {
                    if (strlen(trim($_POST[$campo])) == 0) {
                        $comando.="$coma $campo=null";
                    } else {
                        $comando.=$coma . ' ' . $campo . '="' . $_POST[$campo] . '"';
                    }
                }
            }
        }
        $comando.=" where id=\"" . $this->datosURL['id'] . "\"";
        if (!$this->bdd->ejecuta($comando)) {
            return $this->errorBD($comando);
        }
        $this->datosURL['id'] = null;
        $this->datosURL['opc'] = inicial;
        header('Location: ' . $this->montaURL());
        return;
    }

    protected function muestra($tipoAccion)
    {
        $id = $this->datosURL['id'];
        if ($tipoAccion != ANADIR) {
            $comando = "select * from " . $this->tabla . " where id='$id'";
            $resultado = $this->bdd->ejecuta($comando);
            if (!$resultado) {
                return $this->errorBD("", "No se han podido encontrar datos del identificador $id");
            }
            $fila = $this->bdd->procesaResultado();
        } else {
            $fila = null;
        }
        //Genera un formulario con los datos de la tupla seleccionada.
        return $this->formularioCampos($tipoAccion, $fila);
    }

    //Función que genera un campo de lista con los valores de descripción de la
    //tabla a la cual pertenece la clave foránea.
    protected function generaLista($datos, $campo, $valorInicial, $modo)
    {
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

    private function obtenerCampos()
    {
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
            $this->comandoConsulta = "select SQL_CALC_FOUND_ROWS * from " . $this->tabla . " {buscar} {orden} limit {inferior},{superior}";
        }
    }

    private function cabeceraTabla()
    {
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
                $this->backupURL();
                $this->datosURL['orden'] = $clave2;
                $salida.="<th><b><a title=\"Establece orden por $clave \" href=\"". $this->montaURL() . "\"> " . ucfirst($clave) . " </a></b></th>\n";
                $this->restoreURL();
            } else {
                $salida.='<th><b>' . ucfirst($clave) . '</b></th>' . "\n";
            }
        }

        $salida.="<th><b>Acci&oacute;n</b></th>\n";
        return $salida;
    }

    /**
     * 
     * @param string $tipo ANADIR,EDICION,BORRADO
     * @param array $datos Vector con los datos del registro
     * @return array lista de campos y formulario de entrada
     */
    private function formularioCampos($tipo, $datos)
    {
        $modo = $tipo == BORRADO ? "readonly" : "";
        $nfechas = 0;
        switch ($tipo) {
            case ANADIR:
                $this->datosURL['opc'] = "insertar"; $this->datosURL['id'] = null;
                break;
            case EDICION:
                $this->datosURL['opc'] = "modificar";
                break;
            case BORRADO:
                $this->datosURL['opc'] = "borrar";
                break;
        }
        $accion = $this->montaURL();
        $salida.='<div class="col-sm-8"><form name="mantenimiento.form" enctype="multipart/form-data" class="form-horizontal" role="form" method="post" action="' . $accion . '">' . "\n";
        $salida.="<fieldset style=\"width: 96%;\"><p><legend style=\"color: red;\"><b>$tipo</b></legend>\n";
        foreach ($this->campos as $clave => $valor) {
            if ($valor["Editable"] == "no") {
                //Se salta los campos que no deben aparecer
                continue;
            }
            //Genera una lista con los campos que intervienen en el formulario.
            $salida .='<div class="form-group">';
            $campo = $valor['Campo'];
            $campos.="$campo&";
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
                    if (stripos($tipoCampo, "echa") <> 0 || stripos($tipoCampo, "ate") <> 0) {
                        $valorDato = strftime("%Y/%m/%d");
                    }
                }
                // Calcula el tamaño y el tipo
                $tipo_campo = "text";
                if (stripos($tipoCampo, "echa") || stripos($tipoCampo, "ate")) {
                    $tamano = "19";
                    $tipo_campo = "datetime";
                    $nfechas++;
                    $salida .= '<div class="input-group date" id="datetimepicker' . $nfechas . '">
                    <input type="text" name="' . $campo . '" data-format="YYYY/MM/DD" value="' . $valorDato . '" ' . $modoEfectivo . ' class="form-control" />
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                    </div>';
                    $salida .= '<script type="text/javascript">
                            $(function () {
                                $(' . "'#datetimepicker" . $nfechas . "').datetimepicker({
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
                if ($tipoCampo == "Boolean(1)") {
                    $checked = $valorDato == '1' ? 'checked' : '';
                    $modocheck = $modoEfectivo == "readonly" ? 'onclick="javascript: return false;" readonly ' : '';
                    $salida .= '<input type="checkbox" name="' . $campo . '" ' . $checked . ' ' . $modocheck . ' class="form-control">';
                    $salida .= '</div></div>';
                    continue;
                }
                if (stristr($this->campos[$campo]['Comment'], "imagen")) {
                    $salida .= $this->creaCampoImagen($campo, $valorDato, $tipo);
                    continue;
                }
                if (stristr($this->campos[$campo]['Type'], "int")) {
                    $tipo_campo = "number";
                    $modoEfectivo .= ' onkeypress = "if ( isNaN(this.value + String.fromCharCode(event.keyCode) )) return false;" ';
                }
                //Si no es una clave foránea añade un campo de texto normal
                $salida.='<input class="form-control" type="' . $tipo_campo . '" name="' . $campo . '" value="' . $valorDato .
                        '" maxlength="' . $tamano . '" size="' . (string) (intval($tamano) + 5) . '" ' . $modoEfectivo . " ><br><br>\n";
                $salida.='</div></div>';
            } else {
                $salida.=$this->generaLista($this->foraneas[$campo], $campo, $valorDato, $modoEfectivo);
                $salida.="</div></div>";
            }
        }
        //genera un campo oculto con la lista de campos a modificar.
        $salida .= '<input name="listacampos" type="hidden" value="' . $campos . "\">\n";
        $salida .= "</fieldset><p>";
        $salida .= '<center>';
        $this->datosURL['opc'] = 'inicial';
        $salida .= '<button type="button" onClick="location.href=' . "'" . $this->montaURL() . "'" . '" class="btn btn-info">Volver</button>';
        $salida .= '&nbsp;&nbsp;<button type="reset" class="btn btn-danger">Cancelar</button>';
        $salida .= '&nbsp;&nbsp;<button type=submit class="btn btn-primary">Aceptar</button>';
        $salida .= '<br></center></div>';
        return $salida;
    }
    
    protected function creaCampoImagen($campo, $valor, $tipoAccion)
    {
        
        if (file_exists($valor)) {
            //El fichero existe.
            $existe = true;
            $tipo = "fileinput-exists";
        } else {
            $tipo = "fileinput-new";
            $existe = false;
        }
        $mensaje = '
                <div class="fileinput ' . $tipo . '" data-provides="fileinput">
                <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;"><img src="img/sinImagen.gif" /></div>
                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;">';

        if ($existe) {
            $mensaje .= '<img src="' . $valor . '" onclick="$('."'#mensajeModal1'".').modal();">';
        }
        $mensaje .= '</div>';
        if ($tipoAccion == ANADIR || $tipoAccion == EDICION) {
            $mensaje .= '<div>
                <span class="btn btn-default btn-file" ><span class="fileinput-new">Añadir</span><span class="fileinput-exists">Cambiar</span><input type="file" name="imagen" /></span>
                <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Eliminar</a>';
        }
        if ($existe) {
            $mensaje .='<input type="hidden" name="' . $campo . '" value="' . $valor . '">';
        }

        $mensaje .='</div>';
        if ($tipoAccion == ANADIR || $tipoAccion == EDICION) {        
            $mensaje .= '</div>';
        }
        $mensaje .= $this->creaModal($valor, 1);
        return $mensaje;
 
    }
    
    private function creaModal($valor, $id)
    {
        $mensaje .= '
                <div id="mensajeModal'.$id.'" class="modal fade " tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                <div class="modal-dialog text-center">
                <div class="modal-content text-center">
                <img src="' . $valor . '" class="img-responsive">
                <label>Archivo: ' . $valor . '</label>
                </div>
                </div>
                </div>';
        return $mensaje;
    }

    protected function errorBD($comando, $texto = "", $tipo = "danger", $cabecera = "&iexcl;Atenci&oacute;n!")
    {
        if (!$texto) {
            $texto = "No pudo ejecutar correctamente el comando $comando error=" . $this->bdd->mensajeError();
        } else {
            $texto = "$texto error=" . $this->bdd->mensajeError();
        }
        return $this->panelMensaje($texto, "danger", $cabecera="&iexcl;Error!");
    }
    
    private function panelMensaje($info, $tipo = "danger", $cabecera = "&iexcl;Atenci&oacute;n!") {
        $mensaje = '<div class="panel panel-' . $tipo . '"><div class="panel-heading">';
        $mensaje .= '<h3 class="panel-title">' . $cabecera . '</h3></div>';
        $mensaje .= '<div class="panel-body">';
        $mensaje .= $info;
        $mensaje .= '</div>';
        $mensaje .= '</div>';
        return $mensaje;
    }
    
}

?>