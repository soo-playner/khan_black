<?php
$sub_menu = "200100";
include_once('./_common.php');
include_once('./adm.wallet.php');

auth_check($auth[$sub_menu], 'w');

if ($w == '')
{
	$required_mb_id = 'required';
	$required_mb_id_class = 'required alnum_';
	$required_mb_password = 'required';
	$sound_only = '<strong class="sound_only">필수</strong>';

	$mb['mb_mailling'] = 1;
	$mb['mb_open'] = 1;
	$mb['mb_level'] = $config['cf_register_level'];
	$html_title = '추가';

}else if ($w == 'u'){

	$mb = get_member($mb_id);
	if (!$mb['mb_id'])
		alert('존재하지 않는 회원자료입니다.');

	if ($is_admin != 'super' && $mb['mb_level'] >= $member['mb_level'])
		alert('자신보다 권한이 높거나 같은 회원은 수정할 수 없습니다.');

	$required_mb_id = 'readonly';
	$required_mb_password = '';
	$html_title = '수정';

	$mb['mb_name'] = get_text($mb['mb_name']);
	$mb['mb_nick'] = get_text($mb['mb_nick']);
	$mb['mb_email'] = get_text($mb['mb_email']);
	$mb['mb_homepage'] = get_text($mb['mb_homepage']);
	$mb['mb_birth'] = get_text($mb['mb_birth']);
	$mb['mb_tel'] = get_text($mb['mb_tel']);
	$mb['mb_hp'] = get_text($mb['mb_hp']);
	$mb['mb_addr1'] = get_text($mb['mb_addr1']);
	$mb['mb_addr2'] = get_text($mb['mb_addr2']);
	$mb['mb_addr3'] = get_text($mb['mb_addr3']);
	$mb['mb_signature'] = get_text($mb['mb_signature']);
	$mb['mb_recommend'] = get_text($mb['mb_recommend']);
	$mb['mb_brecommend'] = get_text($mb['mb_brecommend']);
	$mb['mb_profile'] = get_text($mb['mb_profile']);
	$mb['mb_1'] = get_text($mb['mb_1']);
	$mb['mb_2'] = get_text($mb['mb_2']);
	$mb['mb_3'] = get_text($mb['mb_3']);
	$mb['mb_4'] = get_text($mb['mb_4']);
	$mb['mb_5'] = get_text($mb['mb_5']);
	$mb['mb_6'] = get_text($mb['mb_6']);
	$mb['mb_7'] = get_text($mb['mb_7']);
	$mb['mb_8'] = get_text($mb['mb_8']);
	$mb['mb_9'] = get_text($mb['mb_9']);
	$mb['mb_10'] = get_text($mb['mb_10']);
	$mb['grade'] = get_text($mb['grade']);

	$mb['first_name'] = get_text($mb['first_name']);
	$mb['last_name'] = get_text($mb['last_name']);

}
else
	alert('제대로 된 값이 넘어오지 않았습니다.');

// 본인확인방법
switch($mb['mb_certify']) {
	case 'hp':
		$mb_certify_case = '휴대폰';
		$mb_certify_val = 'hp';
		break;
	case 'ipin':
		$mb_certify_case = '아이핀';
		$mb_certify_val = 'ipin';
		break;
	case 'admin':
		$mb_certify_case = '관리자 수정';
		$mb_certify_val = 'admin';
		break;
	default:
		$mb_certify_case = '';
		$mb_certify_val = 'admin';
		break;
}

// 본인확인
$mb_certify_yes  =  $mb['mb_certify'] ? 'checked="checked"' : '';
$mb_certify_no   = !$mb['mb_certify'] ? 'checked="checked"' : '';

// 성인인증
$mb_adult_yes       =  $mb['mb_adult']      ? 'checked="checked"' : '';
$mb_adult_no        = !$mb['mb_adult']      ? 'checked="checked"' : '';

//메일수신
$mb_mailling_yes    =  $mb['mb_mailling']   ? 'checked="checked"' : '';
$mb_mailling_no     = !$mb['mb_mailling']   ? 'checked="checked"' : '';

// SMS 수신
$mb_sms_yes         =  $mb['mb_sms']        ? 'checked="checked"' : '';
$mb_sms_no          = !$mb['mb_sms']        ? 'checked="checked"' : '';

