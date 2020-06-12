<?php
$sub_menu = '400400';
include_once('./_common.php');


auth_check($auth[$sub_menu], "w");

check_admin_token();

$ct_chk_count = count($_POST['ct_chk']);
if(!$ct_chk_count)
    alert('처리할 자료를 하나 이상 선택해 주십시오.');

$status_normal = array('주문','입금','준비','배송','완료','강제입금');
$status_cancel = array('취소','반품','품절');

if (in_array($_POST['ct_status'], $status_normal) || in_array($_POST['ct_status'], $status_cancel)) {
    ; // 통과
} else {
    alert('변경할 상태가 올바르지 않습니다.');
}

$mod_history = '';
$cnt = count($_POST['ct_id']);
$arr_it_id = array();

$chkord_status = "select od_id,  od_status, od_settle_case from g5_shop_order where od_status='주문' and od_id='$od_id';";
$rst_status = sql_query($chkord_status);
$now_stat = sql_fetch_array($rst_status);

if($_POST['ct_status']=='입금' && $now_stat['od_status']=='주문'){

	$sql = " select mb_id, ct_price, ct_qty, it_id, it_name, ct_send_cost, it_sc_type from {$g5['g5_shop_cart_table']} where od_id = '$od_id' and  group by it_id order by ct_id ";
	$result = sql_query($sql);
	for($i=0; $row=sql_fetch_array($result); $i++){
		$total_price = $total_price + $row['ct_price']* $row['ct_qty'];
			$mb_id = $row['mb_id'];
		if($row['ct_price']!=99)
			$total_pv = $total_pv + $row['ct_price']* $row['ct_qty'];
	}
	$now_date = date("Y-m-d H:i:s",time()); 
	$ch = curl_init();
	$sel_id = "select my_walletId from g5_member where mb_id='$mb_id';";
	$rst = sql_query($sel_id);
	$w_rst = sql_fetch_array($rst);
	$wid = $w_rst['my_walletId'];
	curl_setopt($ch,CURLOPT_URL, "http://localhost:3000/merchant/$wid/balance?password=0803bjuung");
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
	//접속할 URL 주소
	$result = curl_exec ($ch);
	curl_close ($ch);
	$obj = json_decode($result);
	$balance =  $obj->{'balance'}; 	
/*
	$sql_price = "select btc_cost from coin_cost";
	$result = sql_query($sql_price);
	$ret = sql_fetch_array($result);
	$exchange_rate24 =  $ret['btc_cost'];
	$price_coin = 1/$exchange_rate24 *$total_price;
*/
	$sql_price = "select payment_btc from g5_shop_order where od_id='$od_id';";
	$result = sql_query($sql_price);
	$ret = sql_fetch_array($result);
	$price_coin =  $ret['payment_btc'];

	$price_coin = round($price_coin,8);
	if($balance/100000000 >= $price_coin && $price_coin!=0 && $balance!=0){//> $price_coin-0.01){
		$send_coin = ($price_coin*100000000-10000);
		$ch = curl_init();
		$sel_id = "select my_walletId from g5_member where mb_id='$mb_id';";
		$rst = sql_query($sel_id);
		$w_rst = sql_fetch_array($rst);
		$wid = $w_rst['my_walletId'];
		$get_wad = "select mb_wallet from g5_member where mb_id = '$mb_id'";
		$wadrst = sql_query($get_wad);
		$ret = sql_fetch_array($wadrst);		
		$from_addr = $ret['mb_wallet'];
		$to_addr = PINNACEL_WALLET;
		
		curl_setopt($ch,CURLOPT_URL, "http://localhost:3000/merchant/{$wid}/payment?password=0803bjuung&second_password=&to=$to_addr&amount=$send_coin&from=$from_addr");
	//	echo "http://localhost:3000/merchant/{$wid}/payment?password=0803bjuung&second_password=&to=$to_addr&amount=$send_coin&from=$from_addr";
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
		$result = curl_exec ($ch);		
		curl_close ($ch);
		$obj = json_decode($result);
		$message =  "message : ".$obj->{'message'}; 

		if(true){
			$get_brcom = "select mb_brecommend from g5_member where mb_id='".$mb_id."'";
			$ret = sql_query($get_brcom);
			$row = sql_fetch($ret);
			if($row['mb_brecommend']!=null && $row['mb_brecommend']==""){
				$sql_purchase = "update g5_shop_order set od_receipt_time='$now_date', od_mining_stime='$now_date', od_status='입금',  pv = truncate(od_cart_price,-2)/2  where od_id = '$od_id'; ";
				calc_sale($mb_id, date('Y-m-d'));
			}
			else{
				$sql_purchase = "update g5_shop_order set od_status='입금', od_mining_stime='$now_date', pv = truncate(od_cart_price,-2)/2  where od_id = '$od_id'; ";
			}			
			sql_query($sql_purchase);	
			$sql_cart = "select ct_id, mb_id, ct_price, ct_qty, it_id, it_name, ct_send_cost, it_sc_type from {$g5['g5_shop_cart_table']} where od_id	= '$od_id' group by it_id order by ct_id ";
			$rst = sql_query($sql_cart);
			//echo $sql_cart;
			for($i=0; $rst_rw=sql_fetch_array($rst); $i++){

				if($rst_rw['it_id'] == '1527096053'){//멤버쉽 구매
					$ct_id = $rst_rw['ct_id'];
					$id = $rst_rw['mb_id'];  
					$qty = $rst_rw['ct_qty'];
					$sql_poolist_up = "update g5_member set membership_yn='Y', mb_level=1 where mb_id='$id';";
					sql_query($sql_poolist_up);
					$sql_chgst = "update g5_shop_cart set ct_status ='입금' where ct_id=$ct_id;";
					sql_query($sql_chgst);
				}
				else if($rst_rw['it_id'] == '1527096045'){//pool1 구매
					$ct_id = $rst_rw['ct_id'];
					$id = $rst_rw['mb_id'];  
					$qty = $rst_rw['ct_qty'];
					$sql_poolist_up = "update g5_member set it_pool1=it_pool1+$qty where mb_id='$id';";
					sql_query($sql_poolist_up);

					$sql_poolist_up2 = "update g5_member set mb_level=2 where mb_level<2 and mb_id='$id';";
					sql_query($sql_poolist_up2);

					$sql_chgst = "update g5_shop_cart set ct_status ='입금' where ct_id=$ct_id;";
					sql_query($sql_chgst);

				}
				else if($rst_rw['it_id'] == '1527096041'){//pool2구매
					$ct_id = $rst_rw['ct_id'];
					$id = $rst_rw['mb_id'];  
					$qty = $rst_rw['ct_qty'];
					$sql_poolist_up = "update g5_member set it_pool2=it_pool2+$qty where mb_id='$id';";
					sql_query($sql_poolist_up);
					$sql_chgst = "update g5_shop_cart set ct_status ='입금' where ct_id=$ct_id;";
					sql_query($sql_chgst);

				}
				else if($rst_rw['it_id'] == '1527096037'){//pool3 구매 
					$ct_id = $rst_rw['ct_id'];
					$id = $rst_rw['mb_id'];  
					$qty = $rst_rw['ct_qty'];
					$sql_poolist_up = "update g5_member set it_pool3=it_pool3+$qty where mb_id='$id';";
					sql_query($sql_poolist_up);
					$sql_chgst = "update g5_shop_cart set ct_status ='입금' where ct_id=$ct_id;";
					sql_query($sql_chgst);

				}
				else if($rst_rw['it_id'] == '1527096030'){//pool4 구매
					$ct_id = $rst_rw['ct_id'];
					$id = $rst_rw['mb_id'];  
					$qty = $rst_rw['ct_qty'];
					$sql_poolist_up = "update g5_member set it_pool4=it_pool4+$qty where mb_id='$id';";
					sql_query($sql_poolist_up);
					$sql_chgst = "update g5_shop_cart set ct_status ='입금' where ct_id=$ct_id;";
					sql_query($sql_chgst);

				}
				else if($rst_rw['it_id'] == '1515148167'){//GPU
					$ct_id = $rst_rw['ct_id'];
					$id = $rst_rw['mb_id'];  
					$qty = $rst_rw['ct_qty'];
					$sql_poolist_up = "update g5_member set it_GPU=it_GPU+$qty where mb_id='$id';";
					sql_query($sql_poolist_up);
					$sql_chgst = "update g5_shop_cart set ct_status ='입금' where ct_id=$ct_id;";
					sql_query($sql_chgst);

				}
				else if($rst_rw['it_id'] == '1526013457'){//POOL5  1526013457
					$ct_id = $rst_rw['ct_id'];
					$id = $rst_rw['mb_id'];  
					$qty = $rst_rw['ct_qty'];
					$sql_poolist_up = "update g5_member set it_pool5=it_pool5+$qty where mb_id='$id';";
					sql_query($sql_poolist_up);
					$sql_chgst = "update g5_shop_cart set ct_status ='입금' where ct_id=$ct_id;";
					sql_query($sql_chgst);
				}
				else if($rst_rw['it_id'] == 'VVIP112500'){//VVip1  VVIP112500
					$ct_id = $rst_rw['ct_id'];
					$id = $rst_rw['mb_id'];  
					$qty = $rst_rw['ct_qty'];
					$sql_poolist_up = "update g5_member set VVIP112500=VVIP112500+$qty where mb_id='$id';";
					sql_query($sql_poolist_up);
					$sql_chgst = "update g5_shop_cart set ct_status ='입금' where ct_id=$ct_id;";
					sql_query($sql_chgst);
				}
				else if($rst_rw['it_id'] == 'VVIP225000'){//VVip2  VVIP225000
					$ct_id = $rst_rw['ct_id'];
					$id = $rst_rw['mb_id'];  
					$qty = $rst_rw['ct_qty'];
					$sql_poolist_up = "update g5_member set VVIP225000=VVIP225000+$qty where mb_id='$id';";
					sql_query($sql_poolist_up);
					$sql_chgst = "update g5_shop_cart set ct_status ='입금' where ct_id=$ct_id;";
					sql_query($sql_chgst);
				}
				else if($rst_rw['it_id'] == 'VVIP337500'){//VVip3  VVIP337500
					$ct_id = $rst_rw['ct_id'];
					$id = $rst_rw['mb_id'];  
					$qty = $rst_rw['ct_qty'];
					$sql_poolist_up = "update g5_member set VVIP337500=VVIP337500+$qty where mb_id='$id';";
					sql_query($sql_poolist_up);
					$sql_chgst = "update g5_shop_cart set ct_status ='입금' where ct_id=$ct_id;";
					sql_query($sql_chgst);
				}
				else if($rst_rw['it_id'] == 'VVIP450000'){//VVip4  VVIP450000
					$ct_id = $rst_rw['ct_id'];
					$id = $rst_rw['mb_id'];  
					$qty = $rst_rw['ct_qty'];
					$sql_poolist_up = "update g5_member set VVIP450000=VVIP450000+$qty where mb_id='$id';";
					sql_query($sql_poolist_up);
					$sql_chgst = "update g5_shop_cart set ct_status ='입금' where ct_id=$ct_id;";
					sql_query($sql_chgst);
				}
			}
		}
		alert("complete");
	}
	else{
		alert("balance is not enight");
	}
}

