<?php
    require_once 'core/crud.php';
    require_once 'core/crud_sub.php';
    require_once 'core/crud_module.php';
    require_once 'core/read_module.php';
    require_once 'core/foreign_form.php';
    require_once 'core/form_need.php';
    require_once 'core/form_need_sub.php';
    require_once 'core/form_module.php';
    require_once 'core/read_module_sub.php';
    
    // echo "Commands: \n
    //     php -r \"require 'AdjiGenerator.php'; crud('table','folder','foreigns');\"
    // \n";
    function crud($table="", $folder="", $foreigns=""){
        crud_single($table, $folder, $foreigns);
    }

    // echo "Commands: \n
    //     php -r \"require 'AdjiGenerator.php'; crud_sub('table','folder1','folder2');\"
    // \n";
    function crud_sub($table="", $folder1="", $folder2=""){
        crud_single_sub($table, $folder1, $folder2);
    }

    // echo "Commands: \n
    //     php -r \"require 'AdjiGenerator.php'; crud_module('table', 'foreigns');\"
    // \n";
    function crud_module($table="", $foreigns=""){
        crud_single_module($table, $foreigns);
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

    // echo "Commands: \n
    //     php -r \"require 'AdjiGenerator.php'; form('table', 'folder1', 'folder2', 'foreigns');\"
    // \n";
    function form_sub($table="", $folder="", $folder2="", $foreigns=""){
        form_need_sub($table, $folder, $folder2, $foreigns);
    }

    // echo "Commands: \n
    //     php -r \"require 'AdjiGenerator.php'; form_module('table', 'foreigns');\"
    // \n";
    function form_module($table="", $foreigns=""){
        form_need_module($table, $foreigns);
    }

    // echo "Commands: \n
    //     php -r \"require 'AdjiGenerator.php'; read_sub_module('controller','folder','tables');\"
    // \n";

    function read_sub_module($controller="", $folder="", $tables=""){
        read_sub_tables($controller, $folder, $tables);
    }
?>