// 정보 공개
$mb_open_yes        =  $mb['mb_open']       ? 'checked="checked"' : '';
$mb_open_no         = !$mb['mb_open']       ? 'checked="checked"' : '';

// 지급차단
if($mb['mb_block']){
	$mb_block_yes = 'checked="checked"';
}else{
	$mb_block_no = 'checked="checked"';
}

if (isset($mb['mb_certify'])) {
	// 날짜시간형이라면 drop 시킴
	if (preg_match("/-/", $mb['mb_certify'])) {
		sql_query(" ALTER TABLE `{$g5['member_table']}` DROP `mb_certify` ", false);
	}
} else {
	sql_query(" ALTER TABLE `{$g5['member_table']}` ADD `mb_certify` TINYINT(4) NOT NULL DEFAULT '0' AFTER `mb_hp` ", false);
}

if(isset($mb['mb_adult'])) {
	sql_query(" ALTER TABLE `{$g5['member_table']}` CHANGE `mb_adult` `mb_adult` TINYINT(4) NOT NULL DEFAULT '0' ", false);
} else {
	sql_query(" ALTER TABLE `{$g5['member_table']}` ADD `mb_adult` TINYINT NOT NULL DEFAULT '0' AFTER `mb_certify` ", false);
}

// 지번주소 필드추가
if(!isset($mb['mb_addr_jibeon'])) {
	sql_query(" ALTER TABLE {$g5['member_table']} ADD `mb_addr_jibeon` varchar(255) NOT NULL DEFAULT '' AFTER `mb_addr2` ", false);
}

// 건물명필드추가
if(!isset($mb['mb_addr3'])) {
	sql_query(" ALTER TABLE {$g5['member_table']} ADD `mb_addr3` varchar(255) NOT NULL DEFAULT '' AFTER `mb_addr2` ", false);
}

// 중복가입 확인필드 추가
if(!isset($mb['mb_dupinfo'])) {
	sql_query(" ALTER TABLE {$g5['member_table']} ADD `mb_dupinfo` varchar(255) NOT NULL DEFAULT '' AFTER `mb_adult` ", false);
}

// 이메일인증 체크 필드추가
if(!isset($mb['mb_email_certify2'])) {
	sql_query(" ALTER TABLE {$g5['member_table']} ADD `mb_email_certify2` varchar(255) NOT NULL DEFAULT '' AFTER `mb_email_certify` ", false);
}

if ($mb['mb_intercept_date']) $g5['title'] = "차단된 ";
else $g5['title'] .= "";
$g5['title'] .= '회원 '.$html_title;
include_once('./admin.head.php');

// add_javascript('js 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_javascript(G5_POSTCODE_JS, 0);    //다음 주소 js
?>
<script src="https://kit.fontawesome.com/21599d63fd.js" crossorigin="anonymous"></script>

