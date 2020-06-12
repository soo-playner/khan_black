<?php
$sub_menu = "200100";
include_once('./_common.php');
include_once('./adm.wallet.php');

auth_check($auth[$sub_menu], 'r');

$sql_common = " from {$g5['member_table']} ";

$sql_search = " where (1) ";
if ($stx) {     
	$sql_search .= " and ( ";
	switch ($sfl) {
		case 'mb_point' :
			$sql_search .= " ({$sfl} >= '{$stx}') ";
			break;
		case 'mb_level' :
			$sql_search .= " ({$sfl} = '{$stx}') ";
			break;
		case 'mb_tel' :
		case 'mb_hp' :
			$sql_search .= " ({$sfl} like '%{$stx}') ";
			break;
		default :
			$sql_search .= " ({$sfl} like '{$stx}%') ";
			break;
	}
	$sql_search .= " ) ";
}

if($_GET['level']){
	$sql_search .= " and mb_level = ".$_GET['level'];
}

if($_GET['grade']){
	$sql_search .= " and grade = ".$_GET['grade'];
}

if($_GET['nation']){
	$sql_search .= " and nation_number = ".$_GET['nation'];
}

if($_GET['block']){
	$sql_search .= " and mb_block = 1 ";
}

if ($is_admin != 'super')
	$sql_search .= " and mb_level <= '{$member['mb_level']}' ";

if (!$sst) {
	$sst = "FIELD(mb_id, '{$config['cf_admin']}','admin') DESC, mb_datetime, mb_no";
	$sod = "desc";
}


$sql_order = " order by {$sst} {$sod}";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";

$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];

$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함


// 탈퇴회원수
$sql = " select count(*) as cnt {$sql_common} {$sql_search} and mb_leave_date <> '' {$sql_order} ";
$row = sql_fetch($sql);
$leave_count = $row['cnt'];


// 차단회원수
$sql = " select count(*) as cnt {$sql_common} {$sql_search} and mb_intercept_date <> '' {$sql_order} ";
$row = sql_fetch($sql);
$intercept_count = $row['cnt'];

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';



$g5['title'] = '회원관리';
include_once('./admin.head.php');

$sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";
$result = sql_query($sql);

$colspan = 16;

/* 레벨 */
$grade = "SELECT grade, count( grade ) as cnt FROM g5_member GROUP BY grade order by grade";
$get_lc = sql_query($grade);

/* 국가 */
$nation_sql = "SELECT nation_number, count( nation_number ) as cnt FROM g5_member GROUP BY nation_number";
$nation_row = sql_query($nation_sql);


$blockRec = sql_fetch("select count(mb_block) as cnt from g5_member where mb_block = 1");

function active_check($val, $target){
    $bool_check = $_GET[$target];
    if($bool_check == $val){
        return " active ";
    }
}

function out_check($val){
	$bonus_OUT_CALC = $val;

	if($bonus_OUT_CALC > 100){
		$class = 'over';
	}else{
		$class = '';
	}
	return "<span class=".$class.">".number_format($bonus_OUT_CALC)." % </span>";
}

?>

<div class="local_ov01 local_ov">
	<?php echo $listall ?>
	총회원수 <?php echo number_format($total_count) ?>명 중,
	<a href="?sst=mb_intercept_date&amp;sod=desc&amp;sfl=<?php echo $sfl ?>&amp;stx=<?php echo $stx ?>">
	차단 <?php echo number_format($intercept_count) ?></a>명,
	<a href="?sst=mb_leave_date&amp;sod=desc&amp;sfl=<?php echo $sfl ?>&amp;stx=<?php echo $stx ?>">탈퇴 <?php echo number_format($leave_count) ?></a>명,
	<a href="?block=1">
		지급차단 <?php echo number_format($blockRec['cnt']) ?>명
	</a>
</div>

<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get">

