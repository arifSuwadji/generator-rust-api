<?php
    require_once 'functions.php';
    
    // echo "Commands: \n
    //     php -r \"require 'AdjiGenerator.php'; foreign('table');\"
    // \n";
    function foreign_form($table=""){
        //all field
        $allField = AllField($table);

        //primary field
        $primaryField = PrimaryField($table);
        var_dump($primaryField);
        
        $column_query = "";
        $type_data = "";
        $data_push = "";
    $i = 1;
    foreach($allField as $fieldName){
        // print($fieldName['column_name'].' : '.$fieldName['data_type']);
        if($primaryField['column_name'] != $fieldName['column_name']){
            $type_data .= $fieldName['data_type'] == 'integer' ? 'i32, ' : 'String, ';
            $data_push .= 'row.'. $fieldName['column_name'].', ';
        }
        $column_query .= $fieldName['column_name'].", ";
        $i++;
    }
    $data_src = "
pub async fn $table(pool: &Pool<PgConnection>) -> tide::Result<Vec<(serde_json::Value, String)>> {
    let mut data = Vec::new();
    let list = sqlx::query!(
        \"SELECT ".substr($column_query, 0, -2)." FROM $table\" )
        .fetch_all(pool) .await?;
    for row in list {
        data.push( (serde_json::json!(row.".$primaryField['column_name']."), ".substr($data_push,0,-2).") )
    }
    Ok(data)
}
";
    $path_src_data = BASE_PATH."/src/data.rs";
    if(!write_file($path_src_data, $data_src, 'a')){
        echo 'Unable to write SRC Data the file'."\r\n";
    }else{
        echo 'SRC Data written!'."\r\n";
    }
}

?>