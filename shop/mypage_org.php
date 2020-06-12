<?php
include_once('./_common.php');

if (!$is_member)
    goto_url(G5_BBS_URL."/login.php?url=".urlencode(G5_SHOP_URL."/mypage.php"));

if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/mypage_org.php');
    return;
}

// 테마에 mypage.php 있으면 include
if(defined('G5_THEME_SHOP_PATH')) {
    $theme_mypage_file = G5_THEME_SHOP_PATH.'/mypage.php';
    if(is_file($theme_mypage_file)) {
        include_once($theme_mypage_file);
        return;
        unset($theme_mypage_file);
    }
}

$g5['title'] = $member['mb_name'].'님 마이페이지';
include_once('./_head.php');

if ($gubun=="B"){
	$class_name     = "g5_member_bclass";
	$recommend_name = "mb_brecommend";
}else{
	$class_name     = "g5_member_class";
	$recommend_name = "mb_recommend";
}


// 쿠폰
$cp_count = 0;
$sql = " select cp_id
            from {$g5['g5_shop_coupon_table']}
            where mb_id IN ( '{$member['mb_id']}', '전체회원' )
              and cp_start <= '".G5_TIME_YMD."'
              and cp_end >= '".G5_TIME_YMD."' ";
$res = sql_query($sql);

for($k=0; $cp=sql_fetch_array($res); $k++) {
    if(!is_used_coupon($member['mb_id'], $cp['cp_id']))
        $cp_count++;
}


