<?php
$sub_menu = "700050";
include_once('./_common.php');

$g5['title'] = "입/출금 설정";

include_once(G5_ADMIN_PATH.'/admin.head.php');
if($_POST['w'] == 'u'){
	
	for($i = 0 ; $i < count($_POST['idx']); $i ++){

		$idx = $_POST['idx'][$i];
		$fee = $_POST['fee'][$i];
		$amt_minimum = $_POST['amt_minimum'][$i];
		$amt_maximum = $_POST['amt_maximum'][$i];
		$day_limit = $_POST['day_limit'][$i];

		$update_wallet_set = 
		"update {$g5['wallet_config']} set 
		fee = '{$fee}',
		amt_minimum = '{$amt_minimum}',
		amt_maximum = '{$amt_maximum}',
		day_limit = '{$day_limit}'
		where idx = $idx ;";
		
		//print_R($update_wallet_set);
		$result = sql_query($update_wallet_set);
		
	}
	
}


$coin_price_sql = "select * from {$g5['wallet_config']}";
$res = sql_query($coin_price_sql);
?>

<link rel="stylesheet" href="/adm/css/switch.css">

<style type="text/css">
	/* xmp {font-family: 'Noto Sans KR', sans-serif; font-size:12px;} */
	.adminWrp{padding:30px; min-height:50vh}
	
	table.regTb {width:100%;table-layout:fixed;border-collapse:collapse;}
	table.regTb th,
	table.regTb td {line-height:28px;}
	table.regTb th {padding: 6px 0;border: 1px solid #d1dee2;background: #e5ecef;color: #383838;letter-spacing: -0.1em;}
	table.regTb td {padding:8px 0;padding-left:10px;border-bottom:solid 1px #ddd;border-right:solid 1px #ddd;}
	table.regTb input[type="text"]{padding:3px;width:80%;padding-left:8px;height:23px;line-height:23px;border:solid 1px #ccc;background-color:#f9f9f9;}
	table.regTb textarea {padding:0;padding-left:8px;line-height:23px;border:solid 1px #ccc;background-color:#f9f9f9;}
	table.regTb label {cursor:pointer;}

	table.regTb input[type="radio"] {}
	table.regTb input[type="radio"] + label{color:#999;}
	table.regTb input[type="radio"]:checked + label {color:#e50000;font-weight:bold;}

	tfoot {
		clear:both;
		display: table-footer-group;
		vertical-align: middle;
		border-color: inherit;
	}
	span.help {font-size:11px;font-weight:normal;color:rgba(38,103,184,1);margin:5px 10px;}

	.name{background:#222437;color:white;font-weight:900}
	.text-center{text-align: center !important;}
	.currency{font-family:'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif; font-size:16px; font-weight:900;letter-spacing:1px; text-indent: 20%;}
	.currency-right{position:relative;float:right;right:25px;}
	.btn_ly{width:50%; min-height:80px; display:block; margin:20px auto; text-align:right;}
	.text-right{text-align:right;float:right;}

	.no{width:5%;text-align:center}

	#withdrawal td{background:floralwhite}
	#deposit td{background:ghostwhite}
</style>
 
<div class="adminWrp mb50">

<form name="site" method="post" action="" onsubmit="return frmnewwin_check(this);" style="margin:0px;">
	<span class='help text-right'>[ 0 이면 제한없음 ]</span>
	<table cellspacing="0" cellpadding="0" border="0" class="regTb">
		<thead>
        <colgroup>
			<th class='no'>IDX</th>
			<th>구분</th>
			<th>CODE</th>
			<th>수수료 (%)</th>
			<th>최소값 제한 (haz, $)</th>
			<th>최대값 제한 (%)</th>
			<th>출금회수제한(일)</th>
        </colgroup>
		</thead>

        <tbody>
			<? while($row = sql_fetch_array($res)){ ?>
				<tr id='<?=$row['function']?>'>
					<input type='hidden' name='w' value='u'/>
					<td class='no'><input type='hidden' name='idx[]' value='<?=$row['idx']?>'><?=$row['idx']?></td>
					<td ><?=$row['function_kor']?></td>
					<td ><?=$row['function']?></td>
					<td ><input type='text' name='fee[]' value="<?=$row['fee']?>"/></td>
					<td ><input type='text' name='amt_minimum[]' value="<?=$row['amt_minimum']?>"/></td>
					<td ><input type='text' name='amt_maximum[]' value="<?=$row['amt_maximum']?>"/></td>
					<td ><input type='text' name='day_limit[]' value="<?=$row['day_limit']?>"/></td>
				</tr>
			<?}?>
		</tbody>
	</table>
	
	<div class='btn_ly '>
		<!-- <button type='button' class="btn btn_wd btn_double blue" onclick="go_to_URL('/coin_price_curl.php?url=/adm/bonus/config_price.php');"> 코인 시세 수동 갱신</button> -->
		<input type="submit" name="submit" class="btn wd btn_submit" value="저장하기" />
	</div>
	
</form>
</div><!-- // adminWrp // -->


<script>

$(document).ready(function(){

	
});


function frmnewwin_check(f)
{
	alert('변경되었습니다.');
	return true;
}

</script>



<?
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>


