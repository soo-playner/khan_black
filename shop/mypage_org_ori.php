<?php
include_once('./_common.php');

if (!$is_member)
    goto_url(G5_BBS_URL."/login.php?url=".urlencode(G5_SHOP_URL."/mypage.php"));

if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/mypage.php');
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
	goto_url("mypage_org.php#org_start");
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
	<div class="infoBx">
		<h3>내정보</h3>
		<ul>
			<li><span>Personal Information</span> <a href="<?php echo G5_BBS_URL; ?>/member_confirm.php?url=register_form.php" class="btn">회원정보수정</a></li>
			<li><span>성명</span> <?=$member['mb_name']?></li>
			<li><span>전화번호</span> <?=($member['mb_hp'])?$member['mb_hp']:$member['mb_tel']?></li>
			<li><span>Rank</span> <?=($member['mb_1'])?$member['mb_1']:"-"?></li>
			<li><span>소속</span> <?=($member['mb_2'])?$member['mb_2']:"-"?></li>
		</ul>
	</div><!-- // infoBx -->

	<div class="infoBx">
		<h3>My team information</h3>
		<ul>
			<li><span>My team information</span> <a href="#" class="btn">보기</a></li>
			<li><span>Enrollment Tree</span> <a href="mypage_tree.php" class="btn">보기(트리)</a> <a href="mypage_org.php" class="btn">보기(박스)</a></li>
		</ul>
	</div>
<?
include_once('../adm/inc.member.class.php');



$sql = "select * from g5_member_class_chk where mb_id='".$member['mb_id']."' and  cc_date='".date("Y-m-d",time())."'";
$row = sql_fetch($sql);

if (!$row['cc_no'] || $_GET["reset"]){
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

	$sql = "insert into g5_member_class_chk set mb_id='".$member['mb_id']."',cc_date='".date("Y-m-d",time())."'";
	sql_query($sql);

	if ($_GET["reset"]){
		goto_url("mypage_org.php?sfl=".$sfl."&stx=".$stx);
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
	<input type="button" class="btn_menu" value="전체 조직도보기" onclick="location.href='mypage_org.php?go=Y'">
<!--	<input type="button" class="btn_menu" style="background:#fadfca" value="조직도 인쇄" onclick="btn_print()"> -->
	</div>
	<div style="float:right;padding-right:10px">
	<input type="button" class="btn_menu" value="조직도 재구성" onclick="btn_org()">
	</div>
</div>
<div style="padding-top:10px;clear:both"></div>
<div id="div_left" style="width:15%;float:left;min-height:710px;border:">
	<div style="margin-left:10px;padding:5px 5px 5px 5px;border:1px solid #d9d9d9;height:723px">
<?
if (!$fr_date) $fr_date = Date("Y-m-d", time()-60*60*24*365);
if (!$to_date) $to_date = Date("Y-m-d", time());
?>
		<form name="sForm2" id="sForm2" method="get" action="mypage_org.php">
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
				<td bgcolor="#f2f5f9" height="30" style="padding-left:10px"><b>주문기간</b></td>
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
$go_id = $member['mb_id'];

if ($member['mb_org_num']){
	$max_org_num = $member['mb_org_num'];
}else{
	$max_org_num = 50;
}
$org_num     = 0;

$sql = "select c.c_id,c.c_class,(select mb_level from g5_member where mb_id=c.c_id) as mb_level,(select mb_name from g5_member where mb_id=c.c_id) as c_name,(select mb_child from g5_member where mb_id=c.c_id) as c_child,(select count(mb_no) from g5_member where mb_recommend=c.c_id and mb_leave_date = '') as m_child from g5_member m join g5_member_class c on m.mb_id=c.mb_id where c.mb_id='{$member['mb_id']}' and c.c_id='$go_id'";
$srow = sql_fetch($sql);

$sql  = "select sum(od_receipt_price) as tprice,sum(pv) as tpv from g5_shop_order where mb_id='".$srow['c_id']."' and od_time between '$fr_date 00:00:00' and '$to_date 23:59:59'";
$row2 = sql_fetch($sql);

$sql  = "select sum(od_receipt_price) as tprice,sum(pv) as tpv from g5_shop_order where mb_id in (select c_id from g5_member_class where mb_id='".$member['mb_id']."' and c_id<>'".$row['c_id']."' and c_class like '".$srow['c_class']."%') and od_time between '$fr_date 00:00:00' and '$to_date 23:59:59'";
$row3 = sql_fetch($sql);

$sql    = "select c_class from g5_member_class where mb_id='".$member['mb_id']."' and c_id='".$go_id."'";
$row4   = sql_fetch($sql);
$mdepth = (strlen($row4['c_class'])/2);
?>
		<ul id="org" style="display:none" >
			<li>
				[<?=(strlen($srow['c_class'])/2)-1?>-<?=($srow['m_child'])?>-<?=($srow['c_child']-1)?>]|<?=get_member_label($srow['mb_level'])?>|<?=$srow['c_id']?>|<?=$srow['c_name']?>|<?=number_format($row2[tprice]/1000)?>|<?=number_format($row3[tprice]/1000)?>
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
	$.get("ajax_get_org_load.php?fr_date<?=$fr_date?>&to_date=<?=$to_date?>&go_id="+go_id, function (data) {
		$('#div_right').html(data);
	});
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
		location.href="mypage_org.php?reset=1&sfl=<?=$sfl?>&stx=<?=$stx?>";
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