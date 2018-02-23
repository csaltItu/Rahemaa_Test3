<?php
require 'config.php';

function set_recording_info($link, $f_id,$c_id,$u_id) {

if(!empty($_GET['file_id']))
{
	$sql = "UPDATE `recordings_info` SET `flag` = '1' WHERE `recordings_info`.`file_id` = $f_id";
	$link->query($sql);
	return 0;
}

else 
{
	
	$flag=0;
	$f_id='null';
	$time='CURRENT_TIMESTAMP';
	
     $sql =  'INSERT INTO `recordings_info` (`file_id`, `call_id`, `u_id`, `timestamp`, `flag`) VALUES ( '.$f_id.', '.$c_id.', '.$u_id.','."$time".', '.$flag.')';

    $link->query($sql);
    $f_id=mysqli_insert_id($link);
    echo "New recording id: " . mysqli_insert_id($link);
    return $f_id;
        

}

}