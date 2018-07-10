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
//Para comprimir las imágenes
require_once 'Zebra_Image.php';
define('HAYQUEGRABAR', 1);
define('HAYQUEBORRAR', 2);
define('NOHACERNADA', 3);

class Imagen
{
    private $archivoSubido;
    public $archivoComprimido;
    private $extension;
    private $dirData;
    public $archivoCopiado;

    public function __construct()
    {
        $this->dirData = IMAGEDATA;
    }

    public function determinaAccion($campo)
    {
        if (isset($_POST[$campo]) && $_POST[$campo] == '') {
            return HAYQUEBORRAR; //Hay que borrar el archivo de imagen
        } elseif (isset($_FILES[$campo]['error']) && $_FILES[$campo]['error'] == 0) {
            return HAYQUEGRABAR; //Hay que guardar el archivo de imagen enviado
        } else {
            return NOHACERNADA; //No hay que hacer nada
        }
    }

    public function procesaEnvio($campo, &$mensaje)
    {
        try {
            // Undefined | Multiple Files | $_FILES Corruption Attack
            // If this request falls under any of them, treat it invalid.
            if (
                !isset($_FILES[$campo]['error']) ||
                is_array($_FILES[$campo]['error'])
            ) {
                throw new RuntimeException('Parámetros inválidos.');
            }

            // Check $_FILES['upfile']['error'] value.
            switch ($_FILES[$campo]['error']) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    throw new RuntimeException('No se ha enviado ningún fichero.');
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new RuntimeException('Se ha excedido el tamaño máximo.');
                default:
                    throw new RuntimeException('Error desconocido.');
            }

            // DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
            // Check MIME Type by yourself.
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            if (false === $ext = array_search(
                $finfo->file($_FILES[$campo]['tmp_name']),
                [
                    'jpg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                ],
                true
            )) {
                throw new RuntimeException('Formato de imagen inválido, no es {jpg, png, gif}');
            }
            $this->extension = $ext;
            // You should name it uniquely.
            // DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
            // On this example, obtain safe unique name from its binary data.
            $this->archivoSubido = sprintf('tmp/%s.%s', sha1_file($_FILES[$campo]['tmp_name']), $ext);
            if (!move_uploaded_file($_FILES[$campo]['tmp_name'], $this->archivoSubido)) {
                throw new RuntimeException('Fallo moviendo el archivo subido.');
            }
            //Todo ha ido correcto
            return true;
        } catch (RuntimeException $e) {
            $mensaje = $e->getMessage();

            return false;
        }
    }

    public static function borraImagenId($tabla, $id)
    {
        $extensiones = ['png', 'gif', 'jpg'];
        foreach ($extensiones as $extension) {
            $archivo = IMAGEDATA.'/'.$tabla.'_'.$id.'.'.$extension;
            if (file_exists($archivo)) {
                unlink($archivo);
            }
        }
    }

    public function copiaImagenId($valorImagen, $tabla, $id, &$mensaje)
    {
        $extension = strrchr($valorImagen, '.');
        $nombre = $this->dirData.'/'.$tabla.'_'.$id.$extension;
        if (!@copy($valorImagen, $nombre)) {
            $errors = error_get_last();
            $mensaje = 'No pudo copiar el archivo '.$valorImagen.' en '.$nombre.' Error = ['.$errors['message'].']';

            return false;
        }
        $this->archivoCopiado = $nombre;

        return true;
    }

    public function mueveImagenId($tabla, $id, &$mensaje)
    {
        if (!$this->comprimeArchivo($tabla.'_'.$id, $mensaje)) {
            return false;
        } else {
            return true;
        }
    }

    private function comprimeArchivo($id, &$mensaje)
    {
        $zebra = new Zebra_Image();
        $zebra->source_path = $this->archivoSubido;
        $this->archivoComprimido = $this->dirData.'/'.$id.'.'.$this->extension;
        $zebra->target_path = $this->archivoComprimido;
        $zebra->jpeg_quality = 100;

        // some additional properties that can be set
        // read about them in the documentation
        $zebra->preserve_aspect_ratio = true;
        $zebra->enlarge_smaller_images = true;
        $zebra->preserve_time = true;

        // resize the image to exactly 100x100 pixels by using the "crop from center" method
        // (read more in the overview section or in the documentation)
        //  and if there is an error, check what the error is about
        if (!$zebra->resize(640, 480, ZEBRA_IMAGE_CROP_CENTER)) {
            // if there was an error, let's see what the error is about
            switch ($zebra->error) {
                case 1: $mensaje = 'El fichero origen no se ha encontrado!';
                    break;
                case 2: $mensaje = 'No se puede leer el archivo origen '.$this->archivoSubido;
                    break;
                case 3: $mensaje = 'No se pudo escribir el archivo destino '.$this->archivoComprimido;
                    break;
                case 4: $mensaje = 'Formato de fichero origen no soportado '.$this->archivoSubido;
                    break;
                case 5: $mensaje = 'Formato de fichero destino no soportado '.$this->archivoComprimido;
                    break;
                case 6: $mensaje = 'La versión de la biblioteca GD no soporta el formato de destino '.$this->archivoComprimido;
                    break;
                case 7: $mensaje = 'La biblioteca GD no está instalada';
                    break;
                case 8: $mensaje = 'el comando "chmod" está deshabilitado por configuración';
                    break;
            }

            return false;
        } else {
            //Borra el archivo subido
            unlink($this->archivoSubido);

            return true;
        }
    }
}
