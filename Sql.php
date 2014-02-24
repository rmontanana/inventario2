<?php
    /** 
     * Gestión de una base de datos MySQL
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
class Sql {
    /**
     * @var mixed Manejador de la base de datos.
     */
    private $bdd=NULL;
    /**
     * @var string Mensaje del último mensaje de error generado
     */
    private $mensajeError='';
    /**
     * @var boolean Almacena el estado de error o no de la última acción.
     */
    private $error=false;
    /**
     * @var boolean Estado de la conexión con la base de datos.
     */
    private $estado=false;
    /**
     * @var mixed Objeto que alberga la última consulta ejecutada.
     */
    private $peticion=NULL;
    /**
     * @var integer Número de tuplas afectadas en la última consulta.
     */
    private $numero=0;
    /**
     * @var string vector de cadenas con los resultados de la petición.
     */
    private $datos=array();
    /**
     * Id del último registro insertado
     * @var integer mysql_
     */
    private $id;
    /**
     * Crea un objeto Sql y conecta con la Base de Datos.
     * @param string $servidor
     * @param string $usuario
     * @param string $baseDatos
     */
    public function __construct($servidor,$usuario,$clave,$baseDatos)
    {
        $this->bdd=new mysqli($servidor,$usuario,$clave,$baseDatos);
        if (mysqli_connect_errno()) {
            $this->mensajeError='<h1>Fallo al conectar con el servidor MySQL.</h1>';
            $this->mensajeError.="Servidor [".$servidor ."] usuario=[".$usuario."] clave [".$clave."] base [".$baseDatos."]";
            $this->error=true;
            $this->estado=false;
        } else {
            $this->mensajeError='';
            $this->error=false;
            $this->estado=true;
        }
        $this->peticion=NULL;
        return $this;
    }
    public function __destruct()
    {
        //Libera la memoria de una posible consulta.
        if ($this->peticion) {
            $this->peticion->free_result();
        }
        // Si estaba conectada la base de datos la cierra.
        if ($this->estado) {
            $this->bdd->close();
        }
    }
    public function filtra($cadena) 
    {
      return $this->bdd->real_escape_string($cadena);
    }
    public function ejecuta($comando)
    {
        if (!$this->estado) {
            $this->error=true;
            $this->mensajeError='No est&aacute; conectado';
            return false;
        }
        
        if (!$this->peticion=$this->bdd->query($comando)) {
            $this->error=true;
            $this->mensajeError='No pudo ejecutar la petici&oacute;n: '.$comando;
            return false;
        }
        $this->numero=$this->bdd->affected_rows;
        $this->id=$this->bdd->insert_id;
        $this->error=false;
        $this->mensajeError='';
        return true;
    }
    public function procesaResultado()
    {
        if (!$this->estado) {
            $this->error=true;
            $this->mensajeError='No está conectado a una base de datos';
            return NULL;
        }
        if (!$this->peticion) {
            $this->error=true;
            $this->mensajeError='No hay un resultado disponible';
            return NULL;
        }
        $datos=$this->peticion->fetch_assoc();
        $this->error=false;
        $this->mensajeError='';
        return ($datos);
    }
        public function camposResultado()
    {
        if (!$this->estado) {
            $this->error=true;
            $this->mensajeError='No está conectado a una base de datos';
            return NULL;
        }
        if (!$this->peticion) {
            $this->error=true;
            $this->mensajeError='No hay un resultado disponible';
            return NULL;
        }
        $datos=$this->peticion->fetch_field();
        $this->error=false;
        $this->mensajeError='';
        return ($datos);
    }
    /** 
     * Devuelve el número de tuplas afectadas en la última petición.
     * @return integer Número de tuplas.
     */
    public function numeroTuplas() {
        return $this->numero;
    }
    /**
     * Devuelve la condición de error de la última petición
     * @return boolean condición de error.
     */
    public function error() {
        return $this->error;
    }
    /**
     * Devuelve el mensaje de error de la última petición
     * @return <type>
     */
    public function mensajeError() {
        return $this->mensajeError.$this->bdd->error;
    }
    /**
     * Devuelve la estructura de campos de una tabla.
     * @param string $tabla Nombre de la tabla.
     * @return string vector asociativo con la descripción de la tabla [campo]->valor
     */
    public function estructura($tabla)
    {
        if ($this->peticion) {
            $this->peticion->free_result();
        }
        $comando="show full columns from $tabla";
        if (!$this->ejecuta($comando)) {
            return false;
        }
        while ($dato=$this->procesaResultado()) {
            $salida[]=$dato;
        }
        return $salida;
    }
    public function ultimoId()
    {
        return $this->id;
    }
    public function obtieneManejador()
    {
        return $this->bdd;
    }
    public function comienzaTransaccion()
    {
        return $this->bdd->autocommit(false);
    }
    public function abortaTransaccion()
    {
        $codigo = $this->bdd->rollback();
        $this->bdd->autocommit(true);
        return $codigo;     
    }
    public function finalizaTransaccion()
    {
        $codigo = $this->bdd->commit();
        $this->bdd->autocommit(true);
        return $codigo;
    }
}
  
?>
