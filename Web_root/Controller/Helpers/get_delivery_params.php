<?php

require('get_db.php');
header('Content-type: application/json');


function getDel(){

	global $conn;
	global $id;

	$reqs = array();

	$sql = 'SELECT type, file_id, dest FROM forward WHERE forward_id= "'.$id.'"';

	if ($result = $conn->query($sql)) {
		if($row = $result->fetch_row())
			return array('error' => false, 'type' => $row[0], 'file_id' => $row[1], 'fuid' => $row[2]);
		else
			return array('error' => true, 'message' => "Fetch Error", 'sql' => $sql);
	} else 
		return array('error' => true, 'message' => "Query Error", 'sql' => $sql);
}


if(isset($_GET['id'])) {

	$id  = $_GET['id'];
	$conn = DBConn();

	echo json_encode(array('result' => getDel()));

	$conn->close();
}else {
	echo json_encode(array('result' => array('error' => true, 'message' => "Params incomplete!")));
}
?>