<label for="sfl" class="sound_only">검색대상</label>
<select name="sfl" id="sfl">
	<option value="mb_id"<?php echo get_selected($_GET['sfl'], "mb_id"); ?>>회원아이디</option>
	<option value="mb_nick"<?php echo get_selected($_GET['sfl'], "mb_nick"); ?>>닉네임</option>
	<option value="mb_name"<?php echo get_selected($_GET['sfl'], "mb_name"); ?>>이름</option>
	<option value="mb_level"<?php echo get_selected($_GET['sfl'], "mb_level"); ?>>권한</option>
	<option value="mb_email"<?php echo get_selected($_GET['sfl'], "mb_email"); ?>>E-MAIL</option>
	<option value="mb_tel"<?php echo get_selected($_GET['sfl'], "mb_tel"); ?>>전화번호</option>
	<option value="mb_hp"<?php echo get_selected($_GET['sfl'], "mb_hp"); ?>>휴대폰번호</option>
	<option value="mb_point"<?php echo get_selected($_GET['sfl'], "mb_point"); ?>>PV</option>
	<option value="mb_datetime"<?php echo get_selected($_GET['sfl'], "mb_datetime"); ?>>가입일시</option>
	<option value="mb_ip"<?php echo get_selected($_GET['sfl'], "mb_ip"); ?>>IP</option>
	<option value="mb_recommend"<?php echo get_selected($_GET['sfl'], "mb_recommend"); ?>>추천인</option>
	<option value="mb_wallet"<?php echo get_selected($_GET['sfl'], "mb_wallet"); ?>>지갑</option>
</select>

<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" required class="required frm_input">
<input type="submit" class="btn_submit" value="검색">



</form>

<!--
<div class="local_desc01 local_desc">
	<p>
		회원자료 삭제 시 다른 회원이 기존 회원아이디를 사용하지 못하도록 회원아이디, 이름, 닉네임은 삭제하지 않고 영구 보관합니다.
	</p>
</div>
-->


