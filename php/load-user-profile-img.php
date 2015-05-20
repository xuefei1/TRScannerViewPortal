<?php
require_once("config.php");

if(session_id() == ''){
    session_start();
}
if(isset( $_POST['action'])){
    if( $_POST['action'] == 'load-user-profile'){
        $dir = "../pictures/".strval($_SESSION[DB_COL_HEALTHCARE_NO]);
        if(!is_dir($dir)){
            echo ' <img src="../res/icon-user-default.png" style="width:216px; height:216px; border:8px solid #FFFFFF;border-radius: 6px;box-shadow: 0px 2px 5px #333333; margin:0 auto; display:inline-block;"/>';
            createPicFolderForPatient($_SESSION[DB_COL_HEALTHCARE_NO]);
            die();
        }
        $file = $dir.'/user.jpg';
        if(!file_exists ($file)){
            echo '<img src="../res/icon-user-default.png" style="width:216px; height:216px; border:8px solid #FFFFFF;border-radius: 6px;box-shadow: 0px 2px 5px #333333; margin:0 auto; display:inline-block;"/>';
            die();
        }else{
            echo '<img src="'.$file.'" style="width:216px; height:216px; border:8px solid #FFFFFF;border-radius: 6px;box-shadow: 0px 2px 5px #333333; margin:0 auto; display:inline-block;"/>';
        }
    }else if($_POST['action'] == 'load-patient-profile'){
        if(!isset($_POST[DB_COL_HEALTHCARE_NO])){
            die();
        }
        $dir = "../pictures/".strval($_POST[DB_COL_HEALTHCARE_NO]);
        if(!is_dir($dir)){
            echo '<img src="../res/icon-user-default.png" class="profile-img" style="margin: 0px auto 20px auto;"/>';
            createPicFolderForPatient($_POST[DB_COL_HEALTHCARE_NO]);
            die();
        }
        $file = $dir.'/user.jpg';
        if(!file_exists ($file)){
            echo '<img src="../res/icon-user-default.png" class="profile-img" style="margin: 0px auto 20px auto;"/>';
            die();
        }else{
            echo '<img src="'.$file.'" class="profile-img" style="margin: 0px auto 20px auto;"/>';
        }
    }
}

function createPicFolderForPatient($id){
    mkdir("../pictures/$id");
}

?>