if($_POST['ct_status']=='강제입금' && $now_stat['od_status']=='주문'){
 
	$sql = " select mb_id, ct_price, ct_qty, it_id, it_name, ct_send_cost, it_sc_type from {$g5['g5_shop_cart_table']} where od_id = '$od_id' and  group by it_id order by ct_id ";
	$result = sql_query($sql);
	for($i=0; $row=sql_fetch_array($result); $i++){
		$total_price = $total_price + $row['ct_price']* $row['ct_qty'];
			$mb_id = $row['mb_id'];
		if($row['ct_price']!=99)
			$total_pv = $total_pv + $row['ct_price']* $row['ct_qty'];
	}
	$now_date = date("Y-m-d H:i:s",time()); 

	if(true){//> $price_coin-0.01){

		if($now_stat['od_settle_case']=='btcPayment'){//btc 출금
			$ch = curl_init();
			$sel_id = "select my_walletId from g5_member where mb_id='$mb_id';";
			$rst = sql_query($sel_id);
			$w_rst = sql_fetch_array($rst);
			$wid = $w_rst['my_walletId'];
			curl_setopt($ch,CURLOPT_URL, "http://localhost:3000/merchant/$wid/balance?password=0803bjuung");
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
			//접속할 URL 주소
			$result = curl_exec ($ch);
			curl_close ($ch);
			$obj = json_decode($result);
			$balance =  $obj->{'balance'}; 	

			$sql_price = "select payment_btc from g5_shop_order where od_id='$od_id';";
			$result = sql_query($sql_price);
			$ret = sql_fetch_array($result);
			$price_coin =  $ret['payment_btc'];
			$price_coin = round($price_coin,8);
			$send_coin = ($balance-10000);
			$ch = curl_init();
			$sel_id = "select my_walletId from g5_member where mb_id='$mb_id';";
			$rst = sql_query($sel_id);
			$w_rst = sql_fetch_array($rst);
			$wid = $w_rst['my_walletId'];
			$get_wad = "select mb_wallet from g5_member where mb_id = '$mb_id'";
			$wadrst = sql_query($get_wad);
			$ret = sql_fetch_array($wadrst);		
			$from_addr = $ret['mb_wallet'];
			$to_addr = PINNACEL_WALLET;
			
			curl_setopt($ch,CURLOPT_URL, "http://localhost:3000/merchant/{$wid}/payment?password=0803bjuung&second_password=&to=$to_addr&amount=$send_coin&from=$from_addr");
			//	echo "http://localhost:3000/merchant/{$wid}/payment?password=0803bjuung&second_password=&to=$to_addr&amount=$send_coin&from=$from_addr";
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
			$result = curl_exec ($ch);		
			curl_close ($ch);
			$obj = json_decode($result);
			$message =  "message : ".$obj->{'message'}; 
		}
		else {//eth 출금
			$ch_balance = curl_init(); //잔액 조회
			curl_setopt($ch_balance,CURLOPT_URL, "http://202.239.44.110:8888/?from=$from_addr");
			curl_setopt ($ch_balance, CURLOPT_RETURNTRANSFER, 1); 
			$balance = curl_exec ($ch_balance);
			curl_close ($ch_balance);

			$now_balance = $balance/1000000000000000000; //WEI에서 이더로 변환
			$transfer_fee = 0.000441;
			$transfer_eth = $now_balance -  $transfer_fee;
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL, "http://202.239.44.110:7777/?private_key=$private_key-$to_addr-$transfer_eth-$from_addr");
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
			//접속할 URL 주소
			$result = curl_exec ($ch);
			curl_close ($ch);			
		}

		if(true){
			$get_brcom = "select mb_brecommend from g5_member where mb_id='".$mb_id."'";
			$ret = sql_query($get_brcom);
			$row = sql_fetch_array($ret);
			if($row['mb_brecommend']!=null && $row['mb_brecommend']==""){
				 $sql_purchase = "update g5_shop_order set od_receipt_time='".$now_date."', od_mining_stime='$now_date', od_status='입금', pv = truncate(od_cart_price,-2)/2  where od_id = '$od_id'; ";
				sql_query($sql_purchase);	
				calc_sale($mb_id, date('Y-m-d'));
			}
			else{
				$sql_purchase = "update g5_shop_order set od_status='입금', od_mining_stime='$now_date', od_settle_case='매장카드',  pv = truncate(od_cart_price,-2)/2  where od_id = '$od_id'; ";
				sql_query($sql_purchase);	
			}			
			
			$sql_cart = "select ct_id, mb_id, ct_price, ct_qty, it_id, it_name, ct_send_cost, it_sc_type from {$g5['g5_shop_cart_table']} where od_id	= '$od_id' group by it_id order by ct_id ";
			$rst = sql_query($sql_cart);
			//echo $sql_cart;
			for($i=0; $rst_rw=sql_fetch_array($rst); $i++){

				if($rst_rw['it_id'] == '1527096053'){//멤버쉽 구매
					$ct_id = $rst_rw['ct_id'];
					$id = $rst_rw['mb_id'];  
					$qty = $rst_rw['ct_qty'];
					$sql_poolist_up = "update g5_member set membership_yn='Y', mb_level=1 where mb_id='$id';";
					sql_query($sql_poolist_up);
					$sql_chgst = "update g5_shop_cart set ct_status ='입금' where ct_id=$ct_id;";
					sql_query($sql_chgst);
				}
				else if($rst_rw['it_id'] == '1527096045'){//pool1 구매
					$ct_id = $rst_rw['ct_id'];
					$id = $rst_rw['mb_id'];  
					$qty = $rst_rw['ct_qty'];
					$sql_poolist_up = "update g5_member set it_pool1=it_pool1+$qty where mb_id='$id';";
					sql_query($sql_poolist_up);

					$sql_poolist_up2 = "update g5_member set mb_level=2 where mb_level<2 and mb_id='$id';";
					sql_query($sql_poolist_up2);

					$sql_chgst = "update g5_shop_cart set ct_status ='입금' where ct_id=$ct_id;";
					sql_query($sql_chgst);

				}
				else if($rst_rw['it_id'] == '1527096041'){//pool2구매
					$id = $rst_rw['mb_id'];  
					$qty = $rst_rw['ct_qty'];
					$sql_poolist_up = "update g5_member set it_pool2=it_pool2+$qty where mb_id='$id';";
					sql_query($sql_poolist_up);
					$sql_chgst = "update g5_shop_cart set ct_status ='입금' where ct_id=$ct_id;";
					sql_query($sql_chgst);

				}
				else if($rst_rw['it_id'] == '1527096037'){//pool3 구매 
					$ct_id = $rst_rw['ct_id'];
					$id = $rst_rw['mb_id'];  
					$qty = $rst_rw['ct_qty'];
					$sql_poolist_up = "update g5_member set it_pool3=it_pool3+$qty where mb_id='$id';";
					sql_query($sql_poolist_up);
					$sql_chgst = "update g5_shop_cart set ct_status ='입금' where ct_id=$ct_id;";
					sql_query($sql_chgst);

				}
				else if($rst_rw['it_id'] == '1527096030'){//pool4 구매
					$ct_id = $rst_rw['ct_id'];
					$id = $rst_rw['mb_id'];  
					$qty = $rst_rw['ct_qty'];
					$sql_poolist_up = "update g5_member set it_pool4=it_pool4+$qty where mb_id='$id';";
					sql_query($sql_poolist_up);
					$sql_chgst = "update g5_shop_cart set ct_status ='입금' where ct_id=$ct_id;";
					sql_query($sql_chgst);

				}
				else if($rst_rw['it_id'] == '1515148167'){//GPU
					$ct_id = $rst_rw['ct_id'];
					$id = $rst_rw['mb_id'];  
					$qty = $rst_rw['ct_qty'];
					$sql_poolist_up = "update g5_member set it_GPU=it_GPU+$qty where mb_id='$id';";
					sql_query($sql_poolist_up);
					$sql_chgst = "update g5_shop_cart set ct_status ='입금' where ct_id=$ct_id;";
					sql_query($sql_chgst);

				}
				else if($rst_rw['it_id'] == '1526013457'){//POOL5  1526013457
						$ct_id = $rst_rw['ct_id'];
						$id = $rst_rw['mb_id'];  
						$qty = $rst_rw['ct_qty'];
						$sql_poolist_up = "update g5_member set it_pool5=it_pool5+$qty where mb_id='$id';";
						sql_query($sql_poolist_up);
						$sql_chgst = "update g5_shop_cart set ct_status ='입금' where ct_id=$ct_id;";
						sql_query($sql_chgst);
					}
				else if($rst_rw['it_id'] == 'VVIP112500'){//VVip1  VVIP112500
					$ct_id = $rst_rw['ct_id'];
					$id = $rst_rw['mb_id'];  
					$qty = $rst_rw['ct_qty'];
					$sql_poolist_up = "update g5_member set VVIP112500=VVIP112500+$qty where mb_id='$id';";
					sql_query($sql_poolist_up);
					$sql_chgst = "update g5_shop_cart set ct_status ='입금' where ct_id=$ct_id;";
					sql_query($sql_chgst);
				}
				else if($rst_rw['it_id'] == 'VVIP225000'){//VVip2  VVIP225000
					$ct_id = $rst_rw['ct_id'];
					$id = $rst_rw['mb_id'];  
					$qty = $rst_rw['ct_qty'];
					$sql_poolist_up = "update g5_member set VVIP225000=VVIP225000+$qty where mb_id='$id';";
					sql_query($sql_poolist_up);
					$sql_chgst = "update g5_shop_cart set ct_status ='입금' where ct_id=$ct_id;";
					sql_query($sql_chgst);
				}
				else if($rst_rw['it_id'] == 'VVIP337500'){//VVip3  VVIP337500
					$ct_id = $rst_rw['ct_id'];
					$id = $rst_rw['mb_id'];  
					$qty = $rst_rw['ct_qty'];
					$sql_poolist_up = "update g5_member set VVIP337500=VVIP337500+$qty where mb_id='$id';";
					sql_query($sql_poolist_up);
					$sql_chgst = "update g5_shop_cart set ct_status ='입금' where ct_id=$ct_id;";
					sql_query($sql_chgst);
				}
				else if($rst_rw['it_id'] == 'VVIP450000'){//VVip4  VVIP450000
					$ct_id = $rst_rw['ct_id'];
					$id = $rst_rw['mb_id'];  
					$qty = $rst_rw['ct_qty'];
					$sql_poolist_up = "update g5_member set VVIP450000=VVIP450000+$qty where mb_id='$id';";
					sql_query($sql_poolist_up);
					$sql_chgst = "update g5_shop_cart set ct_status ='입금' where ct_id=$ct_id;";
					sql_query($sql_chgst);
				}
			}
		}

		alert("complete");
	}
}