if ($_GET[go]=="Y"){
	goto_url("mypage_org.php?gubun=".$gubun."#org_start");
	exit;
}
?>
<p class="blk" style="height:60px;"></p>
<div class="pk_page">
<style type="text/css">
.pk_page {font-size:14px;}
span.btn,
a.btn {display:inline-block;*display:inline;*zoom:1;height:33px;line-height:33px;padding:0 15px;border-radius:3px;background-color:#1DC2BB;color:#fff;}
.infoBx {border:solid 2px rgba(39,48,62,0.4);border-radius:8px;margin-bottom:30px;}
.infoBx h3 {line-height:40px;font-size:15px;padding-left:20px;border-bottom:solid 1px rgba(0,0,0,0.1);background-color:rgba(39,48,62,0.05);}
.infoBx ul {margin:15px;}
.infoBx ul li {display:inline-block;*display:inline;*zoom:1;width:33%;line-height:40px;font-size:14px;color:#777;border-bottom:solid 1px #fff;}
.infoBx ul li.prc {color:rgba(59,105,178,1);}
.infoBx ul li span {display:inline-block;*display:inline;*zoom:1;color:#000;padding-left:20px;width:100px;background-color:rgba(39,48,62,0.05);margin-right:20px;}
</style>

<?
include_once('../adm/inc.member.class.php');



$sql  = "select count(*) as cnt from g5_member";
$mrow = sql_fetch($sql);

$sql = "select * from g5_member_class_chk where mb_id='".$member['mb_id']."' and  cc_date='".date("Y-m-d",time())."' order by cc_no desc";
$row = sql_fetch($sql);

if ($mrow['cnt']>$row['cc_usr'] || !$row['cc_no'] || $_GET["reset"]){

	$sql = "delete from g5_member_class where mb_id='".$member['mb_id']."'";
	sql_query($sql);

	get_recommend_down($member['mb_id'],$member['mb_id'],'11');

	$sql  = " select * from g5_member_class where mb_id='{$member['mb_id']}' order by c_class asc";	
	$result = sql_query($sql);
	for ($i=0; $row=sql_fetch_array($result); $i++) { 
		$row2 = sql_fetch("select count(c_class) as cnt from g5_member_class where  mb_id='".$member['mb_id']."' and c_class like '".$row['c_class']."%'");
		$sql = "update g5_member set mb_child='".$row2['cnt']."' where mb_id='".$row['c_id']."'";
		sql_query($sql);
	}

	$sql = "insert into g5_member_class_chk set mb_id='".$member['mb_id']."',cc_date='".date("Y-m-d",time())."',cc_usr='".$mrow['cnt']."'";
	sql_query($sql);

}

$sql = "select * from g5_member_bclass_chk where mb_id='".$member['mb_id']."' and  cc_date='".date("Y-m-d",time())."' order by cc_no desc";
$row = sql_fetch($sql);

if ($mrow['cnt']>$row['cc_usr'] || !$row['cc_no'] || $_GET["reset"]){

	$sql = "delete from g5_member_bclass where mb_id='".$member['mb_id']."'";
	sql_query($sql);

	get_brecommend_down($member['mb_id'],$member['mb_id'],'11');

	$sql  = " select * from g5_member_bclass where mb_id='{$member['mb_id']}' order by c_class asc";	
	$result = sql_query($sql);
	for ($i=0; $row=sql_fetch_array($result); $i++) { 
		$row2 = sql_fetch("select count(c_class) as cnt from g5_member_bclass where  mb_id='".$member['mb_id']."' and c_class like '".$row['c_class']."%'");
		$sql = "update g5_member set mb_b_child='".$row2['cnt']."' where mb_id='".$row['c_id']."'";
		sql_query($sql);
	}

	$sql = "insert into g5_member_bclass_chk set mb_id='".$member['mb_id']."',cc_date='".date("Y-m-d",time())."',cc_usr='".$mrow['cnt']."'";
	sql_query($sql);


	if ($_GET["reset"]){
		goto_url("mypage_org.php?gubun=".$gubun."&sfl=".$sfl."&stx=".$stx."&gubun=".$gubun);
		exit;		
	}
}

if ($mb_org_num){
	$sql = "update g5_member set mb_org_num='".$mb_org_num."' where mb_id='".$member['mb_id']."'";
	sql_query($sql);	
	$member['mb_org_num'] = $mb_org_num;
}


?>
<style type="text/css">
	#div_right table { border:0px }
	.btn_menu {padding:5px;border:1px solid #ced9de;background:rgb(246,249,250);cursor:pointer}
</style>
<div style="padding:0px 0px 0px 10px;">
	<a name="org_start"></a>
	<div style="float:left">
	<input type="button" class="btn_menu" value="검색메뉴닫기" onclick="btn_menu()">
	<input type="button" class="btn_menu" value="전체 조직도보기" onclick="location.href='mypage_org.php?gubun=<?=$gubun?>&go=Y'">
	<input type="button" class="btn_menu" style="background:#fadfca" value="신규회원등록" onclick="open_register()">
	</div>
	<div style="float:right;padding-right:10px">
	<input type="button" class="btn_menu" value="조직도 재구성" onclick="btn_org()">
	</div>
</div>
<div style="padding-top:10px;clear:both"></div>
<div id="div_left" style="width:15%;float:left;min-height:710px;border:">
	<div style="margin-left:10px;padding:5px 5px 5px 5px;border:1px solid #d9d9d9;height:100%">
<?
if (!$fr_date) $fr_date = Date("Y-m-d", time()-60*60*24*365);
if (!$to_date) $to_date = Date("Y-m-d", time());
?>
		<form name="sForm2" id="sForm2" method="get" action="mypage_org.php">
		<input type="hidden" name="now_id" id="now_id" value="<?=$now_id?>">

		<table width="100%">
			<tr>
				<td bgcolor="#f2f5f9" height="30" style="padding-left:10px">
				<div style="float:left">
				<b>표시인원</b>
				</div>
				<div style="float:right">
				<input type="text" id="mb_org_num"  name="mb_org_num" value="<?php echo $member['mb_org_num']; ?>" class="frm_input" style="width:40px;text-align:center" size="3" maxlength="3"> 명 &nbsp;
				</div>
				</td>
			</tr>
			<tr>
				<td bgcolor="#f2f5f9" height="20" style="padding:10px 10px 10px 10px" align=left>
				<input type="radio" id="gubun" name="gubun" onclick="document.sForm2.submit();" value=""<?if ($gubun=="") echo " checked"?>> 추천인 <br>
				<input type="radio" id="gubun" name="gubun" onclick="document.sForm2.submit();" value="B"<?if ($gubun=="B") echo " checked"?>> 바이너리레그

				</td>
			</tr>
			<tr>
				<td bgcolor="#f2f5f9" height="30" style="padding-left:10px"><b>매출기간</b></td>
			</tr>
			<tr>
				<td bgcolor="#f2f5f9" height="30" style="padding:10px 10px 10px 10px" align=center>
				<input type="text" id="fr_date"  name="fr_date" value="<?php echo $fr_date; ?>" class="frm_input" style="width:100%" size="10" maxlength="10"> ~
				<input type="text" id="to_date"  name="to_date" value="<?php echo $to_date; ?>" class="frm_input" style="width:100%" size="10" maxlength="10">

				</td>
			</tr>
			<tr>
				<td bgcolor="#f2f5f9" height="30" align="center">
				<input type="submit"  class="btn_submit" style="padding:5px" value=" 적 용 ">
				</td>
			</tr>
		</table>
		</form>
		<div id="div_member"></div>
		<form name="sForm" id="sForm" method="post" style="padding-top:10px" onsubmit="return false;">
		<table width="100%">
			<tr>
				<td bgcolor="#f2f5f9" height="30" style="padding-left:10px"><b>회원검색</b></td>
			</tr>
			<tr>
				<td bgcolor="#f2f5f9" height="30" style="padding:10px 10px 10px 10px">
				
				<select name="sfl" id="sfl">
					<option value="mb_name"<?php echo get_selected($_GET['sfl'], "mb_name"); ?>>이름</option>
					<option value="mb_id"<?php echo get_selected($_GET['sfl'], "mb_id"); ?>>회원아이디</option>
				</select>
				<div style="padding-top:5px">
				<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
				<input type="text" name="stx" value="<?php echo $stx ?>" id="stx"  class="required frm_input" style="width:100%;" onkeypress="event.keyCode==13?btn_search():''">
				</div>
				</td>
			</tr>
			<tr>
				<td bgcolor="#f2f5f9" height="30" align="center">
				<input type="button" onclick="btn_search();" class="btn_submit" style="padding:5px" value=" 검 색 ">
				</td>
			</tr>
		</table>
		</form>

		<div id="div_result" style="margin-top:5px;overflow-y: auto;height:418px">

		</div>
	</div>
</div>
  <link type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/themes/base/jquery-ui.css" rel="stylesheet" />
  <link rel="stylesheet" href="/adm/css/font-awesome.min.css">
  <link rel="stylesheet" href="/adm/jquery.orgchart.css">
  <script type="text/javascript" src="/adm/jquery.orgchart.js"></script>
  <script type="text/javascript" src="/adm/js/bluebird.min.js"></script>
  <script type="text/javascript" src="/adm/js/html2canvas.min.js"></script>
  <script type="text/javascript" src="/adm/js/jspdf.min.js"></script>
  <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/jquery-ui.min.js"></script>
<script>
    $.datepicker.regional["ko"] = {
        closeText: "close",
        prevText: "이전달",
        nextText: "다음달",
        currentText: "오늘",
        monthNames: ["1월(JAN)","2월(FEB)","3월(MAR)","4월(APR)","5월(MAY)","6월(JUN)", "7월(JUL)","8월(AUG)","9월(SEP)","10월(OCT)","11월(NOV)","12월(DEC)"],
        monthNamesShort: ["1월","2월","3월","4월","5월","6월", "7월","8월","9월","10월","11월","12월"],
        dayNames: ["일","월","화","수","목","금","토"],
        dayNamesShort: ["일","월","화","수","목","금","토"],
        dayNamesMin: ["일","월","화","수","목","금","토"],
        weekHeader: "Wk",
        dateFormat: "yymmdd",
        firstDay: 0,
        isRTL: false,
        showMonthAfterYear: true,
        yearSuffix: ""
    };
	$.datepicker.setDefaults($.datepicker.regional["ko"]);

</script>
<style type="text/css">

.orgChart{

}

#chart-container {
margin:0px 10px 0px 10px;
min-height:573px;
border:1px solid #d9d9d9;
text-align:center !important;
  position: relative;
  display: inline-block;
  width: calc(100% - 24px);
  overflow: auto;
  text-align: center;
}

.orgchart .node {
  box-sizing: border-box;
  display: inline-block;
  position: relative;
  margin: 0;
  padding: 3px;
  height:142px;
  text-align: center;
  width: 100px;
}

.orgchart .node .title {
	background:#fff;
	border:2px solid #e17572;
	color:#000;
	height:122px;
	font-weight:normal;
	line-height:15px;
	padding-top:5px;
	cursor:pointer;
}
.orgchart .node .title .symbol{
	display:none;
}

</style>
<div id="div_right" style="width:85%;float:left;min-height:500px">
<!--  -->

<?
if ($now_id){
	$go_id = $now_id;
}else{
	$go_id = $member['mb_id'];
}

if ($member['mb_org_num']){
	$max_org_num = $member['mb_org_num'];
}else{
	$max_org_num = 50;
}
$org_num     = 0;

$sql = "select c.c_id,c.c_class,(select mb_level from g5_member where mb_id=c.c_id) as mb_level,(select pool_level from g5_member where mb_id=c.c_id) as pool_level,(select mb_name from g5_member where mb_id=c.c_id) as c_name,(select count(*) from g5_member where mb_recommend=c.c_id) as c_child,(select mb_b_child from g5_member where mb_id=c.c_id) as b_child,(select mb_id from g5_member where mb_brecommend=c.c_id and mb_brecommend_type='L') as b_recomm,(select mb_id from g5_member where mb_brecommend=c.c_id and mb_brecommend_type='R') as b_recomm2,(select count(mb_no) from g5_member where ".$recommend_name."=c.c_id and mb_leave_date = '') as m_child from g5_member m join ".$class_name." c on m.mb_id=c.mb_id where c.mb_id='{$member['mb_id']}' and c.c_id='$go_id'";

$srow = sql_fetch($sql);

$sql  = "select sum(od_receipt_price) as tprice,sum(pv) as tpv from g5_shop_order where mb_id='".$srow['c_id']."' and od_time between '$fr_date 00:00:00' and '$to_date 23:59:59'";
$row2 = sql_fetch($sql);

$sql  = "select ".$order_field." as tpv from g5_shop_order where mb_id in (select c_id from ".$class_name." where mb_id='".$member['mb_id']."' and c_id<>'".$srow['c_id']."' and c_class like '".$srow['c_class']."%') and od_receipt_time between '$fr_date 00:00:00' and '$to_date 23:59:59'";
$row3 = sql_fetch($sql);

//이전 30일
$sql  = "select ".$order_field." as tpv from g5_shop_order where mb_id in (select c_id from ".$class_name." where mb_id='".$member['mb_id']."' and c_id<>'".$srow['c_id']."' and c_class like '".$srow['c_class']."%') and od_receipt_time between '".Date("Y-m-d",time()-(60*60*24*30))." 00:00:00' and '".Date("Y-m-d",time())." 23:59:59'";
$row5 = sql_fetch($sql);

//바이너리 왼쪽 오늘 매출
if ($srow['b_recomm']){
	$sql  = "select ".$order_field." as tpv from g5_shop_order where mb_id ='".$srow['b_recomm']."' and od_receipt_time between '".Date("Y-m-d",time())." 00:00:00' and '".Date("Y-m-d",time())." 23:59:59'";
	$row6 = sql_fetch($sql);

	$sql  = "select ".$order_field." as tpv from iwol where mb_id ='".$srow['b_recomm']."'";
	$row8 = sql_fetch($sql);

	$row6['tpv'] += $row8['tpv'];
}else{
	$row6['tpv'] = 0;
}

//바이너리 오른쪽 오늘 매출
if ($srow['b_recomm2']){
	$sql  = "select ".$order_field." as tpv from g5_shop_order where mb_id ='".$srow['b_recomm2']."' and od_receipt_time between '".Date("Y-m-d",time())." 00:00:00' and '".Date("Y-m-d",time())." 23:59:59'";
	$row7 = sql_fetch($sql);

	$sql  = "select ".$order_field." as tpv from iwol where mb_id ='".$srow['b_recomm2']."'";
	$row9 = sql_fetch($sql);
	$row7['tpv'] += $row9['tpv'];
}else{
	$row7['tpv'] = 0;
}

$sql    = "select c_class from ".$class_name." where mb_id='".$member['mb_id']."' and c_id='".$go_id."'";
$row4   = sql_fetch($sql);
$mdepth = (strlen($row4['c_class'])/2);

if (!$srow['b_child']) $srow['b_child']=1;
//if (!$srow['c_child']) $srow['c_child']=1;
?>
		<ul id="org" style="display:none" >
			<li>
				[<?=(strlen($srow['c_class'])/2)-1?>-<?=($srow['c_child'])?>-<?=($srow['b_child']-1)?>]|<?=get_member_label($srow['mb_level'])?>|<?=$srow['c_id']?>|<?=$srow['c_name']?>|<?=number_format($row3['tpv']/$order_split)?>|<?=number_format($row5['tpv']/$order_split)?>|<?=$srow['mb_level']?>|<?=number_format($row6['tpv']/$order_split)?> - <?=number_format($row7['tpv']/$order_split)?>|<?=$srow[pool_level]?>
<?
			get_org_down($srow);
?>
			</li>
<?
?>
	</ul>

    <div id="chart-container" class="orgChart"></div>
    <script>
    $(function() {
      $('#chart-container').orgchart({
        'data' : $('#org'),
		 'zoom': true
		});

    });
    </script>


</div>

<script type="text/javascript">
<!--

$(document).ready(function(){
	$("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
	<?if ($stx && $sfl){?>
		btn_search();
	<?}?>
});
function go_member(go_id){
	$("#now_id").val(go_id);
	$.get("ajax_get_up_member.php?gubun=<?=$gubun?>&go_id="+go_id, function (data) {

		data = $.trim(data);
		temp = data.split("|");

		data2 = "<table style='width:100%'>";
		data2 += "			<tr>";
		data2 += "				<td bgcolor='#f9f9f9' height='30' style='padding-left:10px'><b>상위 회원</b></td>";
		data2 += "			</tr>";
		for(i=(temp.length-1);i>=0;i--){
			data2 += temp[i];
		}
		
		data2 += "</table>";

		$('#div_member').html(data2);
		//$('#div_member').html(data);
		$.get("ajax_get_org_load.php?gubun=<?=$gubun?>&fr_date=<?=$fr_date?>&to_date=<?=$to_date?>&go_id="+go_id, function (data) {
			$('#div_right').html(data);
		});
	});

/*

*/
}
function set_member(set_id,set_type){
	window.open('/shop/recommend_set.php?set_id='+set_id+'&set_type='+set_type, 'set_recomm', 'width=520, height=500, resizable=no, scrollbars=yes, left=0, top=0');
}
function open_register(){
	window.open('/shop/recommend_register.php?gp=mo&now_id='+$("#now_id").val(), 'set_register', 'width=600, height=500, resizable=no, scrollbars=no, left=0, top=0');
}

function edit_member(edit_id){
	go_member(edit_id);
}
function btn_print(){

	var html = $('#chart-container');

	var strHtml = '<!doctype html><html lang="ko"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><meta http-equiv="imagetoolbar" content="no" /><title></title><link rel="stylesheet" type="text/css" media="all" href="jquery.orgchart.css"><link rel="stylesheet" type="text/css" media="all" href="chart.css"></';
	strHtml += 'head><body style="padding:0px;margin:0px;"><div id="chart-container" class="orgChart"><!--body--></div></body></html>';
	var strContent = html.html();
	var objWindow = window.open('', 'print', 'width=640, height=800, resizable=yes, scrollbars=yes, left=0, top=0');
	if(objWindow)
	{
		 var strSource = strHtml;
		 strSource  = strSource.replace(/\<\!\-\-body\-\-\>/gi, strContent);

		 objWindow.document.open();
		 objWindow.document.write(strSource);
		 objWindow.document.close();

		 setTimeout(function(){ objWindow.print(); }, 500);
	}

}
function btn_menu(){
	if($("#div_left").css("display") == "none"){ 
		$("#div_left").show();
		$("#div_right").css("width","85%");
	} else { 
		$("#div_left").hide(); 
		$("#div_right").css("width","100%");
	} 
}
function btn_search(){
	if($("#stx").val() == ""){ 
		//alert("검색어를 입력해주세요.");
		$("#stx").focus();
	}else{
		$.post("ajax_get_tree_member.php", $("#sForm").serialize(),function(data){
			$("#div_result").html(data);
		});
	}
}

function btn_org(){
	if (confirm("조직도를 재구성 하시겠습니까?")){
		location.href="mypage_org.php?reset=1&sfl=<?=$sfl?>&stx=<?=$stx?>&gubun=<?=$gubun?>";
	}
}
//-->
</script>






</div><!-- // pk_page -->

<!-- 마이페이지 시작 { -->
<div id="smb_my">

    <!-- 회원정보 개요 시작 { -->
    <section id="smb_my_ov">
        <h2>회원정보 개요</h2>

        <div id="smb_my_act" style="display:none;">
            <ul>
                <li><a href="<?php echo G5_BBS_URL; ?>/member_confirm.php?url=member_leave.php" onclick="return member_leave();" class="btn02">회원탈퇴</a></li>
            </ul>
        </div>
    </section>
    <!-- } 회원정보 개요 끝 -->

</div>

<script>
$(function() {
    $(".win_coupon").click(function() {
        var new_win = window.open($(this).attr("href"), "win_coupon", "left=100,top=100,width=700, height=600, scrollbars=1");
        new_win.focus();
        return false;
    });
});

function member_leave()
{
    return confirm('정말 회원에서 탈퇴 하시겠습니까?')
}
</script>
<!-- } 마이페이지 끝 -->

<?php
include_once("./_tail.php");
?>