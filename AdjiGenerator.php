<?php
    require_once 'core/crud.php';
    require_once 'core/crud_sub.php';
    
    // echo "Commands: \n
    //     php -r \"require 'AdjiGenerator.php'; crud('table','folder');\"
    // \n";
    function crud($table="", $folder=""){
        crud_single($table, $folder);        
    }

    // echo "Commands: \n
    //     php -r \"require 'AdjiGenerator.php'; crud_sub('table','folder1','folder2');\"
    // \n";
    function crud_sub($table="", $folder1="", $folder2=""){
        crud_single_sub($table, $folder1, $folder2);
    }
?>