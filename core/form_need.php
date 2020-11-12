<?php
    require_once 'functions.php';
    
    // echo "Commands: \n
    //     php -r \"require 'AdjiGenerator.php'; form('table', 'folder', 'foreigns');\"
    // \n";
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
    function form_need($table="", $folder="", $foreigns=""){
        //all field
        $allField = AllField($table);

        //primary field
        $primaryField = PrimaryField($table);
        var_dump($primaryField);
        
    $string .="
use crate::{data, data::{fields, Type}};
pub async fn form(req: Request<PgPool>) -> tide::Result<Body> {
    let pool = req.state();
    fields(vec![
        ";
    $arrForeigns = explode(';', $foreigns);
    foreach($allField as $fieldName){
        // var_dump($arrForeigns);
        $found_string = find_string($fieldName['column_name'], $arrForeigns);
        // echo($found_string);
        if($found_string){
    $string .="(\"".$fieldName['column_name']."\", Type::Select, data::".$fieldName['column_name']."(pool).await?),
    ";
        }else{
            if($fieldName['data_type'] == 'USER-DEFINED'){
    $string .="(\"".$fieldName['column_name']."\", Type::Select, data::".$fieldName['column_name']."()),
    ";
            }else{
                if($fieldName['column_name'] == 'id'){
    $string .="(\"".$fieldName['column_name']."\", Type::Number, data::auto_inc()),
    ";
                }else{
    $string .="(\"".$fieldName['column_name']."\", Type::Text, vec![]),
    ";
                }
            }
        }
    }
    $string .="
    ])
}
";
        //controller
        $pathController = BASE_PATH."/src/handler/$folder/".$table.".rs";
        if(!write_file($pathController, $string, 'a')){
            echo 'Unable to write controller the file'."\r\n";
        }else{
            echo 'Controller written!'."\r\n";
        }
        
        //path
        $path = BASE_PATH."/src/paths.rs";
        $dataPath = "
    app.at(\"/$folder/$table\")
        .get( $table::list)
        .post($table::tambah)
        .patch($table::edit)
        .delete($table::hapus);";
        $newDataPath ="
    app.at(\"/$folder/$table\")
        .put($table::form)
        .get($table::list)
        .post($table::tambah)
        .patch($table::edit)
        .delete($table::hapus);";
        $content = file_get_contents($path);
        $content = str_replace($dataPath, $newDataPath, $content);
        if(!file_put_contents($path, $content)){
            echo 'Unable to write paths the file'."\r\n";
        }else{
            echo 'Paths written!'."\r\n";
        }
        
        //docs
        $path_doc = BASE_DOC."/api-spdmlk.html";
        $docs = "
                        <li>Form<pre>
    PUT $folder/$table
    JSON Response Body : [{".substr($json_param, 0, -2)."}]
                        </pre></li>
        ";
        if(!write_file($path_doc, $docs, 'a')){
            echo 'Unable to write docs the file'."\r\n";
        }else{
            echo 'Docs written!'."\r\n";
        }
    }

?>