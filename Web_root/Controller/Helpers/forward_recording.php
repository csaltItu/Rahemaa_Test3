<?php
require 'config.php';
include 'set_call.php';


$u_id=$_GET['u_id'];
$call_id=$_GET['call_id'];
$dest=$_GET['dest'];
$f_id=$_GET['file_id'];
$action_id=8;
$time='CURRENT_TIMESTAMP';



$sql =  'INSERT INTO `forward` (`forward_id`, `file_id`, `u_id`, `dest`, `call_id`) VALUES ( null , '.$f_id.', '.$u_id.', "'.$dest.'" , '.$call_id.')';  

$result = $link->query($sql);

set_call($link,$u_id,$f_id,$action_id,$call_id);


