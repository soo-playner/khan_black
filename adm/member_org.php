<?php
$sub_menu = "650200";

include_once('./_common.php');
include_once('./inc.member.class.php');
auth_check($auth[$sub_menu], 'r');



//$g5['body_script'] = ' oncontextmenu="return false"';
/*
if($member['mb_id'] == 'admin')
{
	$tree_id = $config['cf_admin'];
}else{
	$tree_id = $member['mb_id'];
}
*/
$tree_id = $member['mb_id'];

if ($gubun=="B"){
	$class_name     = "g5_member_bclass";
	$recommend_name = "mb_brecommend";
}else{
	$class_name     = "g5_member_class";
	$recommend_name = "mb_recommend";
}

//adm/recommend_set.php
// ************************

if(!isset($member['mb_child'])) {
    sql_query(" ALTER TABLE `g5_member`
                    ADD `mb_child` int(11) NOT NULL DEFAULT '0'", true);
}
if(!isset($member['mb_org_num'])) {
    sql_query(" ALTER TABLE `g5_member`
                    ADD `mb_org_num` int(11) NOT NULL DEFAULT '80'", true);
}

if ($_GET['go']=="Y"){
	goto_url("member_org.php?gubun=".$gubun."#org_start");
	exit;
}

$token = get_token();


$sql  = "select count(*) as cnt from g5_member";
$mrow = sql_fetch($sql);

$sql = "select * from g5_member_class_chk where mb_id='".$tree_id."' and  cc_date='".date("Y-m-d",time())."' order by cc_no desc";
$row = sql_fetch($sql);

if ($mrow['cnt']>$row['cc_usr'] || !$row['cc_no'] || $_GET["reset"]){

	make_habu('');

	$sql = "delete from g5_member_class where mb_id='".$tree_id."'";
	sql_query($sql);

	get_recommend_down($tree_id,$tree_id,'11');

	$sql  = " select * from g5_member_class where mb_id='{$tree_id}' order by c_class asc";	
	$result = sql_query($sql);
	for ($i=0; $row=sql_fetch_array($result); $i++) { 
		$row2 = sql_fetch("select count(c_class) as cnt from g5_member_class where  mb_id='".$tree_id."' and c_class like '".$row['c_class']."%'");
		$sql = "update g5_member set mb_child='".$row2['cnt']."' where mb_id='".$row['c_id']."'";
		sql_query($sql);
	}

	$sql = "insert into g5_member_class_chk set mb_id='".$tree_id."',cc_date='".date("Y-m-d",time())."',cc_usr='".$mrow['cnt']."'";
	sql_query($sql);

}


$sql = "select * from g5_member_bclass_chk where mb_id='".$tree_id."' and  cc_date='".date("Y-m-d",time())."' order by cc_no desc";
$row = sql_fetch($sql);

if ($mrow['cnt']>$row['cc_usr'] || !$row['cc_no'] || $_GET["reset"]){
	
	make_habu('B');
	$sql = "delete from g5_member_bclass where mb_id='".$tree_id."'";
	sql_query($sql);

	get_brecommend_down($tree_id,$tree_id,'11');

	$sql  = " select * from g5_member_bclass where mb_id='{$tree_id}' order by c_class asc";	
	$result = sql_query($sql);
	for ($i=0; $row=sql_fetch_array($result); $i++) { 
		$row2 = sql_fetch("select count(c_class) as cnt from g5_member_bclass where  mb_id='".$tree_id."' and c_class like '".$row['c_class']."%'");
		$sql = "update g5_member set mb_b_child='".$row2['cnt']."' where mb_id='".$row['c_id']."'";
		sql_query($sql);
	}

	$sql = "insert into g5_member_bclass_chk set mb_id='".$tree_id."',cc_date='".date("Y-m-d",time())."',cc_usr='".$mrow['cnt']."'";
	sql_query($sql);


	if ($_GET["reset"]){
		goto_url("member_org.php?gubun=".$gubun."&sfl=".$sfl."&stx=".$stx."&gubun=".$gubun);
		exit;		
	}
}

if ($mb_org_num){
	if ($mb_org_num>8) $mb_org_num = 8;
	$sql = "update g5_member set mb_org_num='".$mb_org_num."' where mb_id='".$tree_id."'";
	sql_query($sql);	
	$member['mb_org_num'] = $mb_org_num;
}


$qstr.='&fr_date='.$fr_date.'&to_date='.$to_date.'&starter='.$starter.'&partner='.$partner.'&team='.$team.'&bonbu='.$bonbu.'&chongpan='.$chongpan;

$listall = '<a href="'.$_SERVER['PHP_SELF'].'" class="ov_listall">전체목록</a>';


$g5['title'] = '조직도(박스)';
include_once ('./admin.head.php');


if (strstr($sfl, "mb_id"))
    $mb_id = $stx;
else
    $mb_id = "";
    


?>





<div style="padding:0px 0px 0px 10px;">
	<a name="org_start"></a>
	<div style="float:left">
	<input type="button" class="btn_menu" value="검색메뉴닫기" onclick="btn_menu()">
	<input type="button" class="btn_menu" value="전체 조직도보기" onclick="location.href='member_org.php?go=Y'">
	<input type="button" class="btn_menu" style="background:#fadfca" value="신규회원등록" onclick="open_register()">
<!--	<input type="button" class="btn_menu" style="background:#fadfca" value="조직도 인쇄" onclick="btn_print()"> -->
	</div>
	<div style="float:right;padding-right:10px">

	<button type='button' id='zoomOut' class='zoom2-btn'>Zoom Out</button>
	<button type='button' id='zoomIn' class='zoom-btn'>Zoom In</button>
	<button type="button" class="my-class" onclick="clickExportButton();">조직도 인쇄</button>

	<input type="button"  class="my-class" value="조직도 재구성" onclick="btn_org()">
	</div>
</div>
<div style="padding-top:10px;clear:both"></div>
<div id="div_left" style="width:15%;float:left;min-height:710px;">
	<div style="margin-left:10px;padding:5px 5px 5px 5px;border:1px solid #d9d9d9;height:100%">
<?
if (!$fr_date) $fr_date = Date("Y-m-d", time()-60*60*24*365);
if (!$to_date) $to_date = Date("Y-m-d", time());
?>
		<form name="sForm2" id="sForm2" method="get" action="member_org.php">
		<input type="hidden" name="now_id" id="now_id" value="<?=$now_id?>">
		<table width="100%">
			<tr>
				<td bgcolor="#f2f5f9" height="30" style="padding-left:10px">
				<div style="float:left">
				<b>표시인원</b>
				</div>
				<div style="float:right">
				<input type="text" id="mb_org_num"  name="mb_org_num" value="<?php echo $member['mb_org_num']; ?>" class="frm_input" style="text-align:center" size="3" maxlength="3"> 단계 &nbsp;
				</div>
				</td>
			</tr>

			<tr>
				<td bgcolor="#f2f5f9" height="20" style="padding:10px 10px 10px 10px" align=center>
				<input type="radio" id="gubun" name="gubun" onclick="document.sForm2.submit();" value=""<?if ($gubun=="") echo " checked"?>> 추천인
				<input type="radio" id="gubun" name="gubun" onclick="document.sForm2.submit();" value="B"<?if ($gubun=="B") echo " checked"?>> 바이너리레그

				</td>
			</tr>
			<tr>
				<td bgcolor="#f2f5f9" height="30" style="padding-left:10px"><b>매출기간</b></td>
			</tr>
			<tr>
				<td bgcolor="#f2f5f9" height="30" style="padding:10px 10px 10px 10px" align=center>
				<input type="text" id="fr_date"  name="fr_date" style="text-align:center;width:90px" value="<?php echo $fr_date; ?>" class="frm_input" size="10" maxlength="10"> ~
				<input type="text" id="to_date"  name="to_date" style="text-align:center;width:90px" value="<?php echo $to_date; ?>" class="frm_input" size="10" maxlength="10">

				</td>
			</tr>
			<tr>
				<td bgcolor="#f2f5f9" height="30" align="center">
				<input type="submit"  class="btn_submit" style="padding:5px" value="적 용">
				</td>
			</tr>
		</table>
		</form>

		<div id="div_member"></div>

		<form name="sForm" id="sForm" method="post" style="padding-top:10px" onsubmit="return false;">
		<input type="hidden" name="gubun" value="<?=$gubun?>">
		<table width="100%">
			<tr>
				<td bgcolor="#f2f5f9" height="30" style="padding-left:10px"><b>회원검색</b></td>
			</tr>
			<tr>
				<td bgcolor="#f2f5f9" height="30" style="padding:10px 10px 10px 10px">
				
				<select name="sfl" id="sfl">
				    <option value="mb_id"<?php echo get_selected($_GET['sfl'], "mb_id"); ?>>회원아이디</option>
					<!-- <option value="mb_name"<?php echo get_selected($_GET['sfl'], "mb_name"); ?>>이름</option> -->
					</select>
				<div style="padding-top:5px">
				<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
				<input type="text" name="stx" value="<?php echo $stx ?>" id="stx"  class="required frm_input" style="width:100%;" onkeypress="event.keyCode==13?btn_search():''">
				</div>
				</td>
			</tr>
			<tr>
				<td bgcolor="#f2f5f9" height="30" align="center">
				<input type="button" onclick="btn_search();" class="btn_submit" style="padding:5px" value="검 색">
				</td>
			</tr>
		</table>
		</form>

		<div id="div_result" style="margin-top:5px;overflow-y: auto;height:418px">

		</div>
	</div>
</div>
  <link type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/themes/base/jquery-ui.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <link rel="stylesheet" href="jquery.orgchart.css">
  <script type="text/javascript" src="jquery.orgchart2.js"></script>
  <script type="text/javascript" src="js/bluebird.min.js"></script>
  <script type="text/javascript" src="js/html2canvas.min.js"></script>
  <script type="text/javascript" src="js/jspdf.min.js"></script>
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
  /* height:143px; */
  text-align: center;
  width: 130px;
}

.orgchart .node .title {
	background:#fff;
	border:2px solid rgb(95, 95, 95);
	color:#000;
	height:150px;
	font-weight:normal;
	line-height:15px;
	padding-top:5px;
	cursor:pointer;
}
.orgchart .node .title .symbol{
	display:none;
}
.orgchart .node .title .dec{
	font-size: 11px;
	line-height: 11px;
}
.orgchart .node .title .dec.p{
	margin-top:5px;
}

.orgchart .node .title .mb {
	margin: 3px 0;
	word-break: break-all;
	white-space: normal;
	font-weight: bold;
	font-size: 14px;
	line-height: 11px;
	padding: 0;
	color: orange;
}

</style>
<style type="text/css">
.zoom-btn {
  display: inline-block;
  padding: 6px 12px;
  margin-bottom: 0;
  font-size: 14px;
  font-weight: 400;
  line-height: 1.42857143;
  text-align: center;
  white-space: nowrap;
  vertical-align: middle;
  touch-action: manipulation;
  cursor: pointer;
  user-select: none;
  color: #fff;
  background-color: #364fa0;
  border: 1px solid transparent;
  border-color: #364fa0;
  border-radius: 4px;
}
.zoom2-btn {
  display: inline-block;
  padding: 6px 12px;
  margin-bottom: 0;
  font-size: 14px;
  font-weight: 400;
  line-height: 1.42857143;
  text-align: center;
  white-space: nowrap;
  vertical-align: middle;
  touch-action: manipulation;
  cursor: pointer;
  user-select: none;
  color: #fff;
  background-color: #364fa0;
  border: 1px solid transparent;
  border-color: #364fa0;
  border-radius: 4px;
}
.my-class {
  display: inline-block;

  padding: 6px 12px;
  margin-bottom: 0;
  font-size: 14px;
  font-weight: 400;
  line-height: 1.42857143;
  text-align: center;
  white-space: nowrap;
  vertical-align: middle;
  touch-action: manipulation;
  cursor: pointer;
  user-select: none;
  color: #fff;
  background-color: #5cb85c;
  border: 1px solid transparent;
  border-color: #4cae4c;
  border-radius: 4px;
}
</style>
<style type="text/css">
.oc-export-btn {
    display: none;
}
</style>
<script type="text/javascript">

	function clickExportButton(){
		 $(".oc-export-btn").click();
	}
//-->
</script>
<div id="div_right" style="width:85%;float:left;min-height:500px">
<!--  -->
<?

if ($now_id){
	$go_id = $now_id;
}else{
	$go_id = $tree_id;
}

if ($member['mb_org_num']){
	$max_org_num = $member['mb_org_num'];
}else{
	$max_org_num = 4;
}
$org_num     = 0;

//(select mb_child from g5_member where mb_id=c.c_id) as c_child
$sql = "select c.c_id,c.c_class,(select grade from g5_member where mb_id=c.c_id) as grade,(select pool_level from g5_member where mb_id=c.c_id) as pool_level,(select mb_name from g5_member where mb_id=c.c_id) as c_name,(select count(*) from g5_member where mb_recommend=c.c_id) as c_child,(select mb_b_child from g5_member where mb_id=c.c_id) as b_child,(select mb_id from g5_member where mb_brecommend=c.c_id and mb_brecommend_type='L') as b_recomm,(select mb_id from g5_member where mb_brecommend=c.c_id and mb_brecommend_type='R') as b_recomm2,(select count(mb_no) from g5_member where ".$recommend_name."=c.c_id and mb_leave_date = '') as m_child, (select it_pool1 from g5_member where mb_id=c.c_id) as it_pool1, (select it_pool2 from g5_member where mb_id=c.c_id) as it_pool2, (select it_pool3 from g5_member where mb_id=c.c_id) as it_pool3, (select it_pool4 from g5_member where mb_id=c.c_id) as it_pool4, (select it_GPU from g5_member where mb_id=c.c_id) as it_GPU, (select mb_no from g5_member where mb_id=c.c_id) as m_no from g5_member m join ".$class_name." c on m.mb_id=c.mb_id where c.mb_id='{$tree_id}' order by m_no asc";
//$sql = "select c.c_id,c.c_class,(select grade from g5_member where mb_id=c.c_id) as grade,(select pool_level from g5_member where mb_id=c.c_id) as pool_level,(select mb_name from g5_member where mb_id=c.c_id) as c_name,(select count(*) from g5_member where mb_recommend=c.c_id) as c_child,(select mb_b_child from g5_member where mb_id=c.c_id) as b_child,(select mb_id from g5_member where mb_brecommend=c.c_id and mb_brecommend_type='L') as b_recomm,(select mb_id from g5_member where mb_brecommend=c.c_id and mb_brecommend_type='R') as b_recomm2,(select count(mb_no) from g5_member where ".$recommend_name."=c.c_id and mb_leave_date = '') as m_child, (select it_pool1 from g5_member where mb_id=c.c_id) as it_pool1, (select it_pool2 from g5_member where mb_id=c.c_id) as it_pool2, (select it_pool3 from g5_member where mb_id=c.c_id) as it_pool3, (select it_pool4 from g5_member where mb_id=c.c_id) as it_pool4, (select it_GPU from g5_member where mb_id=c.c_id) as it_GPU from g5_member m join ".$class_name." c on m.mb_id=c.mb_id where c.mb_id='{$tree_id}' and c.c_id='{$go_id}' ";
//print_r($sql);

$srow = sql_fetch($sql);

$my_depth = strlen($srow['c_class']);


if ($order_proc==1){
	$sql  = "select today as tpv from ".$ngubun."today where mb_id='".$srow['c_id']."'";
	$row2 = sql_fetch($sql);

	$sql  = "select noo as tpv from ".$ngubun."noo where mb_id='".$srow['c_id']."'";
	$row3 = sql_fetch($sql);

	$sql  = "select thirty as tpv from ".$ngubun."thirty where mb_id='".$srow['c_id']."'";
	$row5 = sql_fetch($sql);
}else{

	$sql  = "select no,today as tpv from ".$ngubun."today where mb_id='".$srow['c_id']."'";
	$row2 = sql_fetch($sql);

	if ($row2['no']){

	}else{

		$sql  = "select ".$order_field." as tpv from g5_shop_order where mb_id='".$srow['c_id']."' and od_time between '$fr_date 00:00:00' and '$to_date 23:59:59'";
		$row2 = sql_fetch($sql);
		if (!$row2['tpv']) $row2['tpv'] = 0;
		sql_query("insert ".$ngubun."today SET today=".$row2['tpv']." ,mb_id='".$srow['c_id']."'");	
	}

	$sql  = "select no,noo as tpv from ".$ngubun."noo where mb_id='".$srow['c_id']."'";
	$row3 = sql_fetch($sql);
	if ($row3['no']){

	}else{
		$sql  = "select ".$order_field." as tpv from g5_shop_order where mb_id in (select c_id from ".$class_name." where mb_id='".$tree_id."'  and c_class like '".$srow['c_class']."%') and od_receipt_time between '$fr_date 00:00:00' and '$to_date 23:59:59'";
		$row3 = sql_fetch($sql);

		$row3 = sql_fetch($sql);
		if (!$row3['tpv']) $row3['tpv'] = 0;
		$sql  = "insert ".$ngubun."noo SET noo=".$row3['tpv']." ,mb_id='".$srow['c_id']."'";
		sql_query($sql);	
	}

	//이전 30일
	$sql  = "select no,thirty as tpv from ".$ngubun."thirty where mb_id='".$srow['c_id']."'";
	$row5 = sql_fetch($sql);
	if ($row5['no']){

	}else{
		$sql  = "select ".$order_field." as tpv from g5_shop_order where mb_id in (select c_id from ".$class_name." where mb_id='".$tree_id."' and c_class like '".$srow['c_class']."%') and od_receipt_time between '".Date("Y-m-d",time()-(60*60*24*30))." 00:00:00' and '".Date("Y-m-d",time())." 23:59:59'";
		$row5 = sql_fetch($sql);
		if (!$row5['tpv']) $row5['tpv'] = 0;
		sql_query("insert ".$ngubun."thirty SET thirty=".$row5['tpv']." ,mb_id='".$srow['c_id']."'");	
	}

}

//바이너리 왼쪽 오늘 매출


if ($srow['b_recomm']){
	//$sql  = "select ".$order_field." as tpv from g5_shop_order where mb_id ='".$srow['b_recomm']."' and od_receipt_time between '".$to_date." 00:00:00' and '".$to_date." 23:59:59'";
    
	$sql  = "select (mb_my_sales+habu_day_sales) as tpv from g5_member where mb_id ='".$srow['b_recomm']."' and sales_day='".date("Y-m-d")."'";
	// and sales_day='".date("Y-m-d")."'
	$row6 = sql_fetch($sql);
	if (!$row6['tpv']) $row6['tpv'] = 0;

 	$sql  = "select ".$order_field." as tpv from iwol where mb_id ='".$srow['b_recomm']."'";
	$row8 = sql_fetch($sql);

	$row6['tpv'] += $row8['tpv'];

}else{
	$row6['tpv'] = 0;
}

//바이너리 오른쪽 오늘 매출
if ($srow['b_recomm2']){
//	$sql  = "select ".$order_field." as tpv from g5_shop_order where mb_id ='".$srow['b_recomm2']."' and od_receipt_time between '".$to_date." 00:00:00' and '".$to_date." 23:59:59'";
	//echo '<br>';
    $sql  = "select (mb_my_sales+habu_day_sales) as tpv from g5_member where mb_id ='".$srow['b_recomm2']."' and sales_day='".date("Y-m-d")."'";
	// and sales_day='".date("Y-m-d")."'
	$row7 = sql_fetch($sql);
	if (!$row7['tpv']) $row7['tpv'] = 0;

	$sql  = "select ".$order_field." as tpv from iwol where mb_id ='".$srow['b_recomm2']."'";
	$row9 = sql_fetch($sql);
	$row7['tpv'] += $row9['tpv'];

}else{
	$row7['tpv'] = 0;
}




$sql    = "select c_class from ".$class_name." where mb_id='".$tree_id."' and c_id='".$go_id."'";

$row4   = sql_fetch($sql);
$mdepth = (strlen($row4['c_class'])/2);

			$mb_my_sales=$row2['tpv'];
			$mb_habu_sum=$row3['tpv'];

			if($mb_my_sales==''){ $mb_my_sales=0; }
			if($mb_habu_sum==''){ $mb_habu_sum=0;}


			//업데이트유무 확인
			$sql = "select * from ".$class_name."_chk where cc_date='".date("Y-m-d",time())."' order by cc_no desc";
			$mrow = sql_fetch($sql);

			if ($mrow['cc_run']==0){  //업데이트가 안되었으면
				if ($gubun){
					$sql  = "update g5_member set mb_b_my_sales=".$mb_my_sales." , mb_b_habu_sum=".$mb_habu_sum."   where mb_id='".$go_id."'";
				}else{
					$sql  = "update g5_member set mb_my_sales=".$mb_my_sales." , mb_habu_sum=".$mb_habu_sum."   where mb_id='".$go_id."'";
				}
				sql_query($sql);
			}

			if (!$srow['b_child']) $srow['b_child']=1;
			//if (!$srow['c_child']) $srow['c_child']=1;
?>
		<ul id="org" style="display:none" >
			<li>
				<?=(strlen($srow['c_class'])/2)-1?>-<?=($srow['c_child'])?>-<?=($srow['b_child']-1)?>|<?=get_member_label($srow['grade'])?>|<?=$srow['c_id']?>|<?=$srow['c_name']?>|<?=number_format($row3['tpv']/$order_split)?>|<?=number_format($row5['tpv']/$order_split)?>|<?=$srow['grade']?>|<?=number_format($row6['tpv']/$order_split)?>|<?=number_format($row7['tpv']/$order_split)?>|<?=$srow['it_pool1']?>|<?=$srow['it_pool2']?>|<?=$srow['it_pool3']?>|<?=$srow['it_pool4']?>|<?=(strlen($srow['c_class'])/2)-1?>|<?=($srow['c_child'])?>|<?=($srow['b_child']-1)?>|<?=$gubun?>
<? 
			get_org_down($srow);
?>
			</li>
<?
		//업데이트 완료 
		if ($mrow['cc_run']==0){
			$sql = "update ".$class_name."_chk set cc_run=1 where cc_no='{$mrow['cc_no']}'";
			sql_query($sql);
		}
?>
	</ul>


    <div id="chart-container" class="orgChart"></div>
    <script>
    $(function() {

      $('#chart-container').orgchart({
        'data' : $('#org'),
		 'zoom': false
		});

		var $container = $('#chart-container');
		
		var $chart = $('.orgchart');
		$chart.css('transform', "scale(1,1)");
		var div = $chart.css('transform');
		var values = div.split('(')[1];
		values = values.split(')')[0];
		values = values.split(',');
		var a = values[0];
		var b = values[1];
		var currentZoom = Math.sqrt(a*a + b*b);
		var zoomval = .8;

		$container.scrollLeft(($container[0].scrollWidth - $container.width())/2);
		var my_num = 0;

		// zoom buttons	
		$('#zoomIn').on('click', function () {
			my_num++;
			zoomval = currentZoom += 0.1;
			$chart.css("transform",'matrix('+zoomval+', 0, 0, '+zoomval+', 0 ,'+((my_num)*85)+')');    
			$container.scrollLeft(($container[0].scrollWidth - $container.width())/2);
		});

		$('#zoomOut').on('click', function () {
			zoomval = currentZoom -= 0.1;
			my_num--;
			$chart.css("transform",'matrix('+zoomval+', 0, 0, '+zoomval+', 0 ,'+((my_num)*85)+')');    
			$container.scrollLeft(($container[0].scrollWidth - $container.width())/2);

		});

    });
    </script>
</div>

<script type="text/javascript">
function set_member(set_id,set_type){
	window.open('recommend_set.php?set_id='+set_id+'&set_type='+set_type+'&now_id='+$("#now_id").val(), 'set_recomm', 'width=520, height=500, resizable=no, scrollbars=yes, left=0, top=0');
}
function open_register(){
	window.open('/shop/recommend_register.php?gp=ao&now_id='+$("#now_id").val(), 'set_register', 'width=600, height=500, resizable=no, scrollbars=no, left=0, top=0');
}

$(document).ready(function(){
	$("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
	<?php if ($stx && $sfl){ ?>
		btn_search();
	<?php } ?>
});

function edit_member(edit_id){
	window.open('recommend_edit.php?gubun=<?=$gubun?>&edit_id='+edit_id, 'edit_recomm', 'width=520, height=500, resizable=no, scrollbars=yes, left=0, top=0');
	/*
	if(event.button==2){	
		window.open('recommend_edit.php?gubun=<?=$gubun?>&edit_id='+edit_id, 'edit_recomm', 'width=520, height=500, resizable=no, scrollbars=yes, left=0, top=0');
	}else{
		go_member(edit_id);
	}
	*/
}

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

/**/
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
	//	alert("검색어를 입력해주세요.");
		$("#stx").focus();
	}else{
		$.post("ajax_get_tree_member.php", $("#sForm").serialize(),function(data){
			$("#div_result").html(data);
		});
	}
}

function btn_org(){
	if (confirm("조직도를 재구성 하시겠습니까?")){
		location.href="member_org.php?reset=1&sfl=<?=$sfl?>&stx=<?=$stx?>&gubun=<?=$gubun?>";
	}
}
//-->
</script>

<?php
include_once ('./admin.tail.php');

?>



<style type="text/css">
	#div_right table { border:0px }
	.btn_menu {padding:5px;border:1px solid #ced9de;background:rgb(246,249,250);cursor:pointer}
	.pool_img{display:none;}
	.lvl_img{
		width:30px;
	}
</style>