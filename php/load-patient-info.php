<?php 
require_once('load.php');

if(isset( $_GET['action'])){
    if( $_GET['action'] == 'update-patient'){
        $a = loadSelectedPatientInfo($_GET[DB_COL_HEALTHCARE_NO]);
    }
}

function loadSelectedPatientInfo($id){
    global $db;
    $result = $db->select("SELECT * FROM ".DB_PATIENT_TABLE." WHERE ".DB_COL_HEALTHCARE_NO." = $id LIMIT 1");
    if(!$result){return;}
    $r = mysql_fetch_assoc($result);
    $p = json_encode($r);
    echo $p;
}


?>