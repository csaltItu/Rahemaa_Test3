<?php

require('get_db.php');
header('Content-Type: application/json');

if(isset($_GET['uid']) && isset($_GET['type']) && isset($_GET['call_id'])) {

  $uid  = $_GET['uid'];
  $cid  = $_GET['call_id'];
  $type = $_GET['type'];

  echo json_encode(array('result' => getfileid($uid,$cid,$type) ));

}else{
  echo json_encode(array('result' => array('error' => true, 'message' => 'wrong params')));
}

function getCostID($result, $type){

  $files = array();

  while($row = $result->fetch_row()) {
      $file = array(
          'file_id' => $row[0],
          'name' => $type.$row[0].".wav",
          'info' => $type.$row[0]."-1.wav",
          'tip' => $type.$row[0]."tip.wav",
        );
      if ($type == "Story") {
        $file["cat"] = $row[1];
        if ($row[1] == "user") {
          $file["name"] = $row[0].".wav";
        }
      }
      $files[] = $file;
  }

  return array('error' => false, 'files' => $files, 'type' => $type);
}

function getfileid($uid,$cid,$type){


    switch ($type)  {

      case 'Tip':

        $sql = "SELECT doctor_tip.file_id
                FROM   doctor_tip  
                WHERE  doctor_tip.file_id  > (SELECT MAX(IFNULL(calls.file_id, 0)) FROM calls WHERE u_id = $uid AND action_id = 1)
                ORDER BY doctor_tip.file_id ASC ";

        $inner_sql = " SELECT doctor_tip.file_id FROM   doctor_tip ORDER BY doctor_tip.file_id ASC ";

        break;

      case 'Cost':

        $sql=  "SELECT cost.file_id , file_id
                FROM   cost
                WHERE  cost.file_id  > (SELECT MAX(IFNULL(calls.file_id, 0)) FROM calls WHERE u_id = $uid AND action_id = 2)
                ORDER BY cost.file_id ASC ";
        
        $inner_sql = " SELECT cost.file_id FROM   cost ORDER BY cost.file_id ASC ";

        break;

      case 'Story':

        /*
        
        $sql=  "SELECT story.file_id , 'admin' as cat
                FROM   story
                WHERE  story.file_id  > (SELECT MAX(IFNULL(calls.file_id, 0)) FROM calls WHERE u_id = $uid AND action_id = 3)
                ORDER BY story.file_id ASC ";
        
        */

        $sql = "SELECT story.file_id , 'admin' as cat
                FROM   story
                WHERE  story.file_id  > (SELECT MAX(IFNULL(calls.file_id, 0)) FROM calls WHERE u_id = $uid AND action_id = 3)
                
                UNION
                
                SELECT user_story.id , 'user' as cat
                FROM   user_story
                WHERE  user_story.id  > (SELECT IFNULL(MAX(IFNULL(calls.file_id, 0)),0) FROM calls WHERE u_id = $uid AND action_id = 17) AND user_story.is_recorded = 1 AND user_story.approved = 1";

        $inner_sql = " SELECT story.file_id, 'admin' as cat FROM story UNION SELECT user_story.id, 'user' as cat FROM user_story WHERE user_story.is_recorded = 1 AND user_story.approved = 1";

        break;

      default:  return array('error' => true, 'message' => "Unrecognized Type", 'type' => $type);
    }

    $conn = DBConn();

    if ($result = $conn->query($sql)) {
      if($result->num_rows > 0){
          return getCostID($result, $type);
      }else {
        if ($inner_result = $conn->query($inner_sql)) {
          if($inner_result->num_rows > 0){
              return getCostID($inner_result, $type);
          }else {
            return array('error' => true, 'message' => "No Tips Exist?", 'sql' => $inner_sql);
          }
        }else return array('error' => true, 'message' => "Error in Fetching Basic Tips", 'sql' => $inner_sql);
      }
    } else return array('error' => true, 'message' => "Query Error : getfileid", 'sql' => $sql, 'type' => $type);
}



 



