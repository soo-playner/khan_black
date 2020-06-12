<?php
include_once('./_common.php');

if (!$is_member)
    goto_url(G5_BBS_URL."/login.php?url=".urlencode(G5_SHOP_URL."/mypage.php"));

if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/mypage.php');
    return;
}

// 테마에 mypage.php 있으면 include
if(defined('G5_THEME_SHOP_PATH')) {
    $theme_mypage_file = G5_THEME_SHOP_PATH.'/mypage.php';
    if(is_file($theme_mypage_file)) {
        include_once($theme_mypage_file);
        return;
        unset($theme_mypage_file);
    }
}

$g5['title'] = $member['mb_name'].'님 마이페이지';
include_once('./_head.php');

// 쿠폰
$cp_count = 0;
$sql = " select cp_id
            from {$g5['g5_shop_coupon_table']}
            where mb_id IN ( '{$member['mb_id']}', '전체회원' )
              and cp_start <= '".G5_TIME_YMD."'
              and cp_end >= '".G5_TIME_YMD."' ";
$res = sql_query($sql);

for($k=0; $cp=sql_fetch_array($res); $k++) {
    if(!is_used_coupon($member['mb_id'], $cp['cp_id']))
        $cp_count++;
}


?>
<p class="blk" style="height:60px;"></p>
<div class="pk_page">
<style type="text/css">
.pk_page {font-size:18px;}
.mb_info {float:left;width:65%;border:solid 1px #ddd;}
.mb_info h2 {height:65px;line-height:65px;font-size:20px;padding-left:20px;color:#333;background-color:#E0E0E7;}
.mb_info .cont {padding:30px;min-height:242px;}
.mb_info .cont li {line-height:45px;color:#444;}
.mb_info .cont li span {display:inline-block;*display:inline;*zoom:1;font-weight:bold;color:#222;width:160px;}

.status_info {float:right;width:32%;}
.info_Bx {padding:20px;border:solid 1px #ddd;background-color:#E0E0E7;color:#777;min-height:67px;}
.info_Bx h2 {font-size:20px;color:#333;line-height:40px;margin-bottom:20px;}
.info_Bx h2 span,
.info_Bx h2 a {float:right;color:#1DC2BB;font-family:"arial";}

.etc_info {border:solid 1px #ddd;}
.etc_info h2 {height:65px;line-height:65px;font-size:20px;padding-left:20px;color:#333;background-color:#E0E0E7;}
.etc_info .cont {padding:30px;}
.etc_info .cont li {display:inline-block;*display:inline;*zoom:1;width:33%;line-height:45px;color:#444;}
.etc_info .cont li span:first-child {display:inline-block;*display:inline;*zoom:1;font-weight:bold;color:#222;width:160px;}

span.btn,
a.btn {display:inline-block;*display:inline;*zoom:1;height:33px;line-height:33px;padding:0 15px;border-radius:3px;background-color:#1DC2BB;color:#fff;}

</style>
	<div class="my_info">
		<div class="mb_info">
			<h2>Personal Information</h2>

			<div class="cont">
				<ul>
					<li><span>Personal Information</span> <a href="<?php echo G5_BBS_URL; ?>/member_confirm.php?url=register_form.php" class="btn">회원정보수정</a></li>
					<li><span>성명</span> <?=$member['mb_name']?></li>
					<li><span>전화번호</span> <?=($member['mb_hp'])?$member['mb_hp']:$member['mb_tel']?></li>
					<li><span>Rank</span> <?=($member['mb_1'])?$member['mb_1']:"-"?></li>
					<li><span>소속</span> <?=($member['mb_2'])?$member['mb_2']:"-"?></li>
				</ul>
			</div><!-- // cont -->
		</div><!-- // mb_info -->

		<div class="status_info">
			<div class="info_Bx">
				<h2>누적구매 <span><?=number_format(4564654);?> 원</span></h2>
			</div><!-- // info_Bx -->
		
			<p class="blk" style="height:20px;"></p>

			<div class="info_Bx">
				<h2>금월구매 <span><?=number_format(4564654);?> 원</span></h2>
			</div><!-- // info_Bx -->

			<p class="blk" style="height:20px;"></p>

			<div class="info_Bx">
				<h2>PV <span><a href="<?php echo G5_BBS_URL; ?>/point.php" target="_blank" class="win_point"><?php echo number_format($member['mb_point']); ?> 원</a></span></h2>
			</div><!-- // info_Bx -->
		</div><!-- // status_info -->
		<p class="clr"></p>

		<p class="blk" style="height:50px;"></p>

		<div class="etc_info">
			<h2>파트너정보</h2>

			<div class="cont">
				<ul>
					<li><span>추천파트너</span> <?=($member['mb_recommend'])?$member['mb_recommend']:"-"?></li>
					<li><span>파트너 누적실적</span> -</li>
					<li><span>파트너 금월실적</span> -</li>
				</ul>
			</div><!-- // cont -->
		</div><!-- // etc_info -->

		<p class="blk" style="height:50px;"></p>

		<div class="etc_info">
			<h2>후원정보</h2>

			<div class="cont">
				<ul>
					<li><span>산하조직</span> -</li>
					<li><span>산하 누적실적</span> -</li>
					<li><span>산하 금월실적</span> -</li>
				</ul>
			</div><!-- // cont -->
		</div><!-- // etc_info -->

		<p class="blk" style="height:50px;"></p>

		<div class="etc_info">
			<h2>My team information</h2>

			<div class="cont">
				<ul>
					<li><span>My team information</span> <span class="btn">보기</span></li>
					<li><span>Enrollment Tree</span> <a href="#" class="btn">보기</a></li>
				</ul>
			</div><!-- // cont -->
		</div><!-- // etc_info -->

		<p class="blk" style="height:50px;"></p>

		<div class="etc_info">
			<h2>최근 주문내역</h2>

			<div class="cont">
        <?php
        // 최근 주문내역
        define("_ORDERINQUIRY_", true);

        $limit = " limit 0, 5 ";
        include G5_SHOP_PATH.'/orderinquiry.sub.php';
        ?>

        <div class="smb_my_more">
            <a href="./orderinquiry.php" class="btn01">주문내역 더보기</a>
        </div>
			</div><!-- // cont -->
		</div><!-- // etc_info -->

	</div><!-- // my_info -->

</div><!-- // pk_page -->

<!-- 마이페이지 시작 { -->
<div id="smb_my">

    <!-- 회원정보 개요 시작 { -->
    <section id="smb_my_ov">
        <h2>회원정보 개요</h2>

        <div id="smb_my_act" style="display:none;">
            <ul>
                <li><a href="<?php echo G5_BBS_URL; ?>/member_confirm.php?url=member_leave.php" onclick="return member_leave();" class="btn02">회원탈퇴</a></li>
            </ul>
        </div>
    </section>
    <!-- } 회원정보 개요 끝 -->

</div>

<script>
$(function() {
    $(".win_coupon").click(function() {
        var new_win = window.open($(this).attr("href"), "win_coupon", "left=100,top=100,width=700, height=600, scrollbars=1");
        new_win.focus();
        return false;
    });
});

function member_leave()
{
    return confirm('정말 회원에서 탈퇴 하시겠습니까?')
}
</script>
<!-- } 마이페이지 끝 -->

<?php
include_once("./_tail.php");
?>