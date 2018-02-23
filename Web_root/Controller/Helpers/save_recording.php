<?php

require('get_db.php');
header('Content-Type: application/json');

function updateUQ(){

	global $conn;
	global $record_id;

	$sql = 'UPDATE suggestion SET is_recorded = 1 WHERE sugg_id = "'.$record_id.'"';

	if ($result = $conn->query($sql)) {
		echo json_encode(array('result' =>  array('error' => false, 'message' => "Update Query Executed")));
	}else{
		echo json_encode(array('result' =>  array('error' =>  true, 'message' => "DB Update Error")));
	}
}

function updateQO(){

	global $conn;
	global $record_id;

	$sql = 'UPDATE user_story SET is_recorded = 1 WHERE id = "'.$record_id.'"';

	if ($result = $conn->query($sql)) {
		echo json_encode(array('result' =>  array('error' => false, 'message' => "Update Query Executed")));
	}else{
		echo json_encode(array('result' =>  array('error' =>  true, 'message' => "DB Update Error")));
	}
}

function updateUN(){
	
	echo json_encode(array('result' =>  array('error' => false, 'message' => "Name Must Have Been Saved?")));
}

function updateUC(){

	global $conn;
	global $record_id;

	$sql = 'UPDATE comment SET is_recorded = 1 WHERE id = "'.$record_id.'"';

	if ($result = $conn->query($sql)) {
		echo json_encode(array('result' =>  array('error' => false, 'message' => "Update Query Executed")));
	}else{
		echo json_encode(array('result' =>  array('error' =>  true, 'message' => "DB Update Error")));
	}
}

function updateFB(){

	global $conn;
	global $record_id;

	$sql = 'UPDATE main_feedback SET is_recorded = 1 WHERE feedback_id = "'.$record_id.'"';

	if ($result = $conn->query($sql)) {
		echo json_encode(array('result' =>  array('error' => false, 'message' => "Update Query Executed")));
	}else{
		echo json_encode(array('result' =>  array('error' =>  true, 'message' => "DB Update Error")));
	}
}

if(isset($_GET['type']) && isset($_GET['call_id']) && isset($_GET['uid']) && isset($_GET['record_id'])) {

	$uid       = $_GET['uid'];
	$type 	   = $_GET['type'];
	$Call_ID   = $_GET['call_id'];
	$record_id = $_GET['record_id'];

	$conn = DBConn();

	if($type == 'request'){
		updateUQ();
	}else if($type == 'name'){
		updateUN();
	}else if($type == 'story'){
		updateQO();
	}else if($type == 'comment'){
		updateUC();
	}else if($type == 'fb'){
		updateFB();
	}else{
		echo json_encode(array('result' => array('error' => true, 'message' => 'wrong type')));
	}

}else{
	echo json_encode(array('result' => array('error' => true, 'message' => 'wrong params')));
}

?>