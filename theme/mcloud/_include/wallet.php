<?
if($_GET['debug']) $is_debug = 1;

/*부분서비스점검*/
$sql = " select * from maintenance";
$nw = sql_fetch($sql);
$nw_with = $nw['nw_with'];
$nw_upstair = $nw['nw_upstair'];


// 시세업데이트 시간
if($is_date){
	$last_rate_time = last_exchange_rate_time();
	echo "exchage last : ".$last_rate_time."<br>";
	echo "exchage next : ".$next_rate_time."<br>";
}


//비회원,일반회원
if($member['mb_level'] < 10){
	// $user = "<img src='".G5_THEME_URL."/img/symbol.png' title='".$member['grade']."'/>";
	$user ="<i class='ri-user-line icon_user'></i><i class='ri-number-".$member['mb_level']." level_txt'></i>";
}else{
	$user = "<i class='ri-user-settings-line icon_user'></i>";
}


/*날짜선택 기본값 지정*/
if (empty($fr_date)) {$fr_date = date("Y-m-d", strtotime(date("Y-m-d")."-3 month"));}
if (empty($to_date)){$to_date =  date("Y-m-d", strtotime(date("Y-m-d")));}


// 회사지갑 설정
define('BONUS_CURENCY','ETH');
define('BALANCE_CURENCY','$');

define('ETH_ADDRESS','0xfAd6dB131138dA1B8FF8092337b0067805af60C2');


$bonus_sql = "select * from {$g5['bonus_config']} order by idx";
$list = sql_query($bonus_sql);
$pre_setting = sql_fetch($bonus_sql);

$limited = $pre_setting['limited'];
if($limited < 0){
	$limited_per = 100/$limited*100;
}


// 현재 통화 시세
$haz_price = coin_price('haz');
$eth_price = coin_price('eth');
$usdt_price = coin_price('usdt');
$usd_price = coin_price('usd');

if($is_debug) echo "<br>ETH CURRENT PRICE :: ".$eth_price."<br><br>";


function coin_price($income){
	global $g5;

	$currency_sql = " SELECT * from {$g5['coin_price']} where symbol = '{$income}' ";
	$result = sql_fetch($currency_sql);

	if($result['manual_use'] == 1){
		return $result['manual_cost'];
	}else{
		return $result['current_cost'];
	}
}


function shift_price($income,$val = 1, $outcome){
	$in_price = coin_price($income);
	$out_price = coin_price($outcome);
	
	return $in_price * $val / $out_price;
}


// 토탈 잔고 - 대쉬보드에서만 사용
// 잔고 토탈 = 입금(usdt,eth +) + 출금(-) + 예치금전환(-) + 수당(+)
$math_sql = "select sum(mb_usdt_account + mb_usdt_calc + mb_usdt_amt) as usdt_total, sum(mb_eth_account + mb_eth_calc + mb_eth_amt) as eth_total, sum(mb_balance + mb_shift_amt) as balance_total  from g5_member where mb_id = '{$member['mb_id']}' ";
$math = sql_fetch($math_sql);

$total_usdt = $math['usdt_total'];
$total_eth = $math['eth_total'];
$total_balance = $math['balance_total'];

$total_usdt_usd = $math['usdt_total']*$usdt_price;
$total_eth_usd = $math['eth_total']*$eth_price;
$total_balance_usd = $math['balance_total']*$haz_price;

$total_account = $total_usdt + $total_eth + $math['balance_total'];
$total_balance_num = Number_format($total_balance, 1); // 콤마 포함 소수점 2자리까지




// 출금-업스테어 가능 잔고 계산시
$ava_sql = "select sum(mb_account + mb_calc + mb_amt + mb_balance) as total from g5_member where mb_id = '".$member['mb_id']."'";
$ava_total = sql_fetch($ava_sql);
$ava_balance = $ava_total['total'];
$ava_balance_num = Number_format($ava_balance, 2); // 콤마 포함 소수점 2자리까지




// 예치금/수당 퍼센트
function bonus_state($mb_id){
	global $limited_per;

	$math_percent_sql = "select sum(mb_balance / mb_deposit_point) * {$limited_per} as percent from g5_member where mb_id ='{$mb_id}' ";

	$math_percent = sql_fetch($math_percent_sql)['percent'];
	if($is_debug) echo "BONUS PERCENT :".$math_percent;
	return $math_percent;
}


