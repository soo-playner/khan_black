<?php
include_once('./_common.php');
include_once(G5_THEME_PATH.'/_include/gnb.php');
include_once(G5_THEME_PATH.'/_include/wallet.php');

$title = 'Purchase';

?>

<?
/*날짜선택 기본값 지정*/
// if (empty($fr_date)) {$fr_date = date("Y-m-d", strtotime(date("Y-m-d")."-3 month"));}
// if (empty($to_date)){$to_date =  date("Y-m-d", strtotime(date("Y-m-d")."+1 day"));}

//시세 업데이트 시간
$next_rate_time = next_exchange_rate_time();

/*날짜계산*/
$qstr = "stx=".$stx."&fr_date=".$fr_date."&amp;to_date=".$to_date;
$query_string = $qstr ? '?'.$qstr : '';

$sql_common ="FROM g5_shop_order";
$sql_search = " WHERE mb_id = '{$member['mb_id']}' ";
$sql_search .= " AND od_date between '{$fr_date}' and '{$to_date}' ";

// $reset_sql = "
// SELECT count(*) as cnt
// FROM g5_shop_upstair_reset_log
// WHERE mb_id = '{$member['mb_id']}'  AND od_date between '{$fr_date}' and '{$to_date}'";
// $reset_row = sql_fetch($reset_sql);
// $reset_count = $reset_row['cnt'];


$sql = " select count(*) as cnt
{$sql_common}
{$sql_search} ";

$row = sql_fetch($sql);

$total_count = $row['cnt'] + $reset_count;

$rows = 15; //한페이지 목록수
$total_page  = ceil($total_count / $rows);
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지
$from_record = ($page - 1) * $rows; // 시작 열

$sql = "SELECT mb_id, od_cart_price, od_receipt_time, od_cash, od_settle_case, upstair, od_status,od_date
{$sql_common}
{$sql_search} ";

// $sql .= "UNION SELECT mb_id,od_date, acc_num, current_deposit FROM g5_shop_upstair_reset_log;
// WHERE mb_id = '{$member['mb_id']}'  AND od_date between '{$fr_date}' and '{$to_date}'";

$sql .= "order by od_receipt_time desc limit {$from_record}, {$rows} ";
$result = sql_query($sql);

//print_R($sql );

?>

