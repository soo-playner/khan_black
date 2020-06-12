<?php
include_once('./_common.php');

$count = count($_POST['chk_wr_id']);

if(!$count) {
    alert($_POST['btn_submit'].' 하실 항목을 하나 이상 선택하세요...');
}

if($_POST['btn_submit'] == 'delete') {
    include './delete_all.php';
} else if($_POST['btn_submit'] == 'copy') {
    $sw = 'copy';
    include './move.php';
} else if($_POST['btn_submit'] == 'move') {
    $sw = 'move';
    include './move.php';
} else if($_POST['btn_submit'] == 'hide') {
    include './hide.php';
}else {
    alert('올바른 방법으로 이용해 주세요.');
}
?>