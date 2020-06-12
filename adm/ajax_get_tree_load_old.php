<?php
$sub_menu = "600300";
include_once('./_common.php');
include_once('./inc.member.class.php');

//$mrow = sql_fetch("select * from g5_member where mb_id='{$go_id}'");
$crow = sql_fetch("select c_class from g5_member_class where mb_id='{$member['mb_id']}' and c_id='{$go_id}'");
$mdepth = (strlen($crow['c_class'])/2);
?>
		<div class="zTreeDemoBackground left" style="min-height:573px;margin:0px 10px 0px 10px;border:1px solid #d9d9d9;">
			<ul id="treeDemo" class="ztree"></ul>
		</div>
		<SCRIPT type="text/javascript">
			<!--
			var setting = {
				view: {
					nameIsHTML: true
				},
				data: {
					simpleData: {
						enable: true
					}
				}
			};
			var zNodes =[
		<?
		$sql = "select c.c_id,c.c_class,(select mb_level from g5_member where mb_id=c.c_id) as mb_level,(select mb_name from g5_member where mb_id=c.c_id) as c_name,(select mb_child from g5_member where mb_id=c.c_id) as c_child,(select count(mb_no) from g5_member where mb_recommend=c.c_id and mb_leave_date = '') as m_child from g5_member m join g5_member_class c on m.mb_id=c.mb_id where c.mb_id='{$member['mb_id']}' and c.c_class like '{$crow['c_class']}%' order by c.c_class";
		$result = sql_query($sql);
		for ($i=0; $row=sql_fetch_array($result); $i++) {
			if (strlen($row['c_class'])==2){
				$parent_id = 0;
			}else{
				$parent_id = substr($row['c_class'],0,strlen($row['c_class'])-2);
			}
			$sql  = "select sum(od_receipt_price+od_receipt_cash) as tprice,sum(pv) as tpv from g5_shop_order where mb_id='".$row['c_id']."' and od_time between '$fr_date 00:00:00' and '$to_date 23:59:59'";
			$row2 = sql_fetch($sql);

			$sql  = "select sum(od_receipt_price+od_receipt_cash) as tprice,sum(pv) as tpv from g5_shop_order where mb_id in (select c_id from g5_member_class where mb_id='".$member['mb_id']."' and c_id<>'".$row['c_id']."' and c_class like '".$row['c_class']."%') and od_time between '$fr_date 00:00:00' and '$to_date 23:59:59'";
			$row3 = sql_fetch($sql);
		?>
				{ id:<?=$row['c_class']?>, pId:<?=$parent_id?>, name:"[<?=get_member_label($row['mb_level'])?>-<?=(strlen($row['c_class'])/2)-$mdepth?>-<?=($row['m_child'])?>-<?=($row['c_child']-1)?>] <?=$row['c_name']?> (<?=$row['c_id']?>)  <img src='img/dot.gif'> 자기매출 <?=number_format($row2[tprice]/1000)?>/<?=number_format($row2['tpv']/1000)?> <img src='img/dot.gif'> 하부매출 <?=number_format($row3[tprice]/1000)?>/<?=number_format($row3['tpv']/1000)?> ", open:true},
		<?
		}
		?>
			];

			$(document).ready(function(){
				<?if ($stx && $sfl){?>
					btn_search();
				<?}?>
				$.fn.zTree.init($("#treeDemo"), setting, zNodes);
			});

			//-->
		</SCRIPT>