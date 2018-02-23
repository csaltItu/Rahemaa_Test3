<?php
require 'config.php';
include 'set_call.php';
include 'set_recording_info.php';

$type=$_GET['type'];
$fileid=$_GET['file_id'];
$u_id = $_GET['u_id'];
$call_id = $_GET['call_id'];
$data = '';


$data = moreinfo_file_id($type,$fileid,$link,$u_id,$call_id);

if (!$data) {
  http_response_code(404);
  die("ERROR");
}
if ($data->num_rows > 0) {
    // output data of each row
    while($row = $data->fetch_assoc()) {
       // echo  $row["file_id"];
        echo json_encode($row);
    }
} else {
    echo "0 results";
}



function moreinfo_file_id($type,$fileid,$link,$u_id,$call_id){

$time='CURRENT_TIMESTAMP';

switch ($type) 
 {
  case 'DT':
  $action_id=6;

 $sql = "SELECT dt_more_info.file_id 
          FROM dt_more_info join doctor_tip on dt_more_info.tip_id=doctor_tip.tip_id 
            WHERE doctor_tip.file_id = $fileid ";
   
             $result = $link->query($sql);
  
              $result->num_rows;
   
               $row = $result->fetch_assoc();

               set_call($link,$u_id,$row["file_id"],$action_id,$call_id);
        
              
 
 return $result;

   case 'cost':
  
   $action_id=5;

  $sql = "SELECT cost_more_info.file_id 
          FROM cost_more_info JOIN cost ON cost_more_info.cost_id= cost.cost_id 
            WHERE cost.file_id = $fileid ";

            $result = $link->query($sql);

            $result->num_rows;
   
            $row = $result->fetch_assoc();

            set_call($link,$u_id,$row["file_id"],$action_id,$call_id);
        
        
 return $result;
}

}