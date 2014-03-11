<?php

require_once 'Configuracion.php';

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-03-07 at 23:58:14.
 */
class ConfiguracionTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Configuracion
     */
    protected $config;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->config = new Configuracion;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    /**
     * @covers Configuracion::ejecuta
     */
    public function testEjecuta()
    {
        $this->assertFileExists('inc/Configuracion.inc', "El fichero de configuración no existe");
        $permisos = fileperms('inc/Configuracion.inc');
        //Comprueba que el propietario o el grupo tengan derecho de escritura
        $test = $permisos & 0x0080 || $permisos & 0x0010;
        $this->assertTrue($test, "El fichero de configuración no tiene los permisos adecuados");
        //Comprueba que existan todas las claves editables en el fichero de configuración.
        $fichero = $this->config->obtieneFichero();
        $this->assertNotNull($fichero, "El fichero de configuración no se ha leído o no tiene contenido");
        $lineas = explode("\n", $fichero);
        $campos = $this->config->obtieneLista();
        $numero = 0;
        $lista = implode(",", $campos);
        //var_dump($campos);
        //var_dump($lista);
        foreach ($lineas as $linea) {
            if (stripos($linea, "DEFINE") !== false) {
                $this->assertStringMatchesFormat("define('%s', '%s');%S", $linea, "La línea [" . $linea . "] del fichero de configuración no tiene el formato correcto");
                $this->config->obtieneDatos($linea, $clave, $valor);
                if (stripos($lista, $clave) !== false) {
                    $this->assertContains($clave, $campos, "El fichero de configuración no tiene la clave [" . $clave . "]");
                    $numero++;
                    $verificados[] = $clave;
                }
            }
        }
        $this->assertCount($numero, $campos, "No se han verificado todas las claves");
        if ($numero != count($campos)) {
            echo "Las claves verificadas han sido: \n";
            var_export($verificados);
        }
        // @todo comprobar que la salida incluye una tabla con todos los campos editables.
        // @todo comprobar que los valores seleccionados se corresponden co los del fichero
        $salida = $this->config->ejecuta();
    }

}

?>