<?php
include_once('./_common.php');
//include_once(G5_LIB_PATH.'/mailer.lib.php');
$now_datetime = date('Y-m-d H:i:s');
$now_date = date('Y-m-d');
if($_GET['debug']) $debug = 1;

$uid = $_POST['uid'];
$status = $_POST['status'];
$refund = $_POST['refund'];
$coin = $_POST['coin'];
$amt = $_POST['amt'];
$func = $_POST['func'];

/* if($debug){
	$uid=2;
	$status=1;
	$refund='Y';
	$func= 'deposit';
	$coin = 'usdt';
	$amt = '100';
} */

if($func =='withrawal'){
	if($status=='4' && $refund == 'Y'){
		$get_row = "SELECT * from {$g5['withdrawal']} where uid = {$uid} ";
		if($debug){
			print_r($get_row );
		}
		$ret = sql_fetch($get_row);
		$mb_id = $ret['mb_id'];
		$amt_total = $ret['amt_total'];
		
		$sql1 = "update g5_member set mb_shift_amt = mb_shift_amt + {$amt_total} where mb_id='{$mb_id}' ";

		if($debug){
			print_r($sql1);
			echo "<br>";
		}else{
			sql_query($sql1);
		}
	}

		$sql = "UPDATE {$g5['withdrawal']} set status = '{$status}' ";
		$sql .= ", update_dt = now() ";
		$sql .= " where uid = {$uid} ";

}else if($func =='deposit'){
	if($status=='1' && $refund == 'Y' && $coin != '' && $amt > 0){
		$get_row = "SELECT * from {$g5['deposit']} where uid = {$uid} ";
		if($debug){
			print_r($get_row);
			echo "<br>";
		}
		$ret = sql_fetch($get_row);
		$mb_id = $ret['mb_id'];

		$coin_target = "mb_".strtolower($coin)."_account";
		$sql1 = "update g5_member set {$coin_target} = ($coin_target + $amt) where mb_id='{$mb_id}' ";

		if($debug){
			print_r($sql1);
			echo "<br>";
		}else{
			sql_query($sql1);
		}
	}

		$sql = "UPDATE {$g5['deposit']} set status = '{$status}' ";
		$sql .= ", in_amt = {$amt}";
		$sql .= ", update_dt = now() ";
		$sql .= " where uid = {$uid} ";
	
}else{
	echo (json_encode(array("result" => "failed", "code" => "9999", "sql" => "func can't find ERROR ")));
}

if($debug){
	echo "<br>";
	print_r($sql);
	echo "<br>";
}else{
	$result = sql_query($sql);
}

if($result){
	echo (json_encode(array("result" => "OK", "code" => "1000")));
}else{
	echo (json_encode(array("result" => "failed", "code" => "0000")));
}
