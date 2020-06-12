<?
include_once('./_common.php');

include_once ('./admin.head.php');

$stx = $_GET['id'];

if($stx == 'change'){
    $sql_clear2 = " TRUNCATE table g5_shop_change; ";
    $sql_result = sql_query($sql_clear2);
}else if($stx == 'with'){
    $sql_clear2 = " TRUNCATE table withdrawal_request; ";
    $sql_result = sql_query($sql_clear2);
}

if($sql_result){
    alert('초기화되었습니다.');
    goto_url('./withdrawal_batch.php');
}
?>