for ($i=0; $i<$cnt; $i++)
{
    $k = $_POST['ct_chk'][$i];
    $ct_id = $_POST['ct_id'][$k];

    if(!$ct_id)
        continue;

    $sql = " select * from {$g5['g5_shop_cart_table']} where od_id = '$od_id' and ct_id  = '$ct_id' ";
    $ct = sql_fetch($sql);
    if(!$ct['ct_id'])
        continue;

    // 수량이 변경됐다면
    $ct_qty = $_POST['ct_qty'][$k];
    if($ct['ct_qty'] != $ct_qty) {
        $diff_qty = $ct['ct_qty'] - $ct_qty;

        // 재고에 차이 반영.
        if($ct['ct_stock_use']) {
            if($ct['io_id']) {
                $sql = " update {$g5['g5_shop_item_option_table']}
                            set io_stock_qty = io_stock_qty + '$diff_qty'
                            where it_id = '{$ct['it_id']}'
                              and io_id = '{$ct['io_id']}'
                              and io_type = '{$ct['io_type']}' ";
            } else {
                $sql = " update {$g5['g5_shop_item_table']}
                            set it_stock_qty = it_stock_qty + '$diff_qty'
                            where it_id = '{$ct['it_id']}' ";
            }

            sql_query($sql);
        }

        // 수량변경
        $sql = " update {$g5['g5_shop_cart_table']}
                    set ct_qty = '$ct_qty'
                    where ct_id = '$ct_id'
                      and od_id = '$od_id' ";
        sql_query($sql);




        $mod_history .= G5_TIME_YMDHIS.' '.$ct['ct_option'].' 수량변경 '.$ct['ct_qty'].' -> '.$ct_qty."\n";
    }

    // 재고를 이미 사용했다면 (재고에서 이미 뺐다면)
    $stock_use = $ct['ct_stock_use'];
    if ($ct['ct_stock_use'])
    {
        if ($ct_status == '주문' || $ct_status == '취소' || $ct_status == '반품' || $ct_status == '품절')
        {
            $stock_use = 0;
            // 재고에 다시 더한다.
            if($ct['io_id']) {
                $sql = " update {$g5['g5_shop_item_option_table']}
                            set io_stock_qty = io_stock_qty + '{$ct['ct_qty']}'
                            where it_id = '{$ct['it_id']}'
                              and io_id = '{$ct['io_id']}'
                              and io_type = '{$ct['io_type']}' ";
            } else {
                $sql = " update {$g5['g5_shop_item_table']}
                            set it_stock_qty = it_stock_qty + '{$ct['ct_qty']}'
                            where it_id = '{$ct['it_id']}' ";
            }

            sql_query($sql);
        }
    }
    else
    {
        // 재고 오류로 인한 수정
        if ($ct_status == '배송' || $ct_status == '완료')
        {
            $stock_use = 1;
            // 재고에서 뺀다.
            if($ct['io_id']) {
                $sql = " update {$g5['g5_shop_item_option_table']}
                            set io_stock_qty = io_stock_qty - '{$ct['ct_qty']}'
                            where it_id = '{$ct['it_id']}'
                              and io_id = '{$ct['io_id']}'
                              and io_type = '{$ct['io_type']}' ";
            } else {
                $sql = " update {$g5['g5_shop_item_table']}
                            set it_stock_qty = it_stock_qty - '{$ct['ct_qty']}'
                            where it_id = '{$ct['it_id']}' ";
            }

            sql_query($sql);
        }
        /* 주문 수정에서 "품절" 선택시 해당 상품 자동 품절 처리하기
        else if ($ct_status == '품절') {
            $stock_use = 1;
            // 재고에서 뺀다.
            $sql =" update {$g5['g5_shop_item_table']} set it_stock_qty = 0 where it_id = '{$ct['it_id']}' ";
            sql_query($sql);
        } */
    }

    $point_use = $ct['ct_point_use'];
    // 회원이면서 PV가 0보다 크면
    // 이미 PV를 부여했다면 뺀다.
    if ($mb_id && $ct['ct_point'] && $ct['ct_point_use'])
    {
        $point_use = 0;
        //insert_point($mb_id, (-1) * ($ct[ct_point] * $ct[ct_qty]), "주문번호 $od_id ($ct_id) 취소");
        delete_point($mb_id, "@delivery", $mb_id, "$od_id,$ct_id");
    }

    // 히스토리에 남김
    // 히스토리에 남길때는 작업|아이디|시간|IP|그리고 나머지 자료
    $now = G5_TIME_YMDHIS;
    $ct_history="\n$ct_status|{$member['mb_id']}|$now|$REMOTE_ADDR";

    $sql = " update {$g5['g5_shop_cart_table']}
                set ct_point_use  = '$point_use',
                    ct_stock_use  = '$stock_use',
                    ct_status     = '$ct_status',
                    ct_history    = CONCAT(ct_history,'$ct_history')
                where od_id = '$od_id'
                and ct_id  = '$ct_id' ";
    sql_query($sql);

    // it_id를 배열에 저장
    if($ct_status == '주문' || $ct_status == '취소' || $ct_status == '반품' || $ct_status == '품절' || $ct_status == '완료')
        $arr_it_id[] = $ct['it_id'];
}

