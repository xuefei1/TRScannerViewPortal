<?php
include_once('load.php');

if(isset($_POST['action'])){
    if($_POST['action'] == 'android-login'){
        initLogin();
    }
}

function initLogin(){
    global $main;
    $main->androidClientLogin();
}
?>