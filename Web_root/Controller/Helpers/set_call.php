<?php

require('get_db.php');
header('Content-type: application/json');

if(isset($_GET['uid']) && isset($_GET['call_id']) && isset($_GET['f_id']) && isset($_GET['action_id'])) {

	$uid     = $_GET['uid'];
	$call_id = $_GET['call_id'];
	$f_id     = $_GET['f_id'];
	$action_id = $_GET['action_id'];

	$conn = DBConn();

	$sql  =  'INSERT INTO `calls` (`call_id`, `u_id`, `action_id`, `time`, `file_id`)  VALUES ('.$call_id.' ,'.$uid.', '.$action_id.', NOW(), "'.$f_id.'")';

	if ($result = $conn->query($sql)) {
		echo json_encode(array('result' =>  array('error' => false, 'id' => $conn->insert_id, 'message' => "Call Info Added")));
	}else{
		echo json_encode(array('result' =>  array('error' => true, 'message' => "DB Insert Error")));
	}
	$conn->close();
}else {
	echo json_encode(array('result' => array('error' => true, 'message' => "Params incomplete!")));
}
?>




