<?php
    require_once 'functions.php';
    
    // echo "Commands: \n
    //     php -r \"require 'AdjiGenerator.php'; crud('controller','tables');\"
    // \n";
    function read_tables($controller="",$tables=""){
        $arrTables = explode(';', $tables);
        $string .="use tide::{Request, Response};
use sqlx::PgPool;
        ";
        $all_query = array();
        $all_json_param = array();
        foreach($arrTables as $table){
            //all field
            $allField = AllField($table);

            //primary field
            $primaryField = PrimaryField($table);
            var_dump($primaryField);

            $string .="
#[derive(serde::Serialize, serde::Deserialize, Debug)]
struct ".ucfirst($table)." {
        ";
            $column_query = "";
            $json_param = "";
            $i = 1;
            foreach($allField as $fieldName){
        // print($fieldName['column_name'].' : '.$fieldName['data_type']);
        $string .= $fieldName['column_name']." : "; $string .= $fieldName['data_type'] == 'integer' ? 'i32'.",
        " : 'String'.",
        ";
                $column_query .= $fieldName['column_name'].", ";
                $json_param .= "\"".$fieldName['column_name']."\":\"\", ";
                $i++;
            }
            array_push($all_query, [$table => $column_query ]);
            array_push($all_json_param, [$table => $json_param]);
    $string .="
}
    ";
        }

    $string .="
// http://127.0.0.1:8182/$controller
pub async fn list(req: Request<PgPool>) -> tide::Result<Response> {
    let pool = req.state();
    ";
    // echo("all query ;");
    // var_dump($all_query);
    foreach($all_query as $key => $value){
        foreach($value as $last_key => $last_value){
    $string .="
    if('$last_key'){
        let list_".$last_key." = sqlx::query_as!( ".ucfirst($last_key).",
            \"SELECT ".substr($last_value,0,-2)." FROM ".$last_key."\" )
            .fetch_all(pool) .await?;

        crate::to_json(&list_".$last_key.")
    }
    ";
        }
    }
    $string .="
}
    ";
        //controller
        createFile($string, BASE_PATH."/src/handler/".$controller.".rs");
        
        //initilize controller
        $mod = BASE_PATH."/src/handler/mod.rs";
        $data = "pub mod $controller;\n";
        // if(!write_file($mod, $data, 'a')){
        //     echo 'Unable to write mod the file'."\r\n";
        // }else{
        //     echo 'Mod written!'."\r\n";
        // }

        //path
        $path = BASE_PATH."/src/paths.rs";
        $dataPath = "
    app.at(\"/$controller\")
        .get( $controller::list);\n";
        // if(!write_file($path, $dataPath, 'a')){
        //     echo 'Unable to write paths the file'."\r\n";
        // }else{
        //     echo 'Paths written!'."\r\n";
        // }
        
        //docs
        $path_doc = BASE_DOC."/api-spdmlk.html";
        $docs = "
                <li>".ucfirst($controller)."
                    <ol>
                        <li>List<pre>
    GET /$controller
        ";
        // echo("all json param;");
        // var_dump($all_json_param);
        foreach($all_json_param as $key => $value){
            foreach($value as $last_key => $last_value){

        $docs .="
        jika ".ucfirst($last_key)."
    JSON Response Body : [{".substr($last_value, 0, -2)."}]
        ";
            }
        }
        $docs .="
                        </pre></li>
                    </ol>
                </li>
        ";
        // if(!write_file($path_doc, $docs, 'a')){
        //     echo 'Unable to write docs the file'."\r\n";
        // }else{
        //     echo 'Docs written!'."\r\n";
        // }
    }

?>