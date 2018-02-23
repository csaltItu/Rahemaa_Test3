<?php

require('get_db.php');
header('Content-Type: application/json');

if(isset($_GET['story_id'])) {

	$story_id  = $_GET['story_id'];
	$conn = DBConn();

	$sql  = 'SELECT id FROM comment WHERE is_recorded = 1 AND approved = 1 AND story_id = '.$story_id.' ORDER BY id ASC';

	if ($result = $conn->query($sql)) {

		$comments = array();
		
	    while($row = $result->fetch_row()) {
	    	$comments[] = $row[0];
	    }

	    echo json_encode(array('result' => array('error' => false, 'comments' => $comments, 'len' => sizeof($comments))));
	} else {
		echo json_encode(array('result' => array('error' => true, 'message' => 'Query Error', 'sql' => $sql)));
	}
	$conn->close();
}else{
	echo json_encode(array('result' => array('error' => true, 'message' => 'wrong params')));
}

?>



