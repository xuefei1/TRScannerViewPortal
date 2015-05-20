<?php

include_once('load.php');

if(session_id() == ''){
    session_start();
}

if(isset($_POST['action'])){
    if($_POST['action'] == 'load-image-detail'){
        $main->showDetailImg($_POST[DB_COL_HEALTHCARE_NO]);
    }else if($_POST['action'] == 'load-recent-image'){
        $main->showRecentUploads($_POST[DB_COL_HEALTHCARE_NO]);
    }
}

?>