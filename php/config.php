<?php

//credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'scannerdb');
define('DB_USER', 'root');
define('DB_PASS', 'aiwprtoN94305');
define('DB_RF_NAME', 'rfdb');

//table names
define('DB_PATIENT_TABLE', 'patient');
define('DB_DOCTOR_TABLE', 'doctor');
define('DB_PICTURES_TABLE','picture');
define('DB_JOURNALS_TABLE','notes');
define('DB_COMMENTS_TABLE','picture_comment');
define('DB_USER_TABLE', 'users');
define('DB_COMMENT_TABLE','picture_comment');
define('DB_SESSION_RECORD','session_record');
define('DB_SCAN_RECORD', 'scan_record');
define('DB_SCAN_TABLE', 'scan');

//columns
define('DB_COL_NAME', 'name');
define('DB_COL_USERLOGIN', 'userlogin');
define('DB_COL_PASSWORD', 'password');
define('DB_COL_EMAIL', 'email');
define('DB_COL_REGDATE', 'date');
define('DB_COL_GENDER', 'gender');
define('DB_COL_PHONE', 'phone');
define('DB_COL_ADDRESS', 'address');
define('DB_COL_AGE', 'age');
define('DB_COL_PATIENT_LOGIN','login');
define('DB_COL_HEALTHCARE_NO','healthcare_no');
define('DB_COL_USERNAME','username');
define('DB_COL_SESSION_USER','username');
define('DB_COL_SESSION_LOGIN','login');
define('DB_COL_SESSION_LOGOUT','logout');
define('DB_COL_SESSION_IP','client_ip');
define('DB_COL_SESSION_ID','session_id');
define('DB_COL_JOURNAL_ID','note_id');
define('DB_COL_JOURNAL_TITLE','note_title');
define('DB_COL_JOURNAL_CONTENT', 'note');
define('DB_COL_JOURNAL_DATE', 'note_date');
define('DB_COL_PICTURE_ID', 'picture_id');
define('DB_COL_COMMENT_DATE', 'comment_date');
define('DB_COL_COMMENT', 'comment');
define('DB_COL_FILENAME', 'filename');
define('DB_COL_ID', 'id');
define('DB_COL_LOGIN','login');
define('DB_COL_COMMENTER', 'commenter');
define('DB_COL_SCAN_ID', 'scan_id');
define('DB_COL_USER_ID', 'user_id');
define('DB_COL_DATE', 'date');
define('DB_COL_CITY', 'city');
define('DB_COL_COUNTRY', 'country');
define('DB_COL_POSTAL_CODE', 'postal_code');
define('DB_COL_PROVINCE', 'province');
define('DB_COL_DIRECTORY', 'directory');
define('DB_COL_SACN_ID', 'scan_id');
define('DB_COL_DATA', 'data');
define('DB_COL_DATETIME', 'datetime');
define('DB_COL_TYPE', 'type');



//keys
define('SITE_KEY', 'C22CCCD59FFE9837D2C7175D18C5D');
define('NONCE_SALT', '557C564EB5BD5366286252FFF68DF');
define('AUTH_SALT', 'B5FBFF2BF5EA6DB7AADBD4C7F8359');
?>