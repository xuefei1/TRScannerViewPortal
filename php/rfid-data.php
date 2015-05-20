<?php
include_once('rf-config.php');
include_once('rf-db.php');


if(isset($_POST['data'])){
    postData($_POST['data']);
}

if(isset($_POST['action'])){
    if($_POST['action'] == 'save-setting'){
        saveSettingsPreference();
    }
}

if(isset($_POST['action'])){
    if ($_POST['action'] == 'load-prefs'){
        getSettingsForPI();
    }
}

if(isset($_GET['action'])){
    if ($_GET['action'] == 'load-settings'){
        getSettingPreference();
    }elseif ($_GET['action'] == 'load-prefs'){
        getSettingsForPI();
    }
}

function postData(){
    global $db;
    if($db->execSQL("INSERT INTO ".DB_SCAN_TABLE." VALUES(DEFAULT, '".$_POST['data']."', NOW()
)")){
        echo 'success';
    }else{
        echo 'operation failed';
    }
}

function saveSettingsPreference(){
    global $db;
    $continuous_scanning = 'K';
    $tag_id_streaming = 'D';
    $frequency_hopping = 'H';
    $upload_tag_to_server = 'false';
    $protocol_type_output = 'M';
    $reader_id_output = 'I';
    $frequency_channel_output ='V';
    $protocol_control_output = "O";
    if(isset($_POST['continuous_scanning'])){
        $continuous_scanning = $_POST['continuous_scanning'];
    }
    if(isset($_POST['tag_id_streaming'])){
        $tag_id_streaming = $_POST['tag_id_streaming'];
    }
    if(isset($_POST['frequency_hopping'])){
        $frequency_hopping = $_POST['frequency_hopping'];
    }
    if(isset($_POST['upload_tag_to_server'])){
        $upload_tag_to_server = $_POST['upload_tag_to_server'];
    }
    if(isset($_POST['protocol_type_output'])){
        $protocol_type_output = $_POST['protocol_type_output'];
    }
    if(isset($_POST['reader_id_output'])){
        $reader_id_output = $_POST['reader_id_output'];
    }
    if(isset($_POST['frequency_channel_output'])){
        $frequency_channel_output = $_POST['frequency_channel_output'];
    }
    if(isset($_POST['protocol_control_output'])){
        $protocol_control_output = $_POST['protocol_control_output'];
    }

    $sql = "UPDATE ".DB_SCANNER_SETTING_TABLE." SET ".DB_COL_CONTINUOUS_SCANNING."='$continuous_scanning', ".DB_COL_TAG_ID_STREAMING."='$tag_id_streaming', ".DB_COL_PROTOCOL_MODE."='".$_POST['protocol_mode']."', ".DB_COL_TRANSMIT_FREQUENCY."='".$_POST['transmit_frequency']."', ".DB_COL_FREQUENCY_HOPPING."='".$frequency_hopping."', ".DB_COL_FREQUENCY_CHANNEL."='".$_POST['frequency_channel']."', ".DB_COL_OUTPUT_POWER_LEVEL."='".$_POST['output_power_level']."', ".DB_COL_IQ_RECEIVE_CHANNEL."='".$_POST['iq_receive_channel']."', ".DB_COL_UPLOAD_TAG_TO_SERVER."='".$upload_tag_to_server."', ".DB_COL_USERNAME."='".$_POST['username']."', ".DB_COL_PASSWORD."='".$_POST['password']."', ".DB_COL_PROTOCOL_TYPE_OUTPUT."='".$protocol_type_output."', ".DB_COL_READER_ID_OUTPUT."='".$reader_id_output."', ".DB_COL_FREQUENCY_CHANNEL_OUTPUT."='".$frequency_channel_output."', ".DB_COL_PROTOCOL_CONTROL_OUTPUT."='".$protocol_control_output."', ".DB_COL_SET_READER_SESSION."='".$_POST['set_reader_session']."', ".DB_COL_NUMBER_OF_COLLISION_SLOTS."='".$_POST['slt']."', ".DB_COL_NUMBER_OF_COLLISION_ATTEMPTS."='".$_POST['atp']."', ".DB_COL_SET_ACCESS_PASSWORD."='".$_POST['set_access_password']."', ".DB_COL_SET_KILL_PASSWORD."='".$_POST['set_kill_password']."' WHERE id = 1" ;

    if($db->execSQL($sql)){
        echo 'saved';
    }else{
        echo 'error: '.mysql_error();
    }
}

function getSettingPreference(){
    global $db;
    $result = $db->select("SELECT * FROM ".DB_SCANNER_SETTING_TABLE." WHERE id = 1 LIMIT 1");
    if(!$result){
        echo json_encode(array('message' => 'db error'));
    }else if(mysql_num_rows($result) == 0){
        echo json_encode(array('message' => 'no rows selected'));
    }else{
        echo(json_encode(mysql_fetch_assoc($result)));
    }
}

function getSettingsForPI(){
    global $db;
    $result = $db->select("SELECT ".DB_COL_CONTINUOUS_SCANNING.", ".DB_COL_TAG_ID_STREAMING.", ".DB_COL_PROTOCOL_MODE.", ".DB_COL_TRANSMIT_FREQUENCY.", ".DB_COL_FREQUENCY_HOPPING.", ".DB_COL_FREQUENCY_CHANNEL.", ".DB_COL_OUTPUT_POWER_LEVEL.", ".DB_COL_IQ_RECEIVE_CHANNEL.", ".DB_COL_PROTOCOL_TYPE_OUTPUT.", ".DB_COL_READER_ID_OUTPUT.", ".DB_COL_FREQUENCY_CHANNEL_OUTPUT.", ".DB_COL_PROTOCOL_CONTROL_OUTPUT.", ".DB_COL_SET_READER_SESSION.", ".DB_COL_NUMBER_OF_COLLISION_SLOTS.", ".DB_COL_NUMBER_OF_COLLISION_ATTEMPTS." FROM ".DB_SCANNER_SETTING_TABLE." WHERE id = 1 LIMIT 1");
    if(!$result){
        echo implode(";", array('message' => 'db error'));
    }else if(mysql_num_rows($result) == 0){
        echo implode(";", array('message' => 'no rows selected'));
    }else{
        
        echo("CONFIG;".implode(";", mysql_fetch_assoc($result))." success");
    }
}

?>