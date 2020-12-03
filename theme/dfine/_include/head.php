<!DOCTYPE HTML>
<html lang="ko">
<head>
	<title><?php echo $g5['title'] ?></title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="height=device-height , width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
	<link rel="shortcut icon" href="../favicon.ico" />
	<link rel="icon" href="../favicon.ico">
	<meta name="Robots" content="ALL">

	<!-- 기본 공유 설정 //-->
	<meta name="title" content="D fine" />
	<meta name="subject" content="D fine" />
	<meta name="keywords" content="D fine" />
	<meta name="description" content="D fine" />
	<link rel="image_src" href="<?=G5_THEME_URL?>/img/default.png" />

	<!--대표 이미지 URL (이미지를 여러 개 지정할 수 있음) //-->
	<meta name="apple-mobile-web-app-title" content="<?=G5_THEME_URL?>/img/default.png" />
	<meta name="format-detection" content="telephone=no" />

	<!-- 페이스북 공유 + 카카오톡 설정 //-->
	<meta property="fb:app_id" content="" />
	<meta property="og:type" content="website" />
	<meta property="og:title" content="D fine" />
	<meta property="og:description" content="GLOBAL SMART CONTRACT CROWD FUNDING PLATFORM" />
	<meta property="og:site_name" content="D fine" />
	<meta property="og:image" content="" />
	<meta property="og:url" content="" />



	<!-- 기본 설정 //-->
	
	<link href="<?=G5_URL?>/css/bootstrap.min.css" rel="stylesheet">
	<link href="<?=G5_THEME_URL?>/_common/css/normalize.css" rel="stylesheet">
	<link href="<?=G5_THEME_URL?>/_common/css/common.css?ver=20200429" rel="stylesheet">
	<link href="<?=G5_THEME_URL?>/_common/css/jquery-ui.min.css" rel="stylesheet">
	<link href="<?=G5_THEME_URL?>/_common/css/gnb.css" rel="stylesheet">
	
	<!-- <link href="<?=G5_THEME_URL?>/_common/css/common_dark.css" rel="stylesheet">
	<link href="<?=G5_THEME_URL?>/_common/css/gnb_dark.css" rel="stylesheet"> -->



	<!-- CSS  기본 설정 //
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.0/normalize.css">-->
	<!--<link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">-->


	<!-- JQuery  기본 설정 //-->
	<script src="https://code.jquery.com/jquery-latest.min.js"></script>
	<script src="<?=G5_THEME_URL?>/_common/js/jquery-ui.min.js"></script>
	<script src="<?=G5_THEME_URL?>/_common/js/common.js"></script>
	<script src="<?=G5_THEME_URL?>/_common/js/gnb.js"></script>

	<!-- // 확인할것////////////////////////////////////////////////////////////////////////////////////////
	<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
	-->
	<link href="https://cdn.jsdelivr.net/npm/remixicon@2.3.0/fonts/remixicon.css" rel="stylesheet">

	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/js-sha256/0.9.0/sha256.js" type="text/javascript"></script>
  	<script src="https://cdnjs.cloudflare.com/ajax/libs/i18next/1.9.0/i18next.min.js" type="text/javascript"></script>





	<?
	include_once(G5_THEME_PATH.'/modal.html');
	include_once(G5_THEME_PATH.'/_include/popup.php');
	?>


	<script>
	// 자바스크립트에서 사용하는 전역변수 선언
	var g5_url       = "<?php echo G5_URL ?>";
	var g5_bbs_url   = "<?php echo G5_BBS_URL ?>";
	var g5_is_member = "<?php echo isset($is_member)?$is_member:''; ?>";
	var g5_is_admin  = "<?php echo isset($is_admin)?$is_admin:''; ?>";
	var g5_is_mobile = "<?php echo G5_IS_MOBILE ?>";
	var g5_bo_table  = "<?php echo isset($bo_table)?$bo_table:''; ?>";
	var g5_sca       = "<?php echo isset($sca)?$sca:''; ?>";
	var g5_editor    = "<?php echo ($config['cf_editor'] && $board['bo_use_dhtml_editor'])?$config['cf_editor']:''; ?>";
	var g5_cookie_domain = "<?php echo G5_COOKIE_DOMAIN ?>";
	<?php if(defined('G5_IS_ADMIN')) { ?>
	var g5_admin_url = "<?php echo G5_ADMIN_URL; ?>";
	<?php } ?>
	</script>

</head>
<body>

<?
/*서비스점검*/
$sql = " select * from maintenance";
$nw = sql_fetch($sql);

if($nw['nw_use'] == 'Y'){
	$maintenance = 'Y';
}else{
	$maintenance = 'N';
}

/* 접속내보내기*/
if($maintenance == 'Y' && $is_admin != 'super' &&  strpos($url,'adm')  < 1){
	$_SESSION['ss_mb_id']=0;
	include_once(G5_PATH.'/index_pop.php');
}

?>
