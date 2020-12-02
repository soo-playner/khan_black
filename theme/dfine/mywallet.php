<?
	include_once('./_common.php');
	include_once(G5_THEME_PATH.'/_include/gnb.php');
	include_once(G5_THEME_PATH.'/_include/wallet.php');

  if($_REQUEST['sel_price']){
    $sel_price = $_REQUEST['sel_price'];
  }

  $wallet_sql = " SELECT * FROM {$g5['wallet_config']} ";
  $walelt_config = sql_query($wallet_sql);
  $wc_arr = [];
  while( $wc = sql_fetch_array($walelt_config) ){
    array_push($wc_arr,$wc);
  }
 
  $fee = $wc_arr[0]['fee'];

  $min_limit = $wc_arr[0]['amt_minimum'];
  $max_limit = $wc_arr[0]['amt_maximum'];
  $day_limit = $wc_arr[0]['day_limit'];

	//매출액
	$mysales = $member['mb_deposit_point'];

  //시세 업데이트 시간
  $next_rate_time = next_exchange_rate_time();
  
  //보너스/예치금 퍼센트
  
	  $bonus_per = bonus_state($member['mb_id']);
    $title = 'Mywallet';

    // 입금 OR 출금
    if($_GET['view'] == 'withdraw'){

        $view = 'withdraw';
        $history_target = $g5['withdrawal'];

    }else{

        $view = 'deposit';
        $history_target = $g5['deposit'];
    }


    /*날짜계산*/
    $qstr = "stx=".$stx."&fr_date=".$fr_date."&amp;to_date=".$to_date;
    $query_string = $qstr ? '?'.$qstr : '';

    $fr_date = date("Y-m-d", strtotime(date("Y-m-d")."-1 day"));
    $to_date = date("Y-m-d", strtotime(date("Y-m-d")."+1 day"));

    $sql_search_deposit = " WHERE mb_id = '{$member['mb_id']}' ";
    $sql_search_deposit .= " AND create_dt between '{$fr_date}' and '{$to_date}' ";
    $rows = 15; //한페이지 목록수


        //입금내역
        $sql_common_deposit ="FROM {$g5['deposit']}";

        $sql_deposit = " select count(*) as cnt {$sql_common_deposit} {$sql_search_deposit} ";
        $row_deposit = sql_fetch($sql_deposit);

        $total_count_deposit = $row_deposit['cnt'];
        $total_page_deposit  = ceil($total_count_deposit / $rows);

        if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지
        $from_record_deposit = ($page - 1) * $rows; // 시작 열

        $sql_deposit = " select * {$sql_common_deposit} {$sql_search_deposit} order by create_dt desc limit {$from_record_deposit}, {$rows} ";
        $result_deposit = sql_query($sql_deposit);


        //출금내역
        $sql_common ="FROM {$g5['withdrawal']}";
				// $sql_common ="FROM wallet_withdrawal_request";

				$sql_search = " WHERE mb_id = '{$member['mb_id']}' ";
				$sql_search .= " AND create_dt between '{$fr_date}' and '{$to_date}' ";

        $sql = " select count(*) as cnt {$sql_common} {$sql_search} ";
        if($debug) echo "<code>".$sql."</code>";

        $row = sql_fetch($sql);
        $total_count = $row['cnt'];

        $total_page  = ceil($total_count / $rows);
        if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지
        $from_record = ($page - 1) * $rows; // 시작 열

        $sql = " select * {$sql_common} {$sql_search} order by create_dt desc limit {$from_record}, {$rows} ";
        $result_withdraw = sql_query($sql);

?>

<link rel="stylesheet" href="<?=G5_THEME_CSS_URL?>/withdrawal.css">

