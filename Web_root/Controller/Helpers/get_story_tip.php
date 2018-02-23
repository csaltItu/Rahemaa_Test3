<?php
require 'config.php';
include 'set_call.php';

$storyid=$_GET['story_id'];
$call_id=$_GET['call_id'];
$u_id=$_GET['u_id'];
$action_id=14;
$time='CURRENT_TIMESTAMP';
$data = '';

$data = get_story_tip($storyid,$link);

if (!$data) {
  http_response_code(404);
  die("ERROR");
}
if ($data->num_rows > 0) {
    // output data of each row
    while($row = $data->fetch_assoc()) {
       // echo  $row["file_id"];
        echo json_encode($row) . "<br>";
        set_call($link,$u_id,$row["file_id"],$action_id,$call_id);
    }
} else {
    echo "0 results";
}

function get_story_tip($storyid,$link){

 $sql = "SELECT story_tip.file_id
          FROM  story_tip 
          WHERE story_tip.story_id = $storyid";
 
 $result = $link->query($sql);
 return $result;
}


 