// 상품 판매수량 반영
if(is_array($arr_it_id) && !empty($arr_it_id)) {
    $unq_it_id = array_unique($arr_it_id);

    foreach($unq_it_id as $it_id) {
        $sql2 = " select sum(ct_qty) as sum_qty from {$g5['g5_shop_cart_table']} where it_id = '$it_id' and ct_status = '완료' ";
        $row2 = sql_fetch($sql2);

        $sql3 = " update {$g5['g5_shop_item_table']} set it_sum_qty = '{$row2['sum_qty']}' where it_id = '$it_id' ";
        sql_query($sql3);
    }
}

// 장바구니 상품 모두 취소일 경우 주문상태 변경
$cancel_change = false;
if (in_array($_POST['ct_status'], $status_cancel)) {
    $sql = " select count(*) as od_count1,
                    SUM(IF(ct_status = '취소' OR ct_status = '반품' OR ct_status = '품절', 1, 0)) as od_count2
                from {$g5['g5_shop_cart_table']}
                where od_id = '$od_id' ";
    $row = sql_fetch($sql);

    if($row['od_count1'] == $row['od_count2']) {
        $cancel_change = true;

        $pg_res_cd = '';
        $pg_res_msg = '';
        $pg_cancel_log = '';

        // PG 신용카드 결제 취소일 때
        if($pg_cancel == 1) {
            $sql = " select * from {$g5['g5_shop_order_table']} where od_id = '$od_id' ";
            $od = sql_fetch($sql);

            if($od['od_tno'] && ($od['od_settle_case'] == '신용카드' || $od['od_settle_case'] == '간편결제' || $od['od_settle_case'] == 'KAKAOPAY')) {
                switch($od['od_pg']) {
                    case 'billgate':
                        $od_tno = $od['od_tno'];
                        include G5_SHOP_PATH."/billgate/php/config.php";
                        include G5_SHOP_PATH."/billgate/php/class/Message.php";
                        include G5_SHOP_PATH."/billgate/php/class/MessageTag.php";
                        include G5_SHOP_PATH."/billgate/php/class/ServiceCode.php";
                        include G5_SHOP_PATH."/billgate/php/class/Command.php";
                        include G5_SHOP_PATH."/billgate/php/class/ServiceBroker.php";

                        //parameter
                        $orderDate 	= $today_time;
                        $orderId = $od_id;
                        $transactionId = $od['od_tno'];;	//취소건의 거래번호
                        $amount = $amount;    //취소건의 금액

                        //---------------------------------------
                        //Create Instance
                        //---------------------------------------
                        $reqMsg = new Message(); 
                        $resMsg = new Message(); 
                        $tag = new MessageTag();
                        $svcCode = new ServiceCode(); 
                        $cmd = new Command(); 
                        $broker = new ServiceBroker($COMMAND, $CONFIG_FILE);

                        //---------------------------------------
                        //Header 
                        //---------------------------------------
                        $reqMsg->setVersion("0100"); 
                        $reqMsg->setMerchantId($serviceId); 
                        $reqMsg->setServiceCode($_POST['SERVICE_CODE']);
                        if ($_POST['SERVICE_CODE'] == "0900") {
                            $reqMsg->setCommand($cmd->CANCEL_SMS_REQUEST); 
                        } else {
                            $reqMsg->setCommand($cmd->CANCEL_REQUEST); //승인 취소 요청 Command
                        }
                        $reqMsg->setOrderId($orderId); 
                        $reqMsg->setOrderDate($orderDate);

                        //---------------------------------------
                        //Body 
                        //---------------------------------------
                        if($transactionId != NULL) 
                            $reqMsg->put($tag->TRANSACTION_ID, $transactionId);                              
                        if($amount != NULL) 
                            $reqMsg->put($tag->DEAL_AMOUNT, $amount);   

                        //---------------------------------------
                        //Request
                        //---------------------------------------
                        $broker->setReqMsg($reqMsg); 
                        $broker->invoke($_POST['SERVICE_CODE']); 
                        $resMsg = $broker->getResMsg();

                        break;
                    case 'lg':
                        include_once(G5_SHOP_PATH.'/settle_lg.inc.php');

                        $LGD_TID = $od['od_tno'];

                        $xpay = new XPay($configPath, $CST_PLATFORM);

                        // Mert Key 설정
                        $xpay->set_config_value('t'.$LGD_MID, $config['cf_lg_mert_key']);
                        $xpay->set_config_value($LGD_MID, $config['cf_lg_mert_key']);

                        $xpay->Init_TX($LGD_MID);

                        $xpay->Set('LGD_TXNAME', 'Cancel');
                        $xpay->Set('LGD_TID', $LGD_TID);

                        if ($xpay->TX()) {
                            $res_cd = $xpay->Response_Code();
                            if($res_cd != '0000' && $res_cd != 'AV11') {
                                $pg_res_cd = $res_cd;
                                $pg_res_msg = $xpay->Response_Msg();
                            }
                        } else {
                            $pg_res_cd = $xpay->Response_Code();
                            $pg_res_msg = $xpay->Response_Msg();
                        }
                        break;
                    case 'inicis':
                        include_once(G5_SHOP_PATH.'/settle_inicis.inc.php');
                        $cancel_msg = iconv_euckr('쇼핑몰 운영자 승인 취소');

                        /*********************
                         * 3. 취소 정보 설정 *
                         *********************/
                        $inipay->SetField("type",      "cancel");                        // 고정 (절대 수정 불가)
                        $inipay->SetField("mid",       $default['de_inicis_mid']);       // 상점아이디
                        /**************************************************************************************************
                         * admin 은 키패스워드 변수명입니다. 수정하시면 안됩니다. 1111의 부분만 수정해서 사용하시기 바랍니다.
                         * 키패스워드는 상점관리자 페이지(https://iniweb.inicis.com)의 비밀번호가 아닙니다. 주의해 주시기 바랍니다.
                         * 키패스워드는 숫자 4자리로만 구성됩니다. 이 값은 키파일 발급시 결정됩니다.
                         * 키패스워드 값을 확인하시려면 상점측에 발급된 키파일 안의 readme.txt 파일을 참조해 주십시오.
                         **************************************************************************************************/
                        $inipay->SetField("admin",     $default['de_inicis_admin_key']); //비대칭 사용키 키패스워드
                        $inipay->SetField("tid",       $od['od_tno']);                   // 취소할 거래의 거래아이디
                        $inipay->SetField("cancelmsg", $cancel_msg);                     // 취소사유

                        /****************
                         * 4. 취소 요청 *
                         ****************/
                        $inipay->startAction();

                        /****************************************************************
                         * 5. 취소 결과                                           	*
                         *                                                        	*
                         * 결과코드 : $inipay->getResult('ResultCode') ("00"이면 취소 성공)  	*
                         * 결과내용 : $inipay->getResult('ResultMsg') (취소결과에 대한 설명) 	*
                         * 취소날짜 : $inipay->getResult('CancelDate') (YYYYMMDD)          	*
                         * 취소시각 : $inipay->getResult('CancelTime') (HHMMSS)            	*
                         * 현금영수증 취소 승인번호 : $inipay->getResult('CSHR_CancelNum')    *
                         * (현금영수증 발급 취소시에만 리턴됨)                          *
                         ****************************************************************/

                        $res_cd  = $inipay->getResult('ResultCode');
                        $res_msg = $inipay->getResult('ResultMsg');

                        if($res_cd != '00') {
                            $pg_res_cd = $res_cd;
                            $pg_res_msg = iconv_utf8($res_msg);
                        }
                        break;
                    case 'KAKAOPAY':
                        include_once(G5_SHOP_PATH.'/settle_kakaopay.inc.php');
                        $_REQUEST['TID']               = $od['od_tno'];
                        $_REQUEST['Amt']               = $od['od_receipt_price'];
                        $_REQUEST['CancelMsg']         = '쇼핑몰 운영자 승인 취소';
                        $_REQUEST['PartialCancelCode'] = 0;
                        include G5_SHOP_PATH.'/kakaopay/kakaopay_cancel.php';
                        break;
                    default:
                        include_once(G5_SHOP_PATH.'/settle_kcp.inc.php');
                        require_once(G5_SHOP_PATH.'/kcp/pp_ax_hub_lib.php');

                        // locale ko_KR.euc-kr 로 설정
                        setlocale(LC_CTYPE, 'ko_KR.euc-kr');

                        $c_PayPlus = new C_PP_CLI_T;

                        $c_PayPlus->mf_clear();

                        $tno = $od['od_tno'];
                        $tran_cd = '00200000';
                        $g_conf_home_dir  = G5_SHOP_PATH.'/kcp';
                        $g_conf_key_dir   = '';
                        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
                        {
                            $g_conf_log_dir   = G5_SHOP_PATH.'/kcp/log';
                            $g_conf_key_dir   = G5_SHOP_PATH.'/kcp/bin/pub.key';
                        }
                        $g_conf_site_cd  = $default['de_kcp_mid'];

                        if (preg_match("/^T000/", $g_conf_site_cd) || $default['de_card_test']) {
                            $g_conf_gw_url  = "testpaygw.kcp.co.kr";
                        } else {
                            $g_conf_gw_url  = "paygw.kcp.co.kr";
                        }
                        $cancel_msg = iconv_euckr('쇼핑몰 운영자 승인 취소');
                        $cust_ip = $_SERVER['REMOTE_ADDR'];
                        $bSucc_mod_type = "STSC";

                        $c_PayPlus->mf_set_modx_data( "tno",      $tno                         );  // KCP 원거래 거래번호
                        $c_PayPlus->mf_set_modx_data( "mod_type", $bSucc_mod_type              );  // 원거래 변경 요청 종류
                        $c_PayPlus->mf_set_modx_data( "mod_ip",   $cust_ip                     );  // 변경 요청자 IP
                        $c_PayPlus->mf_set_modx_data( "mod_desc", $cancel_msg );  // 변경 사유

                        $c_PayPlus->mf_do_tx( $tno,  $g_conf_home_dir, $g_conf_site_cd,
                                              $g_conf_site_key,  $tran_cd,    "",
                                              $g_conf_gw_url,  $g_conf_gw_port,  "payplus_cli_slib",
                                              $ordr_idxx, $cust_ip, "3" ,
                                              0, 0, $g_conf_key_dir, $g_conf_log_dir);

                        $res_cd  = $c_PayPlus->m_res_cd;
                        $res_msg = $c_PayPlus->m_res_msg;

                        if($res_cd != '0000') {
                            $pg_res_cd = $res_cd;
                            $pg_res_msg = iconv_utf8($res_msg);
                        }

                        // locale 설정 초기화
                        setlocale(LC_CTYPE, '');
                        break;
                }

                // PG 취소요청 성공했으면
                if($pg_res_cd == '') {
                    $pg_cancel_log = ' PG 신용카드 승인취소 처리';
                    $sql = " update {$g5['g5_shop_order_table']}
                                set od_refund_price = '{$od['od_receipt_price']}'
                                where od_id = '$od_id' ";
                    sql_query($sql);
                }
            }
        }

        // 관리자 주문취소 로그
        $mod_history .= G5_TIME_YMDHIS.' '.$member['mb_id'].' 주문'.$_POST['ct_status'].' 처리'.$pg_cancel_log."\n";
    }
}

