<?php
require 'config.php';
include 'set_call.php';
include 'set_recording_info.php';

$u_id=$_GET['u_id'];
$call_id=$_GET['call_id'];

set_user_story($link,$u_id,$call_id);

function set_user_story($link,$u_id,$call_id){
$flag=0;
$f_id='null';
$action_id=12;
$time='CURRENT_TIMESTAMP';
	
	$f_id=set_recording_info($link,$f_id,$call_id,$u_id);
    

    $sql =  'INSERT INTO `story` (`story_id`, `file_id`) VALUES ( null , '.$f_id.' ) ';

    $link->query($sql);

    echo "New story has id: " . mysqli_insert_id($link);

    //set call logs
   set_call($link,$u_id,$f_id,$action_id,$call_id);

}
