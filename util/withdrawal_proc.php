<?php
include_once('./_common.php');
include_once(G5_THEME_PATH.'/_include/wallet.php');

$now_datetime = date('Y-m-d H:i:s');
$now_date = date('Y-m-d');

$wallet_sql = " SELECT * FROM {$g5['wallet_config']} WHERE function = 'withdrawal' ";
$wallet_config = sql_fetch($wallet_sql);


/*  $func = 'withdraw';
$wallet_addr = 'test1';
$mb_id = 'test1';
$amt_eth = 4.97506;
$amt_haz = 1000; 
 */
//include_once('../lib/otphp/lib/otphp.php');
//include_once(G5_LIB_PATH.'/mailer.lib.php');

/* $f				= trim($_POST['func']);
$mb_id			= trim($_POST['mb_id']);
$wallet_addr	= trim($_POST['wallet_addr']);
$amt_eth		= trim($_POST['amt_eth']);
$amt_haz		= trim($_POST['amt_haz']); */


// 출금 설정 
$fee = $wallet_config['fee']*0.01; // 수수료
$calc_fee = (1 + $fee);
	
// 표시는 ETH, 실제로는 HAZ전환 차감
$amt_eth_cal = $amt_eth;
$amt_haz_cal = $amt_haz * $calc_fee;

$day_limit = $wallet_config['day_limit']; // 일제한
$min_limit = $wallet_config['amt_minimum']; // 최소 출금액
$max_limit = $wallet_config['amt_maximum'];
$max_bal_limit = $member['mb_deposit_point'] * ($max_limit*0.01); // 최대 출금액

//출금기록 확인
$today_ready_sql = "SELECT * FROM {$g5['withdrawal']} WHERE mb_id = '{$mb_id}' AND date_format(create_dt,'%Y-%m-%d') = '{$now_date}' ";
$today_ready = sql_query($today_ready_sql);
$today_ready_cnt = sql_num_rows($today_ready);

if($is_debug) echo "<code>일제한: ".$day_limit .' / 오늘 : '.$today_ready_cnt."<br><br>".$today_ready_sql."</code><br><br>";

// 일 요청 제한
if($day_limit != 0 && $today_ready_cnt >= $day_limit){
	echo (json_encode(array("result" => "Failed", "code" => "0010","sql"=>"out of the day withdraw request"))); 
	return false;
}

if($is_debug) echo "<code>최소: ".$min_limit .' / 최대 : '.$max_bal_limit."  (".$max_limit."%) / 현재예치금".$member['mb_deposit_point']."</code><br><br>";

// 최소금액 제한
if( $min_limit != 0 && $amt_haz < $min_limit ) {
	echo (json_encode(array("result" => "Failed", "code" => "0002","sql"=>"Input correct Minimum Quantity value")));
	return false;
}
// 최대금액 제한
if( $max_limit != 0 && $amt_haz > $max_bal_limit ) {
	echo (json_encode(array("result" => "Failed", "code" => "0002","sql"=>"Input correct Maximun Quantity value")));
	return false;
}



// HAZ 토탈잔고
if($is_debug) {echo "수수료 ".$calc_fee." /   토탈 :".$total_balance_usd." /  출금요청 : ".$amt_haz_cal."<br>" ;}

// 잔고 초과
if($amt_haz_cal > $total_balance_usd){
	echo (json_encode(array("result" => "Failed", "code" => "0002","sql"=>"Not enough balance<br>Check you balance")));
	return false;
}

// 출금주소 확인
if(!$wallet_addr){
	echo (json_encode(array("result" => "Failed", "code" => "0003","sql"=>"Please Input Your Etherium Wallet Address")));
	return false;
}


//출금 처리
$proc_receipt = "insert {$g5['withdrawal']} set
mb_id ='".$mb_id."'
, addr = '".$wallet_addr."'
, account = '{$total_account}'
, amt ={$amt_haz}
, fee = ($amt_haz_cal - $amt_haz)
, fee_rate = {$fee}
, amt_total = {$amt_haz_cal}
, coin = 'eth'
, status = '0'
, create_dt = '".$now_datetime."'
, cost = '{$eth_price}'
, out_amt = '{$amt_eth_cal}'
, od_type = '출금요청' ";



if($is_debug){ 
	$rst = 1;
	echo "<br>".$proc_receipt."<br><br>"; 
}else{
	$rst = sql_query($proc_receipt);
}

/*전환금액 업데이트*/
//  $amt_total = $member['mb_usdt_amt'] + (-1*$amt);
 $amt_query = "UPDATE g5_member set mb_shift_amt = mb_shift_amt - {$amt_haz_cal} where mb_id = '{$mb_id}' ";
 
if($is_debug){ 
	$amt_result = 1;
	print_R($amt_query); 
}else{ 
	$amt_result = sql_query($amt_query);
}


if($amt_result){
	echo (json_encode(array("result" => "OK", "code" => "1000")));
}else{
	echo (json_encode(array("result" => "Failed", "code" => "0001","sql"=>"Please retry again")));
}
