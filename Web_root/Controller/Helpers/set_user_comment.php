<?php
require 'config.php';
include 'set_call.php';
include 'set_recording_info.php';

$s_id=$_GET['story_id'];
$u_id=$_GET['u_id'];
$call_id=$_GET['call_id'];

set_user_comment($link,$s_id,$u_id,$call_id);


function set_user_comment($link,$s_id,$u_id,$call_id){
$flag=0;
$f_id='null';
$action_id=11;
$time='CURRENT_TIMESTAMP';
	

    $f_id=set_recording_info($link,$f_id,$call_id,$u_id);

    $sql =  'INSERT INTO `comment` (`commment_id`, `story_id`, `file_id`, `u_id`, `status`) VALUES ( null , '.$s_id.' , '.$f_id.', '.$u_id.' , " " ) ';

    $link->query($sql);

    echo "New comment has id: " . mysqli_insert_id($link);
    
    set_call($link,$u_id,$f_id,$action_id,$call_id);

}
