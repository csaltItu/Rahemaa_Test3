<?php
require 'config.php';
include 'set_call.php';
include 'set_recording_info.php';

$u_id=$_GET['u_id'];
$call_id=$_GET['call_id'];

set_user_request($link,$u_id,$call_id);


function set_user_request($link,$u_id,$call_id){
$flag=0;
$f_id='null';
$action_id=13;
$time='CURRENT_TIMESTAMP';
    

    $f_id=set_recording_info($link,$f_id,$call_id,$u_id);
    
    set_call($link,$u_id,$f_id,$action_id,$call_id);

}