// 미수금 등의 정보
$info = get_order_info($od_id);

if(!$info)
    alert('주문자료가 존재하지 않습니다.');
//                od_misu         = '{$info['od_misu']}',
$sql = " update {$g5['g5_shop_order_table']}
            set od_cart_price   = '{$info['od_cart_price']}',
                od_cart_coupon  = '{$info['od_cart_coupon']}',
                od_coupon       = '{$info['od_coupon']}',
                od_send_coupon  = '{$info['od_send_coupon']}',
                od_cancel_price = '{$info['od_cancel_price']}',
                od_send_cost    = '{$info['od_send_cost']}',
                od_tax_mny      = '{$info['od_tax_mny']}',
                od_vat_mny      = '{$info['od_vat_mny']}',
                od_free_mny     = '{$info['od_free_mny']}' ";
if ($mod_history) { // 주문변경 히스토리 기록
    $sql .= " , od_mod_history = CONCAT(od_mod_history,'$mod_history') ";
} else {
	/*## 무조건 기록하기 ################################################*/
	$mod_history .= G5_TIME_YMDHIS.' '.$member['mb_id'].' 주문'.$_POST['ct_status'].' 처리'.$pg_cancel_log."\n";
    $sql .= " , od_mod_history = CONCAT(od_mod_history,'$mod_history') ";
	/*@@End. 무조건 기록하기 #####*/
}

