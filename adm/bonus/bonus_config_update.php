<?
include_once('./_common.php');

$total_count = count($_POST['idx']);

for($i = 0 ; $i < $total_count; $i ++){

    $idx = $_POST['idx'][$i];
    $name = $_POST['name'][$i];
    $code = $_POST['code'][$i];
    $rate = $_POST['rate'][$i];
    $limited = $_POST['limited'][$i];
    $layer = $_POST['layer'][$i];
    $condition = $_POST['condition'][$i];
    $memo = $_POST['memo'][$i];
    $source = $_POST['source'][$i];

    if(!$_POST['used'][$i]){
        $used = '0';
    }else{
        $used = $_POST['used'][$i];
    }

    $update_bounus_set = 
    "update {$g5['bonus_config']} set 
    name = '{$name}',
    code = '{$code}',
    limited = '{$limited}',
    rate = '{$rate}',
    layer = '{$layer}',
    source = '{$source}',
    bonus_condition = '{$condition}',
    memo = '{$memo}',
    used = '{$used}'
    where idx = $idx ;";

    $up_query = sql_query($update_bounus_set);
    

    if(!is_dir(G5_PATH."/data/log/".$code)){ 
        umask(0); 
        if(!mkdir(G5_PATH."/data/log/".$code, 0777, true)){ 
            print_r(error_get_last()); return; 
        } 
    }

    print_R($update_bounus_set."<br>");
}

if( $up_query){
    alert('마케팅 수당 설정이 저장되었습니다.');
    goto_url('./bonus_config.php');
}
?>