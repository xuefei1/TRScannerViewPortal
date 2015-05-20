<?php
require_once('load.php');
if( !empty( $_POST )){
    if(isset($_POST['action'])){
        if($_POST['action'] == 'submit_journal'){
            $title = $_POST['title'];
            $content = $_POST['content'];
            $id = $_POST[DB_COL_HEALTHCARE_NO];
            return $main->saveJournal($id, $title, $content);
        }
    }
}
?>