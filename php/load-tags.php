<?php
include_once('rf-config.php');
include_once('rf-db.php');

if(isset($_POST['action'])){
    if($_POST['action'] == 'load-tags'){
        loadAllTags();
    }
}

function loadAllTags(){
    global $db;
    $result = $db->select("SELECT * FROM ".DB_SCAN_TABLE." ORDER BY ".DB_COL_DATETIME." DESC");
    if(!$result){
        die('<p style="text-align:center"> No Tag(s) found.</p>');
    }
    if(mysql_num_rows($result) == 0){
        die('<p style="text-align:center"> No Tag(s) found.</p>');
    }

    echo '<div style="margin:10px auto; min-height:65px; max-height:2000px; height:auto; overflow-y:auto; overflow-x:hidden;">';  
    while ($row = mysql_fetch_assoc($result)) {
        generateTagRow($row[DB_COL_DATA], $row[DB_COL_DATETIME]);
    }
    echo '</div>';
}



function generateTagRow($data, $date){
    echo '<div class="list-group-item">';
    echo '<h5 class="list-group-item-heading" style=" padding: 10px; text-align:left">'.$date.'</h5>';
    echo '<h4 class="list-group-item-text" style="padding: 10px; text-align:left">'.$data.'</h4>';
    echo '</div>';
}

?>