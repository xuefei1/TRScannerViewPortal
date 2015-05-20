<?php 
require_once('load.php');
if(session_id() == ''){
    session_start();
}
if(isset($_POST['action'])){
    if($_POST['action'] == 'get_comments'){
        get_comments();
    }else if($_POST['action'] == 'insert_comment'){
        insert_comment();
    }else if($_POST['action'] == 'get_comments_with_id'){
        getCommentsById($_POST['patient_id']);
    }else if($_POST['action'] == 'insert_comment_for_patient'){
        insertCommentForPatient();
    }
}

function insert_comment(){
    global $db;
    global $main;
    $id = $main -> getPictureId($_POST[DB_COL_HEALTHCARE_NO], $_POST['picture_file']);
    $comment = $_POST['comment'];
    if($db->execSQL("INSERT INTO ".DB_COMMENTS_TABLE." VALUES(DEFAULT, $id,". $_POST[DB_COL_HEALTHCARE_NO].", '$comment', NOW(), ". $_POST[DB_COL_HEALTHCARE_NO].")")){refresh_comments($id);}else{
        die($id."//". $_POST[DB_COL_HEALTHCARE_NO]."//".$comment."//".$_POST['picture_file']);
    }
}

function insertCommentForPatient(){
    global $db;
    global $main;
    $id = $main -> getPictureId($_POST[DB_COL_HEALTHCARE_NO], $_POST['picture_file']);
    $comment = $_POST['comment'];
    if($db->execSQL("INSERT INTO ".DB_COMMENTS_TABLE." VALUES(DEFAULT, $id,". $_POST[DB_COL_HEALTHCARE_NO].", '$comment', NOW(), ". $_POST[DB_COL_COMMENTER].")")){refresh_comments($id);}else{
        die($id."//". $_POST[DB_COL_HEALTHCARE_NO]."//".$comment);
    }
}

function refresh_comments($id){
    global $db;
    $result = $db->select("SELECT * FROM ".DB_COMMENTS_TABLE." WHERE ".DB_COL_PICTURE_ID." = $id ORDER BY ".DB_COL_COMMENT_DATE." DESC");
    echo '<div class="listview" id="comment-container">';

    if(!$result){
        echo '<div id="list-group" class=" single_line_container">';
        echo '<script>document.getElementById("comments_list").style.height="150px";</script>';
        echo '<p class="text_notify_grey" style="margin:0px auto 60px auto;">No comments found</p>';
        echo '</div>';
        echo '</div>';
        return;
    }elseif(mysql_num_rows($result)==0){
        echo '<div id="list-group" class=" single_line_container">';
        echo '<script>document.getElementById("comments_list").style.height="150px";</script>';
        echo '<p class="text_notify_grey" style="margin:0px auto 60px auto;">No comments found</p>';
        echo '</div>';
        echo '</div>';
        return;
    }
    echo '<div id="list-group" class="" text-align="left">';
    echo '<script>document.getElementById("comments_list").style.height="auto";</script>';
    //echo '<script>document.getElementById("comments_list").style.maxHeight="900px";</script>';
    while ($row = mysql_fetch_assoc($result)) {
        $person = $db->select("SELECT * FROM ".DB_PATIENT_TABLE." WHERE ".DB_COL_HEALTHCARE_NO." = ".$row[DB_COL_COMMENTER]." LIMIT 1");
        echo '<div class="list-group-item">';
        if(!$person){
            echo '<p class="list-group-item-heading">Unknown user</p>';
        }elseif(mysql_num_rows($result)==0){
            echo '<p class="list-group-item-heading">Unknown user</p>';
        }else{
            echo '<p class="list-group-item-heading">'.mysql_fetch_assoc($person)[DB_COL_NAME].'</p>';
        }
        echo '<h5 class="list-group-item-heading">'.$row[DB_COL_COMMENT_DATE].'</h5>';
        echo '<h4 class="list-group-item-text">'.$row[DB_COL_COMMENT].'</h4>';
        echo '</div>';

    }
    echo '</div>';
    return true;
}

function get_comments(){

    global $main;
    if(!isset($_POST['file'])){die('');return;}
    $id = $main -> getPictureId($_SESSION[DB_COL_HEALTHCARE_NO], $_POST['file']);
    return refresh_comments($id);
}

function getCommentsById($id){
    global $main;
    if(!isset($_POST['file'])){return;}
    $id = $main -> getPictureId($id, $_POST['file']);
    return refresh_comments($id);
}

?>



