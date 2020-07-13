<?php
include_once('./_common.php');

/*수당설정 로드*/
define('BONUS_CURENCY','ETH');


if($_GET['debug']){
	$debug = 1;
}

$bonus_sql = "select * from {$g5['bonus_config']} WHERE used < 2 order by used desc, idx asc";
$list = sql_query($bonus_sql);
$pre_setting = sql_fetch($bonus_sql);
$pre_condition ='';
$admin_condition = " mb_level < 10 ";

// 이미지급받은경우
$file_name = explode(".",basename($_SERVER['PHP_SELF']));
$code=$file_name[1];
$bonus_day = $_GET['to_date'];

if(!$debug){
    $dupl_check_sql = "select mb_id from {$g5['bonus']} where day='".$bonus_day."' and allowance_name = '{$code}' ";
	$get_today = sql_fetch( $dupl_check_sql);

	if($get_today['mb_id']){
		alert($bonus_day.' '.$code." 수당은 이미 지급되었습니다.");
		die;
	}
}

/*수당지급조건*/
if($pre_setting['layer'] != ''){
    $pre_condition = ' and '.$pre_setting['layer'];
    $pre_condition_in = $pre_setting['layer'];
}else{
    $pre_condition_in = 1;
}


// 지난주 날짜 구하기 
$today=$bonus_day;
$timestr        = strtotime($today);
$week           = date('w', strtotime($today));
$weekfr         = $timestr - ($week * 86400);
$weekla         = $weekfr + (6 * 86400);
$week_frdate    = date('Y-m-d', $weekfr - (86400 * 6)); // 지난주 시작일자
$week_todate    = date('Y-m-d', $weekla - (86400 * 6)); // 지난주 종료일자


function bonus_pick($val){    
    global $g5;
    $pick_sql = "select * from {$g5['bonus_config']} where code = '{$val}' ";
    $list = sql_fetch($pick_sql);
    return $list;
}

function bonus_condition_tx($bonus_condition){
    if($bonus_condition == 1){
        $bonus_condition_tx = '추천 계보';
    }else if($bonus_condition == 2){
        $bonus_condition_tx = '후원(바이너리) 계보';
    }else{
        $bonus_condition_tx='';
    }
    return $bonus_condition_tx;
}

function bonus_layer_tx($bonus_layer){
    if($bonus_layer == ''){
        $bonus_layer_tx = '전체지급';
    }else{
        $bonus_layer_tx = $bonus_layer.'단계까지 지급';
    }
    return $bonus_layer_tx;
}
?>



