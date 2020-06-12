<?
	include_once('./_common.php');
	
	include_once(G5_THEME_PATH.'/_include/gnb.php'); 
    include_once(G5_THEME_PATH.'/_include/wallet.php'); 
	include_once(G5_THEME_PATH.'/_include/shop.php');
	

	//login_check($member['mb_id']);

    print_r($_POST);
    //echo "<br><br>";
    //print_r($member);
    

    if(!$_POST['b_it_id'] && !$_POST['q_it_id']){
        alert("error :: no_item");
        return;
    }
    

    if($_POST['b_it_id']){
        $order_B = get_shop_item($_POST['b_it_id']); // B팩
        //$order_B_date = $member['it_pool1_profit'];

        $B_sql =  ",it_pool1 =  '$order_B[it_name]' ,
                   it_pool1_profit =  '".G5_TIME_YMDHIS."',
                   b_autopack = '$_POST[b_it_auto]' ";
    }

    if($_POST['q_it_id']){
        $order_Q = get_shop_item($_POST['q_it_id']); // Q팩
        $Q_sql =  ",it_pool2 =  '$order_Q[it_name]' ,
                   it_pool2_profit =  '".G5_TIME_YMDHIS."',
                   q_autopack = '$_POST[q_it_auto]' ";
    }


    $order_price = $_POST['order_price']; // 주문 총금액
	

    /*멤버 btc차감 | 팩구매기록 */
    if($btc_account > $order_price){

        $sql = "UPDATE g5_member set 
                mb_btc_calc = mb_btc_calc - '$order_price'"; 

                if($B_sql){
                    $sql.=   $B_sql;  
                }
                if($Q_sql){
                    $sql.=   $Q_sql;  
                }
               
        $sql .= "where mb_id = '$member['mb_id']' ";
        //echo "<br>".$sql."<br><br>"; 

        $mem_result = sql_query($sql, false);
        //$mem_result = 1;

    }else{
        commonModal('Error',"잔고가 부족합니다.",80);
		return;
    }



    /*주문로그*/
    $cnt= $_POST['cnt'];
    $orderid = date("YmdHis",time()).$cnt;
    $mb_id = $member['mb_id'];
    $coin = $_POST['coin'];
    $now_date = date('Y-m-d H:i:s');
    $expire_date = date("Y-m-d", strtotime(date("Y-m-d")."+1 month"));

    
    if($B_sql){
        $cp_price = calc_price($order_B['it_price'], $btc_cost,'btc');
        $sql = "insert g5_shop_cart set
		od_id				= '".$orderid."'
		, mb_id             = '".$mb_id."'
		, it_id     = '{$order_B['it_id']}'
        , it_name     = '{$order_B['it_name']}'
        , it_sc_type    ='1'
        , it_sc_method    ='".$coin."'
        , ct_status         = '구매'
        , ct_price         = '".$order_price."'
        , ct_point         = '".$order_price."'
        , cp_price         = '".$cp_price."'
        , io_price         = '{$order_B['it_price']}'
        , ct_qty         = '".$cnt."'
        , ct_option         = '{$_POST['b_it_auto']}'
        , ct_time         = '".G5_TIME_YMDHIS."'
        , ct_ip         = '{$_SERVER['REMOTE_ADDR']}'
        , ct_select_time         = '".$expire_date."' ";
        
        $order_result = sql_query($sql, false);
        //echo $sql."<br><br>";
        //$order_result = 1;
    }

    if($Q_sql){
        $cp_price = calc_price($order_Q['it_price'], $btc_cost,'btc');
        $sql = "insert g5_shop_cart set
		od_id				= '".$orderid."'
		, mb_id             = '".$mb_id."'
		, it_id     = '{$order_Q['it_id']}'
        , it_name     = '{$order_Q['it_name']}'
        , it_sc_type    ='1'
        , it_sc_method    ='".$coin."'
        , ct_status         = '구매'
        , ct_price         = '".$order_price."'
        , ct_point         = '".$order_price."'
        , cp_price         = '".$cp_price."'
        , io_price         = '{$order_Q['it_price']}'
        , ct_qty         = '".$cnt."'
        , ct_option         = '{$_POST['q_it_auto']}'
        , ct_time         = '".G5_TIME_YMDHIS."'
        , ct_ip         = '{$_SERVER['REMOTE_ADDR']}'
        , ct_select_time         = '".$expire_date."' ";
            
        $order_result = sql_query($sql, false);
        //echo $sql."<br><br>";
        //$order_result = 1;
    }

	
?>

	<section class="v_center purchase_1wrap wrap">

			<ul class="p1_ul clear_fix">
				<li style="width:100%;">
					<p>Purchase details</p>
					<strong><?=$mb_id?></strong>
                </li>
                <!--
				<li>
					<p>시세 유효기간</p>
					<strong>13:39</strong>
                </li>
                -->
			</ul>
			<hr>	
			<table>
				<tbody>
					<tr>
						<th>PACKS</th>
						<th>PRICE</th>
                        <th>EXPIRE DATE</th>
                        <th>Auto Repurchase</th>
                    </tr>
                    <?if($Q_sql){?>
					<tr>
                        <td><?=$order_Q['it_name']?></td>
						<td>&#36; <?=$order_Q['it_price']?></td>
                        <td><?=$expire_date?></td>
                        <td><?=autoYn($_POST['q_it_auto'])?></td>
                    </tr>
                    <?}?>

                    <?if($B_sql){?>
					<tr>
						<td><?=$order_B['it_name']?></td>
						<td>&#36; <?=$order_B['it_price']?></td>
                        <td><?=$expire_date?></td>
                        <td><?=autoYn($_POST['b_it_auto'])?></td>
                    </tr>
                    <?}?>

                </tbody>
               
				<tfoot style="border-top:1px solid #ddd;">
					<td >TOTAL:</td>
					<td >&#36;<?=$_POST['total_cp_price']?></td>
					<td></td>
				</tfoot>
			</table>
			<hr>	

			<div class="invoice_div">
				<!--<p><span class="font_gray">인보이스 번호 : </span>201906142155220</p>
				<ul class="pay_coin_ul clear_fix">
					<li><img src="_images/bit_round.gif" alt="비트코인"> BTC로 지불</li>
					<li><img src="_images/eth_round.gif" alt="비트코인"> ETH로 지불</li>
					<li><img src="_images/rock_round.gif" alt="비트코인"> RWD로 지불</li>
				</ul>
				-->
				<p class="font_gray">PAYMENT AMOUNT</p>
				<p><?=$order_price?> <span class="upper"><?=$coin?></span></p>
			</div>


			<div class="btn_block_btm_wrap">
				<input type="button" value="OK" class="btn_basic_block" onclick="location.href='/';">
			</div>

		</section>


        <div class="gnb_dim"></div>

    </section>

	<script>
		$(function() {
			$(".top_title h3").html("<img src='<?=G5_THEME_URL?>/_images/top_purchase.png' alt='아이콘'> <span data-i18n='title.팩상품구매하기'> Purchase Packs </span>");
			

        });
        
        $( document ).ready(function(){
            //console.log('sendmail');
            var mem_result = "<?= $mem_result?>";
            var order_result = "<?= $order_result?>";

            if(mem_result && order_result){
                dimShow();
                successModal('Confirm Payment','<p>Your payment completed successfully</p>',150);
                return;
            }else{
                dimShow();
                commonModal('ERROR','<p>Plese Try Again</p>',150);
                history.back();
            }
        });
	</script>


<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>
