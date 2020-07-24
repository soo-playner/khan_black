<?php
$sub_menu = "600300";
include_once('./_common.php');
include_once('./bonus_inc.php');

auth_check($auth[$sub_menu], 'r');

$sql = "select * from {$g5['bonus_config']} where used = '1' order by idx";
$list = sql_query($sql);


// 수당검색 필터 
$allowcnt=0;
for ($i=0; $row=sql_fetch_array($list); $i++) {

	if($i != 0){
		$nnn="allowance_chk".$i;
		$html.= "<input type='checkbox' class='search_item' name='".$nnn."' id='".$nnn."'";

		if($$nnn !=''){
			$html.=" checked='true' ";
		}

		$html.=" value='".$row['code']."'><label for='".$nnn."' class='allow_btn'>". $row['name']."수당</label>";
	}

	if(${"allowance_chk".$i}!=''){
		if($allowcnt==0){
			$sql_search .= " and ( (allowance_name='".${"allowance_chk".$i}."')";
		}else{
			$sql_search .= "  or ( allowance_name='".${"allowance_chk".$i}."' )";
		}
			$qstr.='&'.$nnn.'='.$row['allowance_name'];

		$allowcnt++;
	}
}




if ($allowcnt>0) $sql_search .= ")";


$token = get_token();

$fr_date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3", $fr_date);
$to_date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3", $to_date);

$qstr.='&fr_date='.$fr_date.'&to_date='.$to_date.'&chkc='.$chkc.'&chkm='.$chkm.'&chkr='.$chkr.'&chkd='.$chkd.'&chke='.$chke.'&chki='.$chki;
$qstr.='&diviradio='.$diviradio.'&r='.$r;
$qstr.='&stx='.$stx.'&sfl='.$sfl;
$qstr.='&aaa='.$aaa;

$sql_common = " from {$g5['bonus']} where (1) ";

if(!$to_date){
	$to_date=date("Y-m-d");
	$fr_date=$to_date;
	
}

if(($allowance_name) ){
	$sql_search .= " and (";
		if($chkc){
		$sql_search .= " allowance_name='".$allowance_name."'";
		}
 $sql_search .= " )";
 
}/*else if($dv_gubun){
	 $sql_search .= " and dv_gubun='".$dv_gubun."'";
}
*/

if($_GET['start_dt']){
	$sql_search .= " and day >= '".$_GET['start_dt']."'";
	$qstr .= "&start_dt=".$_GET['start_dt'];
}
if($_GET['end_dt']){
	$sql_search .= " and day <= '".$_GET['end_dt']."'";
	$qstr .= "&end_dt=".$_GET['end_dt'];
}

if ($stx) {
    $sql_search .= " and ( ";
	if(($sfl=='mb_id') || ($sfl=='mb_id')){
            $sql_search .= " ({$sfl} = '{$stx}') ";
          
	}else{
            $sql_search .= " ({$sfl} like '%{$stx}%') ";
          
    }
    $sql_search .= " ) ";
}

$sql = " select count(*) as cnt
            {$sql_common}
            {$sql_search}
            {$sql_order} ";



$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함


$sql_order='order by day,no desc';


$sql = "select * 
		{$sql_common}
        {$sql_search}
        {$sql_order}
        limit {$from_record}, {$rows} ";
print_r($sql);
$result = sql_query($sql);


$send_sql = $sql;
$listall = '<a href="'.$_SERVER['PHP_SELF'].'" class="ov_listall">전체목록</a>';

$g5['title'] = '수당지급 및 지급내역';
include_once ('../admin.head.php');
include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');

$colspan = 16;

?>   
 


<style>

.date_input{height:16px; min-width:80px;font-size:16px;font-weight:600;padding:12px;color:red;border:1px solid black;}

.local_ov {height: auto;line-height: 36px;}
.local_ov li{list-style: none;margin-left:10px;display:inline-block;vertical-align:top}
.local_ov01 {position:inherit;}

