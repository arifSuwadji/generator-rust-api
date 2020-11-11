<?php
    require_once 'core/crud.php';
    require_once 'core/crud_sub.php';
    require_once 'core/crud_module.php';
    require_once 'core/read_module.php';
    
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

    // echo "Commands: \n
    //     php -r \"require 'AdjiGenerator.php'; crud_module('table');\"
    // \n";
    function crud_module($table=""){
        crud_single_module($table);
    }

    // echo "Commands: \n
    //     php -r \"require 'AdjiGenerator.php'; read_module('controller','tables');\"
    // \n";
    function read_module($controller="", $tables=""){
        read_tables($controller, $tables);
    }
?>