<?php
include_once('./_common.php');

$registerId = $_POST['registerId'];

$sql = "SELECT mb_id FROM g5_member WHERE mb_id='$registerId'";

$result = sql_query($sql);

$count = sql_num_rows($result);

if($count > 0){
  $response = json_encode(array("response"=>"Aready exist"));
}else{
  $response = json_encode(array("response"=>"Available ID"));
}

echo $response;

 ?>
