# Inventario de Centro Educativo
Copyright (c) 2008-2014, Ricardo Montañana Gómez

Inventario2 is free software: you can redistribute it and/or modify 
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.


Aplicación de inventario para un centro educativo.

Utiliza:
*PHP v. 5.3.x
*MySQL v. 5.1.x
*Apache

##Instalación
Para instalar la aplicación basta con seguir estos pasos:
###1. Copiar los archivos en una ubicación a la que tenga acceso el usuario con el que se ejecuta el servidor Apache (apache, _www, etc.).

    mkdir Inventario
    cd Inventario
    unizp ../1.02.zip 
        o
    tar xvzf ../1.02.tgz

###2. Crear un directorio temporal y dar derechos de escritura a los ficheros de configuración.
    
    mkdir tmp
    chown apache tmp
    chown apache inc/configuracion.inc
    
###3. Crear una base de datos en MySQL con el siguiente comando

    create database Inventario character set utf8 collate utf8_spanish_ci;

###4. Dar todos los derechos de acceso a esa base de datos a un usuario:

    grant all on Inventario.* to usuario identified by "contraseña";

###5. Crear la estructura de la base de datos para poder comenzar a trabajar:

    mysql -u usuario --password=contraseña <sql/setup.sql

Con esto queda instalado el programa. Se crean en este proceso dos usuarios:

    Usuario: admin              Usuario: demo
    Contraseña: pruebas         Contraseña: pruebas

##Modelo
El modelo de datos que se ha utilizado ha sido:
![modeloERInventario2.png](https://bitbucket.org/repo/Ag6zy8/images/4106389828-modeloERInventario2.png)

###Procedimiento de actualización de ISO-8859-1 a UTF-8 en MySQL
Para actualizar la base de datos tan sólo hay que modificar el tipo de la base de datos y de las tablas a utf8 mediante workbench o mediante el comando:

    use Inventario2;
    alter database Inventario2 character set utf8 collate utf8_spanish_ci;
    alter table Articulos convert to character set utf8 collate utf8_spanish_ci;
    alter table Ubicaciones convert to character set utf8 collate utf8_spanish_ci;
    alter table Elementos convert to character set utf8 collate utf8_spanish_ci;
    alter table Usuarios convert to character set utf8 collate utf8_spanish_ci;
    
Una vez hecho esto hay que asegurarse que el charset con el que trabaja el servidor MySQL es utf8 para eso se entra en mysql y mediante el comando status se averigua. Se puede cambiar en my.cnf o mediante workbench crear/modificar el fichero de configuración.

Contenido mínimo del fichero /etc/my.cnf

    [mysqld]
    character-set-server = utf8
    collation-server = utf8_spanish_ci

    [client]
    default-character-set=utf8