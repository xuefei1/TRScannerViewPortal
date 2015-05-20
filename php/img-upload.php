<?php 
include_once('config.php');
include_once('db.php');
// Path to move uploaded files
$target_path = "../pictures/";
if(isset($_POST["id"])){
    $target_path = "../pictures/".$_POST["id"]."/";
}else{
    echo json_encode(array('error'=>true, 'message'=>'No id submitted'));
    die();
}
 
// array for final json respone
$response = array();
 
// getting server ip address
$server_ip = '192.168.100.191';
//$server_ip = gethostbyname(gethostname());
 
// final file url that is being uploaded
$file_upload_url = 'http://' . $server_ip . '/'.$target_path;
 
 
if (isset($_FILES['image']['name'])) {
	$file_count = count($_FILES['image']['name']);
	
 for ($i=0; $i<$file_count; $i++) {
	$target_path = $target_path . basename($_FILES['image']['name'][$i]);
    // reading other post parameters
    $response['file_name'] = basename($_FILES['image']['name'][$i]);
    $response['file_count'] = $file_count;
    try {
        // Throws exception incase file is not being moved
        if (!move_uploaded_file($_FILES['image']['tmp_name'][$i], $target_path)) {
            // make error flag true
            $response['error'] = true;
            $response['message'] = 'Could not move the file!';
			die(json_encode($response));
        }
 
        // File successfully uploaded
        $response['message'] = 'File uploaded successfully!';
        $response['error'] = false;
        $response['file_path'] = $file_upload_url . basename($_FILES['image']['name'][$i]);
        
        //update db
        insertFilePath($target_path, $_POST["id"]);
		echo json_encode($response);
    } catch (Exception $e) {
        // Exception occurred. Make error flag true
        $response['error'] = true;
        $response['message'] = $e->getMessage();
		echo json_encode($response);
    }
	}	
} else {
    // File parameter is missing
    $response['error'] = true;
    $response['message'] = 'Not received any file!F';
}
 
// Echo final json response to client
echo json_encode($response);

function insertFilePath($filepath, $id){
    global $db;
    $tmp =  basename($filepath);
    $filename = preg_replace('/\\.[^.\\s]{3,4}$/', '', $tmp);
    $sql = "INSERT INTO ".DB_PICTURES_TABLE." VALUES(DEFAULT, $id, '$filename', '$filepath', '$filepath', NOW())";
    $db->execSQL($sql);
}

?>