<script type="text/javascript" src="./js/qrcode.js"></script>

    <section class='breadcrumb'>
        <ol>
            <li class="active title" data-i18n="deposit.내 지갑"><?=$title?></li>
            <li class='home'><i class="ri-home-4-line"></i><a href="<?php echo G5_URL; ?>" data-i18n='deposit.홈'>Home</a></li>
            <li><a href="/page.php?id=<?=$title?>" data-i18n="deposit.내 지갑"><?=$title?></a></li>
        </ol>
        
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

            <div class="innerBox round mt20">
                <dt class='col-5'><span> BONUS </span></dt>
                <dd class='col-7 '><?=$total_balance_num?> <?=BONUS_CURENCY?></dd>
            </div>

            

            <div class="row mt20">
              <article class="col-sm-12"><button type='button' class='btn wd c_btn b_blue' onclick="switch_func_paging('deposit')" data-i18n='deposit.대문자 입금'> DEPOSIT</button></article>
              <!-- <article class="col-md-6 col-sm-6"><button type='button' class='btn wd c_btn b_red' onclick="switch_func_paging('withdraw')" data-i18n='withdraw.대문자 출금'> WITHDRAW</button></article> -->
            </div>
        </div>

        <hr class='hr_dash'>

        <!-- 입금 -->
        <section  id='deposit' class='loadable'>

            <!-- ETH -->
            <div class="col-sm-12 col-12 content-box round mt20" id="eth" style='border-top:2px solid dodgerblue'>
                <h3 class="wallet_title font_dodgerblue" data-i18n="deposit.eht 입금 주소">Deposit Ethereum Address</h3>

                <div class="wallet qrBox">
                    <div class="eth_qr_img qr_img" id="eth_qr_img"></div>
                    <input type="text" id="eth_wallet_addr" class="wallet_addr" value="" title='my address' disabled/>
                </div>

                <div class="btn_ly">
                    <?if($sel_price){?> 
                      <div class='col-sm-12 col-12'>
                        <div class='pre_price round'>
                          <span class='deposit_price'>Selected Deposit Price</span>
                          <h2 class='d_price'><?=Round(shift_price('usd',$sel_price,'eth'),5)?> ETH </h2>
                        </div>
                      </div>
                    <?}?>

                    <div class='col-sm-12 col-12 '>
                        <button class="btn wd line_btn" id="accountCopy" onclick="copyURL('#eth_wallet_addr')">
                            <span data-i18n="deposit.주소복사"> Copy Address </span><i class="ri-file-copy-2-line"></i>
                        </button>
                    </div>
                     
                    <div class="col-sm-12 col-12 withdraw mt20">
                        <input type="text" class='confirm_hash b_ghostwhite f_small' placeholder="" data-i18n='[placeholder]deposit.입금완료된 Hash를 입력해주세요'>
                    </div>

                    <div class='col-sm-12 col-12 '>
                        <button class="btn btn_wd c_btn b_dodgerblue font_white deposit_request" data-currency="eth" >
                            <span data-i18n="deposit.입금확인요청">입금확인요청</span>
                        </button>
                    </div>
                </div>
            </div>

            
            <!-- 입금 요청 내역 -->
            <div class="history_box content-box round secondary ">
                <h3 class="hist_tit" data-i18n="deposit.입금 내역">Deposit History</h3>

										<?while( $row = sql_fetch_array($result_deposit) ){?>
												<div class="hist_con">
												<div class="hist_con_row1">
                          <div class="row1_left"><span class="hist_name" data-i18n='deposit.입금'>Deposit</span></div>
                          <div class="row1_right">
                            <span class="hist_date" style='float:right;'><?=$row['create_dt']?></span>
                          </div>
												</div>

												<div class="hist_con_row2 row mt10">
                          <div class="col-4">
                          <span class="hist_th" data-i18n='deposit.입금액'>Deposit Amount</span>
                            <span class="hist_th" data-i18n='deposit.영수증'>Txn Hash</span>
                            <span class="hist_th" data-i18n='withdraw.상태'>Status</span>
                          </div>

                          <div class="col-8">
                            <? if($row['coin'] == 'eth'){ $decimal = 5; }else{ $decimal=2; }?>
                            <span class="hist_td"><h2 class='inline'><?=number_format($row['in_amt'],2)?></h2> <?=strtoupper($row['coin'])?></span>
                            <span class="hist_td"><?=$row['txhash']?></span>
                            <span class="hist_td"><?string_shift_code($row['status'])?></span>
                          </div>
												</div>
										</div>
										<?}?>

										<?php
										$pagelist = get_paging($config['cf_write_pages'], $page, $total_page_deposit, "{$_SERVER['SCRIPT_NAME']}?id=mywallet&$qstr&view=deposit");
										echo $pagelist;
										?>
            </div>

        </section>





















        <!-- 출금 -->
        <section id='withdraw' class='loadable'>


            <div class="col-sm-12 col-12 content-box round primary mt20" >
            <h3 class="title" data-i18n="withdraw.출금 지갑 주소">Withdrawal Address</h3>

                <div class="input_address">
                  <label class="sub_title" data-i18n="withdraw.출금주소">Account Address</label>
                  <input type="text" id="withdrawal-address" class="eos_account" placeholder="Enter the Account address (ETH)" data-i18n='[placeholder]withdraw.ETH 출금 주소를 입력해주세요'>
                </div>


                <label class="sub_title" data-i18n="withdraw.출금 금액">Withdrawal quantity </label> 
                
                <div class="input_shift_value">
                  <input type="text" id="sendCoin" class="send_coin" placeholder="Enter Withdraw quantity" data-i18n='[placeholder]withdraw.출금 금액을 입력해주세요'>
                  <label class='currency-right'>HAZ</label>

                  <div class='row'>
                    <div class='col-4'><span class='text-center' style='font-size:90%;vertical-align: middle'>Withdrawal ETH</span></div>
                    <div class='col-8'>
                      <input type="text" id="shiftCoin" class="send_coin" readonly ><label class='currency-right'>ETH</label>
                      <?if($fee != 0){?>
                        <div class='fee'>
                          <!-- <?if($fee != 0){echo "Charge : ".$fee."<span class='i'>%</span>";}?> - -->
                          Withdrawal Charge -  <span id='fee_val'></span>
                        </div>
                      <?}?>
                  </div>
                  </div>
                </div>

                <hr class="hr_w">

                <div class="otp-auth-code-container">
                  <div class="verifyContainerOTP">
                    <label class="sub_title" data-i18n="withdraw.출금 핀코드">Account Pin-code</label>
                    <input type="password" id="pin_auth_with" class="trans_input " name="pin auth code"
                    placeholder="Please enter 6-digits pin number" maxlength="6" data-i18n='[placeholder]withdraw.6 자리 핀코드를 입력해주세요'>

                    <div class='text_center'>
                        <button id="pin_open" class="btn wide yellow form-send-button" data-i18n="withdraw.인증">Authenticate</button>
                    </div>
                  </div>
                </div>

                <div class="send-button-container">
                    <button type="button" class="btn btn_wd b_red form-send-button"  id="Withdrawal_btn" data-toggle="modal" data-target="" data-i18n="withdraw.출금 신청" disabled>Withdrawal USDT</button>
                </div>

            </div>



        <!-- 출금내역 -->
        <!-- <div class="col-sm-12 col-12 content-box round secondary"> -->
				  <div class="history_box content-box round secondary ">
            <h3 class="hist_tit" data-i18n='withdraw.출금 내역'>Withdrawal History</h3>

            <?while( $row = sql_fetch_array($result_withdraw) ){?>
                <div class="hist_con">
                <div class="hist_con_row1">
                    <div class="row1_left">
                    <span class="hist_name" data-i18n='withdraw.출금'>Withdraw</span>
                    <span class="hist_date"><?=$row['create_dt']?></span>
                    </div>
                    <div class="row1_right">
                      <span class="hist_value"><strong><?=Number_format($row['amt'],2)?></strong> haz</span>
                      <span class="hist_value eth"><strong><?=Number_format($row['amt_total'],5)?></strong> Eth</span>
                    </div>
                </div>

                <div class="hist_con_row2">
                    <div class="row2_left">
                    <span class="hist_th" data-i18n='withdraw.출금주소'>Account</span>
                    <!-- <span class="hist_th" data-i18n='withdraw.메모'>Memo</span> -->
                    <span class="hist_th" data-i18n='withdraw.상태'>Status</span>
                    </div>
                    <div class="row2_right">
                    <span class="hist_td"><?=$row['addr']?></span>
                    <!-- <span class="hist_td addr"><?= $row['addrmemo'] ? $row['addrmemo'] : "-" ?></span> -->
                    <span class="hist_td"><?string_shift_code($row['status'])?></span>
                    </div>
                </div>
            </div>
            <?}?>

            <?php
            $pagelist = get_paging($config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?id=mywallet&$qstr&view=withdraw");
            echo $pagelist;
            ?>
          </div>
        </section>
    </div>
    </main>


	<div class="gnb_dim"></div>
</section>






<!-- 완료 모달 팝업 -->

<div class="modal fade" id="ethereumAddressModalCenter" tabindex="-1" role="dialog"
aria-labelledby="ethereumAddressModalCenterTitle" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="ethereumAddressModalLongTitle">USDT WALLET</h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="modal-body">
      <i class="fa fa-check-circle fa-lg"></i>
      <h4>Your wallet address has been saved.</h4>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    </div>
  </div>
</div>
</div>



<script src="<?=G5_THEME_URL?>/_common/js/timer.js"></script>
<script>
$(function(){
    $(".top_title h3").html("<a href='/'><img src='<?=G5_THEME_URL?>/img/title.png' alt='logo'></a>");

    var debug = "<?=$is_debug?>";
    if(debug){
      console.log('[ Mode : debug ]');
      $('#Withdrawal_btn').attr('disabled',false);
    }

    /* 입금 */
    /* var usdt_wallet_addr = '<?=USDT_ADDRESS?>';
    if(usdt_wallet_addr != ''){
        $('#usdt_wallet_addr').val(usdt_wallet_addr);
        generateQrCode("usdt_qr_img",usdt_wallet_addr, 150, 150);
    } */

    var eth_wallet_addr = '<?=ETH_ADDRESS?>';
    if(eth_wallet_addr != ''){
        $('#eth_wallet_addr').val(eth_wallet_addr);
        generateQrCode("eth_qr_img",eth_wallet_addr, 150, 150);
    }


    /* 출금*/
    var mb_block = Number("<?=$member['mb_block']?>"); // 차단
    var mb_id = '<?=$member['mb_id']?>';
    var auth_mail_code='';
    var nw_with = '<?=$nw_with?>';
    
    var min_limit = '<?=$min_limit?>';
    var max_limit = '<?=$max_limit?>';
    var day_limit = '<?=$day_limit?>';
    var mb_max_limit = <?=$total_balance?> * max_limit * 0.01;
    var fee = (<?=$fee?>*0.01);

    if(debug) console.log("ETH PRICE :: " + "<?=$eth_price?>" + ' / ' + fee);

    onlyNumber('sendCoin');
    onlyNumber('pin_auth_with');


    // 전환 
    $('#sendCoin').change(function(){
      var inpuValue = $(this).val();
      $('#shiftCoin').val(shift_price('<?=$haz_price?>' ,(inpuValue*(1-fee)),'<?=$eth_price?>',5));
      $('.fee').css('display','flex');
      $('#fee_val').text(shift_price('<?=$haz_price?>' ,(inpuValue*fee),'<?=$eth_price?>',5));
    });

    function shift_price(income =1, val = 1, outcome =1, decimal = 2){
      var calc = income * val / outcome;
      if(debug) console.log(income +'*'+val + '/'+ outcome + ' = '+ calc);
      return calc.toFixed(decimal);
    }



    $('#Withdrawal_btn').on('click', function () {
      var debug_code='';

      if(debug) {
        var debug_code = "?debug=1";
        console.log('withdraw click!' +  $('#shiftCoin').val());
        console.log(`minimum : ${min_limit} / ${$('#sendCoin').val()} \n`);
        console.log(`maximum : ${max_limit} / ${<?=$total_balance?>} * ${(max_limit*0.01)} = ${ mb_max_limit}\n`);
      }

      if(nw_with == 'Y'){
        dialogModal('Not available right now','<strong>Not available right now.</strong>','warning');
        return false;
      }

      // 금액 입력 없을때 
      if($('#sendCoin').val() == ''){
        dialogModal('check field quantity','<strong>please check field and retry.</strong>','warning');
        return false;
      }
      // 최소 금액 확인
      if($('#sendCoin').val() != 0  && $('#sendCoin').val() < min_limit){
        dialogModal('check input quantity','<strong> out of the mimimum amount 100 haz.</strong>','warning');
        return false;
      }
      //최대 금액 확인
      /* if($('#sendCoin').val() != 0 && $('#sendCoin').val() > max_limit){
        dialogModal('check input quantity','<strong> out of the maximum amount.</strong>','warning');
        return false;
      } */
      

      if (!mb_block) {
        $.ajax({
          type: "POST",
          url: "./util/withdrawal_proc.php"+debug_code,
          cache: false,
          async: false,
          dataType: "json",
          data: {
            mb_id: mb_id,
            func: 'withdraw',
            wallet_addr: $('#withdrawal-address').val(),
            amt_eth: $('#shiftCoin').val(),
            amt_haz: $('#sendCoin').val()

            // auth_code: $('#otp_auth_with').val(),
            // auth_mail_code: auth_mail_code,
          },
          success: function (data) {
            //console.log(data.result + "/" +data.sql);
            if (data.result == "OK") {
              dialogModal('Withdraw has been successfully withdrawn','<p>Please allow up to 72 hours for the transaction to complete.</p>','success');
              $('.closed').click(function(){
                location.href='/page.php?id=mywallet&view=withdraw';
              });
            }else{
              dialogModal('Withdraw Failed', "<p>"+data.sql+"</p>", 'warning');
            }
          }
        });

      } else {
        commonModal('<strong>Not available right now</strong>',
        '<i class="fa fa-exclamation-triangle red fa-lg" style="font-size:2em;"></i><h4>Not available right now</h4>',120);
      }
    });



    /*핀 입력*/
    $('#pin_open').on('click', function (e) {

      if($('#pin_auth_with').val()==""){
          dialogModal('Withdraw PIN authentication','<p>Empty!</p>','warning');
          return;
      }

      $.ajax({
        url: './util/pin_number_check_proc.php',
        type: 'POST',
        cache: false,
        async: false,
        data: {
          "mb_id" : mb_id,
          "pin": $('#pin_auth_with').val()
        },
        dataType: 'json',
        success: function(result) {
          if(result.response == "OK"){
            dialogModal('Withdraw PIN authentication','<p>Pin number match</p>','success');

            $('#Withdrawal_btn').attr('disabled',false);
            $('#pin_open').attr('disabled',true);
            $("#pin_auth_with").attr("readonly",true);
          }else{
            dialogModal('Withdraw PIN authentication','<p>Pin number mismatch. retry </p>','failed');
          }
        },
        error: function(e){
          //console.log(e);
        }
      });

    });


    /*핀번호 보이기*/
    $('#show_pwd').on('mouseup mouseleave', function() {
      $('#pin_auth_with').attr('type',"text");
    });


    function onlyNumber(id){
      document.getElementById(id).oninput = function(){
        // if empty
        if(!this.value) return;

        // if non numeric
        let isNum = this.value[this.value.length - 1].match(/[0-9]/g);
        if(!isNum) this.value = this.value.substring(0, this.value.length - 1);

      }
    }



		/*입금 확인 요청*/
    $('.deposit_request').on('click', function (e) {

      var coin = $(this).data('currency');
      var hash_target = $(this).parent().parent().find('.confirm_hash');

      if(hash_target.val()==""){
          dialogModal('Deposit Confirmation Request','<p>Transaction Hash is empty!</p>','warning');
          return;
      }

      if(debug) console.log('입금 : '+ coin +' || tx :' + hash_target.val());

      $.ajax({
        url: '/util/request_deposit.php',
        type: 'POST',
        cache: false,
        async: false,
        data: {
          "mb_id" : mb_id,
          "coin" : coin,
          "hash": hash_target.val()
        },
        dataType: 'json',
        success: function(result) {
          if(result.response == "OK"){
            dialogModal('Deposit Request', 'Deposit Request success', 'success');
            $('.closed').click(function(){
              location.reload();
            });
          }else{
            if(debug) dialogModal('Deposit Request',result.data,'failed'); 
            else dialogModal('Deposit Request','<p>ERROR<br>Please try later</p>','failed');
          }
        },
        error: function(e){
          if(debug) dialogModal('ajax ERROR','IO ERROR','failed'); 
        }
        
      });
    });
});




window.onload = function(){
  // move(<?=$bonus_per?>);
  switch_func("<?=$view?>");
  // getTime("<?=$next_rate_time?>");
}


function switch_func(n){
    $('.loadable').removeClass('active');
    $('#'+n).toggleClass('active');
}

function switch_func_paging(n){
    $('.loadable').removeClass('active');
    $('#'+n).toggleClass('active');
		//window.location.href=window.location.pathname+"?id=mywallet&'<?=$qstr?>'&page=1&view="+n;
}

function copyURL(addr){
    alert("지갑 주소가 복사 되었습니다");
		var temp = $("<input>");
		$("body").append(temp);
		temp.val($(addr).val()).select();
		document.execCommand("copy");
		temp.remove();
}

function generateQrCode(qrImg, text, width, height){
    return new QRCode(document.getElementById(qrImg), {
        text: text,
        width: width,
        height: height,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });
}
</script>