<link rel="stylesheet" href="<?=G5_THEME_CSS_URL?>/withdrawal.css">
	<section class='breadcrumb'>
		<ol>
			<li class="active title" data-i18n='upstair.투자'><?=$title?></li>
			<li class='home'><i class="ri-home-4-line"></i><a href="<?php echo G5_URL; ?>" data-i18n='upstair.홈'>Home</a></li>
			<li><a href="/page.php?id=<?=$title?>" data-i18n='upstair.투자'><?=$title?></a></li>
		</ol>
		<ol class='f_right black' id='timer'>
          <div class='counters '>
            <div class='counter tx'>
              <span class='exchange_tx'>Exchange Rate</span>
              <p class='time_left_tx'>Time LEFT</p>
            </div>

            <div class='counter'>
              <span id='hours' class='num'>12</span>
              <!-- <p>Hours</p> -->
              <p>H</p>
            </div>
            
            <div class='counter'>
              <span id='minutes' class='num'>00</span>
              <!-- <p>Minutes</p> -->
              <p>M</p>
            </div>
            
            <div class='counter'>
              <span id='seconds' class='num'>00</span>
              <!-- <p>Seconds</p> -->
              <p>S</p>
            </div>
          </div>
        <ol>
	</section>

	<main>
	<div class='container'>

		<div class="col-sm-12 col-12 content-box round primary">

			<div class='user-content'>
              <li><p class='userid grade_<?=$member['grade']?>'></p></li>
              <li><p class='userid user_level'> <?=$user?></p></li>
              <li>
                <h4><?=$member['mb_id']?></h4>
                <h6><?=$member['mb_name']?></h6>
              </li>
			 </div>
			 
			<div class="incard mt30">
				<dt data-i18n="">My Package</dt>
				<dd class='font_red'>P<?=$member['mb_level']?></dd>
			</div>

			<div class="incard mt30">
				<dt data-i18n="dashboard.총 실적">My Sales</dt>
				<dd>$ <?=number_format($member['mb_deposit_point'])?></dd>
			</div>

			<div class="innerBox round mt20">
				<dt class='col-2'><span> <?=BONUS_CURENCY?></span></dt>
				<dd class='col-6 '><?=Number_format($total_balance)?> <?=BONUS_CURENCY?></dd>
				<dd class='col-4 '>$ <?=Number_format($total_balance_usd,1)?></dd>
			</div>

			<div class="innerBox round mt20">
				<dt class='col-2'><span> ETH </span></dt>
				<dd class='col-6 '><?=Number_format($total_eth,5)?> ETH</dd>
				<dd class='col-4 '>$ <?=Number_format($total_eth_usd,5)?></dd>
			</div>

			<div class="innerBox round mt20">
				<dt class='col-2'><span> USDT</span></dt>
				<dd class='col-6 '><?=Number_format($total_usdt,2)?> USDT</dd>
				<dd class='col-4 '>$ <?=Number_format($total_usdt_usd,2)?></dd>
			</div>

		</div>

			<?
			if( $bonus_per >= 100 ){?>
				<div class="col-sm-12 col-12 content-box round  mt20">
					<button id="reset_btn" class="btn btn_wd btn_primary btnOut2">Upstair Reset</button>
				</div>
			<?}?>


		<hr class='hr_dash'>
		
		<div class="col-sm-12 col-12 content-box round mt20 pakage_list" >
			<div class="box-header row">
				<div class="col-9 text-left"><h3 class="title upper" style='margin:10px 0;' data-i18n="upstair.예치금 리스트">Package list</h3></div>
				<div class="col-3 text-right">
					<button type='button' class="caret round"><i class="ri-arrow-up-s-line"></i></button>
				</div>
			</div>

			<div class="box-body row">
				<div class='col-4 '><div class="content-box pack_btn round primary" data-val="300">P1 <i>$300</i></div></div>
				<div class='col-4 '><div class="content-box pack_btn round primary" data-val="900">P2 <i>$900</i></div></div>
				<div class='col-4 '><div class="content-box pack_btn round primary" data-val="1500">P3 <i>$1,500</i></div></div>

				<div class='col-4 '><div class="content-box pack_btn round primary" data-val="2100">P4 <i>$2,100</i></div></div>
				<div class='col-4 '><div class="content-box pack_btn round primary" data-val="3000">P5 <i>$3,000</i></div></div>
				<div class='col-4 '><div class="content-box pack_btn round primary" data-val="6000">P6 <i>$6,000</i></div></div>

				<div class='col-4 '><div class="content-box pack_btn round primary" data-val="9000">P7 <i>$9,000</i></div></div>
				<div class='col-4 '><div class="content-box pack_btn round primary" data-val="12000">P8 <i>$12,000</i></div></div>
				<div class='col-4 '><div class="content-box pack_btn round primary" data-val="30000">P9 <i>$30,000</i></div></div>
			</div>

		</div>



		<div class="col-sm-12 col-12 content-box round primary mt20 pakage_sale" >
            <h3 class="title upper " data-i18n="upstair.예치금 투자">SALES Package</h3>
			
			
			
			<div class='row select_box' id='haz'>
			<div class='col-12'><label class="sub_title" data-i18n="">SELECT Account HAZ</label></div>
				<div class='col-7'>
				<input type='radio' value='haz' class='radio_btn' name='currency'><input type="text" id="trade_money_haz" class="trade_money" placeholder="0" min=5 data-currency='haz' >
				<span class="currency-right">HAZ</span>
				</div>
				<div class='col-1 shift_usd'><div class='ex_dollor'><i class="ri-money-dollar-circle-fill"></i></div></div>
				<div class='col-4 '>
					<input type="text" class='shift_dollor read_only'>
				</div>
			</div>

			<label class="sub_title" data-i18n="">SELECT Account ETH</label>
			<div class='row select_box' id='eth'>
				<div class='col-7'>
				<input type='radio' value='eth' class='radio_btn' name='currency'><input type="text" id="trade_money_eth" class="trade_money" placeholder="0" min=5 data-currency='eth' >
					<span class="currency-right">ETH</span>
				</div>
				<div class='col-1 shift_usd'><div class='ex_dollor'><i class="ri-money-dollar-circle-fill"></i></div></div>
				<div class='col-4 '>
				<input type="text" class='shift_dollor read_only'>
				</div>
			</div>

			<label class="sub_title" data-i18n="">SELECT Account USDT</label>
			<div class='row select_box' id='usdt'>
				
				<div class='col-7'>
				<input type='radio' value='usdt' class='radio_btn' name='currency'><input type="text" id="trade_money_usdt" class="trade_money" placeholder="0" min=5 data-currency='usdt' >
				<span class="currency-right">USDT</span>
			</div>
				<div class='col-1 shift_usd'><div class='ex_dollor'><i class="ri-money-dollar-circle-fill"></i></div></div>
				<div class='col-4'>
				<input type="text" class='shift_dollor read_only'>
				</div>
			</div>

			<label class="sub_title" data-i18n="">Purchase</label>
			<div class='row'>
				<div class='col-3 current_currency'><span class='txt upper'>HAZ</span> <i class="ri-exchange-fill exchange"></i></div>
				<div class='col-9'><input type="text" id="trade_total" class="trade_money read_only" placeholder="0" min=5 readonly><span class='currency-right'>$</span></div>
			</div>

			<div class="submit" >
				<button id="exchange" class="btn btn_wd c_btn b_green btnOut2 upper" data-i18n="upstair.투자" > Purchase</button>
		
				<button id="go_wallet_btn" class="btn btn_wd c_btn b_blue mt20 " data-i18n="dashboard.내 지갑" > MY WALLET</button>
			</div>
		</div>

		<!-- <div class="col-sm-12 col-12 content-box round secondary mt20" > -->
		<div class="history_box content-box round secondary" >
			<h3 class="hist_tit" data-i18n="upstair.투자 내역" >Purchase History</h3>

			<?while( $row = sql_fetch_array($result) ){?>
			<div class="hist_con">

				<div class="hist_con_row1">
					<div class="row1_left">
						<span class="hist_name" data-i18n="upstair.투자">Upstairs</span><br>
						<span class="hist_date"><?=$row['od_receipt_time']?></span>
					</div>
					<div class="row1_right">
						<?if($row['od_status'] != '매출'){?>
							<div class="hist_value">
								<span style='color:red;font-weight:600'><strong> RESET  - <?=Number_format($row['pv'],0)?></strong> USDT</span>
							</div>
						<?}else{?>
							<div class="hist_value">
								<span class='usd_value'> $<?=$row['od_cart_price']?></span>
								<span class='coin_value'>( <?=$row['upstair']?> <?=strtoupper($row['od_settle_case'])?> )</span>
							</div>
						<?}?>
					</div>
				</div>
			</div>
			<?}?>

			<?php
			$pagelist = get_paging($config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?id=upstairs&$qstr");
			echo $pagelist;
			?>
		</div>


	</div>
	</main>
	
	<div class="gnb_dim"></div>
</section>

<script src="<?=G5_THEME_URL?>/_common/js/timer.js"></script>
<script>
$(function(){
	var debug = false;
	var debug = "<?=$is_debug?>";
    if(debug){console.log('[ Mode : debug ]');}

	var mb_id = "<?=$member['mb_id']?>";
	var mb_no = "<?=$member['mb_no']?>";
	
	var mb_upstair = "<?=$member['mb_deposit_point']?>";

	var haz_price = "<?=$haz_price?>";
	var usdt_price = "<?=$usdt_price?>";
	var eth_price = "<?=$eth_price?>";
	var usd_price = "<?=$usd_price?>";

	window.onload = function(){
		getTime("<?=$next_rate_time?>");
	}


	// 환산
    function shift_price(income =1, val = 1, outcome =1, decimal = 2){
      var calc = income * val / outcome;
      if(debug) console.log(income +'*'+val + '/'+ outcome + ' = '+ calc);
      return calc.toFixed(decimal);
	}

	$('#go_wallet_btn').click(function(e){
		var cel_price = conv_number( $('.shift_dollor').val() );
		console.log(cel_price);
		
		if(cel_price > 0){
			go_to_url('mywallet'+'&sel_price='+cel_price);
		}else{
			go_to_url('mywallet');
		}
	});

	// 금액 선택
	$('.trade_money').on('click',function(e){
		$('.select_box').removeClass('active');
		$(this).parent().find('.radio_btn').prop('checked', true); 
		$(this).parent().parent().addClass('active');

		var radioVal = $('input[name="currency"]:checked').val();
		var shift_val = $('#'+ radioVal +' .shift_dollor').val();
		$('#trade_total').val('+ ' + numberWithCommas(shift_val));
		$('.current_currency > .txt').text(radioVal);
	});


	// 금액 입력
	$('.trade_money').on('change',function(e){
		if( $(this).data('currency') == 'haz')
			var income = haz_price;
		else if($(this).data('currency') == 'usdt')
			var income = usdt_price;
		else if($(this).data('currency') == 'eth')
			var income = eth_price;

		if(debug) console.log(income);

		var input_val = $(this).val();
		var target = $(this).parent().parent().find('.shift_dollor');
		var shift_val = shift_price(income, input_val, usd_price, 2);

		$('#trade_total').val('+ ' + numberWithCommas(shift_val));

		target.val(shift_val)
	});


	// 패키지 리스트
	$('.pakage_list .caret').on('click',function(){
		$('.pakage_list').find('.box-body').toggle('linear');
			
			if($(this).children().hasClass('ri-arrow-up-s-line')){
				$(this).children().attr('class','ri-arrow-down-s-line');
			}else{	
				$(this).children().attr('class','ri-arrow-up-s-line');
			}
	});

	// 패키지 리스트 선택
	$('.pack_btn').on('click',function(){
		console.log($(this).data('val'));

		var input_val = $(this).data('val');

		var haz_val = shift_price(usd_price, input_val, haz_price, 2);
		var usdt_val = shift_price(usd_price, input_val, usdt_price, 2);
		var eth_val = shift_price(usd_price, input_val, eth_price, 5);

		console.log(haz_val+ '/' + usdt_val + '/' + eth_val);

		$('#trade_money_haz').val(haz_val);
		$('#trade_money_usdt').val(usdt_val);
		$('#trade_money_eth').val(eth_val);

		$('#trade_total').val('+ ' + numberWithCommas(input_val));
		$('.shift_dollor').val(numberWithCommas(input_val));

		$('.select_box').removeClass('active');
		$('.select_box').first().addClass('active');
		$('.select_box').first().find('.radio_btn').prop('checked', true); 
	});

	function numberWithCommas(x) {
    	return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}

	// 매출
	$('#exchange').on('click', function(){
		
		var nw_upstair = '<?=$nw_upstair?>'; // 점검코드
		var mb_level = "<?=$member['mb_level']?>";

		var radioVal = $('input[name="currency"]:checked').val(); // 선택된 코인
		var input_val = $('#'+radioVal +' .trade_money').val(); // 입력한 금액 coin
		var output_val = $('#'+radioVal +' .shift_dollor').val(); // 실제 계산될 금액 $
		
		if( radioVal == 'haz'){ var total = <?=$total_balance?>;}
		else if( radioVal == 'eth'){ var total = <?=$total_eth?>;}
		else if( radioVal == 'usdt'){ var total = <?=$total_usdt?>;}
		

		if(debug) console.log('input :: '+ input_val+ ' / outcom :: ' + output_val );
	
		// 부분시스템 점검
		if(nw_upstair == 'Y'){
			dialogModal('Not available right now','<strong>Not available right now.</strong>','warning');
			if(debug) console.log('error : 1');
			return false;
		}

		// 금액이 0 일때
		if( output_val <=0 ){
			dialogModal('Check input amount','<strong>Please check the input amount.</strong>','warning');
			if(debug) console.log('error : 2' );
			return false;
		}

		// 최초_최소금액
		if( output_val < 300 && mb_level == 0){ // 레벨 0 회원은 최소 300불이상 부터 입금 가능
			dialogModal('Check input amount','<strong> The minimum deposit amount is $300.</strong>','warning');
			if(debug) console.log('error : 3' );
			return false;
		}

		// 잔고 확인 
		if( input_val > total){  
			dialogModal('check your balance','<strong> Not enough balance ('+ radioVal +').</strong>','warning');
			if(debug) console.log('error : 4' );
			return false;
		}

		// if( mb_out >= 100 ){ // 리셋버튼 활성화 중 일때 업스테어 추가 금액 입력시
		// 	commonModal(' You achieved 100% Upstairs','<strong> Please reset button for Upstair.</strong>',80);
		// 	return false;
		// }

		$.ajax({
			type: "POST",
			url: "/util/upstairs_proc.php",
			cache: false,
			async: false,
			dataType: "json",
			data:  {
				"input_val" : input_val,
				"output_val" : output_val,
				"coin_val" : radioVal
			},
			success: function(data) {
				// commonModal('Congratulation! Complete Deposit EOS','<strong> Congratulation! Complete Deposit USDT.</strong>',80);
				dialogModal('Purchase','<strong>Congratulation! Complete Purchase </strong>','success');
				$('.closed').on('click', function(){
					location.reload();
				});

			},
			error:function(e){
				commonModal('Error!','<strong> Please check retry.</strong>',80);
			}
		});
	});



	// 리셋 확인 
	$('#reset_btn').on('click', function(){
		$.ajax({
			type: "POST",
			url: "/util/upstairs_reset.php",
			cache: false,
			async: false,
			dataType: "json",
			data:  {
				"mb_id" : mb_id,
				"amount" : mb_upstair,
				"upstair_acc" : upstair_acc
			},
			success: function(data) {
				commonModal('Complete Upstair','<strong> Complete Upstair reset. <br> Available upstairs now!</strong>',80);

				$('#closeModal').on('click', function(){
					location.reload();
				});

			},
			error:function(e){
			}
		});

	});
});

/*콤마제거숫자표시*/
function conv_number(val) {
	number = val.replace(',', '');
	return number;
}

</script>
</html>
