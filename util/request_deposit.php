<?php
include_once('./_common.php');


/*현재시간*/
$now_datetime = date('Y-m-d H:i:s');
$now_date = date('Y-m-d');

$mb_id = $_POST['mb_id'];
$txhash = $_POST['hash'];
$coin = $_POST['coin'];

$sql = "INSERT INTO wallet_deposit_request(mb_id, txhash, create_dt,create_d,status,coin) VALUES('$mb_id','$txhash','$now_datetime','$now_date',0,'$coin')";
$result = sql_query($sql);

if($result){
  echo json_encode(array("response"=>"OK", "data"=>$sql));
}else{
  echo json_encode(array("response"=>"FAIL", "data"=>$sql));
}
?>