<script>
	$(function() {
		$('#field_upstair').on('change', function() {
			//$('#field_upstair').val()
			var after = $(this).val();
			var before ="<?=$mb['mb_deposit_point']?>";
			var calc = after - before;
			console.log(after +'/'+ before);
			$('.be_to').val(calc);
		});

		$('#rwc_save').on('click', function() {
			var mb_id = "<?echo $mb['mb_id']?>";
			alert(mb_id);
			$.ajax({
				type: "POST",
				url: "adm_coin_save.php",
				cache: false,
				async: false,
				dataType: "json",
				data:  {
					kind : 'rwc',
					mb_id : mb_id,
					amt : $('#rwc_coin').val()

				},
				success: function(data) {
					alert(data.result);
					console.log(data.code);
				},
				error: function(e){
					console.log(e);
				}

			});
		});
		$('#lkc_save').on('click', function() {
			var mb_id = "<?echo $mb['mb_id']?>";
			alert(mb_id);
			$.ajax({
				type: "POST",
				url: "adm_coin_save.php",
				cache: false,
				async: false,
				dataType: "json",
				data:  {
					kind : 'lkc',
					mb_id : mb_id,
					amt : $('#lkc_coin').val()

				},
				success: function(data) {
					alert(data.result);
					console.log(data.code);
				},
				error: function(e){
					console.log(e);
				}

			});

		});




		$('#btc_pack_save').on('click', function() { //패키지 숫자 설정.
			var mb_id = "<?echo $mb['mb_id']?>";
			alert(mb_id);
			$.ajax({
				type: "POST",
				url: "adm_coin_save.php",
				cache: false,
				async: false,
				dataType: "json",
				data:  {
					kind : 'package',
					p1 : $('#it_pool1').val(),
					p2 : $('#it_pool2').val(),
					p3 : $('#it_pool3').val(),
					p4 : $('#it_pool4').val(),
					p5 : $('#it_pool5').val(),
					p6 : $('#it_pool6').val(),
					p7 : $('#it_pool7').val(),
					p8 : $('#it_pool8').val(),
					gpu : $('#it_GPU').val(),
					mb_id : mb_id,
				},
				success: function(data) {
					alert(data.result);
					console.log(data.code);
				},
				error: function(e){
					alert(e.message);
					console.log(e);
				}

			});
		});
		$('#wid_btc').on('click', function() { //패키지 숫자 설정.
			var mb_id = "<?echo $mb['mb_id']?>";
			alert($('#receive_bwallet').val());
			$.ajax({
				type: "POST",
				url: "adm.coin_withdrawal.php",
				cache: false,
				async: false,
				dataType: "json",
				data:  {
					kind : 'btc',
					wallet_addr : $('#receive_bwallet').val(),
					mb_id : mb_id,
				},
				success: function(data) {
					alert(data.result);
					console.log(data.code);
				},
				error: function(e){

					console.log(e);
				}
			});
		});
		$('#wid_eth').on('click', function() { //패키지 숫자 설정.
			var mb_id = "<?echo $mb['mb_id']?>";
			alert($('#receive_ewallet').val());
			$.ajax({
				type: "POST",
				url: "adm.coin_withdrawal.php",
				cache: false,
				async: false,
				dataType: "json",
				data:  {
					kind : 'eth',
					wallet_addr : $('#receive_ewallet').val(),
					mb_id : mb_id,
				},
				success: function(data) {
					alert(data.result);
					console.log(data.code);
				},
				error: function(e){

					console.log(e);
				}
			});
		});
	}); // ready close


	function copyAddress(param){
		//commonModal("Address copy",'Your Wallet address is copied!',100);

		console.log($(param).text());
		var $temp = $("<input>");
			$("body").append($temp);
		$temp.val($(param).text()).select();
			document.execCommand("copy");
		$temp.remove();

		alert('주소가 복사되었습니다.');
	}

</script>

<?
	$rank_sql = "select * from rank where mb_id = '{$mb['mb_id']}' and rank = '{$mb['mb_level']}' ";
	$rank_result = sql_fetch($rank_sql);
