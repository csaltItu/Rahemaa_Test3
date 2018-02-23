<?php
include 'set_call.php';
require 'config.php';

$story_id = $_GET['story_id'];
$u_id = $_GET['u_id'];
$call_id = $_GET['call_id'];
$data = '';
$action_id=9;
$time='CURRENT_TIMESTAMP';

$data = comments($story_id,$link);

if (!$data) {
  http_response_code(404);
  die("ERROR");
}
if ($data->num_rows > 0) {
    // output data of each row
    while($row = $data->fetch_assoc()) {
       // echo  $row["file_id"];
        echo json_encode($row) . "<br>";
		$f_id= $row["file_id"];
	  set_call($link,$u_id,$f_id,$action_id,$call_id);
   		 
    }
} else {
    echo "0 results";
}

function comments($story_id,$link){
 
 $sql = "SELECT comment.file_id FROM story INNER JOIN comment ON story.story_id =comment.story_id
            WHERE story.story_id= $story_id ";

 	$result = $link->query($sql);

 return $result;

}
