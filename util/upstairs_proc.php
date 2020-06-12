<?php
include_once('./_common.php');
include_once(G5_THEME_PATH.'/_include/wallet.php');

$now_datetime = date('Y-m-d H:i:s');
$now_date = date('Y-m-d');

$mb_id = $member['mb_id'];
$mb_no = $member['mb_no'];

// $input_val ='1';
// $output_val ='169.09';
// $coin_val = 'eth';

$input_val= $_POST['input_val'];
$output_val = $_POST['output_val'];
$coin_val = $_POST['coin_val'];

$target = "mb_".$coin_val."_calc";
$target_price = coin_price($coin_val);
$orderid = date("YmdHis",time()).'01';


$sql = "insert g5_shop_order set
	od_id				= '".$orderid."'
	, mb_no             = '".$mb_no."'
	, mb_id             = '".$mb_id."'
	, od_cart_price     = ".$output_val."
	, od_cash    		= ".$target_price."
	, od_receipt_time   = '".$now_datetime."'
	, od_time           = '".$now_datetime."'
	, od_date           = '".$now_date."'
	, od_settle_case    = '".$coin_val."'
	, od_status         = '매출'
	, upstair    		= ".$input_val."
	, pv				= ".$input_val." ";

if($is_debug){
	$rst = 1;
	echo $sql."<br><br>";
}else{
	$rst = sql_query($sql);
}


/* 레벨계산 */
$save_p = $member['mb_deposit_point'] + $output_val;

if($save_p<300){$mb_level = 0;} 
if($save_p>=300){$mb_level = 1;} 
if($save_p>=900){$mb_level = 2;} 
if($save_p>=1500){$mb_level = 3;} 
if($save_p>=2100){$mb_level = 4;} 
if($save_p>=3000){$mb_level = 5;} 
if($save_p>=6000){$mb_level = 6;} 
if($save_p>=9000){$mb_level = 7;} 
if($save_p>=12000){$mb_level = 8;} 
if($save_p>=30000){$mb_level = 9;}

if($is_debug) echo "After saveP = ".$save_p." level : ".$mb_level."<br><br>";

if($rst){
	$update_point = " update g5_member set {$target} = ({$target}".  - $input_val.")";
	if($member['mb_level'] != 10){
		$update_point .= ", mb_level = {$mb_level}" ;
	}
	$update_point .= ", mb_deposit_point = (mb_deposit_point + {$output_val}) ";
	$update_point .= " where mb_id ='".$mb_id."'";

	if($is_debug){
		echo $update_point."<br>";
	}else{
		sql_query($update_point);
		echo (json_encode(array("result" => "success",  "code" => "0000", "sql" => $save_hist)));
	}
}else{
	echo (json_encode(array("result" => "failed",  "code" => "0001", "sql" => $save_hist)));
}

?>
