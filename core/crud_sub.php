<?php
    require_once 'functions.php';
    
    // echo "Commands: \n
    //     php -r \"require 'AdjiGenerator.php'; crud('table','folder1','folder2');\"
    // \n";
    function crud_single_sub($table="", $folder1="", $folder2=""){
        //all field
        $allField = AllField($table);

        //primary field
        $primaryField = PrimaryField($table);
        var_dump($primaryField);

        $string .="use tide::{Request, Response};
use sqlx::PgPool;
use crate::{data, data::{fields, Type}};

#[derive(serde::Serialize, serde::Deserialize, Debug)]
struct ".ucfirst($table)." {
        ";
        $column_query = "";
        $value_query = "";
        $param_query = "";
        $value_update = "";
        $json_param = "";
    $i = 1;
    foreach($allField as $fieldName){
        // print($fieldName['column_name'].' : '.$fieldName['data_type']);
        $string .= $fieldName['column_name']." : "; $string .= $fieldName['data_type'] == 'integer' ? 'i32'.",
        " : 'String'.",
        ";
        $column_query .= $fieldName['column_name'].", ";
        $param_query .= $table.".".$fieldName['column_name'].", ";
        $value_query .= "$".$i.", ";
        $value_update .= $fieldName['column_name']."=$".$i.", ";
        $json_param .= "\"".$fieldName['column_name']."\":\"\", ";
        $i++;
    }
    $string .="
    #[serde(skip_deserializing)]
}

#[derive(serde::Deserialize)]
struct PK { ".$primaryField['column_name'].": "; $string .= $primaryField['data_type'] == 'integer' ? 'i32 } // primary key' : 'String'." } // primary key
    ";
    $string .="
// http://127.0.0.1:8182/$folder1/$folder2/$table
pub async fn list(req: Request<PgPool>) -> tide::Result<Response> {
    let pool = req.state();

    let list_".$table." = sqlx::query_as!( ".ucfirst($table).",
        \"SELECT ".substr($column_query,0,-2)." FROM ".$table."\" )
        .fetch_all(pool) .await?;

    crate::to_json(&list_".$table.")
}

// http://127.0.0.1:8182/$folder1/$folder2/$table
pub async fn tambah(mut req: Request<PgPool>) -> tide::Result<Response> {
    let ".$table.": ".ucfirst($table)." = req.body_json().await?;
    let pool = req.state();

    let _result = sqlx::query!(
        \"INSERT INTO ".$table." (".substr($column_query,0,-2).") VALUES (".substr($value_query,0, -2).")\","
        .substr($param_query,0,-2).")
        .execute(pool) .await?;

    crate::ws_response(\"OK\", \"Data telah tersimpan\")
}

// http://127.0.0.1:8182/$folder1/$folder2/$table
pub async fn edit(mut req: Request<PgPool>) -> tide::Result<Response> {
    let ".$table.": ".ucfirst($table)." = req.body_json().await?;
    let pool = req.state();

    let _result = sqlx::query!(
        \"UPDATE ".$table." SET ".substr($value_update,0, -2)." WHERE ".$primaryField['column_name']."=$1\",
        ".substr($param_query, 0, -2).")
        .execute(pool) .await?;

    crate::ws_response(\"OK\", \"Data telah diupdate\")
}

// http://127.0.0.1:8182/$folder1/$folder2/$table?".$primaryField['column_name']."=
pub async fn hapus(req: Request<PgPool>) -> tide::Result<Response> {
    let pool = req.state();
    let pk: PK = req.query()?;

    let _result = sqlx::query!(\"DELETE FROM ".$table." WHERE ".$primaryField['column_name']." = $1\", pk.".$primaryField['column_name'].")
        .execute(pool) .await?;

    crate::ws_response(\"OK\", \"Data telah dihapus\")
}
";
$string .="
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
            }else if($fieldName['data_type'] == 'json'){
    $string .="(\"".$fieldName['column_name']."\", Type::Number, vec![]),
    ";
    $string .="(\"jenisbayar_".$fieldName['column_name']."\", Type::Select, data::jenis_pembayaran()),
    ";
            }else{
                if($fieldName['column_name'] == 'id'){
    $string .="(\"".$fieldName['column_name']."\", Type::Number, data::auto_inc()),
    ";
                }else{
                    if($fieldName['data_type'] == 'integer'){
    $string .="(\"".$fieldName['column_name']."\", Type::Number, vec![]),
    ";
                    }else if($fieldName['data_type'] == 'smallint'){
    $string .="(\"".$fieldName['column_name']."\", Type::Number, vec![]),
    ";
                    }else{
    $string .="(\"".$fieldName['column_name']."\", Type::Text, vec![]),
    ";
                    }
                }
            }
        }
    }
    $string .="
    ])
}
";
        //controller
        createFile($string, BASE_PATH."/src/handler/$folder1/$folder2/".$table.".rs");
        
        //initilize controller
        $mod = BASE_PATH."/src/handler/$folder1/$folder2/mod.rs";
        $data = "pub mod $table;\n";
        if(!write_file($mod, $data, 'a')){
            echo 'Unable to write mod the file'."\r\n";
        }else{
            echo 'Mod written!'."\r\n";
        }

        //path
        $path = BASE_PATH."/src/paths.rs";
        $dataPath = "
    app.at(\"/$folder1/$folder2/$table\")
        .get( $table::list)
        .post($table::tambah)
        .patch($table::edit)
        .delete($table::hapus);\n";
        if(!write_file($path, $dataPath, 'a')){
            echo 'Unable to write paths the file'."\r\n";
        }else{
            echo 'Paths written!'."\r\n";
        }
        
        //docs
        $path_doc = BASE_DOC."/api-spdmlk.html";
        $docs = "
                <li>".ucfirst($table)."
                    <ol>
                        <li>List<pre>
    GET /$folder1/$folder2/$table
    JSON Response Body : [{".substr($json_param, 0, -2)."}]
                        </pre></li>
                        <li>Tambah<pre>
    POST /$folder1/$folder2/$table
    JSON Request Body: {".substr($json_param, 0, -2)."}

    Keterangan :
                        </pre></li>
                        <li>Edit<pre>
    PATCH /$folder1/$folder2/$table
    JSON Request Body: {".substr($json_param, 0, -2)."}
                        </pre></li>
                        <li>Delete<pre>
    DELETE /$folder1/$folder2/$table?".$primaryField['column_name']."=
                        </pre></li>
                    </ol>
                </li>
        ";
        if(!write_file($path_doc, $docs, 'a')){
            echo 'Unable to write docs the file'."\r\n";
        }else{
            echo 'Docs written!'."\r\n";
        }
    }

?>