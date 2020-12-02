<?
	include_once('./_common.php');
	include_once(G5_THEME_PATH.'/_include/gnb.php');
	include_once(G5_THEME_PATH.'/_include/wallet.php');

	//매출액
	$mysales = $member['mb_deposit_point'];

	//보너스/예치금 퍼센트
	$bonus_per = bonus_state($member['mb_id']);
    $title = 'Bonus history';

    if (empty($stx)) $stx = 'daily';

/*
    $sql_common ="FROM {$g5['bonus']} ";
	$sql_search = " WHERE ";
	$sql_search .= "day between '{$fr_date}' and '{$to_date}' ";
	$sql_search .= "AND mb_id = '{$member['mb_id']}' GROUP BY allowance_name ";

	$sql = " select allowance_name, COUNT(*) AS cnt
			{$sql_common}
			{$sql_search} ";
    print_R($sql);
*/


$sql ="SELECT a.cate, a.day,COUNT(DAY) AS cnt, SUM(a.c_sum) AS d_sum FROM
(
SELECT allowance_name AS cate, DAY, round(SUM(benefit),3) AS c_sum  FROM soodang_pay WHERE day between '{$fr_date}' and '{$to_date}' AND mb_id = '{$member['mb_id']}' GROUP BY allowance_name,DAY
) a GROUP BY a.cate";
    $result = sql_query($sql);

?>

<!-- <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script> -->
<!-- <link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" />
<script src="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script> -->

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>



<section class='breadcrumb'>
        <ol>
            <li class="active title" data-i18n='bonus.보너스 내역'><?=$title?></li>
            <li class='home'><i class="ri-home-4-line"></i><a href="<?php echo G5_URL; ?>" data-i18n='bonus.홈'>Home</a></li>
            <li><a href="/page.php?id=<?=$title?>" data-i18n='bonus.보너스 내역'><?=$title?></a></li>
        </ol>
    </section>

    <main>
        <div class='container'>

			<div class="col-sm-12 col-12 content-box round primary">
                <div class='user-content'>
                    <li><p class='userid id_<?=$member['mb_level']?>'><?=$user?></p></li>
                    <li>
                        <h4><?=$member['mb_id']?></h4>
                        <h6><?=$member['mb_name']?></h6>
                    </li>
                </div>
                <div class="innerBox round col-sm-12 mt20" >
                    <dt class='col-7'><span class='t_shadow_white'>TOTAL BONUS</span></dt>
                    <dd class='col-5'><?=$member['mb_balance'];?></dd>
                </div>

                <div class="innerBox round col-sm-12" >
                    <div class='bonus_state_bar' id='total_B_bar'></div>
                    <dt class='col-7'><span class='t_shadow_white'>BONUS LIMIT</span></dt>
                    <dd class='col-5'><?=Number_format($bonus_per,1);?>%</dd>
                </div>
            </div>

            <!-- SEARCH -->
            <section class="col-sm-12 col-12 content-box round secondary" id="search-container">
            <form name="fsearch" id="fsearch" action="/page.php" method="GET">
                <input type="hidden" name="id" id="" value="bonus_history">
                <input type="hidden" name="stx" id="stx" value="">
                <div class="row">
                    <li class="col-40"><input type="text" id="fr_date" name="fr_date" class='date_picker' data-i18n="[placeholder]order.fromDate" placeholder="Date range from" value=<?=$fr_date?> /></li>
                    <li class="col-40"><input type="text" id="to_date" name="to_date" class='date_picker' data-i18n="[placeholder]order.toDate" placeholder="Date range to" value=<?=$to_date?> /></li>
                    <li class="col-20"><button type='button' class="btn wd inline blue filter_btn" onclick="search_submit();" data-i18n='bonus.검색'>Search</button></li>
                </div>
            </form>
            </section>
            <!-- //SEARCH -->


            <!-- 수당 -->
            <? while($row = sql_fetch_array($result) ){?>
            <div class="col-sm-12 col-12 content-box round" id="<?=$row['cate']?>">

                <div class="box-header row">
                    <div class='col-7 text-left'>
                        <span data-i18n='bonus.수당'><?=strtoupper($row['cate'])?> Bonus </span>
                        <span class='badge'><?=$row['cnt']?></span>
                    </div>

                    <div class='col-5 text-right nopadding'>
                        <span class='d_sum font_deepblue'> + <?=$row['d_sum']?></span>
                        <span class='btn inline caret'><i class="ri-arrow-down-s-line"></i></span>
                    </div>
                </div>

                <div class="box-body history_detail">
                    <?
                        $sub_sql = "SELECT *,round(SUM(benefit),3) as total_benefit FROM soodang_pay WHERE day between '{$fr_date}' and '{$to_date}' AND mb_id = '{$member['mb_id']}' and allowance_name='{$row['cate']}' GROUP BY DAY";
                        $sub_result = sql_query($sub_sql);
                        while($row_ = sql_fetch_array($sub_result) ){?>

                        <div class='inblock row' id="<?=$row['cate']?>_detail " data-target="<?=$row['cate']?>" data-day="<?=$row_['day']?>">
                            <dt><?=$row_['day']?></dt>
                            <dd>
                                <span> <i class="ri-add-line"></i></span>
                                <span><?=$row_['total_benefit']?></span>
                                <a href="/dialog.php?id=bonus_detail&cate=<?=$row['cate']?>&day=<?=$row_['day']?>" dat-rel='dialog' data-transition="slideup"><span class='btn inline more_btn'><i class="ri-more-2-line"></i></span></a>
                            </dd>
                        </div>
                    <?}?>
                </div>

            </div>
            <?}?>
            <!-- // 수당 -->

    </main>


	<div class="gnb_dim"></div>
</section>



<script>

var mb_balance = '<?=$member["mb_balance"]?>';

window.onload = function(){
  move(<?=$bonus_per?>);
}


function search_submit(act = null)
{
    console.log('search');
    var f = document.fsearch;
    f.stx.value = act;
    f.submit();
}

$(function(){
	$(".top_title h3").html("<a href='/'><img src='<?=G5_THEME_URL?>/img/title.png' alt='logo'></a>");

    //
    $('.hist').click(function () {
        var target = $(this).data('target');
    });

    $('.caret').click(function(){

        $(this).parent().parent().parent().find('.history_detail').slideToggle(300);
    });

    /*상단 분류 탭*/
    $('ul.tabs li').click(function () {
        search_submit($(this).attr('data-category'));
    });

    /*날짜선택 피커*/
    $("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
});
</script>

<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>
