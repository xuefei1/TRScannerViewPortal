<?php
require_once('load.php');

if(isset($_POST['action'])){

    if($_POST['action'] == "update"){
        updateJournal($_POST[DB_COL_JOURNAL_ID], $_POST[DB_COL_JOURNAL_TITLE], $_POST[DB_COL_JOURNAL_CONTENT]);
    }else if($_POST['action'] == "delete"){
        deleteJournal($_POST[DB_COL_JOURNAL_ID]);
    }
}

function updateJournal($id, $title, $content){
    global $db;
    echo $db->execSQL("UPDATE ".DB_JOURNALS_TABLE." SET ".DB_COL_JOURNAL_TITLE." = '$title', ".DB_COL_JOURNAL_CONTENT." = '$content', ".DB_COL_JOURNAL_DATE." = NOW() WHERE ".DB_COL_JOURNAL_ID." = $id");
}

function deleteJournal($id){
    global $db;
    echo $db->execSQL("DELETE FROM ".DB_JOURNALS_TABLE." WHERE ".DB_COL_JOURNAL_ID." = $id");
}
?>