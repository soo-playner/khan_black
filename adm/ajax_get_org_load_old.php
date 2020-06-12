<?php
$sub_menu = "600300";
include_once('./_common.php');
include_once('./inc.member.class.php');
?>
<?
if ($member['mb_org_num']){
	$max_org_num = $member['mb_org_num'];
}else{
	$max_org_num = 50;
}
$org_num     = 0;

$sql = "select c.c_id,c.c_class,(select mb_level from g5_member where mb_id=c.c_id) as mb_level,(select mb_name from g5_member where mb_id=c.c_id) as c_name,(select mb_child from g5_member where mb_id=c.c_id) as c_child,(select count(mb_no) from g5_member where mb_recommend=c.c_id and mb_leave_date = '') as m_child from g5_member m join g5_member_class c on m.mb_id=c.mb_id where c.mb_id='{$member['mb_id']}' and c.c_id='$go_id'";
$srow = sql_fetch($sql);

$sql  = "select sum(od_receipt_price+od_receipt_cash) as tprice,sum(pv) as tpv from g5_shop_order where mb_id='".$srow['c_id']."' and od_time between '$fr_date 00:00:00' and '$to_date 23:59:59'";
$row2 = sql_fetch($sql);

$sql  = "select sum(od_receipt_price+od_receipt_cash) as tprice,sum(pv) as tpv from g5_shop_order where mb_id in (select c_id from g5_member_class where mb_id='".$member['mb_id']."' and c_id<>'".$srow['c_id']."' and c_class like '".$srow['c_class']."%') and od_time between '$fr_date 00:00:00' and '$to_date 23:59:59'";
$row3 = sql_fetch($sql);

$sql    = "select c_class from g5_member_class where mb_id='".$member['mb_id']."' and c_id='".$go_id."'";
$row4   = sql_fetch($sql);
$mdepth = (strlen($row4['c_class'])/2);
?>
		<ul id="org" style="display:none" >
			<li>
				[<?=(strlen($srow['c_class'])/2)-$mdepth?>-<?=($srow['m_child'])?>-<?=($srow['c_child']-1)?>]|<?=get_member_label($srow['mb_level'])?>|<?=$srow['c_id']?>|<?=$srow['c_name']?>|<?=number_format($row2['tpv']/1000)?>|<?=number_format($row3['tpv']/1000)?>
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