/*레퍼러 하부매출*/
function refferer_habu_sales($mb_id){

	$referrer_sql = "select day,noo from recom_bonus_noo where mb_id ='{$mb_id}' ORDER BY day desc limit 1";
	$referrer_result = sql_fetch($referrer_sql);
	$referrer_sales = $referrer_result['noo'];

	if($referrer_sales > 0){
		$referrer_sales = $referrer_sales;
	}else{
		$referrer_sales = 0;
	}
	return $referrer_sales;
}

/*레퍼러 POWER LEG 하부매출*/
function refferer_habu_sales_power($mb_id){
	$max_recom_sql = "SELECT mb_id,MAX(noo) as big FROM recom_bonus_noo AS A WHERE A.mb_id IN (select mb_id FROM g5_member WHERE mb_recommend = '{$mb_id}' )";
	// $max_recom_result = sql_query($max_recom_sql);
	$max_recom = sql_fetch($max_recom_sql);

	$max_recom_point = $max_recom['big'];
	
	if($max_recom_point > 0){
		$max_recom_point = $max_recom_point;
	}else{
		$max_recom_point = 0;
	}
	return $max_recom_point;
}



/*스폰서 하부매출*//* 
function sponsor_habu_sales($mb_id){
	$b_recomm_sql = "select mb_id as b_recomm from g5_member where mb_brecommend='".$mb_id."' and mb_brecommend_type='L'";
	$b_recomm_res = sql_fetch($b_recomm_sql);
	$b_recomm = $b_recomm_res['b_recomm'];
	if($b_recomm){
		$left_noo_sql = "select noo from bnoo2 where mb_id ='{$b_recomm}' order by day desc limit 0 ,1";
		$left_noo_result = sql_fetch($left_noo_sql);
		$left_noo = $left_noo_result['noo'];
	}


	$b_recomm2_sql = "select mb_id as b_recomm2 from g5_member where mb_brecommend='".$mb_id."' and mb_brecommend_type='R'";
	$b_recomm2_res = sql_fetch($b_recomm2_sql );
	$b_recomm2 = $b_recomm2_res['b_recomm2'];
	if($b_recomm2){
		$right_noo_sql = "select noo from bnoo2 where mb_id ='{$b_recomm2}' order by day desc limit 0 ,1";
		$right_noo_result = sql_fetch($right_noo_sql);
		$right_noo = $right_noo_result['noo'];
	}

	$sponsor_sales_sum = $left_noo + $right_noo;

	if($sponsor_sales_sum > 0){
		$sponsor_sales = Number_format($sponsor_sales_sum);
	}else{
		$sponsor_sales = 0;
	}

	return $sponsor_sales;
} */


/*국가코드*/
$nation_name=array('Japan'=>81,'Republic of Korea'=>82,'Vietnam'=>84,'China'=>86,'Indonesia'=>62,'Philippines'=>63,'Thailand'=>66);

	// 회원가입시
function get_member_nation_select($name, $selected=0, $key="")
{
    global $g5,$nation_name;

	$str = "\n<select id=\"{$name}\" name=\"{$name}\"";
	$str .= ">\n";

	foreach($nation_name as $key => $value){
		$str .= '<option value="'.$value.'"';
		echo $value." | ".$selected."<br>";
        if ($value == $selected)
            $str .= ' selected="selected"';
        $str .= ">0".$value." - {$key}</option>\n";
	}
	$str .= "</select>\n";
	return $str;
}

// 프로필
function get_member_nation($value)
{
    global $g5,$nation_name;
	$key = array_search($value, $nation_name);
	return $key;
}

// 시세 마지막 업데이트 시간
function last_exchange_rate_time(){
	$sql = "SELECT * FROM m3cron_log ORDER BY DATETIME desc limit 1";
	$result = sql_fetch($sql);
	$last_time = $result['datetime'];
	return $last_time;
}

// 시세 다음 업데이트 시간
function next_exchange_rate_time(){
	$sql = "SELECT * FROM m3cron_log ORDER BY DATETIME desc limit 1";
	$result = sql_fetch($sql);
	$last_time = $result['datetime'];
	$next_time = date("Y-m-d h:i:s a", strtotime(date($last_time)."+12 hour"));
	return $next_time;
}

