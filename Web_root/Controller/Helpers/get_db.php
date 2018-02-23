<?php

function DBConn($db = "suno_abbu", $servername = "127.0.0.1", $username = "root", $password = ""){
	$conn = new mysqli($servername, $username, $password, $db);
	if ($conn->connect_error) return false;
	return $conn;
}

?>