<?
include_once('./_common.php');

include_once ('./admin.head.php');



if($func == 'member'){
	$sql_clear = "update g5_member set mb_balance = '0', mb_v7_account = '0', mb_level=0, mb_deposit_point = '0',  mb_deposit_acc ='0'  where mb_id != 'coolrunning' ";
	$sql_result = sql_query($sql_clear);

	echo "result : ".$sql_result;
	
}else if($func == 'soodang'){
	$sql_clear2 = " TRUNCATE table bnoo2; ";
		$sql_result = sql_query($sql_clear2);
	$sql_clear2 = " TRUNCATE table bthirty2; ";
		$sql_result = sql_query($sql_clear2);
	$sql_clear2 = " TRUNCATE table iwol;";
		$sql_result = sql_query($sql_clear2);
	$sql_clear2 = " TRUNCATE table soodang_pay;";
		$sql_result = sql_query($sql_clear2);
	$sql_clear2 = " TRUNCATE table noo2;";
		$sql_result = sql_query($sql_clear2);
	$sql_clear2 = "TRUNCATE table btoday2;";
		$sql_result = sql_query($sql_clear2);
	$sql_clear2 = " TRUNCATE table thirty2";
		$sql_result = sql_query($sql_clear2);
	$sql_clear2 = " TRUNCATE table rank";
		$sql_result = sql_query($sql_clear2);
	
	echo "result : ".$sql_result;
}
else if($func == 'pack'){
	$sql_clear2 = " TRUNCATE table bnoo2; ";
		$sql_result = sql_query($sql_clear2);
	$sql_clear2 = " TRUNCATE table bthirty2; ";
		$sql_result = sql_query($sql_clear2);
	$sql_clear2 = " TRUNCATE table iwol;";
		$sql_result = sql_query($sql_clear2);

	$sql_clear2 = " TRUNCATE table noo2;";
		$sql_result = sql_query($sql_clear2);
	$sql_clear2 = "TRUNCATE table btoday2;";
		$sql_result = sql_query($sql_clear2);
	$sql_clear2 = " TRUNCATE table thirty2";
		$sql_result = sql_query($sql_clear2);
	
	$sql_clear3 = 	"delete from soodang_pay where allowance_name = 'Binary' or allowance_name =  'B Pack'" ;
		$sql_result = sql_query($sql_clear3);
	
	echo "result : ".$sql_result;
}
else if($func == 'bpack'){
	$sql_clear2 = " TRUNCATE table bnoo2; ";
		$sql_result = sql_query($sql_clear2);
	$sql_clear2 = " TRUNCATE table bthirty2; ";
		$sql_result = sql_query($sql_clear2);
	$sql_clear2 = " TRUNCATE table iwol;";
		$sql_result = sql_query($sql_clear2);

	$sql_clear2 = " TRUNCATE table noo2;";
		$sql_result = sql_query($sql_clear2);
	$sql_clear2 = "TRUNCATE table btoday2;";
		$sql_result = sql_query($sql_clear2);
	$sql_clear2 = " TRUNCATE table thirty2";
		$sql_result = sql_query($sql_clear2);
	
	$sql_clear3 = 	"delete from soodang_pay where allowance_name = 'Binary' or allowance_name =  'B Pack'" ;
		$sql_result = sql_query($sql_clear3);
	
	echo "result : ".$sql_result;
}
else if($func == 'amt'){
	$sql_clear = "update g5_member set mb_btc_calc = '0', mb_btc_amt = '0', mb_v7_calc = '0' ";
	$sql_result = sql_query($sql_clear);
	
	echo "result : ".$sql_result;
}
else if($func == 'balance'){
	$sql_clear = "update g5_member set mb_v7_account = '0', mb_balance = '0', mb_deposit_point = '0', mb_deposit_acc = '0', mb_level = '0', grade = '0' where mb_id != 'coolrunning' ";
	$sql_result = sql_query($sql_clear);
	
	echo "result : ".$sql_result;
}

else if($func == 'pack_order'){
	$sql_clear2 = " TRUNCATE table g5_shop_cart; ";
		$sql_result = sql_query($sql_clear2);
	
	$sql_clear2 = " UPDATE g5_member set it_pool1 = '', it_pool2 = '',it_pool1_profit = '',it_pool2_profit='', q_autopack ='', b_autopack='' ;";
		$sql_result = sql_query($sql_clear2);

	$sql_clear3 = 	"delete from soodang_pay where allowance_name = 'Q Pack' or allowance_name =  'B Pack'" ;
		$sql_result = sql_query($sql_clear3);
	
	echo "result : ".$sql_result;
}
?>
