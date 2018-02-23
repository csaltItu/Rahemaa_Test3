<?php

require('get_db.php');
header('Content-type: application/json');


function getDel(){

	global $conn;
	global $uid;

	$reqs = array();

	$sql = 'SELECT COUNT(u_id) FROM forward WHERE u_id= "'.$uid.'"';

	if ($result = $conn->query($sql)) {
		if($row = $result->fetch_row())
			return array('error' => false, 'count' => $row[0]);
		else
			return array('error' => false, 'count' => 0, 'message' => "Unable to fetch data.", 'sql' => $sql);
	} else 
		return array('error' => true, 'message' => "Query Error", 'sql' => $sql);
}


if(isset($_GET['uid'])) {

	$uid  = $_GET['uid'];
	$conn = DBConn();

	echo json_encode(array('result' => getDel()));

	$conn->close();
}else {
	echo json_encode(array('result' => array('error' => true, 'message' => "Params incomplete!")));
}
?>



