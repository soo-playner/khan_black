<?php
include_once('/home/sdevftv/html/common.php');
//include_once(G5_ADMIN_PATH.'/admin.lib.php');
//auth_check($auth[$sub_menu], 'r');

function get_mining_days($p_date){//지급일 수 구하기
	$now = new DateTime();
	$birthday = new DateTime($p_date);
	$diff = $now->diff($birthday);
	return $diff->days;
}

/* BTC 시세구하기 www.Whattomine.com 사이트에서 24시간 시세를 구해서 활용 */
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,"http://whattomine.com/coins/1.json?utf8=%E2%9C%93&hr=1000&p=0&fee=0.0&cost=0&hcost=0.0&commit=Calculate");
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
	//접속할 URL 주소
	$result = curl_exec ($ch);
	curl_close ($ch);
	$obj = json_decode($result);
	$day_rev =  str_replace("$", "", $obj->{'btc_revenue'}); 
	$bitrate =  str_replace("$", "", $obj->{'exchange_rate'}); 
	$bitrate = $bitrate*1.05;
	$btc_difficulty = $obj->{'difficulty'};
	$one_hash_rev = $day_rev/1000;
/*BTC 시세구하기 End*/

/*ETH 예상 수익 */
	$ch2 = curl_init();
	curl_setopt($ch2, CURLOPT_URL,"https://whattomine.com/coins/151.json?utf8=%E2%9C%93&hr=1&p=0&fee=0.0&cost=0&hcost=0.0&commit=Calculate");
	curl_setopt ($ch2, CURLOPT_RETURNTRANSFER, 1); 
	$result = curl_exec ($ch2);
	curl_close ($ch2);
	$obj = json_decode($result);
	$onep_mhash = str_replace("$", "", $obj->{'estimated_rewards'});   //1mh당 예상 수익
	$eth_difficulty = $obj->{'difficulty'};
/*ETH 예상 수익 End*/

/*ETH 시세*/
	$ch3 = curl_init();
	curl_setopt($ch3, CURLOPT_URL,"https://min-api.cryptocompare.com/data/price?fsym=ETH&tsyms=USD");
	curl_setopt ($ch3, CURLOPT_RETURNTRANSFER, 1); 
	$result = curl_exec ($ch3);
	curl_close ($ch3);
	$obj = json_decode($result);
	$etherate = str_replace("$", "", $obj->{'USD'});  //1이더당 $가격.
	$etherate = $etherate * 1.05;
/*ETH 시세 End*/

	$get_btc = "SELECT * FROM coin_cost";
	$now_btc_cost = sql_fetch($get_btc);
	$btc_usd = round($now_btc_cost[btc_cost]*1.05,2);
	$eth_usd = round($now_btc_cost[eth_cost]*1.05,2);
//	$today = date("Y-m-d",time() - 3600*24); 
	$today = '2019-06-06';
	$check_exec = "SELECT DAY FROM pinna_soodang_mining_pay WHERE day='".$today."'";
	$rst = sql_fetch($check_exec);
	if($rst){
		//echo "You already execute"."<br>";
		//die;
	}
	$save_sql = "insert pinna_mining_day4  ";
	$save_sql .= "set day = '".$today."'";
	$save_sql .= ", btc = '".$one_hash_rev."'";
	$save_sql .= ", eth = '".$onep_mhash."'";
	$save_sql .= ", btcrate = '".$bitrate."'";
	$save_sql .= ", etcrate  = '".$etherate."'";
	$save_sql .= ", rwcrate  = 1";
	$save_sql .= ", lkcrate  = 0.1";
	//sql_query($save_sql);

	$set_day_btc = "insert pinna_coin_rate set day = '".$today."', btc_rate=".$btc_usd.", eth_rate = ".$eth_usd.", btc_difficulty =".$btc_difficulty.", eth_difficulty ='".$eth_difficulty."'" ;
	echo $set_day_btc ;
	//sql_query($set_day_btc);
	echo "<br>";
/*지급 조건 가져오기 */
	$get_cond = "select * from pinna_mining_cond";
	$list = sql_query($get_cond);
	$payed_amt = array();
	$manage_fee = array();
	while($row = sql_fetch_array($list)){
		$payed_amt[$row['pool_name']] = $row['payed_amt']; 
		$manage_fee[$row['pool_name']] = $row['manage_fee'];
	}
