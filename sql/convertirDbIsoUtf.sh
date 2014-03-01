#!/bin/bash
echo "Proceso de conversión de base de datos de ISO-8859-1 a UTF-8"
echo "Sintaxis: " $0 " {usuario} {contraseña} {BaseDatosOrigen} {BaseDatosDestino}"
echo "El usuario debe tener acceso para crear la base de datos destino y acceso a la base de datos de origen"
echo "La base de datos Destino se borrará. Si no estás seguro pulsa control c"
read k
echo "Volcando la base de datos " $1
mysqldump -u $1 --password=$2 --result-file=salida.sql $3
echo "Creando la base de datos " $4
mysql -u $1 --password=$2 <<EOF
drop database $4 if exists
create database $4 charset utf8 collate utf8_spanish_ci;
EOF
echo "Importando los datos en la base de datos " $4
sed "s/latin1/utf8/g" salida.sql >salida2.sql
iconv -f iso-8859-1 -t utf8 salida2.sql >salida.iconv.sql
mysql -u $1 --password=$2 $4 < salida2.iconv.sql
echo "Re-exportando los datos"
mysqldump -u $1 --password=$2 --result-file=salida-final.sql $4
echo "Re-importando los datos"
mysql -u $1 --password=$2 $4 <salida-final.sql
echo "Proceso terminado"
