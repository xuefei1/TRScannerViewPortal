<?php
require_once('config.php');
require_once('db.php');
//begining of class
if(!class_exists('Main')){
    class Main{

        function register($redirect){

            global $db;

            //check to make sure the registration is from our site
            //our site
            $current = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

            //where the form comes from
            if(isset($_SERVER['HTTP_REFERER'])){
                $referrer = $_SERVER['HTTP_REFERER'];
            } 


            if( !empty( $_POST )){

                //needs fields of the user table
                $fields = array(DB_COL_USERLOGIN,DB_COL_PASSWORD,DB_COL_REGDATE);

                $values = $db->clean($_POST);

                //add more info to match the user table
                $userpass = $_POST['password'];
                $userlogin = $_POST['userlogin'];
                $userreg = $_POST['date'];

                $nonce = md5('registration-'.$userlogin.$userreg.NONCE_SALT);

                $userpass = $db->hash($userpass, $nonce);

                $values = array(DB_COL_USERLOGIN=>$userlogin,
                                DB_COL_PASSWORD=>$userpass,
                                DB_COL_REGDATE=>$userreg
                               );

                $insert = $db->insert(DB_USER_TABLE, $fields, $values);

                if($insert == TRUE){
                    $url = "http".(!empty($_SERVER['HTTPS'])?"s":"")."://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
                    ////////////////????
                    $redirect = str_replace('register.php', $redirect, $url);

                    //send a location header to redirect the page
                    //pass in parameter reg = true
                    header("Location: $redirect?reg=true");
                    exit;
                }else{
                    die('registration failed');
                }

            }

        }

        function androidClientLogin(){
            global $db;
            $rv = array("success"=>false, "error"=>"", "id"=>-1);

            if(!empty($_POST)){
                $values = $db->clean($_POST);

                $username = $_POST['username'];
                $password = $_POST['password'];

                $sql = "SELECT * FROM ".DB_USER_TABLE." WHERE ".DB_COL_USERLOGIN." = '".$username."'";
                $results = $db->select($sql);

                //only check if the username exists
                if(!$results || mysql_num_rows($results) == 0){
                    //better error code can go in here
                    $rv["success"] = false;
                    $rv["error"] = "The username entered is not recognized";
                    echo json_encode($rv);
                    return;
                }

                $results = mysql_fetch_assoc($results);
                //prepare to un hash the password
                $date = $results[DB_COL_REGDATE];
                $pass = $results[DB_COL_PASSWORD];

                //recreate the nonce
                $nonce = md5('registration-'.$username.$date.NONCE_SALT);

                $hashed_password = $db->hash($password, $nonce);

                //check if the encrypted passwords are equal
                if($hashed_password == $pass){

                    //build redirect
                    $url = "http".((!empty($_SERVER['HTTPS'])) ? "s":"")."://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

                    $p = $db->select("SELECT * FROM ".DB_PATIENT_TABLE." WHERE ".DB_COL_LOGIN." = '$username' LIMIT 1");
                    $p = mysql_fetch_assoc($p);
                    $this->registerLoginSession($username);
                    $rv["success"] = true;
                    $rv["id"] = $p[DB_COL_HEALTHCARE_NO];
                    $rv["name"] = $p[DB_COL_NAME];
                    $rv["gender"] = $p[DB_COL_GENDER];
                    $rv["address"] = $p[DB_COL_ADDRESS];
                    $rv["city"] = $p[DB_COL_CITY];
                    $rv["province"] = $p[DB_COL_PROVINCE];
                    $rv["country"] = $p[DB_COL_COUNTRY];
                    $rv["phone"] = $p[DB_COL_PHONE];
                    $rv["age"] = $p[DB_COL_AGE];
                    $rv["email"] = $p[DB_COL_EMAIL];
                    echo json_encode(array("Android"=>$rv));
                    return;
                }else{
                    $rv["success"] = false;
                    $rv["error"] = "The password is not correct";
                    echo json_encode(array("Android"=>$rv));
                    return;
                } 
            }
        }

        function login($redirect){

            global $db;

            if(!empty($_POST)){
                if($this->cookieExists()){
                    return 'loggedin';
                }
                $values = $db->clean($_POST);

                $username = $_POST['username'];
                $password = $_POST['password'];

                $sql = "SELECT * FROM ".DB_USER_TABLE." WHERE ".DB_COL_USERLOGIN." = '".$username."'";
                $results = $db->select($sql);

                //only check if the username exists
                if(!$results){
                    //better error code can go in here
                    die('username does not exist');
                }

                $results = mysql_fetch_assoc($results);
                //prepare to un hash the password
                $date = $results[DB_COL_REGDATE];
                $pass = $results[DB_COL_PASSWORD];

                //recreate the nonce
                $nonce = md5('registration-'.$username.$date.NONCE_SALT);

                $hashed_password = $db->hash($password, $nonce);

                //check if the encrypted passwords are equal
                if($hashed_password == $pass){
                    //init authentication
                    $authnonce = md5('cookie-'.$username.$date.AUTH_SALT);
                    $authID = $db->hash($hashed_password, $authnonce);
                    //set cookies, use 'user' and authID to access info stored
                    setcookie('loginauth[user]', $username, 0, '','','',TRUE);
                    setcookie('loginauth[authID]', $authID, 0, '','','',TRUE);
                    //build redirect
                    $url = "http".((!empty($_SERVER['HTTPS'])) ? "s":"")."://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

                    $p = $db->select("SELECT * FROM ".DB_PATIENT_TABLE." WHERE ".DB_COL_LOGIN." = '$username' LIMIT 1");
                    $p = mysql_fetch_assoc($p);
                    $redirect = str_replace('login.php', $this->getLoginRedirect($p[DB_COL_HEALTHCARE_NO]), $url);
                    $this->registerLoginSession($username);
                    header("Location: $redirect");
                    exit;
                }else{
                    return 'invalid';
                } 
            }else{
                return 'empty';
            }
        }

        function cookieExists(){
            return isset( $_COOKIE['loginauth']);
        }

        function checkLogin($index){
            global $db;
            //grab the cookie by name
            $cookie = $_COOKIE['loginauth'];
            //grap info from the cookie
            $user = $cookie['user'];
            $authID = $cookie['authID'];

            //if the cookie value is empty, we redirect to login, otherwise we check the cookie
            if(!empty($cookie)){

                $sql = "SELECT * FROM ".DB_USER_TABLE ." WHERE ".DB_COL_USERLOGIN." = '".$user."'";
                $results = $db->select($sql);

                //does the user name exist?
                if(!$results){
                    die('Invalid cookie, username does not exist');
                }

                //turn the result into an associative array
                $results = mysql_fetch_assoc( $results );

                $reg_date = $results[DB_COL_REGDATE];
                $stored_password = $results[DB_COL_PASSWORD];

                //rehash the password to see if it matches the stored one
                $authnonce = md5('cookie-'.$user.$reg_date.AUTH_SALT);
                $stored_password = $db->hash($stored_password, $authnonce);

                //check the authID of the cookie
                if($stored_password == $authID){
                    return true;
                }else{
                    return false;
                }
            }else{
                $url = "http".((!empty($_SERVER['HTTPS']))? "s":"")."://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
                $redirect = str_replace($index, 'login.php', $url);

                header("Location: $redirect?msg=login");
                exit;
            }
        }


        function logout(){
            $idout = setcookie('loginauth[authID]','',-3600,'','','',true);
            $userout = setcookie('loginauth[user]','',-3600,'','','',true);

            if( $idout == true && $userout == true){
                $this->registerLogoutSession();
                session_destroy();
                return true;
            }else{
                return false;
            }
        }

        function saveJournal($healthcare_no, $title, $content){
            global $db;
            $sql = "INSERT INTO ".DB_JOURNALS_TABLE." VALUES(DEFAULT, ".$healthcare_no.", '$title', '$content', NOW())";
            return $db->execSQL($sql);
        }

        function showViewOnlyJournals($id) {
            global $db;
            $sql = "SELECT * FROM ".DB_JOURNALS_TABLE." n WHERE n.".DB_COL_HEALTHCARE_NO." = ".$id. " ORDER BY ".DB_COL_JOURNAL_DATE." DESC";
            $notes = $db->select($sql);
            if(!$notes){
                echo '<p class="text_notify_grey"> No journal(s) found.</p>';
                echo '<script type="text/javascript">document.getElementById("journals_container").style.height = "100px";</script>';
                return false;
            }
            if (mysql_num_rows($notes)==0) {
                echo '<p class="text_notify_grey"> No journal(s) found.</p>';
                echo '<script type="text/javascript">document.getElementById("journals_container").style.height = "100px";</script>';
                return false;
            }
            echo '<script type="text/javascript">document.getElementById("journals_container").style.height = "auto";</script>';
            $counter = 0;
            while ($row = mysql_fetch_assoc($notes)) {
                $this->generateViewOnlyListGroup($row[DB_COL_JOURNAL_TITLE], $row[DB_COL_JOURNAL_DATE], $row[DB_COL_JOURNAL_CONTENT], $row[DB_COL_JOURNAL_ID]);
                $counter++;
            }
            return true;
        }

        function generateViewOnlyListGroup($title, $date, $content, $id){
            echo '<div class="list-group-item">';
            echo '<h4 class="list-group-item-heading" style="padding: 10px 5px; text-align:center">'.$title.'</h4>';
            echo '<h5 class="list-group-item-heading" style="padding: 5px ; text-align:center">'.$date.'</h5>';
            echo '<p class="list-group-item-text" style="padding: 15px ;">'.$content.'</p>';
            echo '</div>';
        }

        function showJournals($id){
            global $db;
            $sql = "SELECT * FROM ".DB_JOURNALS_TABLE." n WHERE n.".DB_COL_HEALTHCARE_NO." = ".$id. " ORDER BY ".DB_COL_JOURNAL_DATE." DESC";
            $notes = $db->select($sql);
            if(!$notes){
                echo '<p class="text_notify_grey"> No journal(s) found.</p>';
                return false;
            }
            if (mysql_num_rows($notes)==0) {
                echo '<p class="text_notify_grey"> No journal(s) found.</p>';
                return false;
            }
            $counter = 0;
            while ($row = mysql_fetch_assoc($notes)) {
                $this->generateListGroup($row[DB_COL_JOURNAL_TITLE], $row[DB_COL_JOURNAL_DATE], $row[DB_COL_JOURNAL_CONTENT], $row[DB_COL_JOURNAL_ID]);
                $counter++;
            }
            return true;
        }

        function generateListGroup($title, $date, $content, $id){
            echo '<div class="list-group-item">';
            echo '<h4 class="list-group-item-heading" style="padding: 10px 5px; text-align:center">'.$title.'</h4>';
            echo '<h5 class="list-group-item-heading" style="padding: 5px ; text-align:center">'.$date.'</h5>';
            echo '<p class="list-group-item-text" style="padding: 15px ;">'.$content.'</p>';
            echo '<div style="margin:50px auto 20px auto; text-align:center" >';
            echo '<button type="button" class="btn btn-default" aria-labe="Edit" id= "'.$id.'" style="width:40%; margin: 0px 5px;" onclick="editJournal(this, \''.$title.'\', \''.$content.'\')">
                    <span class="glyphicon glyphicon-pencil" aria-hidden="true" style="padding:0px 10px 0px 0px"></span>Edit
                    </button>';
            echo '<button type="button" class="btn btn-default" aria-labe="Delete" id= "'.$id.'" style="width:40%; margin: 0px 5px;" onclick="deleteJournal(this)">
                    <span class="glyphicon glyphicon-trash" aria-hidden="true" style="padding:0px 10px 0px 0px"></span>Delete
                    </button>';
            echo '</div>';
            echo '</div>';
        }

        function is_dir_empty($dir) {
            if (!is_readable($dir)) return NULL; 
            $handle = opendir($dir);
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    return FALSE;
                }
            }
            return TRUE;
        }

        function showRecentUploads($id){
            global $db;
            $dir = "../pictures/".strval($id);
            if(!is_dir($dir)){
                echo '<p class="text_notify_grey"> No recent uploads.</p>';
                createPicFolderForPatient($id);
                die();
            }elseif($this->is_dir_empty($dir)){
                echo '<p class="text_notify_grey"> No recent uploads.</p>';
                die();
            }elseif(!$this->picPresent($dir)){
                echo '<p class="text_notify_grey"> No recent uploads.</p>';
                die();
            }

            $sql = "SELECT * FROM ".DB_PICTURES_TABLE." WHERE ".DB_COL_HEALTHCARE_NO."= $id AND DATEDIFF(NOW(), ".DB_COL_DATE.") <= 7 ORDER BY ".DB_COL_DATE." DESC";

            $result = $db->select($sql);
            if(!$result){
                echo '<p class="text_notify_grey"> No recent uploads.</p>';
                die();
            }elseif(mysql_num_rows($result) == 0){
                echo '<p class="text_notify_grey"> No recent uploads.</p>';
                die();
            }

            $counter = 0;
            while ($row = mysql_fetch_assoc($result)) {
                if($counter == 10){break;}
                echo '<a class="highslide" href="'.$row[DB_COL_DIRECTORY].'" onclick="return hs.expand(this)">';
                echo '<div class="thumbnail_crop">';
                echo'<img src="'.$row[DB_COL_DIRECTORY].'" alt=""/>';
                echo '</div>';
                echo '</a>';
                $counter++;
            }
        }

        function showAllImg($id){
            $dir = "../pictures/".strval($id);
            if(!is_dir($dir)){
                echo '<p class="text_notify_grey"> No uploaded picture(s) found.</p>';
                echo '<script type="text/javascript">document.getElementById(\'view-select\').style.display = "none";</script>';
                createPicFolderForPatient($id);
                return FALSE;
            }
            elseif($this->is_dir_empty($dir)){
                echo '<p class="text_notify_grey"> No uploaded picture(s) found.</p>';
                echo '<script type="text/javascript">document.getElementById(\'view-select\').style.display = "none";</script>';
                return FALSE;
            }elseif(!$this->picPresent($dir)){
                echo '<p class="text_notify_grey" > No uploaded picture(s) found.</p>';
                echo '<script type="text/javascript">document.getElementById(\'view-select\').style.display = "none";</script>';
                return false;
            }

            $files = glob($dir.'/*.{jpg,png,gif}', GLOB_BRACE);
            echo '<div id="img-container">';
            foreach($files as $file) {
                if(basename($file) == 'user.jpg'){continue;}
                echo '<a class="highslide" href="'.$file.'" onclick="return hs.expand(this)">';
                echo '<div class="thumbnail_crop">';
                echo'<img src="'.$file.'" alt=""/>';
                echo '</div>';
                echo '</a>';
            }
            echo '</div>';
            return TRUE;
        }

        function showDetailImg($id){
            $dir = "../pictures/".strval($id);
            //echo '<script type="text/javascript">document.getElementById(\'galleria-container\').innerHTML=\'\';</script>';
            if(!is_dir($dir)){
                //echo '<script type="text/javascript">document.getElementById(\'galleria-container\').style.display = "none"; document.getElementById(\'no-image-notify-div\').innerHTML=\'<p class="text_notify_grey">No uploaded picture(s) found.</p>\'; window.onload = function () { document.getElementById(\'comment-section-div\').style.display = "none";}</script>';
                createPicFolderForPatient($id);
                echo '0';
                return false;
            }
            elseif($this->is_dir_empty($dir)){
                //echo '<script type="text/javascript">document.getElementById(\'galleria-container\').style.display = "none"; document.getElementById(\'no-image-notify-div\').innerHTML=\'<p class="text_notify_grey">No uploaded picture(s) found.</p>\'; window.onload = function () { document.getElementById(\'comment-section-div\').style.display = "none";}</script>';
                echo '0';
                return false;
            }elseif(!$this->picPresent($dir)){
                //echo '<script type="text/javascript">document.getElementById(\'galleria-container\').style.display = "none"; document.getElementById(\'no-image-notify-div\').innerHTML=\'<p class="text_notify_grey">No uploaded picture(s) found.</p>\';window.onload = function () { document.getElementById(\'comment-section-div\').style.display = "none";}</script>';
                echo '0';
                return false;
            }
            
            //echo '<script type="text/javascript">document.getElementById(\'galleria-container\').style.display = "block";document.getElementById(\'no-image-notify-div\').innerHTML=\'\'; window.onload = function () { document.getElementById(\'comment-section-div\').style.display = "block";}</script>';
            $files = glob($dir."/*.{jpg,png,gif}", GLOB_BRACE);
            echo'<div class="galleria">';
            foreach($files as $file) {
                if(basename($file) == 'user.jpg'){continue;}
                echo'<img src="'.$file.'" id="'.basename($file).'"/>';
            }
            echo '</div>';

        }

        function getPictureId($user_id, $file_name){
            global $db;
            $result = $db->select("SELECT * FROM ".DB_PICTURES_TABLE." WHERE ".DB_COL_HEALTHCARE_NO." = $user_id AND ".DB_COL_FILENAME." = '$file_name' LIMIT 1");
            if(!$result){return -1;}
            if(mysql_num_rows($result) == 0){return -2;}
            $result = mysql_fetch_assoc($result);
            return $result[DB_COL_ID];
        }

        function registerLoginSession($username){
            global $db;
            //record login session
            $fields = implode(array(DB_COL_SESSION_USER, DB_COL_SESSION_LOGIN, DB_COL_SESSION_LOGOUT, DB_COL_SESSION_IP));
            $ip = getenv('HTTP_CLIENT_IP')?:
            getenv('HTTP_X_FORWARDED_FOR')?:
            getenv('HTTP_X_FORWARDED')?:
            getenv('HTTP_FORWARDED_FOR')?:
            getenv('HTTP_FORWARDED')?:
            getenv('REMOTE_ADDR');
            $sql = "INSERT INTO ".DB_SESSION_RECORD." VALUES(DEFAULT, '".$username."', NOW(), DEFAULT, '".$ip."')";
            $_SESSION['session_id'] = $db->insert_with_return_id($sql);
        }

        function registerLogoutSession(){
            global $db;
            //record logout session
            if(isset($_SESSION['session_id'])){
                $currId = $_SESSION['session_id'];
                $sql = "UPDATE ".DB_SESSION_RECORD." SET ".DB_COL_SESSION_LOGOUT." = NOW() WHERE ".DB_COL_SESSION_ID." = $currId";
                $db->execSQL($sql);
            }
        }

        function getLoginRedirect($healthcare_no){
            global $db;
            $is_doc = $db->select("SELECT * FROM ".DB_DOCTOR_TABLE." WHERE ".DB_COL_HEALTHCARE_NO." = $healthcare_no");
            if(mysql_num_rows($is_doc) > 0){
                return 'index-doc.php';
            }else{
                return 'index.php';
            }
        }

        function isDoctor($id){
            global $db;
            $is_doc = $db->select("SELECT * FROM ".DB_DOCTOR_TABLE." WHERE ".DB_COL_HEALTHCARE_NO." = $id");
            if(mysql_num_rows($is_doc) > 0){
                return true;
            }else{
                return false;
            }
        }

        function queryAllPatients(){
            global $db;
            $result = $db->select("SELECT * FROM ".DB_PATIENT_TABLE);
            if(!$result){
                return false;
            }
            while($row = mysql_fetch_assoc($result)){
                $this->generatePatientList($row);
            }
            return true;
        }

        function generatePatientList($row){
            echo '<a onclick="onPatientClick(this)" class="list-group-item" id="'.$row[DB_COL_HEALTHCARE_NO].'">';
            echo '<h4 class="list-group-heading">'.$row[DB_COL_NAME].'</h4>';
            echo '<p class="list-group-text">Patient ID: '.$row[DB_COL_HEALTHCARE_NO].'</p>';
            echo '</a>';
        }

        function createPicFolderForPatient($id){
            mkdir("../pictures/$id");
        }

        function picPresent($dir){
            $files = glob($dir.'/*.{jpg,png,gif}', GLOB_BRACE);
            $count = 0;
            foreach($files as $file) {
                if(basename($file) == 'user.jpg'){continue;}
                $count++;
                if($count >= 1){return true;}
            }
            return false;
        }

        function generateScanData($id){
            global $db;
            $scan = $db->select("SELECT * FROM ".DB_SCAN_RECORD." WHERE ".DB_COL_USER_ID." = $id ORDER BY ".DB_COL_DATE." DESC");
            if(!$scan){
                die('error: '.mysql_error());
            }else if(mysql_num_rows($scan) == 0){
                die(json_encode(array()));
            }
            $ra = array();
            while ($row = mysql_fetch_assoc($scan)) {
                $date = $row[DB_COL_DATE];
                $timestamp = strtotime($date)*1000;
                array_push($ra, $timestamp);
            }
            echo json_encode($ra);
        }
        //end of class
    }   
}
$main = new Main;

//request handlers
if(isset($_POST['action'])){
    if($_POST['action'] == 'updateImgOverview'){
        $main->showAllImg($_POST[DB_COL_HEALTHCARE_NO]);
    }elseif($_POST['action'] == 'updateImgDetail'){
        $main->showDetailImg($_POST[DB_COL_HEALTHCARE_NO]);
    }elseif($_POST['action'] == 'updateJournalForUser'){
        $main->showViewOnlyJournals($_POST[DB_COL_HEALTHCARE_NO]);
    }
}else if(isset($_GET['action'])){
    if($_GET['action'] == 'generateScanData'){
        $main->generateScanData($_GET[DB_COL_HEALTHCARE_NO]);
    }
}
?>