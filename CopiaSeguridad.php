<?php
/**
 * @copyright Copyright (c) 2014, Ricardo Montañana Gómez
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
 */
class CopiaSeguridad
{
    private $mensaje;
    private $baseDatos;
    private $imagenes;

    public function creaCopia()
    {
        if (!$this->copiaBaseDatos()) {
            return false;
        }
        if (!$this->copiaImagenes()) {
            return false;
        }
        if (!$this->empaqueta()) {
            return false;
        }

        return true;
    }

    public function dialogo()
    {
        $dialogo = '<div class="container col-5"><div class="jumbotron">
                      <h1>Copia de Seguridad</h1>
                      <p>¿Desea realizar una copia de seguridad de todos los datos de la Base de Datos y de todas las Imágenes?</p>
                      <p><a class="btn btn-primary btn-lg" role="button" onClick="location.href='."'index.php'".'"><span class="glyphicon glyphicon-arrow-left"></span> Volver</a>
                         <a class="btn btn-success btn-lg" role="button" onClick="location.href='."'index.php?copiaseg&confirmado=1'".'">
                             <span class="glyphicon glyphicon-cloud-download"></span> Continuar</a></p>
                    </div></div>';

        return $dialogo;
    }

    private function copiaBaseDatos()
    {
        $archivo_sql = TMP.'/baseDatos'.BASEDATOS.'.sql';
        $baseDatosComprimida = $archivo_sql.'.gz';
        $this->baseDatos = $baseDatosComprimida;
        if (file_exists($baseDatosComprimida)) {
            unlink($baseDatosComprimida);
        }
        $comando = escapeshellcmd(MYSQLDUMP.' -h '.SERVIDOR.' -P '.PUERTO.' -u '.USUARIO.' --password='.CLAVE.' --result-file='.$archivo_sql.' '.BASEDATOS);
        $comando2 = escapeshellcmd(GZIP.' -9f '.$archivo_sql);
        exec($comando);
        exec($comando2);
        if (filesize($baseDatosComprimida) < 1024) {
            //No se ha realizado la copia de seguridad
            $mensaje = 'La copia de seguridad no se ha realizado correctamente.<br><br>';
            $mensaje .= 'Compruebe que las rutas a los programas mysqldump y gzip en configuraci&oacute;n est&aacute;n correctamente establecidas ';
            $mensaje .= 'y que los datos de acceso a la base de datos sean correctos.<br>';
            $mensaje .= 'mysqldump=['.MYSQLDUMP.']<br>';
            $mensaje .= 'gzip=['.GZIP.']';
            $this->mensaje = $mensaje;
            $this->error = true;

            return false;
        }

        return true;
    }

    private function copiaImagenes()
    {
        $copiaImagenes = TMP.'/Imagenes.tbz';
        $this->imagenes = $copiaImagenes;
        if (file_exists($copiaImagenes)) {
            unlink($copiaImagenes);
        }
        $comando = escapeshellcmd('tar cf '.$copiaImagenes.' '.IMAGEDATA);
        exec($comando);

        if (filesize($copiaImagenes) == 0) {
            $this->error = true;
            $mensaje = 'No se ha podido comprimir el directorio de las imágenes '.IMAGEDATA.'<br>';
            $mensaje .= 'Compruebe que la ruta de acceso al programa tar en configuraci&oacute;n est&aacute; correctamente establecida';
            $this->mensaje = $mensaje;

            return false;
        }

        return true;
    }

    private function empaqueta()
    {
        // Empaqueta los dos archivos en el que va a devolver
        $nombreCopia = TMP.'/Copia'.BASEDATOS.strftime('%Y%m%d%H%M').'.tar';
        if (file_exists($nombreCopia)) {
            unlink($nombreCopia);
        }
        $comando = escapeshellcmd('tar cf '.$nombreCopia.' '.$this->baseDatos.' '.$this->imagenes);
        exec($comando);
        if (filesize($nombreCopia) == 0 || !file_exists($nombreCopia)) {
            $this->error = true;
            $mensaje = 'No se ha creado el paquete con los archivos de imágenes en [<b>'.$this->imagenes.'</b>] y <br>';
            $mensaje .= ' con el archivo de Base de Datos [<b>'.$this->baseDatos.'</b>]<br><br>';
            $mensaje .= 'Compruebe que los datos de configuración están correctamente establecidos <br>';
            $mensaje .= 'El comando de copia fue ['.$comando.']<br>';
            $mensaje .= 'gzip=['.GZIP.']';
            $this->mensaje = $mensaje;

            return false;
        }
        $this->error = false;
        unlink($this->baseDatos);
        unlink($this->imagenes);
        $mensaje = 'Copia de seguridad realizada con &eacute;xito.<br><br>Pulse sobre el siguiente enlace para descargar:<br><br>';
        $mensaje .= '<a href="'.$nombreCopia.'">Descargar Copia de Seguridad de Datos</a><br><br>';
        $mensaje .= 'El paquete de copia contiene un archivo con la copia de la información de la base de datos y un archivo que contiene el directorio de las fotografías e imágenes asociadas a los datos';
        $this->mensaje = $mensaje;

        return true;
    }

    public function mensaje()
    {
        return $this->mensaje;
    }
}
