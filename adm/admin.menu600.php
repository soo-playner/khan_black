<?php
if($member['mb_id'] == 'admin'){
$menu['menu600'] = array (

    array('600000', '마케팅플랜', ''.G5_ADMIN_URL.'/bonus/bonus_list.php','bbs_board'),
    array('600080', '통화/시세 설정', G5_ADMIN_URL.'/bonus/config_price.php', 'bbs_board'),
    array('600100', '마케팅 수당 설정', G5_ADMIN_URL.'/bonus/bonus_config.php', 'bbs_board'),
   
    array('600200', '수당지급 및 지급내역', ''.G5_ADMIN_URL.'/bonus/bonus_list.php','bbs_board'),
    array('600300', '회원 직급 승급 기록', ''.G5_ADMIN_URL.'/bonus/member_upgrade.php','bbs_board'),

    /*
    array('600100', '수당지급조건설정 관리', ''.G5_ADMIN_URL.'/allowance_sett.php','bbs_board'),
    
    array('600300', '수당분할지급 관리', ''.G5_ADMIN_URL.'/dividend_list.php'),
    
    array('600400', 'B팩 수당',''.G5_ADMIN_URL.'/binary.php'),
    array('600500', '아바타 적금',''.G5_ADMIN_URL.'/avatar.php'),
	
    array('600400', '조직도(트리) 보기', ''.G5_ADMIN_URL.'/member_tree.php', 'bbs_board'),
    array('600500', '조직도(박스) 보기', ''.G5_ADMIN_URL.'/member_org.php', 'bbs_board'),
	*/
	
	/*
    array('600920', 'MP 승인', G5_ADMIN_URL.'/approve_marketer.php', 'bbs_board'),
    array('600930', 'MP 포인트 수당', G5_ADMIN_URL.'/mp_soodang.php', 'bbs_board'),
    */
);
}else{
    $menu['menu600'] = array (

        array('600000', '마케팅플랜', ''.G5_ADMIN_URL.'/bonus/bonus_list.php','bbs_board'),
        array('600200', '수당지급 및 지급내역', ''.G5_ADMIN_URL.'/bonus/bonus_list.php','bbs_board')
        
    );
}
?>