/*

$math_sql = "select mb_balance,mb_deposit_point,sum(mb_usdt_account + mb_usdt_calc + mb_usdt_amt + mb_balance + mb_shift_amt) as total from g5_member where mb_id = '".$member['mb_id']."'";
$math_total = sql_fetch($math_sql);

$math_percent_sql = "select sum(mb_balance / mb_deposit_point) * {$limited_per} as percent from g5_member where mb_id =  '".$row['mb_id']."'";
$math_percent = sql_fetch($math_percent_sql);

$total_balance = $math_total['total'];



$usdt_account_num = $math_total['usdt_total'];
$usdt_account = number_format($usdt_account_num,2);


$balance_account = number_format($math_total['usdt_total']/2,2);


$mb_upstair = number_format($member['mb_deposit_point'],5);



$usdt_cost = number_format(get_coin_cost('usdt'),2);
$usdt_cost_num = get_coin_cost('usdt');
$eth_cost = number_format(get_coin_cost('eth'),2);
$eth_cost_num = get_coin_cost('eth');


$usdt_rate_num = $math_total['usdt_total'] * get_coin_cost('usdt');
$usdt_rate = number_format( $usdt_rate_num,2);

$eth_rate_num = $math_total['eth_total'] * get_coin_cost('eth');
$eth_rate = number_format($eth_rate_num,2);



$total_rate = number_format(($math_total['usdt_total'] * get_coin_cost('usdt')) + ($math_total['v7_total'] * get_coin_cost('v7')) + ($math_total['eth_total'] * get_coin_cost('eth')) + ($math_total['rwd_total'] * get_coin_cost('rwd'))+ ($math_total['lok_total'] * get_coin_cost('lok')),2);


$exchange_fee = 3;


$deposit_fee = 5;
$deposit_cost =  round($usdt_cost_num - ($usdt_cost_num*($deposit_fee/100)),2);
*/


/*내 지갑 주소
$wallet_sql = "select mb_wallet from g5_member where mb_id = '".$member['mb_id']."'";
$wallet_account = sql_fetch($wallet_sql);
$wallet_addr =  $wallet_account['mb_wallet'];
*/

/*전환 수수료 계산
function exchage_result($val) {
	$exchage_cost = get_coin_cost('usdt')+((get_coin_cost('usdt')*3/100);
	return Number_format($exchage_cost*$val, 2);
}*/


/* 전환
function exchage_result($val) {
	$exchage_cost = 100 + (100*5/100);
	return Number_format($exchage_cost*$val, 2);
}*/

/*업스테어
function deposit_result($val){
	global $usdt_cost;
	$deposit_cost =  $usdt_cost - ($usdt_cost*0.03);
	return Number_format($deposit_cost*$val, 2);
}*/

/*달러 표시
function shift_doller($val){
	return Number_format($val, 2);
}*/

/*usdt 표시*/
function shift_usdt($val){
	return Number_format($val, 8);
}

/*숫자표시*/
function shift_number($val){
	return preg_replace("/[^0-9].*/s","",$val);
}

/*콤마제거숫자표시*/
function conv_number($val) {
	$number = (int)str_replace(',', '', $val);
	return $number;
}

/*날짜형식 변환*/
function timeshift($time){
	return date("d/m/Y ",strtotime($time));
}


function nav_active($val){
		global $stx;
		if($val == $stx) echo "active";
		if(!$stx && $val='all') echo "active";
}

function string_explode($val ){
	$stringArray = explode("member",$val);
	$string1= "<span class='tx1'>".$stringArray[0]." member</span>";
	$string2 = "<span class='tx2'>".$stringArray[1]."</span>";
	return $string1.$string2;
}

function Number_explode($val ){
	$stringArray = explode(".",$val);
	$string1= $stringArray[0].".";
	$string2 = "<string class='demical'>".$stringArray[1]."</string>";
	return $string1.$string2;
}

function string_shift_code($val){
	switch ($val) {
		case "0" :
			echo "Request Checking ..";
			break;
		case "1" :
			echo "<span class='font_green'>Complete</span>";
			break;
		case "2" :
			echo "Processing";
			break;
		case "3" :
			echo "<span class='font_red'>Reject</span>";
			break;
		case "4" :
			echo "<span class='font_red'>Cancle</span>";
			break;
		default :
			echo "Request Checking ..";
	}
}

?>