<style>
	#member_depth{background:lightskyblue}
	#member_depth:hover{background:black; color:white;}
	.mem_icon{width:20px;height:20px;margin-right:5px;}
	.area{display:inline-block;margin-right:20px;vertical-align: middle}
	.area span{cursor:pointer}
	.area span:hover{text-decoration: underline}

	.area.nation{border-right:3px solid black;padding-right:20px;}

	.nation_item{
		display: inline-block;
		padding: 5px 10px;
		border: 1px solid #c8ced1;
		background: #d6dde1;
		text-decoration: none;}
	.nation_item:hover{background:#3e4452;color:white;}
	.nation_item.active{background:#f9a62e;border:1px solid #f9a62e; color:black;}
	.nation_icon{vertical-align:bottom;margin-right:3px;}

	.total{width:8%;background:#555 !important;color:white !important;}
	.bonus_total{width:7%;background:teal !important;color:white !important;}
	.bonus_usdt{width:7%;background:crimson !important;color:white !important;}
	.bonus_eth{width:7%;background:#0062cc !important;color:white !important;}

	.bonus_aa{width:7%;background:yellowgreen !important}
	.bonus_bb{width:7%;background:skyblue !important}
	.bonus_bb.bonus_out{width:7%;background:deepskyblue !important}
	.bonus_bb.bonus_benefit{width:7%;background:gold !important}

	.td_name{width:8%}
	.td_mail{width:10%;}
	.td_mbgrade{width:10%;min-width:120px;text-align:center}
	.td_mbgrade select{min-width:50px;padding:5px 5px }

	.tbl_head02 tbody td{padding:5px;}
	.over{color:red;}
	.td_mngsmall a {border:1px solid #ccc; padding:3px 10px; display:inline-block;text-decoration:none;}
	.td_mngsmall a:hover{background:black;border:1px solid black; color:white;}
	.labelM{text-align:left;}
	
	.red{color:red;font-weight:600;}
	.btn_add01{padding-bottom:10px;border-bottom:1px solid #bbb}
</style>


<div style="padding:8px 20px 10px;font-size:15px;margin-bottom:10px;float:left">
<!-- $nation_name=array('Japan'=>81,'Republic of Korea'=>82,'Vietnam'=>84,'China'=>86,'Indonesia'=>62,'Philippines'=>63,'Thailand'=>66); -->

<form name="search_bar" id="search_bar" action="./member_list.php" method="get">
	<input type='hidden' name ='nation' id='nation' value=''/>
	<input type='hidden' name ='level' id='level' value=''/>
	<input type='hidden' name ='grade' id='grade' value=''/>

	<div class="area nation">
		<?
		while( $row = sql_fetch_array($nation_row)){
			

			if($row['nation_number']=='1'){
				echo "<span onclick='nation_search(1);' class='nation_item ".active_check(1, 'nation')."'><img src='./img/contry_3.png' class='nation_icon'/>".$row['cnt']." </span> ";
			}else if($row['nation_number']=='62'){
				echo "<span onclick='nation_search(62);' class='nation_item ".active_check(62, 'nation')."'> indo ".$row['cnt']." </span> ";
			}else if($row['nation_number']=='63'){
				echo "<span onclick='nation_search(63);' class='nation_item ".active_check(63, 'nation')."'> Phil ".$row['cnt']." </span> ";
			}else if($row['nation_number']=='66'){
				echo "<span onclick='nation_search(66);' class='nation_item ".active_check(66, 'nation')."'> THailand ".$row['cnt']." </span> ";
			}else if($row['nation_number']=='81'){
				echo "<span onclick='nation_search(81);' class='nation_item ".active_check(81, 'nation')."'> JAPAN ".$row['cnt']." </span> ";
			}else if($row['nation_number']=='82'){
				echo "<span onclick='nation_search(82);' class='nation_item ".active_check(82, 'nation')."'><img src='./img/contry_1.png' class='nation_icon'/>".$row['cnt']." </span> ";
			}else{
				echo "<span onclick='nation_search(0);' class='nation_item  ".active_check(0, 'nation')."'> ETC ".$row['cnt']." </span> ";
			}
		}
		?>
	</div>
		
	<div class="area level">
	<?while($l_row = sql_fetch_array($get_lc)){

		if($l_row['grade']==6){
			echo "<span onclick='grade_search(6);'><img src='/img/6.png' class='mem_icon'>".$l_row['cnt']."명</span>";
		}
		else if($l_row['grade']==5){
			echo "<span onclick='grade_search(5);'><img src='/img/5.png' class='mem_icon'>".$l_row['cnt']."명</span> | ";
		}
		else if($l_row['grade']==4){
			echo "<span onclick='grade_search(4);'><img src='/img/4.png' class='mem_icon'>".$l_row['cnt']."명</span> | ";
		}
		else if($l_row['grade']==3){
			echo "<span onclick='grade_search(3);'><img src='/img/3.png' class='mem_icon'>".$l_row['cnt']."명</span> | ";
		}
		else if($l_row['grade']==2){
			echo "<span onclick='grade_search(2);'><img src='/img/2.png' class='mem_icon'>".$l_row['cnt']."명</span> | ";
		}
		else if($l_row['grade']==1){
			echo "<span onclick='grade_search(1);'><img src='/img/1.png' class='mem_icon'>".$l_row['cnt']."명</span> | ";
		}
		else if($l_row['grade']==0){
			echo "<span onclick='grade_search(0);'><img src='/img/0.png' class='mem_icon'>".$l_row['cnt']."명</span> | ";
		}
	}?>
	</div>
</form>
</div>


<!--member_list_excel.php로 넘길 회원엑셀다운로드 데이터-->
<form name="myForm" action="../excel/member_list_excel.php" method="post">
<input type="hidden" name="sql_common" value="<?php echo $sql_common ?>" />
<input type="hidden" name="sql_search" value="<?php echo $sql_search ?>" />
<input type="hidden" name="sql_order" value="<?php echo $sql_order ?>" />
<input type="hidden" name="from_record" value="<?php echo $from_record ?>" />
<input type="hidden" name="rows" value="<?php echo $rows ?>" />
</form>


<?php if ($is_admin == 'super') { ?>
<div class="btn_add01 btn_add">
	<a href="./member_table_fixtest.php">추천관계검사</a>
	<a href="./member_table_depth.php" id="member_depth">회원추천관계갱신</a>
	<a href="./member_form.php" id="member_add">회원직접추가</a>
	<a href="#" onclick="javascript:document.myForm.submit();">회원엑셀다운로드</a>  <!--회원엑셀다운로드 버튼-->
	<a href="../excel/all_member_list_excel.php">전체회원엑셀다운로드</a> <!--회원전체엑셀다운로드 버튼-->
</div>
<?php } ?>

<div style="padding: 20px;font-size:15px;">

<?
$i = 0;
while($l_row = sql_fetch_array($get_lc)){

	if($l_row['grade']==$i){
		echo $start." ".$i."star :".$l_row['cnt']."명 | ";
	}
	++$i;
}?>
</div>



<form name="fmemberlist" id="fmemberlist" action="./member_list_update.php" onsubmit="return fmemberlist_submit(this);" method="post">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">

<div class="tbl_head02 tbl_wrap" style="clear:both">
	<table>
	<caption><?php echo $g5['title']; ?> 목록</caption>
	<colgroup>
		<col width="40"/><col/><col/><col width="50" /><col width="50" /><col width="50" /><col width="50" /><col width="50" /><col width="50" /><col width="120" /><col width="120" /><col width="160" /><col width="120" /><col width="100" />
	</colgroup>
	<thead>
	<tr>
		<th scope="col" rowspan="2" id="mb_list_chk">
			<label for="chkall" class="sound_only">회원 전체</label>
			<input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
		</th>
		<th scope="col" rowspan="2" id="mb_list_id" class="td_name"><?php echo subject_sort_link('mb_id') ?>아이디</a></th>
		<!--<th scope="col" rowspan="2"  id="mb_list_cert"><?php echo subject_sort_link('mb_certify', '', 'desc') ?>메일인증확인</a></th>-->

		<th scope="col" rowspan="2" id="mb_list_mobile" class="td_mail">메일주소</th>
		<th scope="col" id="mb_list_auth"  class="total" rowspan="2">TOTAL <br>( USD / $ )</th>
		<th scope="col" id="mb_list_auth"  class="bonus_total" rowspan="2">HAZ </th>
		<th scope="col" id="mb_list_auth"  class="bonus_usdt" rowspan="2">USDT </th>
		<th scope="col" id="mb_list_auth"  class="bonus_eth" rowspan="2">ETH </th>


		<th scope="col" id="mb_list_auth2" class="bonus_bb bonus_benefit"  rowspan="2"> BENEFIT<br>( 총 발생수당 )</th>
		<th scope="col" id="mb_list_auth2" class="bonus_bb"  rowspan="2">Sales <br> ( 현재예치금 )</th>
		<!--<th scope="col" id="mb_list_auth2" class="bonus_bb"  rowspan="2">UPConversion <br> ( 전환 예치금 )</th>-->
		<th scope="col" id="mb_list_auth2" class="bonus_bb bonus_out"  rowspan="2">수당/예치금<br>(100%)</th>
		<!-- <th scope="col" id="mb_list_auth2" class="bonus_bb"  rowspan="2" style="background:aliceblue !important">UPSTAIR ACC <br> ( 누적 예치금 )</th> -->



		<th scope="col" id="mb_list_authcheck" rowspan="2">상태/<?php echo subject_sort_link('mb_level', '', 'desc') ?>회원등급</a></th>
		<th scope="col" id="mb_list_member"><?php echo subject_sort_link('mb_today_login', '', 'desc') ?>최종접속</a></th>
		<th scope="col" rowspan="3" id="mb_list_mng">관리</th>
	</tr>

	<tr>
		<!--<th scope="col" id="mb_list_mailc"><?php echo subject_sort_link('mb_email_certify', '', 'desc') ?>메일<br>인증</a></th>-->
		<th scope="col" id="mb_list_join"><?php echo subject_sort_link('mb_datetime', '', 'desc') ?>가입일</a></th>
	</tr>

	</thead>
	<tbody>

	<p class="labelM">TOTAL = HAZ_usd + USDT_usd + ETH_usd </p>

	<?php
	for ($i=0; $row=sql_fetch_array($result); $i++) {

		// 접근가능한 그룹수
		$sql2 = " select count(*) as cnt from {$g5['group_member_table']} where mb_id = '{$row['mb_id']}' ";
		$row2 = sql_fetch($sql2);
		
		$pinnacle_bal = $row['mb_balance'];
		$group = '';
		if ($row2['cnt'])
			$group = '<a href="./boardgroupmember_form.php?mb_id='.$row['mb_id'].'">'.$row2['cnt'].'</a>';

		if ($is_admin == 'group') {
			$s_mod = '';
		} else {
			$s_mod = '<a href="./member_form.php?'.$qstr.'&amp;w=u&amp;mb_id='.$row['mb_id'].'">수정</a>';
			$s_mod_binary = '<a href="./modify_binary.php?'.$qstr.'&amp;w=u&amp;mb_id='.$row['mb_id'].'">바이너리 수정</a>';

		}
		$s_grp = '<a href="./boardgroupmember_form.php?mb_id='.$row['mb_id'].'">그룹</a>';

		$leave_date = $row['mb_leave_date'] ? $row['mb_leave_date'] : date('Ymd', G5_SERVER_TIME);
		$intercept_date = $row['mb_intercept_date'] ? $row['mb_intercept_date'] : date('Ymd', G5_SERVER_TIME);

		$mb_nick = get_sideview($row['mb_id'], get_text($row['mb_nick']), $row['mb_email'], $row['mb_homepage']);

		$mb_id = $row['mb_id'];
		$leave_msg = '';
		$intercept_msg = '';
		$intercept_title = '';
		if ($row['mb_leave_date']) {
			$mb_id = $mb_id;
			$leave_msg = '<span class="mb_leave_msg">탈퇴함</span>';
		}
		else if ($row['mb_intercept_date']) {
			$mb_id = $mb_id;
			$intercept_msg = '<span class="mb_intercept_msg">차단됨</span>';
			$intercept_title = '차단해제';
		}
		if ($intercept_title == '')
			$intercept_title = '차단하기';

		$address = $row['mb_zip1'] ? print_address($row['mb_addr1'], $row['mb_addr2'], $row['mb_addr3'], $row['mb_addr_jibeon']) : '';

		$bg = 'bg'.($i%2);

		switch($row['mb_certify']) {
			case 'hp':
				$mb_certify_case = '휴대폰';
				$mb_certify_val = 'hp';
				break;
			case 'ipin':
				$mb_certify_case = '아이핀';
				$mb_certify_val = '';
				break;
			case 'admin':
				$mb_certify_case = '관리자';
				$mb_certify_val = 'admin';
				break;
			default:
				$mb_certify_case = '&nbsp;';
				$mb_certify_val = 'admin';
				break;
		}
	?>

	
	<?
		// 수당설정값 
		// $bonus_sql = "select * from {$g5['bonus_config']} order by idx";
		// $list = sql_query($bonus_sql);
		// $pre_setting = sql_fetch($bonus_sql);
		// $limited = $pre_setting['limited'];
		// $limited_per = 100/$limited*100;

		// $math_sql = "select mb_balance,mb_deposit_point,sum(mb_usdt_account + mb_shift_amt + mb_deposit_calc + mb_balance) as total from g5_member where mb_id = '".$row['mb_id']."'";
		// $math_total = sql_fetch($math_sql);

		// $math_percent_sql = "select sum(mb_balance / mb_deposit_point) * {$limited_per} as percent from g5_member where mb_id =  '".$row['mb_id']."'";
		// $math_percent = sql_fetch($math_percent_sql);
		
		// $bonus_BENEFIT_TOTAL = number_format($row['mb_balance'],5); // 수당
		// $bonus_TOTAL =  $math_total['total'];  //합계잔고
		// $bonus_UPSTAIR = number_format($row['mb_deposit_point'],5); // 매출

				
		// 토탈 잔고 - 대쉬보드에서만 사용
		// 잔고 토탈 = 입금(usdt,eth +) + 출금(-) + 예치금전환(-) + 수당(+)
		$math_sql = "select sum(mb_usdt_account + mb_usdt_calc + mb_usdt_amt) as usdt_total, sum(mb_eth_account + mb_eth_calc + mb_eth_amt) as eth_total, sum(mb_balance + mb_shift_amt) as balance_total  from g5_member where mb_id = '{$row['mb_id']}' ";
		$math = sql_fetch($math_sql);

		$total_usdt = $math['usdt_total'];
		$total_eth = $math['eth_total'];
		$total_balance = $math['balance_total'];

		// 현재 통화 시세
		$haz_price = coin_price('haz');
		$eth_price = coin_price('eth');
		$usdt_price = coin_price('usdt');
		$usd_price = coin_price('usd');

		$total_usdt_usd = $math['usdt_total']*$usdt_price;
		$total_eth_usd = $math['eth_total']*$eth_price;
		$total_balance_usd = $math['balance_total']*$haz_price;

		$total_account = $total_usdt + $total_eth + $math['balance_total'];
		$total_account_usd = $total_usdt_usd + $total_eth_usd + $total_balance_usd;
		$total_balance_num = Number_format($total_balance, 2); // 콤마 포함 소수점 2자리까지

		$bonus_percent = bonus_state($row['mb_id'])
	?>


	<tr class="<?php echo $bg; ?>">
		<td headers="mb_list_chk" class="td_chk" rowspan="2">
			<input type="hidden" name="mb_id[<?php echo $i ?>]" value="<?php echo $row['mb_id'] ?>" id="mb_id_<?php echo $i ?>">
			<label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo get_text($row['mb_name']); ?> <?php echo get_text($row['mb_nick']); ?>님</label>
			<input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
		</td>
		<td headers="mb_list_id" rowspan="2" class="td_name sv_use" style="text-align:center !imporatant">
		<?echo "<img src='/img/".$row['grade'].".png' style='width:20px;height:20px;'>";?>
		<?php echo $mb_id ?></td>
		
		<!-- 
			<td headers="mb_list_name" class="td_mbname"><?php echo get_text($row['mb_name']); ?></td> 
			<td headers="mb_list_name" class="td_mbname"><?php echo get_text($row['first_name']); ?></td>-->
			<!--
			<td headers="mb_list_otp" class="td_chk">
				<label for="otp_flag<?php echo $i; ?>" class="sound_only">OTP인증</label>
				<input type="checkbox" name="otp_flag[<?php echo $i; ?>]" <?php echo $row['otp_flag'] == 'Y' ?'checked':''; ?> value="Y" id="otp_flag<?php echo $i; ?>">
			</td>
			<td headers="mb_list_mailc" class="td_chk"><?php echo preg_match('/[1-9]/', $row['mb_email_certify'])?'<span class="txt_true">Yes</span>':'<span class="txt_false">No</span>'; ?></td>
			<td headers="mb_list_open" class="td_chk">
				<label for="mb_open_<?php echo $i; ?>" class="sound_only">정보공개</label>
				<input type="checkbox" name="mb_open[<?php echo $i; ?>]" <?php echo $row['mb_open']?'checked':''; ?> value="1" id="mb_open_<?php echo $i; ?>">
			</td>
			-->
			<!--
			<td headers="mb_list_mailr" rowspan="2"class="td_chk">
				<label for="mb_mailling_<?php echo $i; ?>" class="sound_only">메일수신</label>
				<input type="checkbox"  name="mb_mailling[<?php echo $i; ?>]" <?php echo $row['mb_mailling']?'checked':''; ?> value="1" id="mb_mailling_<?php echo $i; ?>">
			</td>
			-->
			<!--
			<td headers="mb_list_sms" class="td_chk">
				<label for="mb_sms_<?php echo $i; ?>" class="sound_only">SMS수신</label>
				<input type="checkbox" name="mb_sms[<?php echo $i; ?>]" <?php echo $row['mb_sms']?'checked':''; ?> value="1" id="mb_sms_<?php echo $i; ?>">
			</td>
			<td headers="mb_list_adultc" class="td_chk">
				<label for="mb_adult_<?php echo $i; ?>" class="sound_only">성인인증</label>
				<input type="checkbox" name="mb_adult[<?php echo $i; ?>]" <?php echo $row['mb_adult']?'checked':''; ?> value="1" id="mb_adult_<?php echo $i; ?>">
			</td>
			<td headers="mb_list_deny" class="td_chk">
				<?php if(empty($row['mb_leave_date'])){ ?>
				<input type="checkbox" name="mb_intercept_date[<?php echo $i; ?>]" <?php echo $row['mb_intercept_date']?'checked':''; ?> value="<?php echo $intercept_date ?>" id="mb_intercept_date_<?php echo $i ?>" title="<?php echo $intercept_title ?>">
				<label for="mb_intercept_date_<?php echo $i; ?>" class="sound_only">접근차단</label>
				<?php } ?>
			</td>
		-->

		<td headers="mb_list_mobile" rowspan="2" class="td_tel"><?php echo get_text($row['mb_email']); ?></td>

		<style>
			.td_mbstat{text-align:right;padding-right:10px !important;font-size:12px;}
		</style>

		<td headers="mb_list_auth" class="td_mbstat" rowspan="2"><strong><?=Number_format($total_account_usd,2)?></strong></td>

		<td headers="mb_list_auth" class="td_mbstat" rowspan="2"><?=Number_format($total_balance,2)?></td>

		<td headers="mb_list_auth" class="td_mbstat" rowspan="2"><?=Number_format($total_usdt,2)?></td>

		<td headers="mb_list_auth" class="td_mbstat" rowspan="2"><?=Number_format($total_eth,5)?></td>
		

		<td headers="mb_list_auth" class="td_mbstat" rowspan="2">
			<strong><?=Number_format($row['mb_balance'],2)?> </strong><!--수당잔고-->
		</td>

		<td headers="mb_list_auth" class="td_mbstat" rowspan="2"><strong><?= Number_format($row['mb_deposit_point']) ?> </strong><!--예치금--><br></td>
		<td headers="mb_list_auth" class="td_mbstat" rowspan="2"><? if($bonus_percent > 75){echo "<span class='red'>".$bonus_percent."</span>";}else{ echo $bonus_percent;} ?> %</td>

		<!-- <td headers="mb_list_auth" class="td_mbstat" rowspan="2" ><?= $row['mb_deposit_acc'] ?></td> -->

		<td headers="mb_list_member" class="td_mbgrade" rowspan="2">
			
			<?php
			/*if ($leave_msg || $intercept_msg) echo $leave_msg.' '.$intercept_msg; else echo "정상 / ";*/
			echo "<img src='/img/".$row['grade'].".png' style='width:20px;height:20px;'> ".$row['grade'].' / ';
			?>

			<?php echo get_member_level_select("mb_level[$i]", 0, $member['mb_level'], $row['mb_level']) ?>
		</td>
		<td headers="mb_list_lastcall" class="td_date"><?php echo substr($row['mb_today_login'],2,8); ?></td>
		<!--<td headers="mb_list_grp" rowspan="1" class="td_numsmall"><?php echo $group ?></td>-->
		<td headers="mb_list_mng" rowspan="2" class="td_mngsmall" style="width:200px;"><?php echo $s_mod ?> <?php echo $s_grp ?></br> <?php echo $s_mod_binary ?></td>

	</tr>
	<tr class="<?php echo $bg; ?>">
		<!-- <td headers="mb_list_nick" class="td_name sv_use"><div><?php echo $mb_nick ?></div></td> -->
		<!--<td headers="mb_list_nick" class="td_name sv_use"><div><?php echo get_text($row['last_name']); ?></div></td>-->
			<!--<td headers="mb_list_cert" colspan="6" class="td_mbcert">-->
			<!-- <input type="radio" name="mb_certify[<?php echo $i; ?>]" value="ipin" id="mb_certify_ipin_<?php echo $i; ?>" <?php echo $row['mb_certify']=='ipin'?'checked':''; ?>>
			<label for="mb_certify_ipin_<?php echo $i; ?>">아이핀</label>
			<input type="radio" name="mb_certify[<?php echo $i; ?>]" value="hp" id="mb_certify_hp_<?php echo $i; ?>" <?php echo $row['mb_certify']=='hp'?'checked':''; ?>>
			<label for="mb_certify_hp_<?php echo $i; ?>">휴대폰</label>

			P1 <?=$row['it_pool1']?>개 / P2 - <?=$row['it_pool2']?>개 / P3 - <?=$row['it_pool3']?>개 / P4 - <?=$row['it_pool4']?>개 / G - <?=$row['it_GPU']?>개
			</td>-->


		<!--<td headers="mb_list_tel" class="td_tel"><?php echo get_text($row['mb_tel']); ?></td>
		<td></td>-->
		<!-- // <td headers="mb_list_point" class="td_num"><a href="point_list.php?sfl=mb_id&amp;stx=<?php echo $row['mb_id'] ?>"><?php echo number_format($row['mb_point']) ?></a></td> // -->
		<td headers="mb_list_join" class="td_date"><?php echo substr($row['mb_datetime'],2,8); ?></td>
		<!--
		<td>
			<a href="https://www.blockchain.com/ko/btc/address/<?php echo $row['mb_wallet'] ?>" target="_balnk">
				<?php echo $row['mb_wallet'] ?>
			</a>
		</td>
		-->
	</tr>

	<?php
	}
	if ($i == 0)
		echo "<tr><td colspan=\"".$colspan."\" class=\"empty_table\">자료가 없습니다.</td></tr>";
	?>
	</tbody>
	</table>
</div>

<div class="btn_list01 btn_list">
	<input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value">
	<input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value">
</div>

</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>

<script>
function fmemberlist_submit(f)
{
	if (!is_checked("chk[]")) {
		alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
		return false;
	}

	if(document.pressed == "선택삭제") {
		if(!confirm("선택한 자료를 정말 삭제하시겠습니까?")) {
			return false;
		}
	}
	return true;
}

function level_search(param){
	$('#search_bar #level').val(param);
	//console.log($('#search_bar #level').val());
	$('#search_bar').submit();
}

function grade_search(param){
	$('#search_bar #grade').val(param);
	//console.log($('#search_bar #level').val());
	$('#search_bar').submit();
}

function nation_search(param){
	$('#search_bar #nation').val(param);
	//console.log($('#search_bar #nation').val());
	$('#search_bar').submit();
}

// 엑셀 다운로드
$('#excel_btn').on("click", function () {

	var s_date = $('#s_date').val();
	var e_date = $('#e_date').val();
	//var idx_num = $('.select-btn').val();
	var idx_num = '';
	var ck_box = true;
	$('.ckbox').each(function(){
		if( $(this).prop('checked') ){
			if( ck_box == true ){
				ck_box = false;
				idx_num += $(this).val();
			}else{
				idx_num += '_'+$(this).val();
			}
		}
	})
	//console.log("/excel/metal.php?s_date="+s_date+"&e_date="+e_date+"&idx_num="+idx_num+"&idx=<?=$idx?>");

	window.open("/excel/metal.php?s_date="+s_date+"&e_date="+e_date+"&idx_num="+idx_num+"&idx=<?=$idx?>");
});
</script>

<?php
include_once ('./admin.tail.php');
?>
