<?php
$sub_menu = "600080";
include_once('./_common.php');

$g5['title'] = "통화 시세 설정";

include_once(G5_ADMIN_PATH.'/admin.head.php');

$coin_price_sql = "select * from {$g5['coin_price']}";
$res = sql_query($coin_price_sql);
?>

<link rel="stylesheet" href="/adm/css/switch.css">

<style type="text/css">
	/* xmp {font-family: 'Noto Sans KR', sans-serif; font-size:12px;} */
	.adminWrp{padding:30px; min-height:50vh}
	input[type="radio"] {}
	input[type="radio"] + label{color:#999;}
	input[type="radio"]:checked + label {color:#e50000;font-weight:bold;font-size:14px;}

	table.regTb {width:100%;table-layout:fixed;border-collapse:collapse;}
	table.regTb th,
	table.regTb td {line-height:28px;}
	table.regTb th {padding: 6px 0;border: 1px solid #d1dee2;background: #e5ecef;color: #383838;letter-spacing: -0.1em;}
	table.regTb td {padding:8px 0;padding-left:10px;border-bottom:solid 1px #ddd;border-right:solid 1px #ddd;}
	table.regTb input[type="text"],
	table.regTb input[type="password"] {padding:0;padding-left:8px;height:23px;line-height:23px;border:solid 1px #ccc;background-color:#f9f9f9;}
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
	span.help {font-size:11px;font-weight:normal;color:rgba(38,103,184,1);}

	.name{background:#222437;color:white;font-weight:900}
	.text-center{text-align: center !important;}
	.currency{font-family:'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif; font-size:16px; font-weight:900;letter-spacing:1px; text-indent: 20%;}
	.currency-right{position:relative;float:right;right:25px;}
	.btn_ly{width:50%; min-height:80px; display:block; margin:20px auto; text-align:right;}

</style>
 
<div class="adminWrp mb50">

<form name="site" method="post" action="./config_price.proc.php" onsubmit="return frmnewwin_check(this);" style="margin:0px;">

	<table cellspacing="0" cellpadding="0" border="0" class="regTb">
		<thead>
        <colgroup>
			<th>통화</th>
			<th>기준</th>
			<th>자동 시세 비율 (12h/day)</th>
			<th>자동 시세 반영시간(서버시간)</th>
			<th>수동 설정 사용유무</th>
			<th>수동 설정 비율값</th>
        </colgroup>
		</thead>

        <tbody>
			<? while($row = sql_fetch_array($res)){ ?>
				<tr>
					<input type='hidden' name='idx[]' value='<?=$row['idx']?>'>
					<td class='name'><?=$row['name']?></td>
					<td class='text-center'>1 <?=strtoupper($row['symbol'])?> / $</td>
					<td class='currency'><span style='color:crimson'><?=$row['current_cost'];?></span> <span class='currency-right'>$</span></td>	
					<td><?=$row['update_time'];?> </td>
					<td> 
						<p style="padding:0;">
							<input type="checkbox" id="<?=$row['symbol']?>_use" name="manual_use[]" class="nw_with" value='<?=$row['manual_use']?>' <?if($row['manual_use'] == 1) {echo "checked";}?>/>
							<label for="<?=$row['symbol']?>_use"><span class="ui"></span><span class="nw_with_txt">사용 설정</span></label>
						</p>
					</td>

					<td><input type="text" name="manual_cost[]" value="<? echo $row['manual_cost'];?>" style="width:80%;"/></td>
				</tr>
			<?}?>
		</tbody>
	</table>
	
	<div class='btn_ly '>
		<button type='button' class="btn btn_wd btn_double blue" onclick="go_to_URL('/coin_price_curl.php?url=/adm/bonus/config_price.php');"> 코인 시세 수동 갱신</button>
		<input type="submit" name="submit" class="btn btn_wd btn_double btn_submit" value="저장하기" />
	</div>
	
</form>
</div><!-- // adminWrp // -->


<script>

$(document).ready(function(){

	$('.nw_with').on('click',function(){

		if($(this).is(":checked")){
			//$(this).attr("checked", true);
			$(this).val('1');
			$(this).parent().find('.nw_with_txt').html('사용함');
		}else{
			//$(this).attr("checked",false);
			$(this).val('2');
			$(this).parent().find('.nw_with_txt').html('사용안함');
		}
	});
});


function frmnewwin_check(f)
{
	
	$('input[type=checkbox]').each(function() {
		if(this.value == "2"){ //값 비교
			this.checked = true; //checked 처리
			//console.log(this.value);
		}
	});

	return true;

}

</script>



<?
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>