/*지급 조건 가져오기 End*/

/* 회원 정보 가져오기 & 수당 저장 */
	$sql_mbid = "select * from g5_member where mb_id='8484yhs' order by mb_no";
	$ret_mbid = sql_query($sql_mbid);
	while($row_mb=sql_fetch_array($ret_mbid)){
	/*각 회원별 구매 현황을 가져 온다.*/
		$pool_sql = "SELECT c.*, substring(ct_time,1,10) as p_date FROM g5_shop_cart c LEFT JOIN g5_shop_order o ON c.od_id = o.od_id 
							 WHERE c.mb_id = '".$row_mb['mb_id']."' and o.od_status IN ( '입금', '강제입금') and substring(o.od_mining_stime,1,10)<='".$today."' ORDER BY c.ct_price, c.ct_time ASC ";
		$pool_rst = sql_query($pool_sql);
		echo $pool_sql ;
		echo "<br>";
		$pool_count = 0;
		$gpu_cnt = 0;
		$pool_balance = 0;
		while($list_row=sql_fetch_array($pool_rst)){
			/*구매한 풀별로 (Pool1~Pool8, GPU) 수당을 지급한다.*/{

				if($list_row[it_id]=='1527096045'){//pool1
					$pool_lev = 1;
					$profit =  $payed_amt['it_pool1'] - $manage_fee['it_pool1'];
					$mining_days = get_mining_days($list_row['p_date']);
					$fee = $manage_fee['it_pool1'];
					$pool_count = $pool_count+1; //같은날 중복 구매도 발생 각각 구별을 위하여 삽입.

					/*수당을 지급 한다.
					$mining_days : 수당 지급 일 수(200일 cycle에 이용)
					$row_p['p_date'] : 구매일자 (Pool별 중복구매(2개 이상 구매 시)때 구분자로 활용)
					$fee	
					$pool_balance
					$pool_count
					$exchange_rate
					$pool_lev : 풀 등급.
					$today : 지급 날짜 
					$profit : 지급 USD
					$bitrate : btc 환율
					*/
					save_mining_slog($mining_days, $list_row['p_date'], $fee, $pool_count, $pool_lev, $profit, $row_mb['mb_id'], $today, $btc_usd);
					paid_check($pool_lev, $mining_days, $row_mb['mb_id'], $pool_count);
				}
				else if($list_row[it_id]=='1527096041'){//pool2
					$pool_lev = 2;
					$profit =  $payed_amt['it_pool2'] - $manage_fee['it_pool2'];
					$mining_days = get_mining_days($list_row['p_date']);
					$fee = $manage_fee['it_pool2'];
					$pool_count = $pool_count+1; //같은날 중복 구매도 발생 각각 구별을 위하여 삽입.
					save_mining_slog($mining_days, $row_p['p_date'], $fee,  $pool_count, $pool_lev, $profit, $row_mb['mb_id'], $today, $btc_usd);
					paid_check($pool_lev, $mining_days, $row_mb['mb_id'], $pool_count);
				}
				else if($list_row[it_id]=='1527096037'){//pool3
					$pool_lev = 3;
					$profit =  $payed_amt['it_pool3'] - $manage_fee['it_pool3'];
					$mining_days = get_mining_days($list_row['p_date']);
					$fee = $manage_fee['it_pool3'];
					$pool_count = $pool_count+1; //같은날 중복 구매도 발생 각각 구별을 위하여 삽입.
					save_mining_slog($mining_days, $list_row['p_date'], $fee,  $pool_count, $pool_lev, $profit, $row_mb['mb_id'], $today, $btc_usd);
					paid_check($pool_lev, $mining_days, $row_mb['mb_id'], $pool_count);
				}
				else if($list_row[it_id]=='1527096030'){//pool4
					$pool_lev = 4;
					$profit =  $payed_amt['it_pool4'] - $manage_fee['it_pool4'];
					$mining_days = get_mining_days($list_row['p_date']);
					$fee = $manage_fee['it_pool4'];
					$pool_count = $pool_count+1; //같은날 중복 구매도 발생 각각 구별을 위하여 삽입.
					save_mining_slog($mining_days, $list_row['p_date'], $fee,  $pool_count, $pool_lev, $profit, $row_mb['mb_id'], $today, $btc_usd);
					paid_check($pool_lev, $mining_days, $row_mb['mb_id'], $pool_count);
				}
				else if($list_row[it_id]=='1526013457'){//pool5
					$pool_lev = 5;
					$profit =  $payed_amt['it_pool5'] - $manage_fee['it_pool5'];
					$mining_days = get_mining_days($list_row['p_date']);
					$fee = $manage_fee['it_pool5'];
					$pool_count = $pool_count+1; //같은날 중복 구매도 발생 각각 구별을 위하여 삽입.
					save_mining_slog($mining_days, $list_row['p_date'], $fee,  $pool_count, $pool_lev, $profit, $row_mb['mb_id'], $today, $btc_usd);
					paid_check($pool_lev, $mining_days, $row_mb['mb_id'], $pool_count);
				}
				else if($list_row[it_id]=='POOL6'){//pool6
					$pool_lev = 6;
					$profit =  $payed_amt['it_pool6'] - $manage_fee['it_pool6'];
					$mining_days = get_mining_days($list_row['p_date']);
					$fee = $manage_fee['it_pool6'];
					$pool_count = $pool_count+1; //같은날 중복 구매도 발생 각각 구별을 위하여 삽입.
					save_mining_slog($mining_days, $list_row['p_date'], $fee, $pool_count, $pool_lev, $profit, $row_mb['mb_id'], $today, $btc_usd);
					paid_check($pool_lev, $mining_days, $row_mb['mb_id'], $pool_count);
				}
				else if($list_row[it_id]=='POOL7'){//pool7
					$pool_lev = 7;
					$profit =  $payed_amt['it_pool7'] - $manage_fee['it_pool7'];
					$mining_days = get_mining_days($list_row['p_date']);
					$fee = $manage_fee['it_pool7'];
					$pool_count = $pool_count+1; //같은날 중복 구매도 발생 각각 구별을 위하여 삽입.
					save_mining_slog($mining_days, $list_row['p_date'], $fee, $pool_count, $pool_lev, $profit, $row_mb['mb_id'], $today, $btc_usd);
					paid_check($pool_lev, $mining_days, $row_mb['mb_id'], $pool_count);
				}
				else if($list_row[it_id]=='POOL8'){//pool8
					$pool_lev = 8;
					$profit =  $payed_amt['it_pool8'] - $manage_fee['it_pool8'];
					$mining_days = get_mining_days($list_row['p_date']);
					$fee = $manage_fee['it_pool8'];
					$pool_count = $pool_count+1; //같은날 중복 구매도 발생 각각 구별을 위하여 삽입.
					save_mining_slog($mining_days, $list_row['p_date'], $fee, $pool_count, $pool_lev, $profit, $row_mb['mb_id'], $today, $btc_usd);
					paid_check($pool_lev, $mining_days, $row_mb['mb_id'], $pool_count);
				}
				else if($list_row[it_id]=='1515148167'){//GPU
					$pool_lev = 11;
					$gpu_cnt = $gpu_cnt+1;
					$profit =  $payed_amt['it_GPU'] - $manage_fee['it_GPU'];
					$mining_days = get_mining_days($list_row['p_date']);
					$fee = $manage_fee['it_GPU'];
					save_mining_sglog($mining_days, $list_row['p_date'], $fee, $gpu_cnt, $pool_lev, $profit, $row_mb['mb_id'], $today, $eth_usd);
					paid_check($pool_lev, $mining_days, $row_mb['mb_id'], $gpu_cnt);
				}
			}				
		}
	}
