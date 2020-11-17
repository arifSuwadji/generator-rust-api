<?php

function find_string($string, $array){
    foreach ($array as $key) {
        //if (strstr($string, $url)) { // mine version
        if (strpos($string, $key) !== FALSE) { // Yoshi version
            echo "Match found\n"; 
            return true;
        }
    }
    echo "Not found!\n";
    return false;
}

?>