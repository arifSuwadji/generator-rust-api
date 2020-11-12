<?php
    require_once 'core/crud.php';
    require_once 'core/crud_sub.php';
    require_once 'core/crud_module.php';
    require_once 'core/read_module.php';
    require_once 'core/foreign_form.php';
    require_once 'core/form_need.php';
    
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

    // echo "Commands: \n
    //     php -r \"require 'AdjiGenerator.php'; foreign('table');\"
    // \n";
    function foreign($table=""){
        foreign_form($table);
    }

    // echo "Commands: \n
    //     php -r \"require 'AdjiGenerator.php'; form('table', 'folder', 'foreigns');\"
    // \n";
    function form($table="", $folder="", $foreigns=""){
        form_need($table, $folder, $foreigns);
    }
?>