<?php

require('get_db.php');
header('Content-type: application/json');

if(isset($_GET['uid']) && isset($_GET['file_id']) && isset($_GET['call_id']) && isset($_GET['fuid']) && isset($_GET['info']) && isset($_GET['type'])) {

	$uid     = $_GET['uid'];
	$file_id = $_GET['file_id'];
	$call_id = $_GET['call_id'];
	$fuid    = $_GET['fuid'];
	$info    = $_GET['info'];
	$type    = $_GET['type'];

	$conn = DBConn();

	$sql = 'INSERT INTO `forward` (`file_id`, `u_id`, `dest`, `call_id`, `info`, `type`) VALUES ( '.$file_id.', '.$uid.', "'.$fuid.'" , '.$call_id.', '.$info.', "'.$type.'")';  
	//echo "$sql";
	if ($result = $conn->query($sql)) {
		echo json_encode(array('result' =>  array('error' => false, 'id' => $conn->insert_id, 'message' => "Delivery Created")));
	}else{
		echo json_encode(array('result' =>  array('error' => true, 'message' => "DB Insert Error", 'sql' => $sql)));
	}
	$conn->close();
}else {
	echo json_encode(array('result' => array('error' => true, 'message' => "Params incomplete!")));
}
?>