if($cancel_change) {
    $sql .= " , od_status = '취소' "; // 주문상품 모두 취소, 반품, 품절이면 주문 취소
} else {
    if (in_array($_POST['ct_status'], $status_normal)) { // 정상인 주문상태만 기록
        $sql .= " , od_status = '{$_POST['ct_status']}' ";
    }
}

$sql .= " where od_id = '$od_id' ";
sql_query($sql);

$qstr = "sort1=$sort1&amp;sort2=$sort2&amp;sel_field=$sel_field&amp;search=$search&amp;page=$page";

$url = "./orderform.php?od_id=$od_id&amp;$qstr";

/*## 취소시PV BV 제거 ################################################*/
if (in_array($_POST['ct_status'], $status_cancel)) {
	sql_query(" update g5_shop_order set pv = '', bv = '' where od_id = '{$_POST[od_id]}' ");
	$sql_cart = "select ct_id, mb_id, ct_price, ct_qty, it_id, it_name, ct_send_cost, it_sc_type from {$g5['g5_shop_cart_table']} where od_id	= '{$_POST[od_id]}' order by ct_id ";
				$rst = sql_query($sql_cart);
				for($i=0; $rst_rw=sql_fetch_array($rst); $i++){

					if($rst_rw['it_id'] == '1527096053'){//멤버쉽 구매
						$ct_id = $rst_rw['ct_id'];
						$id = $rst_rw['mb_id'];  
						$qty = $rst_rw['ct_qty'];
						$sql_poolist_up = "update g5_member set membership_yn='N',mb_level=0 where ct_status='입금' and mb_id='$id';";
						sql_query($sql_poolist_up);
						$sql_chgst = "update g5_shop_cart set ct_status ='취소' where ct_id=$ct_id;";
						sql_query($sql_chgst);
					}
					else if($rst_rw['it_id'] == '1527096045'){//pool1 구매
						$ct_id = $rst_rw['ct_id'];
						$id = $rst_rw['mb_id'];  
						$qty = $rst_rw['ct_qty'];
						$sql_poolist_up = "update g5_member set it_pool1=it_pool1-$qty, membership_yn='N', mb_level=0 where ct_status='입금' and mb_id='$id';";
						sql_query($sql_poolist_up);
						$sql_chgst = "update g5_shop_cart set ct_status ='취소' where ct_id=$ct_id;";
						sql_query($sql_chgst);
					}
					else if($rst_rw['it_id'] == '1527096041'){//pool2구매
						$rst_rw['ct_id'];
						$id = $rst_rw['mb_id'];  
						$qty = $rst_rw['ct_qty'];
						$sql_poolist_up = "update g5_member set it_pool2=it_pool2-$qty where ct_status='입금' and mb_id='$id';";;
						sql_query($sql_poolist_up);
						$sql_chgst = "update g5_shop_cart set ct_status ='취소' where ct_id=$ct_id;";
						sql_query($sql_chgst);
					}
					else if($rst_rw['it_id'] == '1527096037'){//pool3 구매 
						$rst_rw['ct_id'];
						$id = $rst_rw['mb_id'];  
						$qty = $rst_rw['ct_qty'];
						$sql_poolist_up = "update g5_member set it_pool3=it_pool3-$qty where ct_status='입금' and mb_id='$id';";
						sql_query($sql_poolist_up);
						$sql_chgst = "update g5_shop_cart set ct_status ='취소' where ct_id=$ct_id;";
						sql_query($sql_chgst);
					}
					else if($rst_rw['it_id'] == '1527096030'){//pool4 구매
						$ct_id = $rst_rw['ct_id'];
						$id = $rst_rw['mb_id'];  
						$qty = $rst_rw['ct_qty'];
						$sql_poolist_up = "update g5_member set it_pool4=it_pool4-$qty where ct_status='입금' and mb_id='$id';";
						sql_query($sql_poolist_up);
						$sql_chgst = "update g5_shop_cart set ct_status ='취소' where ct_id=$ct_id;";
						sql_query($sql_chgst);
					}
					else if($rst_rw['it_id'] == '1515148167'){//GPU  1515148167
						$ct_id = $rst_rw['ct_id'];
						$id = $rst_rw['mb_id'];  
						$qty = $rst_rw['ct_qty'];
						$sql_poolist_up = "update g5_member set it_GPU=it_GPU-$qty where ct_status='입금' and mb_id='$id';";
						sql_query($sql_poolist_up);
						$sql_chgst = "update g5_shop_cart set ct_status ='취소' where ct_id=$ct_id;";
						sql_query($sql_chgst);
					}

					$id = $rst_rw['mb_id'];
					$memrst  = sql_query("select it_pool1, it_pool2, it_pool3, it_pool4, it_GPU where mb_id='$id'");
					$ret = sql_fetch_array($memrst);
					if($ret['it_pool1']==0) {
						$lv_up = "update g5_member set mb_level=0 where mb_id='$id';";
						sql_query($lv_up);
					}

				}

	
}
/*@@End.  #####*/



