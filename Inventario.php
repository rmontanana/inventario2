<?php

/**
 * Clase Inventario que controla la ejecución principal del programa.
 * @author Ricardo Montañana Gómez <rmontanana@gmail.com>
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
// Clase del objeto principal de la aplicación
class Inventario {

    // Declaración de miembros
    private $bdd; // Enlace con el SGBD
    private $registrado; // Usuario registrado s/n
    private $usuario = NULL; // Nombre del usuario
    private $clave; //contraseña del usuario
    private $opcActual; // Opción elegida por el usuario
    private $perfil; //Permisos del usuario.
    private $estado; //BD conectada o no
    private $plant;

    // Constructor
    public function __construct() {
        // Analizamos la cadena de solicitud para saber
        // qué opción es la actual
        $this->opcActual = $_SERVER['QUERY_STRING'] == '' ? 'principal' : $_SERVER['QUERY_STRING'];
        // Iniciamos una sesión
        session_start();
        //Conexión con la base de datos.
        $this->bdd = new Sql(SERVIDOR, USUARIO, CLAVE, BASEDATOS);
        if ($this->bdd->error()) {
            echo '<h1>Fallo al conectar con el servidor MySQL.</h1>';
            echo SERVIDOR;
            echo "Servidor [ " . SERVIDOR . " ] usuario [" . USUARIO . "] clave [" . CLAVE . "] base [" . BASEDATOS . "]";
            $this->estado = false;
            return;
        } else {
            $this->estado = true;
        }
        //Selecciona la plantilla a utilizar
        $this->plant='plant/';
        $this->plant.=PLANTILLA;
        $this->plant.='.html';
        // Comprobamos si el usuario ya está registrado en esta sesión
        $this->registrado = isset($_SESSION['Registrado']);
        if ($this->registrado) {// si está...
            // recuperamos el nombre del usuario
            $this->usuario = $_SESSION['Usuario'];
            $this->perfil = $_SESSION['Perfil'];
            // en caso contrario comprobamos si tiene la cookie que le identifica como usuario
        } elseif (isset($_COOKIE['InventarioId'])) {
            // y usamos el Id para recuperar el nombre de la base de ddtos
            $this->recuperaNombreConId($_COOKIE['InventarioId']);
        } else { // en caso contrario el usuario no está registrado
            $this->usuario = '';
        }
    }

    public function estado() {
        return $this->estado;
    }

//     Esta función pondrá en marcha la aplicación ocupándose
//     de las acciones que no generan contenido, esto es
//     iniciar sesión, cerrarla, etc.
    public function Ejecuta() {
        // Dependiendo de la opción a procesar
        switch ($this->opcActual) {
            // El usuario quiere cerrar la sesión actual
            case 'cerrarSesion':
                // Eliminamos los datos de sesión
                session_unset();
                session_destroy();
                // y borramos la cookie para no reconocerlo
                // la próxima vez
                setcookie('InventarioId', '');
                // y le redirigmos a la página inicial
                header('Location: index.php');
                exit;
            // El usuario quiere iniciar sessión
            case 'registrarse':
                // Se identifica como usuario de la aplicación
                $resultado = $this->usuarioRegistrado();
                // Si lo obtuvimos es que el usuario está registrado
                if ($resultado) {
                    // establecemos las variables de sesión
                    $_SESSION['Registrado'] = true;
                    $_SESSION['Usuario'] = $this->usuario;
                    $_SESSION['Perfil'] = $this->perfil;
                    // y enviamos la cookie para reconocerlo la próxima vez
                    setcookie('InventarioId', $resultado, time() + 3600 * 24 * 365);
                    // Lo enviamos a la página de bienvenida
                    header('Location: index.php?bienvenido');
                    exit;
                }
                //Usuario incorrecto
                header('location:index.php?usuario_incorrecto');
                exit;
            case 'usuario_incorrecto':
                $this->opcActual = "principal";
                $contenido = $this->creaContenido();
                $contenido->usuario_incorrecto();
                $salida = new Distribucion($this->plant, $contenido);
                echo $salida->procesaPlantilla();
                break;

            default:
                // Creamos un objeto Distribución facilitándole el
                // nombre del archivo plantilla y el objeto que aportará
                // el contenido
                $opc = $_GET['opc'];
                list($opcion, $parametro) = explode("&", $this->opcActual);
                switch ($opc) {
                    case 'informe':
                        $enlace = 'xml/informe' . ucfirst($opcion) . '.xml';
                        //$enlace="tmp/inventarioUbicacion.xml";
                        $informe = new InformePDF($this->bdd, $enlace, $this->registrado);
                        $informe->crea($enlace);
                        $informe->cierraPDF();
                        $informe->imprimeInforme();
                        return;
                    default:
                        $salida = new Distribucion($this->plant, $this->creaContenido());
                        echo $salida->procesaPlantilla();
                        break;
                }
        }
    }

    private function creaContenido() {
        return new AportaContenido($this->bdd, $this->registrado, $this->usuario, $this->perfil, $this->opcActual);
    }

    // Esta función comprueba si el usuario está o no registrado,
    // devolviendo su IdSesion en caso afirmativo o false
    // en caso contrario
    private function usuarioRegistrado() {
        $this->usuario = $_POST['usuario'];
        $this->clave = $_POST['clave'];
        // ejecuta la consulta para buscar el usuario
        $res = $this->bdd->ejecuta("SELECT * FROM Usuarios WHERE nombre='$this->usuario'");
        // Si no hemos encontrado el usuario
        if (!$res) {
            return false; // devolvemos FALSE
        }
        // En caso contrario obtenemos en una matriz
        // los datos de las columnas
        $fila = $this->bdd->procesaResultado();
        // Para comprobar si la clave coincide
        if (trim($fila['clave']) == trim($this->clave)) {
            $this->perfil = $this->creaPerfil($fila);
            // caso en el que devolveremos un Id de sesión
            // único para este usuario
            return $fila['idSesion'];
        }
        // Si llegamos aquí es porque el usuario existe pero
        // la clave no es correcta
        return false;
    }

    private function creaPerfil($fila) {
        return array("Consulta" => $fila['consulta'], "Modificacion" => $fila['modificacion'],
            "Alta" => $fila['alta'], "Borrado" => $fila['borrado'], "Informe" => $fila['informe'],
            "Usuarios" => $fila['usuarios'], "Config" => $fila['config']);
    }

    // Esta función intenta recuperar el nombre del usuario
    // a partir del Id de sesión almacenado en una cookie,
    // dejando las variables Registrado y Usuario con
    // los valores apropiados
    // @param String Identificador de sesión del usuario actual
    private function recuperaNombreConId($idSesion) {
        // para ejecutar la consulta para buscar el Id de sesión
        $res = $this->bdd->ejecuta("SELECT * FROM Usuarios WHERE idSesion='$idSesion'");
        // Si no hemos encontrado el ID
        if (!$res) {
            // el usuario no está registrado
            $this->usuario = '';
            $this->registrado = false;
        } else {
            // En caso contrario obtenemos en una matriz
            // los datos de las columnas
            $fila = $this->bdd->procesaResultado();
            // Asignamos el nombre
            $this->usuario = $fila['nombre'];
            // damos el valor TRUE a bRegistrado
            $this->registrado = true;
            // y establecemos las variables de sessión para no tener
            // que efectuar todo este proceso de nuevo con cada
            // solicitud de página
            $_SESSION['Registrado'] = true;
            $_SESSION['Usuario'] = $this->usuario;
            $this->perfil = $this->creaPerfil($fila);
            $_SESSION['Perfil'] = $this->perfil;
        }
    }

}

?>
