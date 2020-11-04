<?php
error_reporting(E_ALL & ~E_NOTICE);
define ("HOST","127.0.0.1");
define ("DB_USER","postgres");
define ("DB_NAME","spdmlk");
define ("BASE_PATH","/home/arifsuwadji/braincode/project/spdmlk-be");

function Connect(){
    $connect = pg_connect("host=".HOST." port=5432 dbname=".DB_NAME." user=".DB_USER);
    $stat = pg_connection_status($connect);
    if($stat === PGSQL_CONNECTION_OK){
        return $connect;
    } else {
      die('Connection failed: ');
      return FALSE;
    }
}
