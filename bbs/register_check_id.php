<?php
include_once('./_common.php');

if($_POST['check'] == "id"){
  $registerId = $_POST['registerId']; 
  $search = "mb_id = '{$registerId}'";
}

if($_POST['check'] == "wallet"){
  $registerId = $_POST['registerId'];
  $search = "mb_name = '{$registerId}'";
}

$sql = "SELECT mb_id FROM g5_member WHERE ".$search;
  
$result = sql_query($sql);

$count = sql_num_rows($result);

if($count > 0){
  $response = json_encode(array("response"=>"Aready exist","code" => "000"));
}else{
  $response = json_encode(array("response"=>"Available","code" => "001"));
}

echo $response;

 ?>
