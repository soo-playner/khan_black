<?
include_once("./_common.php");
define('_INDEX_', true);
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

include_once(G5_PATH.'/head.php');


/*서비스점검*/
$sql = " select * from maintenance";
$nw = sql_fetch($sql);

if($nw['nw_use'] == 'Y'){
	$maintenance = 'Y';
}else{
	$maintenance = 'N';
}
/*
if($member['mb_wallet'] == ''){
	include_once(G5_PATH.'/wallet_create.php');
}
*/

$mb_wallet = $member['mb_wallet'];

/*코인잔고*/
$sql_account = "select mb_id, sum(mb_v7_account ) as v7_total ,sum(mb_btc_account + mb_btc_calc + mb_btc_amt) as btc_total from g5_member where mb_id = '$member[mb_id]'";
$account = sql_fetch($sql_account);
$btc_total = $account['btc_total'];
$v7_total = $account['v7_total'];


if($is_member){
	if(defined('G5_THEME_PATH')) {
		require_once(G5_THEME_PATH.'/index.php');
	}
}else{
	//Header("Location:./bbs/login_pw.php");
	Header("Location:/wallet/intro.php");
	//require_once(G5_THEME_PATH.'/intro.php');
}

include_once(G5_PATH.'/tail.php');
?>

