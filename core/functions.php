<?php
    require_once 'config/conn_postgresql.php';

    function AllField($table){
        $result = pg_query(Connect(), "SELECT column_name, data_type FROM information_schema.columns where table_name = '$table'");
        $allField = [];
        while($row = pg_fetch_object($result)){
            $allField[] = array('column_name' => $row->column_name, 'data_type' => $row->data_type);
        }
        return $allField;
    }

    function PrimaryField($table){
        $result = pg_query(Connect(), "
        SELECT               
            pg_attribute.attname, 
            format_type(pg_attribute.atttypid, pg_attribute.atttypmod) 
        FROM pg_index, pg_class, pg_attribute, pg_namespace 
        WHERE 
            pg_class.oid = '".$table."'::regclass AND 
            indrelid = pg_class.oid AND 
            nspname = 'public' AND 
            pg_class.relnamespace = pg_namespace.oid AND 
            pg_attribute.attrelid = pg_class.oid AND 
            pg_attribute.attnum = any(pg_index.indkey)
        AND indisprimary
        ");
        $primaryField = [];
        while($row = pg_fetch_object($result)){
            $primaryField = array('column_name' => $row->attname, 'data_type' => $row->format_type);
        }
        return $primaryField;
    }

    function createFile($string, $path){
        $create = fopen($path, "wb") or die("Unable to open $path file!");
        fwrite($create, $string);
        fclose($create);
        return $path;
    }

    function write_file($path, $string, $method){
        $fp = fopen($path, "a") or die("Unable to open $path file!");
        fwrite($fp, $string);
        fclose($fp);
        return $path;
    }
?>