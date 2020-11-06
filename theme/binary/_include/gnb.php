<?php
$myLang = 'kor';

if($_COOKIE['myLang'])
{
	$myLang = $_COOKIE['myLang'];
}
 ?>
<script>
$(document).ready(function(){

	function setCookie(cookie_name, value, days) {
	  var exdate = new Date();
	  exdate.setDate(exdate.getDate() + days);
	  var cookie_value = escape(value) + ((days == null) ? '' : ';    expires=' + exdate.toUTCString());
	  document.cookie = cookie_name + '=' + cookie_value;
	}

	$.i18n.init({
		resGetPath: '/locales/my/__lng__.json',
		load: 'unspecific',
		fallbackLng: false,
		lng: 'eng'
	}, function (t){
		$('body').i18n();
	});

	$('#lang').on('change', function(e) {
		$.i18n.setLng($(this).val(), function(){
			$('body').i18n();
		});
		console.log($(this).val());
		setCookie('myLang',$(this).val(),1,'/');
		//localStorage.setItem('myLang',$(this).val());
	});

	$('#lang').val("<?=$myLang?>").change();
});
</script>


<section id="wrapper" >
<header>
	<?if($menubar){?>
	<div class="menuback">
		<a href="javascript:history.back();" class='back_icon'><i class="ri-arrow-left-s-line"></i></a>
	</div>
	<?}else{?>
	<div class="menu">
		<a href="#" class='menu_icon'><i class="ri-menu-2-line"></i></a>
	</div>
	<?}?>

	<?if(!$menubar){?>
	<nav class="left_gnbWrap">
		<a href="#" class="close">X</a>
		<div class="gnb_logo_top">
			<a href="/"><img src="<?=G5_THEME_URL?>/_images/gnb_logo.png" alt="ROCKET"></a>
		</div>
		<ul class="left_gnb">
			<!-- <li><a href="/"><img src="<?=G5_THEME_URL?>/_images/menu01.gif" alt="아이콘" ><span data-i18n="nav.메인화면">dashboard</span></a></li> -->

			<li><a href="/page.php?id=profile"><img src="<?=G5_THEME_URL?>/_images/menu02.gif" alt="아이콘" ><span data-i18n="nav.개인정보 & 보안설정">Profile & Settings</span></a></li>
			<!-- <li><a href="/page.php?id=mywallet"><img src="<?=G5_THEME_URL?>/_images/menu04.gif" alt="아이콘"><span data-i18n="nav.내 지갑">My wallet</span></a></li>
			<li><a href="/page.php?id=upstairs"><img src="<?=G5_THEME_URL?>/_images/menu05.gif" alt="아이콘"><span data-i18n="nav.투자">Purchase</span></a></li> -->
			<!-- <li><a href="/page.php?id=bonus_history"><img src="<?=G5_THEME_URL?>/_images/menu04.gif" alt="아이콘"><span data-i18n="nav.보너스 내역">Bonus history</span></a></li> -->

			<!--
			<li><a href="/page.php?id=deposit"><img src="<?=G5_THEME_URL?>/_images/menu04.gif" alt="아이콘"><span data-i18n="nav.입금">Deposit</span></a></li>
			<li><a href="/page.php?id=withdrawal"><img src="<?=G5_THEME_URL?>/_images/menu04.gif" alt="아이콘"><span data-i18n="nav.출금">Withdraw</span></a></li>
			-->

			<li><a href="/page.php?id=structure"><img src="<?=G5_THEME_URL?>/_images/menu05_1.gif" alt="아이콘"><span data-i18n="nav.추천도 보기">Level Structure</span></a></li>
			<!-- <li><a href="/page.php?id=binary"><img src="<?=G5_THEME_URL?>/_images/menu05_2.gif" alt="아이콘"><span data-i18n="nav.후원도 보기">Binary Structure</span></a></li> -->

			<!-- <li>
				<a href="#0" class="menu_tree clear_fix"><img src="<?=G5_THEME_URL?>/_images/menu05.gif" alt="아이콘"><span data-i18n="nav.비즈니스 현황">Business Status</span></a>
				<ul>

					
				</ul>
			</li> -->

			<li><a href="/page.php?id=news"><img src="<?=G5_THEME_URL?>/_images/menu07.gif" alt="아이콘"><span data-i18n="nav.뉴스">News</span></a></li>
			<li><a href="/page.php?id=support_center"><img src="<?=G5_THEME_URL?>/_images/menu08.gif" alt="아이콘"><span data-i18n="nav.지원 센터">Support Center</span></a></li>
			<!-- <li><a href="/page.php?id=referral_link"><img src="<?=G5_THEME_URL?>/_images/menu11.gif" alt="아이콘"><span data-i18n="nav.내 추천인 링크">My Referral Link</span></a></li> -->


			<li><a href="javascript:void(0);" class="logout_pop_open"><img src="<?=G5_THEME_URL?>/_images/menu12.gif" alt="아이콘"><span data-i18n="nav.로그아웃">LOGOUT</span></a></li>


			<!--<li><a href="/wallet/wallet.php?id=wallet"><img src="<?=G5_THEME_URL?>/_images/menu04.gif" alt="아이콘"><span data-i18n="nav.클립토 월렛">Crypto Wallets</span></a></li>-->
			<!--<li><a href="/page.php?id=purchase"><img src="<?=G5_THEME_URL?>/_images/menu06.gif" alt="아이콘"><span data-i18n="nav.팩 상품 구매하기">Purchase Packs</span></a></li>-->
			<!--<li><a href="/page.php?id=avatar"><img src="<?=G5_THEME_URL?>/_images/menu05_3.gif" alt="아이콘"><span data-i18n="nav.아바타 보기">Avatar Account</span></a></li>-->

			<!-- //GNB 상단으로
			<li>
				<a href="#0" class="menu_tree clear_fix"><img src="<?=G5_THEME_URL?>/_images/menu09.gif" alt="아이콘"><span data-i18n="nav.언어선택">Select Language</a>
				<ul>
					<li><a href="#0"><img src="<?=G5_THEME_URL?>/_images/menu09_eng.gif" alt="아이콘">English</a></li>
					<li><a href="#0"><img src="<?=G5_THEME_URL?>/_images/menu09_kor.gif" alt="아이콘">한국어</a></li>
					<li><a href="#0"><img src="<?=G5_THEME_URL?>/_images/menu09_vtn.gif" alt="아이콘">Tiếng Việt</a></li>
					<li><a href="#0"><img src="<?=G5_THEME_URL?>/_images/menu09_jpn.gif" alt="아이콘">日本語</a></li>
					<li><a href="#0"><img src="<?=G5_THEME_URL?>/_images/menu09_chn.gif" alt="아이콘">中文</a></li>
				</ul>
			</li>
			-->
			<!--<li><a href="/bbs/register_form.php"><img src="<?=G5_THEME_URL?>/_images/menu10.gif" alt="아이콘"><span data-i18n="nav.신규회원 등록">Enroll New Member</span></a></li>-->



		</ul>
	</nav>
	<?}?>

	<div class="top_title">
		<h3><a href="/"><img src= "<?=G5_THEME_URL?>/_images/title.png" alt="logo"></a></h3>
	</div>

	<div class="lang_selection user-drop-down-section">
		<div class="lang-sel">
			<select class="custom-select" id="lang">
			<option value="eng" selected>English</option>
			<option value="chn">中文</option>
			<option value="kor">한국어</option>
			</select>
		</div>
	</div>

</header>
