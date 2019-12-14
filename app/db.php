<?php

function OpenConnection()
{
  session_write_close();
  $serverName = "138.68.2.24"; //serverName\instanceName
  $port = 3308;
  $Database = "tg2019_almacen";
  //$Uid = "tgurus_alumno";
  //$PWD = "tg2019";
  $Uid = "root";
  $PWD = "devtg";
  $conn = new mysqli($serverName, $Uid, $PWD, $Database, $port);
  $conn->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
  $conn->set_charset("utf8");
  if( $conn ) {
       //echo "Conexión establecida.<br />";
  } else {
    echo "Conexión no se pudo establecer.<br />";
    die( print_r( sqlsrv_errors(), true));
  }
  return $conn;
}
