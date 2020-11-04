<?php
    require_once 'functions.php';
    
    // echo "Commands: \n
    //     php -r \"require 'AdjiGenerator.php'; crud('table','folder');\"
    // \n";
    function crud_single($table="", $folder=""){
        //all field
        $allField = AllField($table);

        //primary field
        $primaryField = PrimaryField($table);
        var_dump($primaryField);

        $string .="use tide::{Request, Response};
use sqlx::PgPool;

#[derive(serde::Serialize, serde::Deserialize, Debug)]
struct ".ucfirst($table)." {
        ";
        $column_query = "";
        $param_query = "";
    foreach($allField as $fieldName){
        // print($fieldName['column_name'].' : '.$fieldName['data_type']);
        $string .= $fieldName['column_name']." : "; $string .= $fieldName['data_type'] == 'integer' ? 'i32,' : 'String'.",
        ";
        $column_query .= $fieldName['column_name'].", ";
        $param_query .= $table.".".$fieldName['column_name'].", ";
    }
    $string .="
}

#[derive(serde::Deserialize)]
struct PK { ".$primaryField['column_name'].": "; $string .= $primaryField['data_type'] == 'integer' ? 'i32 } // primary key' : 'String'." } // primary key
    ";
    $string .="
pub async fn list(req: Request<PgPool>) -> tide::Result<Response> {
    let pool = req.state();

    let list_".$table." = sqlx::query_as!( ".ucfirst($table).",
        \"SELECT ".substr($column_query,0,-2)." FROM ".$table."\" )
        .fetch_all(pool) .await?;

    crate::to_json(&list_".$table.")
}

pub async fn tambah(mut req: Request<PgPool>) -> tide::Result<Response> {
    let ".$table.": ".ucfirst($table)." = req.body_json().await?;
    let pool = req.state();

    let _result = sqlx::query!(
        \"INSERT INTO ".$table." (".substr($column_query,0,-2).") VALUES (";
    for($i=0; $i < count($allField); $i++){
        $n = $i + 1;
        $string .= "$".$n.", ";
    }
        $string .=")\","
        .substr($param_query,0,-2).")
        .execute(pool) .await?;

    crate::ws_response(\"OK\", \"Data telah tersimpan\")
}

pub async fn edit(mut req: Request<PgPool>) -> tide::Result<Response> {
    let ".$table.": ".ucfirst($table)." = req.body_json().await?;
    let pool = req.state();

    let _result = sqlx::query!(
        \"UPDATE ".$table." SET ";
    $i=1;
    foreach($allField as $fieldName){
        $string .=$fieldName['column_name']."=$".$i.",";
        $i++;
    }
        $string .=" WHERE ".$primaryField['column_name']."=$1\",
        ".$param_query.")
        .execute(pool) .await?;

    crate::ws_response(\"OK\", \"Data telah diupdate\")
}

pub async fn hapus(req: Request<PgPool>) -> tide::Result<Response> {
    let pool = req.state();
    let pk: PK = req.query()?;

    let _result = sqlx::query!(\"DELETE FROM ".$table." WHERE ".$primaryField['column_name']." = $1\", pk.".$primaryField['column_name'].")
        .execute(pool) .await?;

    crate::ws_response(\"OK\", \"Data telah dihapus\")
}
        ";
        //controller
        createFile($string, BASE_PATH."/src/handler/".$folder."/".$table.".rs");
        
        //initilize controller
        // $mod = BASE_PATH."/src/handler/$folder/mod.rs";
        // $data = "pub mod $table;\n";
        // if(!write_file($mod, $data, 'a')){
        //     echo 'Unable to write mod the file'."\r\n";
        // }else{
        //     echo 'Mod written!'."\r\n";
        // }

        //path
    //     $path = BASE_PATH."/src/paths.rs";
    //     $dataPath = "
    // app.at(\"/$folder/$table\")
    //     .get( $table::list)
    //     .post($table::tambah)
    //     .patch($table::edit)
    //     .delete($table::hapus);\n";
    //     if(!write_file($path, $dataPath, 'a')){
    //         echo 'Unable to write paths the file'."\r\n";
    //     }else{
    //         echo 'Paths written!'."\r\n";
    //     }
    }

?>