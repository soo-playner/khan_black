<?php
$sub_menu = "200100";
include_once('./_common.php');

check_demo();

if (!count($_POST['chk'])) {
    alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
}

auth_check($auth[$sub_menu], 'w');

if ($_POST['act_button'] == "선택수정") {

    for ($i=0; $i<count($_POST['chk']); $i++)
    {
        // 실제 번호를 넘김
        $k = $_POST['chk'][$i];

        $mb = get_member($_POST['mb_id'][$k]);

        if (!$mb['mb_id']) {
            $msg .= $mb['mb_id'].' : 회원자료가 존재하지 않습니다.\\n';
        } else if ($is_admin != 'super' && $mb['mb_level'] >= $member['mb_level']) {
            $msg .= $mb['mb_id'].' : 자신보다 권한이 높거나 같은 회원은 수정할 수 없습니다.\\n';
        } else if ($member['mb_id'] == $mb['mb_id']) {
            $msg .= $mb['mb_id'].' : 로그인 중인 관리자는 수정 할 수 없습니다.\\n';
        } else {
            if($_POST['mb_certify'][$k])
                $mb_adult = $_POST['mb_adult'][$k];
            else
                $mb_adult = 0;

        include_once('../lib/otphp/lib/otphp.php');
        
        $Base32 = new Base32();
        //$encoded = $Base32->encode($_POST['mb_id'][$k]);
        $encoded = $Base32->encode(str_pad($_POST['mb_id'][$k], 20 , "!&%"));
        
            $sql = " update {$g5['member_table']}
                        set grade = '{$_POST['grade'][$k]}',
                            mb_intercept_date = '{$_POST['mb_intercept_date'][$k]}',
                            mb_mailling = '{$_POST['mb_mailling'][$k]}',
                            mb_sms = '{$_POST['mb_sms'][$k]}',
                            mb_open = '{$_POST['mb_open'][$k]}',
                            mb_certify = '{$_POST['mb_certify'][$k]}',
                            mb_adult = '{$mb_adult}',
                            otp_key = '{$encoded}',
                            otp_flag = '{$_POST['otp_flag'][$k]}'
                        where mb_id = '{$_POST['mb_id'][$k]}' ";
            sql_query($sql);
        }
    }

} else if ($_POST['act_button'] == "선택삭제") {
   
    for ($i=0; $i<count($_POST['chk']); $i++)
    {
        // 실제 번호를 넘김
        $k = $_POST['chk'][$i];
        $mb = get_member($_POST['mb_id'][$k]);
        

        if (!$mb['mb_id']) {
            $msg .= $mb['mb_id'].' : 회원자료가 존재하지 않습니다.\\n';
        } else if ($member['mb_id'] == $mb['mb_id']) {
            $msg .= $mb['mb_id'].' : 로그인 중인 관리자는 삭제 할 수 없습니다.\\n';
        } else if (is_admin($mb['mb_id']) == 'super') {
            $msg .= $mb['mb_id'].' : 최고 관리자는 삭제할 수 없습니다.\\n';
        } else if ($is_admin != 'super' && $mb['mb_level'] >= $member['mb_level']) {
            $msg .= $mb['mb_id'].' : 자신보다 권한이 높거나 같은 회원은 삭제할 수 없습니다.\\n';
        } else {
            // 회원자료 삭제
            member_delete($mb['mb_id']);
        }
    }
}

if ($msg)
    //echo '<script> alert("'.$msg.'"); </script>';
    alert($msg);

goto_url('./member_list.php?'.$qstr);
?>