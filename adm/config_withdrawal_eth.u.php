<?php
include_once('./_common.php');




if($_POST[status]=='N'){
	$get_row = "Select * from withdrawal_request_eth where uid  = ".$_POST[uid];
	$slamdunk = sql_query($get_row);
	$ret = sql_fetch_array($slamdunk);
	$mb_id = $ret['mb_id'];
	$amt = $ret['amt'];
	$sql1 = "update g5_member set it_pool5_profit = round(it_pool5_profit+".$amt.",8) where mb_id='".$mb_id."'";
	sql_query($sql1);
}

$sql = " update withdrawal_request_eth set status = '".$_POST[status]."' ";
$sql .= ", update_dt = now() ";
$sql .= " where uid = '".$_POST[uid]."' ";
$obj = new stdClass();
$obj->result = sql_query($sql);
$obj->status = $_POST[status];
echo json_encode($obj);
// goto_url('./config_price.php');
?>