?>
<style>
	.ly_up{background:aliceblue;height:60px;_border-top:2px solid #333;_border-bottom:2px solid #333;}
	.ly_up .ups{background:linen;}
	.ly_up.padding-box{height:60px;}
	.account_box{padding:0px;height:60px;}
	.account_box th,.account_box td{border:0;height:100%;padding-left:10px;}

	.btc_color{background:#ff9b22 url(../../_images/bit_w.png) !important; opacity:0.9}
	.eth_color{background:#3edc89 url(../../_images/ether_w.png) !important; opacity:0.9}
	.haz_color{background:#a172c8 url(../../_images/rock_w.png) !important; opacity:0.9}
	.lok_color{background:#fd8def url(../../_images/rock_w.png) !important; opacity:0.9}
	.v7_color{background:#07b5e5 url(../../_images/v7_w.png) !important; opacity:0.9}

	.hidden{display:none;}
	.wide{min-width:200px;height:36px;padding-left:5px;}

	select{width:auto;min-width:150px;height:36px;}
	option{line-height:36px;}
</style>

<form name="fmember" id="fmember" action="./member_form_update.php" onsubmit="return fmember_submit(this);" method="post" enctype="multipart/form-data">
<input type="hidden" name="w" value="<?php echo $w ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">

<div class="tbl_frm01 tbl_wrap">
	<table>
	<caption><?php echo $g5['title']; ?></caption>
	<colgroup>
		<col class="grid_4">
		<col>
		<col class="grid_4">
		<col>
	</colgroup>
	<tbody>



	<tr>
		<th scope="row"><label for="mb_id">아이디<?php echo $sound_only ?></label></th>
		<td>
			<? if ($w == "u") { ?>
			<input type="hidden" name="mb_id" id="mb_id" value="<?=$mb['mb_id']?>" />
			<?=$mb['mb_id']?>

			<? } else { ?>
			<input type="text" name="mb_id" value="<?php echo $mb['mb_id'] ?>" id="mb_id" <?php echo $required_mb_id ?> class="frm_input <?php echo $required_mb_id_class ?>" size="15" minlength="3" maxlength="20">
			<?php if ($w=='u'){ ?><a href="./boardgroupmember_form.php?mb_id=<?php echo $mb['mb_id'] ?>">접근가능그룹보기</a><?php } ?>
			<? } ?>

		</td>
		<th scope="row"><label for="mb_password">비밀번호<?php echo $sound_only ?></label></th>
		<td><input type="password" name="mb_password" id="mb_password" <?php echo $required_mb_password ?> class="frm_input wide<?php echo $required_mb_password ?>" size="15" maxlength="20"></td>
  </tr>

	<tr>
		<th scope="row"><label for="first_name">지갑주소 </label></th>
		<td><input type="text" name="first_name" value=" <?php echo $mb['first_name'] ?>" id="first_name" maxlength="100" class="frm_input wide" size="30"></td>
		<!--<th scope="row"><label for="last_name">회원명 (Last Name)</label></th>
		<td><input type="text" name="last_name" value="<?php echo $mb['last_name'] ?>" id="last_name" maxlength="100" required class="required frm_input wide" size="30"></td>
			-->
	</tr>

  	<tr>
		<th scope="row"><label for="grade">회원 등급</label></th>
		<td ><?echo "<img src='/img/".$mb['grade'].".png' style='width:40px;height:40px;'>";?><?php echo get_grade_select('grade', 0, $member['grade'], $mb['grade']) ?></td>


		<th scope="row"><label for="mb_level">회원 레벨</label></th>
		<td ><?php echo get_member_level_select('mb_level', 0, $member['mb_level'], $mb['mb_level']) ?> <div><?=$rank_result['rank_day']?></div> </td>
	</tr>

	<style>
		.kyc_btn{
			background:ROYALBLUE;
			padding:10px 20px;
			color:white;
		}
		.kyc_btn i {vertical-align:sub}
	</style>
	<!--
	<tr>
		<th scope="row"><label for="grade">KYC 인증</label></th>
		<td style="height:37px;">

		<?
		/*
			$kyc_sql = "select * from g5_write_kyc where mb_id = '{$mb['mb_id']}' order by wr_datetime limit 0,1";
			$kyc_result = sql_fetch($kyc_sql);

			if($kyc_result['wr_2'] == 1){?>
				<img src="<?=G5_THEME_URL?>/_images/okay_icon.gif" alt="인증됨" style="width:15px;"> 인증됨
				<a href="/adm/board_kyc.php?wr_id=<?=$kyc_result['wr_id']?>" target="_blank" class="kyc_btn"><i class="fas fa-id-card-alt" style="font-size:24px;margin-top:5px;"></i> KYC 바로가기</a>
			<?}else{?>
				<img src='<?=G5_THEME_URL?>/_images/x_icon.gif' alt='인증안됨' style='width:15px;'> 인증안됨
			<?}
		*/
		?>

		</td>
	</tr>
	-->

	<tr>
		<th scope="row"><label for="mb_email">E-mail<strong class="sound_only">필수</strong></label></th>
		<td><input type="text" name="mb_email" value="<?php echo $mb['mb_email'] ?>" id="mb_email" maxlength="100" class="frm_input email wide" size="30">
		<!--
		<?if($member['mb_email_certify'] != ''){?>
				<img src="<?=G5_THEME_URL?>/_images/okay_icon.gif" alt="인증됨" style="width:15px;"> 인증됨
			<?}else{?>
				<img src="<?=G5_THEME_URL?>/_images/x_icon.gif" alt="인증안됨" style="width:15px;"> 인증안됨
		<?}?>
		-->
		</td>
		<th scope="row"><label for="mb_hp">휴대폰번호</label></th>
		<td>
			<input type="text" name="nation_number" value="<?php echo $mb['nation_number'] ?>" id="nation_number" class="frm_input" style="height:36px;text-align:center" size="5" maxlength="50">
			<input type="text" name="mb_hp" value="<?php echo $mb['mb_hp'] ?>" id="mb_hp" class="frm_input  wide" size="15" maxlength="20">
			<!--
			<?if($member['mb_certify'] == 1){?>
				<img src="<?=G5_THEME_URL?>/_images/okay_icon.gif" alt="인증됨" style="width:15px;"> 인증됨
			<?}else{?>
				<img src="<?=G5_THEME_URL?>/_images/x_icon.gif" alt="인증안됨" style="width:15px;"> 인증안됨
			<?}?>
			-->
		</td>
	</tr>
	<tr class="hidden">
		<th scope="row"><label for="mb_homepage">홈페이지</label></th>
		<td><input type="text" name="mb_homepage" value="<?php echo $mb['mb_homepage'] ?>" id="mb_homepage" class="frm_input" maxlength="255" size="15"></td>
		<th scope="row"><label for="mb_tel">전화번호</label></th>
		<td><input type="text" name="mb_tel" value="<?php echo $mb['mb_tel'] ?>" id="mb_tel" class="frm_input" size="15" maxlength="20"></td>
	</tr>

	<?php if ($config['cf_use_recommend']) { // 추천인 사용 ?>
	<tr>
		<th scope="row">추천인</th>
		<td colspan="1">
		<style type="text/css">
		a.btn,
		span.btn {display:inline-block;*display:inline;*zoom:1;padding:0 10px;height:24px;line-height:24px;background-color:rgba(76,100,127,1);vertical-align:middle;color:#fff;cursor:pointer;}
		.btn.flexible{height:38px;line-height:38px;width:60px;text-align:center}

		.wallet_addr{display:inline-block;}
		.badge{padding:10px 10px;font-weight:700;}
		.copybutton{margin-left:10px; background:rgba(76,100,127,1);color:white;padding:5px 20px;border:0;box-shadow:0;border-radius:20px;}
		</style>

			<?//php echo ($mb['mb_recommend'] ? get_text($mb['mb_recommend']) : '없음'); // 081022 : CSRF 보안 결함으로 인한 코드 수정 ?>
			<input type="text" name="mb_recommend" id="mb_recommend" value="<?=$mb['mb_recommend']?>" class="frm_input wide" /><span id="ajax_rcm_search" class="btn flexible">검색</span>
			</td>
		<!--
		<th scope="row">후원인</th>
		<td colspan="1">

			<?//php echo ($mb['mb_brecommend'] ? get_text($mb['mb_brecommend']) : '없음'); // 081022 : CSRF 보안 결함으로 인한 코드 수정 ?>
			<input type="text" name="mb_brecommend" id="mb_brecommend" value="<?=$mb['mb_brecommend']?>" class="frm_input wide" disabled/>
			<span>등록일 : <?=$mb['mb_bre_time']?></span>
			</td>
		</tr>
		-->
		<th scope="row">센터멤버</th>
		<td colspan="1">
			<input type="checkbox" style='width:24px;height:24px' name="center_use" id="center_use" value=" <?=$mb['center_use']?> " class="frm_input" <? if($mb['center_use'] == '1') {echo "checked";}?> />
			<label style='margin-left:20px;'><?=$mb['mb_center']?></label>
		</td>

	<?php } ?>



	<!-- 임시 수동입금/매출  -->
	<tr class="ly_up padding-box">

		<th>잔고입금</th>
		<td colspan = 4 style="padding:10px;height:60px;">

			<table class="account_box" style="border:0;">

				<th scope="row" class="btc_color"><label for="mb_eth_account"> ETH 잔고</label></th>
				<td colspan="1" class="btc_color"><input type="text" name="mb_eth_account" value="<?php echo $mb['mb_eth_account'] ?>" id="field_savepoint_eth" class="frm_input" size="15" minlength="1" maxlength="10"></td>

				<th scope="row" class="eth_color"><label for="mb_usdt_account"> USDT 잔고</label></th>
				<td colspan="1" class="eth_color"><input type="text" name="mb_usdt_account" value="<?php echo $mb['mb_usdt_account'] ?>" id="field_savepoint_usdt" class="frm_input" size="15" minlength="1" maxlength="10"></td>

				<th scope="row" class="ups"><label for="mb_deposit_point"> 매출/예치금</label></th>
				<td colspan="7" class="ups"><input type="text" name="mb_deposit_point" value="<?php echo $mb['mb_deposit_point'] ?>" id="field_upstair" class="frm_input wide" size="15" >
				차액:+
				<input type="text" class="be_to" name="be_to"  style="font-size:15px;margin-left:10px;border:0;box-shadow:none;background:transparent" value="0" readonly>

			</table>
		</td>
	</tr>

	<tr>
		<th scope="row">본인확인방법</th>
		<td >
			<input type="radio" name="mb_certify_case" value="ipin" id="mb_certify_ipin" <?php if($mb['mb_certify'] == 'ipin') echo 'checked="checked"'; ?>>
			<label for="mb_certify_ipin">아이핀</label>
			<input type="radio" name="mb_certify_case" value="hp" id="mb_certify_hp" <?php if($mb['mb_certify'] == 'hp') echo 'checked="checked"'; ?>>
			<label for="mb_certify_hp">휴대폰</label>
		</td>

	</tr>

	<tr class="hidden">
		<th scope="row">본인확인</th>
		<td>
			<input type="radio" name="mb_certify" value="1" id="mb_certify_yes" <?php echo $mb_certify_yes; ?>>
			<label for="mb_certify_yes">예</label>
			<input type="radio" name="mb_certify" value="" id="mb_certify_no" <?php echo $mb_certify_no; ?>>
			<label for="mb_certify_no">아니오</label>
		</td>
		<th scope="row"><label for="mb_adult">성인인증</label></th>
		<td>
			<input type="radio" name="mb_adult" value="1" id="mb_adult_yes" <?php echo $mb_adult_yes; ?>>
			<label for="mb_adult_yes">예</label>
			<input type="radio" name="mb_adult" value="0" id="mb_adult_no" <?php echo $mb_adult_no; ?>>
			<label for="mb_adult_no">아니오</label>
		</td>
	</tr>
		<script>
		function copyAddress(param){
			commonModal("Address copy",'Your Wallet address is copied!',100);
			var $temp = $("<input>");
			$("body").append($temp);

			$temp.val($('#'+param).text()).select();
				document.execCommand("copy");
			$temp.remove();
		}
	</script>
	<!--
	<tr >
		<th scope="row">지갑주소</th>
		<td>
			<div class="wallet_addr">
				<span class="btc_color badge">BTC ADDRESS</span>
				<span id="btc_addr"><?=$mb['mb_wallet']?></span>
				<button type="button" class="copybutton" onclick="copyAddress('#btc_addr')">copyAdress</button>
			</div>
		</td>
		<td>

		</td>
		<td>

		</td>
	</tr>
	-->


	<tr class="hidden">
		<th scope="row">주소</th>
		<td colspan="3" class="td_addr_line">
			<label for="mb_zip" class="sound_only">우편번호</label>
			<input type="text" name="mb_zip" value="<?php echo $mb['mb_zip1'].$mb['mb_zip2']; ?>" id="mb_zip" class="frm_input readonly" size="5" maxlength="6">
			<button type="button" class="btn_frmline" onclick="win_zip('fmember', 'mb_zip', 'mb_addr1', 'mb_addr2', 'mb_addr3', 'mb_addr_jibeon');">주소 검색</button><br>
			<input type="text" name="mb_addr1" value="<?php echo $mb['mb_addr1'] ?>" id="mb_addr1" class="frm_input readonly" size="60">
			<label for="mb_addr1">기본주소</label><br>
			<input type="text" name="mb_addr2" value="<?php echo $mb['mb_addr2'] ?>" id="mb_addr2" class="frm_input" size="60">
			<label for="mb_addr2">상세주소</label>
			<br>
			<input type="text" name="mb_addr3" value="<?php echo $mb['mb_addr3'] ?>" id="mb_addr3" class="frm_input" size="60">
			<label for="mb_addr3">참고항목</label>
			<input type="hidden" name="mb_addr_jibeon" value="<?php echo $mb['mb_addr_jibeon']; ?>"><br>
		</td>
	</tr>

	<tr class="hidden">
		<th scope="row"><label for="mb_icon">회원아이콘</label></th>
		<td colspan="3">
			<?php echo help('이미지 크기는 <strong>넓이 '.$config['cf_member_icon_width'].'픽셀 높이 '.$config['cf_member_icon_height'].'픽셀</strong>로 해주세요.') ?>
			<input type="file" name="mb_icon" id="mb_icon">
			<?php
			$mb_dir = substr($mb['mb_id'],0,2);
			$icon_file = G5_DATA_PATH.'/member/'.$mb_dir.'/'.$mb['mb_id'].'.gif';
			if (file_exists($icon_file)) {
				$icon_url = G5_DATA_URL.'/member/'.$mb_dir.'/'.$mb['mb_id'].'.gif';
				echo '<img src="'.$icon_url.'" alt="">';
				echo '<input type="checkbox" id="del_mb_icon" name="del_mb_icon" value="1">삭제';
			}
			?>
		</td>
	</tr>
	<tr class="hidden">
		<th scope="row">메일 수신</th>
		<td>
			<input type="radio" name="mb_mailling" value="1" id="mb_mailling_yes" <?php echo $mb_mailling_yes; ?>>
			<label for="mb_mailling_yes">예</label>
			<input type="radio" name="mb_mailling" value="0" id="mb_mailling_no" <?php echo $mb_mailling_no; ?>>
			<label for="mb_mailling_no">아니오</label>
		</td>
		<th scope="row"><label for="mb_sms_yes">SMS 수신</label></th>
		<td>
			<input type="radio" name="mb_sms" value="1" id="mb_sms_yes" <?php echo $mb_sms_yes; ?>>
			<label for="mb_sms_yes">예</label>
			<input type="radio" name="mb_sms" value="0" id="mb_sms_no" <?php echo $mb_sms_no; ?>>
			<label for="mb_sms_no">아니오</label>
		</td>
	</tr>
	<tr class="hidden">
		<th scope="row"><label for="mb_open">정보 공개</label></th>
		<td colspan="3">
			<input type="radio" name="mb_open" value="1" id="mb_open_yes" <?php echo $mb_open_yes; ?>>
			<label for="mb_open_yes">예</label>
			<input type="radio" name="mb_open" value="0" id="mb_open_no" <?php echo $mb_open_no; ?>>
			<label for="mb_open_no">아니오</label>
		</td>
	</tr>
	<tr class="hidden">
		<th scope="row"><label for="mb_signature">서명</label></th>
		<td colspan="3"><textarea  name="mb_signature" id="mb_signature"><?php echo $mb['mb_signature'] ?></textarea></td>
	</tr>
	<tr class="hidden">
		<th scope="row"><label for="mb_profile">자기 소개</label></th>
		<td colspan="3"><textarea name="mb_profile" id="mb_profile"><?php echo $mb['mb_profile'] ?></textarea></td>
	</tr>


	<?php if ($w == 'u') { ?>
	<tr>
		<th scope="row">회원가입일</th>
		<td><?php echo $mb['mb_datetime'] ?></td>
		<th scope="row">최근접속일</th>
		<td><?php echo $mb['mb_today_login'] ?></td>
	</tr>
	<tr  class="hidden">
		<th scope="row">IP</th>
		<td ><?php echo $mb['mb_ip'] ?></td>

		<th scope="row">지급차단</th>
		<td>
			<label for="mb_block_yes">
				<input type="radio" name="mb_block" value="1" id="mb_block_yes" <?php echo $mb_block_yes; ?> >예
			</label>
			<label for="mb_block_no">
				<input type="radio" name="mb_block" value="0" id="mb_block_no" <?php echo $mb_block_no; ?> >아니오
			</label>
	</tr>
	<?php if ($config['cf_use_email_certify']) { ?>
	<tr>
		<th scope="row">인증일시</th>
		<td colspan="3">
			<?php if ($mb['mb_email_certify'] == '0000-00-00 00:00:00') { ?>
			<?php echo help('회원님이 메일을 수신할 수 없는 경우 등에 직접 인증처리를 하실 수 있습니다.') ?>
			<input type="checkbox" name="passive_certify" id="passive_certify">
			<label for="passive_certify">수동인증</label>
			<?php } else { ?>
			<?php echo $mb['mb_email_certify'] ?>
			<?php } ?>
		</td>
	</tr>
	<?php } ?>
	<?php } ?>



	<tr>
		<th scope="row"><label for="mb_leave_date">탈퇴일자</label></th>
		<td>
			<input type="text" name="mb_leave_date" value="<?php echo $mb['mb_leave_date'] ?>" id="mb_leave_date" class="frm_input" maxlength="8">
			<input type="checkbox" value="<?php echo date("Ymd"); ?>" id="mb_leave_date_set_today" onclick="if (this.form.mb_leave_date.value==this.form.mb_leave_date.defaultValue) {
this.form.mb_leave_date.value=this.value; } else { this.form.mb_leave_date.value=this.form.mb_leave_date.defaultValue; }">
			<label for="mb_leave_date_set_today">탈퇴일을 오늘로 지정</label>
		</td>
		<th scope="row">접근차단일자</th>
		<td>
			<input type="text" name="mb_intercept_date" value="<?php echo $mb['mb_intercept_date'] ?>" id="mb_intercept_date" class="frm_input" maxlength="8">
			<input type="checkbox" value="<?php echo date("Ymd"); ?>" id="mb_intercept_date_set_today" onclick="if
(this.form.mb_intercept_date.value==this.form.mb_intercept_date.defaultValue) { this.form.mb_intercept_date.value=this.value; } else {
this.form.mb_intercept_date.value=this.form.mb_intercept_date.defaultValue; }">
			<label for="mb_intercept_date_set_today">접근차단일을 오늘로 지정</label>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="mb_memo">메모</label></th>
		<td colspan="3"><textarea name="mb_memo" id="mb_memo" style="height:30px;" ><?php echo $mb['mb_memo'] ?></textarea></td>
	</tr>

	<?php for ($i=1; $i<=10; $i++) { ?>
	<tr class="hidden">
		<th scope="row"><label for="mb_<?php echo $i ?>">여분 필드 <?php echo $i ?></label></th>
		<td colspan="3"><input type="text" name="mb_<?php echo $i ?>" value="<?php echo $mb['mb_'.$i] ?>" id="mb_<?php echo $i ?>" class="frm_input " size="30" maxlength="255"></td>
	</tr>
	<?php } ?>

	</tbody>
	</table>
</div>

<div class="btn_confirm01 btn_confirm">
	<input type="submit" value="확인" class="btn_submit" accesskey='s'>
	<a href="./member_list.php?<?php echo $qstr ?>">목록</a>
</div>
</form>

<script>

function fmember_submit(f)
{

	/*## ##################################*/
	var $rcm_id = $('#mb_recommend').val();
	var $mbs_id = $('#mb_id').val();
	var $break = "ok";

	if ($rcm_id == $mbs_id) {
		alert("회원아이디와 추천인 아이디가 같을 수 없습니다.");
		$('#mb_recommend').focus();
		return false;
	} else {
		$.ajax({
			type: "POST",
			url: "<?=G5_SHOP_URL?>/ajax.id.php",
			data: {
				"rcm_id":$rcm_id,
				"mbs_id":$mbs_id
			},
			cache: false,
			async: false,
				error : function (request, status, error) { // error
							alert("code : " + request.status + "\r\nmessage : " + request.responseText);
						},
				success: function(data) {
					if (data == "break") {
						$break = "break";
					}
			}
		});
	}

	if ($('#center_use').is(":checked")) {
		$('#center_use').val('1');
	} else {
		$('#center_use').val('0');
	}

	if ($break == "break") {
		alert("추천인 아이디를 다시 한번 확인해주세요!");
		$('#mb_recommend').focus();
		return false;
	}
	/*## ##################################*/

	if (!f.mb_icon.value.match(/\.gif$/i) && f.mb_icon.value) {
		alert('아이콘은 gif 파일만 가능합니다.');
		return false;
	}

	return true;
}
</script>

<?php
include_once('./admin.tail.php');
?>
