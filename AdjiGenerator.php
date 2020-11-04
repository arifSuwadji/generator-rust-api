<?php
    require_once 'core/crud.php';
    
    // echo "Commands: \n
    //     php -r \"require 'AdjiGenerator.php'; crud('table','folder');\"
    // \n";
    function crud($table="", $folder=""){
        crud_single($table, $folder);        
    }

?>