.benefit{color:white;border:0;height:40px;cursor:pointer;text-align:center;padding:5px 20px;width:100%;}
.benefit.b1{background:cornflowerblue}
.benefit.b2,.benefit.b11{background:steelblue}
.benefit.b3{background:slateblue}
.benefit.b4{background:dodgerblue}
.benefit.b5{background:slategray}
.benefit.b6{background:teal}
.benefit.black{background:black}
.benefit.red{background:#ff3061}
.benefit.rblue{background:royalblue}
.benefit.pink{background:#ff3061}
.benefit.avatar{background:sienna}
.benefit:hover{background:black;}


.frm_input.date{padding-left:5px;width:100px;}
.view_btn{background:#ccc;color:;border:0;outline:0; height:30px;padding:5px 20px;}

.view_btn:hover{background:rgb(249, 166, 46);cursor:pointer}

.search_item{display:none}
.search_item + .allow_btn{border:1px solid #666; padding:5px 10px;margin-left:10px;cursor:pointer}
.search_item.active + .allow_btn{background:greenyellow;border:1px solid lightskyblue;}

.btn_submit.search{width:100px;}
.btn_submit.excel{width:80px;background:green}

.sysbtn{background:lightyellow;border-bottom:1px solid #ccc;display:block;height:25px;width:100%;text-align:right;padding-top:10px;margin-bottom:15px;}
.sysbtn .btn{margin:10px 0;padding:6px 10px;background:orange;font-size:11px;margin-right:30px;}
.sysbtn .btn.btn2{background:#e4eaec;}
.sysbtn .btn.btn3{background:pink}
.sysbtn .btn:hover{background:black;color:white;text-decoration: none;}

.right-border{padding-right:15px; border-right: 2px solid #333}
.left-border{padding-left:15px; border-left: 2px solid #333}
.outbox{}
.bonus{font-weight:600;text-align:right;padding-right:15px !important;color:#0072d1}
.adm{color:green}
.white{background:white;}
</style>










<link href="https://cdn.jsdelivr.net/npm/remixicon@2.3.0/fonts/remixicon.css" rel="stylesheet">
<div class="local_ov01 local_ov white">

	<?
	/*
    if (isset($mb['mb_id']) && $mb['mb_id']) {
    	  $row2 = sql_fetch(" select sum(dv_money) as sum_dividend_money ,  sum(dv_tax) as sum_dividend_tax,  sum(dv_money-dv_tax) as sum_dividend from dividend where dv_paid<>'R' and dv_paid<>'S' and mb_id='{$mb['mb_id']}' {$sql_search} ");
         echo '&nbsp;전체 합계:  총액('.number_format($row2['sum_dividend_money']).'원)  / 세금('.number_format($row2['sum_dividend_tax']).'원)  / 실 지급금('.number_format($row2['sum_dividend']).'원)';
    } else {
        $row2 = sql_fetch(" select sum(dv_money) as sum_dividend_money,  sum(dv_tax) as sum_dividend_tax,  sum(dv_money-dv_tax) as sum_dividend from dividend where dv_paid<>'R' and dv_paid<>'S' {$sql_search}");
        
        echo '&nbsp;전체 합계:  총액('.number_format($row2['sum_dividend_money']).'원)  / 세금('.number_format($row2['sum_dividend_tax']).'원)  / 실 지급금('.number_format($row2['sum_dividend']).'원)';
    }*/

	$ym=date('Y-01-01');

    ?>

		<!--<input type="text" id="noo_start_day"  name="noo_start_day" value="<?=$ym?>" class="frm_input" size="12" maxlength="10"> &nbsp;|&nbsp;-->
		<li class="right-border outbox{">
        <label for="to_date" class="sound_only">기간 종료일</label>
        <input type="text" name="to_date" value="<?php if($to_date){echo $to_date; }else{echo date("Ymd");} ?>" id="to_date" required class="required frm_input date_input" size="13" maxlength="10"> 
		
			<!--<input type="checkbox" name="save_noo_mon" id="save_noo_mon" value="1">매출정산+즉시계산--> 
		<input type="radio" name="price" id="pv" value='pv' checked='true' style="display:none;"><!--&nbsp;|&nbsp; BV<input type="radio" name="price" id="bv" value='bv' >&nbsp;|&nbsp;판매가<input type="radio" name="price" id="receipt" value='receipt'>&nbsp; -->
			<!--<input type="checkbox" name="save_noo_mon" id="save_benefit" value="1">정산된 매출로 수당계산만&nbsp;&nbsp;&nbsp; -->
			<br>	<span >수당계산 기준일자</span>
		</li>
	



	<?
	$sql = "select * from {$g5['bonus_config']} where used != '0' order by idx";
	$list = sql_query($sql);

	for($i=0; $row = sql_fetch_array($list); $i++ ){?>
		<?
			$code = $row['code'];	
			if($i != 0){
		?>
		
			<?if($code != 'rank'){?>
				<li class='outbox'>
				<input type='submit' name="act_button" value="<?=$row['name']?> 수당 지급"  class="frm_input benefit b<?=$row['idx']?>" onclick="bonus_excute('<?=$code?>');">
				<br><input type="submit" name="act_button" value="<?=$row['name']?> 수당 내역"  class="view_btn" onclick="bonus_view('<?=$code?>');">
				</li>
			<?}else{?>
				<li class='outbox left-border'>
				<button type='button' name="act_button"  class="frm_input benefit b<?=$row['idx']?>" onclick="bonus_excute('<?=$code?>');"> <i class="ri-medal-fill" style='font-size:16px; vertical-align:sub'></i> 직급 승급 </button>
				<br><input type="submit" name="act_button" value="회원 <?=$row['name']?> 내역"  class="view_btn" onclick="bonus_view('<?=$code?>');">
				</li>
			<?}?>
		
		<?}?>
	<?}?>

	<li class="outbox left-border">
		<input type="submit" name="act_button" value=" 수당지급 취소(되돌리기)"  class="frm_input benefit red" onclick="bonus_cancle();">
	</li>
</div>


<!--
<?if($member['mb_id'] = 'admin'){?>
<div class="sysbtn">
	
	수동관리 :: 
	<a href="./member_grade.php" class="btn btn2" >멤버 등급(grade) 수동 갱신</a>
	
	<a href="#" class="btn btn2" onclick="clear_db('balance');">멤버 수당,V7,매출전환,level 초기화(출금,전환 제외)</a>
	<a href="#" class="btn btn2" onclick="clear_db('amt');">멤버 출금, 전환 내역 초기화</a>-->
	<!--<a href="#" class="btn btn3" onclick="clear_db('pack_order');">B팩,Q팩 구매 DB 초기화</a>
	<a href="#" class="btn btn2" onclick="clear_db('soodang');">수당지급 내역 전체 초기화</a>
	
</div>
<?}?>
-->


<form name="fsearch" id="fsearch" class="local_sch01 local_sch" style="clear:both" method="get">
	<label for="sfl" class="sound_only">검색대상</label>
	<select name="sfl" id="sfl">
		<option value="mb_id"<?php echo get_selected($_GET['sfl'], "mb_id"); ?>>회원아이디</option>>
		<option value="mb_name"<?php echo get_selected($_GET['sfl'], "mb_name"); ?>>회원이름</option>
	</select>

	<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
	<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">
	| 검색 기간 : <input type="text" name="start_dt" id="start_dt" placeholder="From" class="frm_input" value="<?=$_GET['start_dt']?>" /> 
	~ <input type="text" name="end_dt" id="end_dt" placeholder="To" class="frm_input" value="<?=$_GET['end_dt']?>"/>
	||
	<?
	echo $html;
	?>
	||
	<input type="submit" class="btn_submit search" value="검색"/>
	<input type="button" class="btn_submit excel" value="엑셀" onclick="document.location.href='benefit_list_excel_out.php?<?echo $qstr?>'" />	
	<br/>
</form>


<form name="benefitlist" id="benefitlist">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="<?php echo $token ?>">
<div class="local_ov01 ">
    <?php echo $listall ?>
    전체 <?php echo number_format($total_count) ?> 건 
</div>
<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
		<th scope="col">수당날짜</th>
		<th scope="col">회원아이디</a></th>
		<th scope="col">회원지갑</a></th>
		<th scope="col">회원등급</a></th>
		<th scope="col">현재예치금 (<?=BONUS_CURENCY?>)</a></th>	
        <th scope="col">수당이름</th>
        <th scope="col">발생수당 (<?=BONUS_CURENCY?>)</a></th>	
		<th scope="col">수당근거</a></th>				
    </tr>
    </thead>
    <tbody>

	<?php
	for ($i=0; $row=sql_fetch_array($result); $i++) {
		$bg = 'bg'.($i%2);
		$soodang = $row['benefit'];
		$soodang_sum += $soodang;
	?>

    <tr class="<?php echo $bg; ?>">
		<td width='100'><? echo $row['day'];?></td>
		<td width="100" style='text-align:center'>
			<a href='/adm/member_form.php?w=u&mb_id=<?=$row['mb_id']?>'><?php echo get_text($row['mb_id']); ?></a>
		</td>
		<td width='80'><?= get_member_level($row['mb_name'])?></td>
		<td width='50' style='text-align:center'><?= get_member_level($row['grade'])?></td>
		<td width="100" style='text-align:center'><?= $row['origin_deposit'] ?></td>
		<td width='80' style='text-align:center'><?php echo get_text($row['allowance_name']); ?></td>
		<td width="100" class='bonus'><?php echo Number_format($soodang,2)  ?></td>
		<td width="500"><?= $row['rec']." <span class='adm'> [".$row['rec_adm']."]</span>" ?></td>
		
    </tr>

    <?php
    }

    if ($i == 0)
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">자료가 없습니다.</td></tr>';
    ?>
    </tbody>
	<tfoot>
	<tr class="<?php echo $bg; ?>">
		<td colspan=6>TOTAL :</td>
		<td width="150" class='bonus' style='color:red'><?=number_format($soodang_sum,2)?></td>
        <td ></td>
    </tr>
	</tfoot>
    </table>
</div>


</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qstr&amp;page="); ?>



    <div class="btn_confirm01 btn_confirm">
    	<? if($what=='u') { ?>  <input type="submit" id="submit" value="수정" class="btn_submit"> <? } else{  ?> <input type="submit" id="submit" value="등록" class="btn_submit">   <? } ?>
    </div>

    </form>

</section>


<!--<script type="text/javascript" src="/adm/js/prototype.js"></script>-->
<script type="text/javascript" src="/js/common.js"></script>


<script>
var str ='';
$(function(){
	$("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
	$("#start_dt, #end_dt").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });

	$('.search_item:checked').each(function() {
		$(this).addClass('active');
	});
	
	$('.search_item').on('click',function(){
		var chk = $(this).is(":checked");
		if(chk){
			$(this).addClass('active');
		}else{
			$(this).removeClass('active');
		}
	});
});



function UrlExists(url)
{
    var http = new XMLHttpRequest();
    http.open('HEAD', url, false);
    http.send();
    return http.status!=404;
}

function bonus_excute(n){
	console.log("bonus_excute");
	str=str+'&to_date='+document.getElementById("to_date").value;
	location.href='/adm/bonus/bonus.'+ n +'.php?'+str; 
}


function bonus_view(n){
	console.log("bonus_view");
	var strdate = document.getElementById("to_date").value;
	file_src = n+"_"+strdate+".html";
	file_path = g5_url+"/data/log/"+n+"/"+file_src ; //롤다운
	console.log(file_path);
	
	if(UrlExists(file_path)){
		window.open(file_path); 
	}else{
		alert('해당내역이 없습니다.');
	}
	
}

function bonus_cancle(){
	
	date = document.getElementById("to_date").value;
	
	var pre = confirm(date+' 수당지급 전으로 되돌립니다.');

    if(pre == true){
	 $.ajax({ 
          type : "POST", 
          url : "./bonus_cancle.php", 
          data:{to_date : date},
          error : function() { 
              alert('실패!!'); 
         }, 
         success : function(data) { 
            alert(data);
			location.reload();
        } 
    }); 
    }else{
        return;
	}
}




/* 하단 스크립트 사용안함 */

	// function go_calc(n)
	// {
	// 	if(document.getElementById("pv").checked==true){

	// 		str=str+'&price=pv';
				
	// 	}else if(document.getElementById("bv").checked==true){
	// 		str=str+'&price=bv';
	// 	}else{
	// 		str=str+'&price=receipt';
	// 	}

	// 	var day_point = document.getElementById("to_date").value;

	// 	str=str+'&to_date='+document.getElementById("to_date").value;
	// 	str=str+'&fr_date='+document.getElementById("to_date").value;

		
		
	// 	switch(n){
	// 		case 0: 
	// 			location.href='bonus.daily.pay.php?'+str;         //일일수당
	// 			break;
	// 		case 1: 
	// 			location.href='bonus.benefit.immediate.php?'+str;// 추천수당
	// 			break;
	// 		case 2: 
	// 			location.href='bonus.member.level.php?'+str;// 멤버승급
	// 			break;

	// 		case 3: 
	// 			location.href='bonus.qpack.php?'+str;// Q팩
	// 			break;
	// 		case 4:
	// 			location.href='bonus.bpack.php?'+str;// B팩
	// 			break;
	// 		case 5: 
	// 			location.href='bonus.upstair.php?'+str;         //임시 해당일 업스테어
	// 			break;
	// 		case 6: 
	// 			location.href='bonus.all.php?'+str;         //전체수당지급
	// 			break;
	// 		case 7: 
	// 			location.href='bonus.auto.php?'+str;         //전체수당지급
	// 			break;
	// 		case 8: 
	// 			location.href='bonus.Bpack_auto.php?'+str;         //B팩
	// 			break;
	// 		case 9: 
	// 			location.href='bonus.avatar_exc.php?'+str;         //아바타
	// 			break;
	// 	}
		
	// }


	// function view_calc(n)
	// {
	// 	var day = document.getElementById("to_date").value;

	// 	switch(n){
	// 		case 0:
	// 			       //일배당
	// 			break;
	// 		case 1:
	// 			file_src = g5_url+"/log/roledown/roledown_"+day+".html"; //롤다운

	// 			if(UrlExists(file_src)){
	// 				window.open(file_src); 
	// 			}else{
	// 				alert('해당내역이 없습니다.');
	// 			}
	// 			break;
	// 		case 2:
	// 			file_src = g5_url+"/log/binary/binary_"+day+".html"; //바이너리

	// 			if(UrlExists(file_src)){
	// 				window.open(file_src); 
	// 			}else{
	// 				alert('해당내역이 없습니다.');
	// 			}
	// 			break;
				
	// 		case 3:
				
	// 			break;
	// 		case 4:
	// 			file_src = g5_url+"/log/team/team_"+day+".html"; //롤다운

	// 			if(UrlExists(file_src)){
	// 				window.open(file_src); 
	// 			}else{
	// 				alert('해당내역이 없습니다.');
	// 			}
	// 			break;
	// 		case 5:
	// 			file_src0 = g5_url+"/log/recom/binary_recom_"+day+"_0.html"; //바이너리 매칭
	// 			file_src1 = g5_url+"/log/recom/binary_recom_"+day+"_1.html"; //바이너리 매칭		
				
	// 			console.log(file_src0);

	// 			if(UrlExists(file_src0)){
	// 				window.open(file_src0); 
	// 			}else{
	// 				if(UrlExists(file_src1)){
	// 					window.open(file_src1); 
	// 				}else{
	// 					alert('해당내역이 없습니다.');
	// 				}
	// 			}
	// 			break;

	// 		case 6:
	// 			file_src2 = g5_url+"/log/recom/binary_recom_"+day+"_2.html"; //바이너리 매칭

	// 			console.log(file_src2);

	// 			if(UrlExists(file_src2)){
	// 				window.open(file_src2); 
	// 			}else{
	// 				alert('해당내역이 없습니다.');
	// 			}
	// 			break;
	// 	}

// }
</script>

<?php
include_once ('../admin.tail.php');
?>
