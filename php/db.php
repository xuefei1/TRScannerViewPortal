<?php
if(!class_exists('Database')){

    class Database{

        function Database(){
            return $this->_construct();
        }

        function _construct(){
            $this->connect();
        }

        function connect(){

            global $link;

            $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);

            if(!$link){
                die('Could not connect: '. mysql_error());
            }

            $DB = mysql_select_db(DB_NAME, $link);

            if(!$DB){
                die('Cannot use: '.DB_NAME.': '. mysql_error());
            }
        }

        function clean($array){
            //clean elements inside the array, prepare them for insertion/deletion
            return array_map('mysql_real_escape_string', $array);
        }

        function hash($password, $nonce){
            $secureHash = hash_hmac('sha512', $password, $nonce, SITE_KEY);
            return $secureHash;
        }

        function insert($table, $fields, $values){
            $fields = implode(", ", $fields);

            //database should have an auto-increment id field
            $values = $this->clean($values);
            $values = implode("', '", $values);
            $sql = "INSERT INTO $table VALUES ('$values')";

            if(!mysql_query($sql)){
                die('error: '.mysql_error());
            }else{
                return true;
            }
        }
        
        function insert_with_return_id($sql){
            if(!mysql_query($sql)){
                die('error: '.mysql_error());
            }else{
                return mysql_insert_id();
            }
        }
        
        function execSQL($sql){
            if(!mysql_query($sql)){
                return false;
            }else{
                return true;
            }
        }

        function select($sql){
            $result = mysql_query($sql);
            return $result;
        }

    }
}

$db = new Database;
?>