// 신용카드 취소 때 오류가 있으면 알림
if($pg_cancel == 1 && $pg_res_cd && $pg_res_msg) {
    alert('오류코드 : '.$pg_res_cd.' 오류내용 : '.$pg_res_msg, $url);
} else {
    // 1.06.06
    $od = sql_fetch(" select od_receipt_point from {$g5['g5_shop_order_table']} where od_id = '$od_id' ");
    if ($od['od_receipt_point'])
        alert("PV로 결제한 주문은,\\n\\n주문상태 변경으로 인해 PV의 가감이 발생하는 경우\\n\\n회원관리 > PV관리에서 수작업으로 PV를 맞추어 주셔야 합니다.", $url);
    else{
       //goto_url($url);
	 }
}



function calc_sale($mb_id, $to_date){

	$sql= " select sales_day g5_member where sales_day<>'".date('Y-m-d')."'";
	$salesday=sql_fetch($sql);
	if(($salesday['sales_day']) || ($update=='y') ){
		$sql= " UPDATE g5_member  SET mb_my_sales=0, noo_my_sales=0,  mon_my_sales=0 ,day_my_sales=0, mb_habu_sum=0, noo_habu_sum=0, mon_habu_sum=0, habu_day_sales=0, sales_day=''";
		sql_query($sql);
		echo $sql."<br>sales_day가 오늘날짜와 다르거나 강제업데이트 0으로 update...<br><br>";
	}
	$sql = "SELECT mb_id, SUM(pv) AS pv FROM g5_shop_order WHERE DATE_FORMAT(od_receipt_time,'%Y-%m-%d')='$to_date' and mb_id='".$mb_id."'";
	$result = sql_query($sql);
	echo $sql;
	if($to_date==''){
		$to_date=date('Y-m-d');
	}
	for ($i=0; $row=sql_fetch_array($result); $i++) {   
		$comp=$row['mb_id'];
		$today_sales=$row['pv'];	
		$history_cnt=0;
		while(  ($comp!='admin')  || ($comp!='coolrunning')  ){   
				$sql = " SELECT mb_id,mb_name,  mb_brecommend FROM g5_member WHERE mb_id= '".$comp."'";
				$recommend = sql_fetch($sql);
				$mb_id=$recommend['mb_id'];
				$mb_name=$recommend['mb_name'];
				$sql3='';
				echo $comp;
					//위로 연속 올라가는 부분 추천인으로 올라갈지 바이너리추천인으로 올라갈지 결정
					$recom=$recommend['mb_brecommend'];
					if(   ($mb_name=='본사')  || ($mb_id=='')  ) { 
						echo "admin , 본사 혹은 ''을 만나 정지됨"; break;
					}
					$sql3 = " update g5_member set sales_day='".$to_date."',";
					if($history_cnt==0)
					{ 
						$sql3 .= " mb_my_sales=mb_my_sales	+ ".$today_sales;
					}else{
						$sql3 .= " habu_day_sales=habu_day_sales+".$today_sales;
					}
					$sql3 .= " where mb_id='".$comp."'";
					sql_query($sql3);
					echo $history_cnt.'--'.$sql3.'<br>';
					$comp=$recom;
					$history_cnt++;	
			} // while
			$history_cnt=0;
			$today_sales=0;
	} //for
}
?>