/* 회원 정보 가져오기 & 수당 저장 */

/*수당 로그를 저장 한다. */
function save_mining_slog($mining_days, $p_date, $fee, $pool_count, $pool_lev, $profit, $mb_id, $today, $bitrate){


	$prev_oday = date("Y-m-d",strtotime ("-1 days", strtotime($today)));
	$get_balance ="SELECT pool_balance FROM  `pinna_soodang_mining_pay`  WHERE 1 AND mb_id =  '".$mb_id."' AND DAY =  '".$prev_oday."' AND pool_count =".$pool_count." and allowance_name='mining payout (btc)'";
	$rst3 = sql_fetch($get_balance);	
	$pv = 0; 
	$allowance = 'mining payout (BTC)';
	$benefit= round($profit / $bitrate,8);
	$fee_forBTC = round($fee / $bitrate,8);
	$hap = round($rst3['pool_balance']+$benefit,8); //오늘의 채굴 누적량

	if($pool_lev==1){
		$rec = 'BTC mining payout (Pool1)';
		$od_settle_case='P1';
	}
	else if($pool_lev==2){
		$rec = 'BTC mining payout (Pool2)';
		$od_settle_case='P2';
	}
	else if($pool_lev==3){
		$rec = 'BTC mining payout (Pool3)';
		$od_settle_case='P3';
	}
	else if($pool_lev==4){
		$rec = 'BTC mining payout (Pool4)';
		$od_settle_case='P4';
	}
	else if($pool_lev==5){
		$rec = 'BTC mining payout (Pool5)';
		$od_settle_case='P5';
	}
	else if($pool_lev==6){
		$rec = 'BTC mining payout (Pool6)';
		$od_settle_case='P6';
	}
	else if($pool_lev==7){
		$rec = 'BTC mining payout (Pool7)';
		$od_settle_case='P7';
	}
	else if($pool_lev==8){
		$rec = 'BTC mining payout (Pool8)';
		$od_settle_case='P8';
	}

	/*POOL별 수당 저장   Table : pinna_soodang_mining_pay*/
	$temp_sql1  = "insert pinna_soodang_mining_pay set day='".$today."'";
	$temp_sql1 .= ", mb_id					= '".$mb_id."'";
	$temp_sql1 .= " ,mb_name			= '".$mbname."'";
	$temp_sql1 .= " ,mb_recommend	= '".$recom."'";
	$temp_sql1 .= ", allowance_name	= '".$allowance."'";
	$temp_sql1 .= ", benefit					=  ".$benefit;
	$temp_sql1 .= ", benefit_usd			=  ".$profit;
	$temp_sql1 .= ", rec						= '".$rec."'";
	$temp_sql1 .= ", rec_adm				= '".$rec."'";
	$temp_sql1 .= ", fee						= ".$fee;
	$temp_sql1 .= ", fee_forBTC			= ".$fee_forBTC;
	$temp_sql1 .= ", pool_balance		= ".$hap;
	$temp_sql1 .= ", pool_count			= ".$pool_count;
	$temp_sql1 .= ", purchase_date		= '".$p_date."'";
	$temp_sql1 .= ", profit_days			= ".$mining_days;
	$temp_sql1 .= ", exchange_rate		= ".$bitrate;
	//sql_query($temp_sql1);
	echo $temp_sql1;
	echo "<br>";
}
/*수당 로그를 저장 한다. GPU */
function save_mining_sglog($mining_days, $p_date, $fee, $pool_count, $pool_lev, $profit, $mb_id, $today, $ethrate){
	$pv = 0; 
	$allowance = 'mining payout (ETH)';
	$benefit = round($profit / $ethrate,8);
	$fee_forETH = round($fee / $ethrate,8);
	$rec = 'ETH mining payout (Gpu)';
	$prev_oday = date("Y-m-d",strtotime ("-1 days", strtotime($today)));



	$get_balance ="SELECT pool_balance FROM  `pinna_soodang_mining_pay`  WHERE 1 AND mb_id =  '".$mb_id."' AND DAY =  '".$prev_oday."' AND pool_count =".$pool_count." and allowance_name='mining payout (eth)'";
	$balance = sql_fetch($get_balance);	
	$new_balance = round($balance['pool_balance'] + $benefit, 8); //오늘의 채굴 누적량

	$temp_sql1  = "insert pinna_soodang_mining_pay set day='".$today."'";
	$temp_sql1 .= ", mb_id					= '".$mb_id."'";
	$temp_sql1 .= ", mb_name			= '".$mbname."'";
	$temp_sql1 .= ", mb_recommend	= '".$recom."'";
	$temp_sql1 .= ", allowance_name	= '".$allowance."'";
	$temp_sql1 .= ", benefit					=  ".$benefit;
	$temp_sql1 .= ", benefit_usd			=  ".$profit;
	$temp_sql1 .= ", rec						= '".$rec."'";
	$temp_sql1 .= ", rec_adm				= '".$rec."'";
	$temp_sql1 .= ", fee						= ".$fee;
	$temp_sql1 .= ", fee_forBTC			= ".$fee_forETH;
	$temp_sql1 .= ", pool_balance		= ".$new_balance;
	$temp_sql1 .= ", pool_count			= ".$pool_count;
	$temp_sql1 .= ", purchase_date		= '".$p_date."'";
	$temp_sql1 .= ", profit_days			= ".$mining_days;
	$temp_sql1 .= ", exchange_rate		= ".$ethrate;
	//sql_query($temp_sql1);
}
function paid_check($pool_lev, $mining_days, $mb_id, $pool_count){
	$quotient = floor($mining_days / 200) ;
	$reminder = $mining_days % 200 ;
	if($reminder == 0){
		if($quotient==1){
			$from = 1; $to = 200;
		}
		else if($quotient==2){
			$from = 201; $to = 400;
		}
		else if($quotient==3){
			$from = 401; $to = 600;
		}
		else if($quotient==4){
			$from = 601; $to = 800;
		}
		else if($quotient==5){
			$from = 801; $to = 10000;
		}

		if($pool_lev==11){
			$get_sum = "select sum(benefit) as mining_sum
					  from pinna_soodang_mining_pay 
					  where pool_count = ".$pool_count." 
						and profit_days>=".$from." 
						and profit_days<=".$to."
						and allowance_name = 'mining payout (ETH)'
						and mb_id='".$mb_id."'";
		}
		else{
			$get_sum = "select sum(benefit) as mining_sum
					  from pinna_soodang_mining_pay 
					  where pool_count = ".$pool_count." 
						and profit_days>=".$from." 
						and profit_days<=".$to." 
						and allowance_name = 'mining payout (BTC)'
						and mb_id='".$mb_id."'";
		}
		$sum_rst = sql_fetch($get_sum);
		echo "<br>".$get_sum."<br>" ;
		$cycle_sum = $sum_rst['mining_sum'];
		if($pool_lev==1){
			$update_soodang = "update g5_member set it_pool1_profit = round(it_pool1_profit+$cycle_sum,8) where mb_id='".$mb_id."'";
			//sql_query($update_soodang);
			echo $update_soodang;
		}
		if($pool_lev==2){
			$update_soodang = "update g5_member set it_pool2_profit = round(it_pool2_profit+$cycle_sum,8) where mb_id='".$mb_id."'";
			//sql_query($update_soodang);
						echo $update_soodang;
		}
		if($pool_lev==3){
			$update_soodang = "update g5_member set it_pool3_profit = round(it_pool3_profit+$cycle_sum,8) where mb_id='".$mb_id."'";
			//sql_query($update_soodang);
						echo $update_soodang;
		}
		if($pool_lev==4){
			$update_soodang = "update g5_member set it_pool4_profit = round(it_pool4_profit+$cycle_sum,8) where mb_id='".$mb_id."'";
			//sql_query($update_soodang);
			echo $update_soodang;
		}
		if($pool_lev==5){
			$update_soodang = "update g5_member set it_pool5_profit = round(it_pool5_profit+$cycle_sum,8) where mb_id='".$mb_id."'";
			//sql_query($update_soodang);
			echo $update_soodang;
		}
		if($pool_lev==6){
			$update_soodang = "update g5_member set it_pool6_profit = round(it_pool6_profit+$cycle_sum,8) where mb_id='".$mb_id."'";
			//sql_query($update_soodang);
			echo $update_soodang;
		}
		if($pool_lev==7){
			$update_soodang = "update g5_member set it_pool7_profit = round(it_pool7_profit+$cycle_sum,8) where mb_id='".$mb_id."'";
			//sql_query($update_soodang);
			echo $update_soodang;
		}
		if($pool_lev==8){
			$update_soodang = "update g5_member set it_pool8_profit = round(it_pool8_profit+$cycle_sum,8) where mb_id='".$mb_id."'";
			//sql_query($update_soodang);
			echo $update_soodang;
		}
		if($pool_lev==11){
			$update_soodang = "update g5_member set it_poolg_profit = round(it_poolg_profit+$cycle_sum,8) where mb_id='".$mb_id."'";
			//sql_query($update_soodang);
			echo $update_soodang;

		}
	}
}
?>
