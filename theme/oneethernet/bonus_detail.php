<?
	include_once('./_common.php');
    include_once(G5_THEME_PATH.'/_include/wallet.php');
    
    $title = 'Bonus history';
    $sub_title = 'Bonus detail';
?>

<style>
body{background:#efefef}
header{
	position: relative;
	width: 100%;
	background:white;
	color: #000;
	text-align: left;
	box-sizing:border-box;
	padding:10px 15px;
    box-shadow:0 1px 0px rgba(0,0,0,0.25);
    font-size:24px;
    line-height: 24px;
    display: flex;
}
header .back_btn i{vertical-align: middle}
header h5{line-height: 28px;}
</style>




<header>
    <a role="button" href="/page.php?id=bonus_history" class="back_btn" data-transition="slideup" data-direction="reverse">
        <i class="ri-arrow-left-s-line"></i>
    </a>
    <h5><?=strtoupper($val['cate'])?> Bonus <?=$val['day']?></h5>
</header>



<section class='breadcrumb'>
    <ol>
        <li class="active title"><?=$title?></li>
        <li ><i class="ri-home-4-line"></i><a href="<?php echo G5_URL; ?>">Home</a></li>
        <li><?=$title?> / </li>
        <li><?=$sub_title?></li>
    </ol>
</section>

    <main>
        <div class='container'>

            <div class="col-sm-12 col-12 content-box round" id="<?=$cate?>">
                <div class="box-header row">  
                <?
                    $sub_sql = "SELECT *,round(SUM(benefit),3) as total_benefit FROM soodang_pay WHERE day = '{$val['day']}' AND mb_id = '{$member['mb_id']}' and allowance_name='{$val['cate']}' GROUP BY DAY";
                    $sub_result = sql_query($sub_sql);
                    while($row_ = sql_fetch_array($sub_result) ){?>
                        <div class='col-8 text-left' >
                            <span><?=$row_['day']?></span>
                            <span style="font-size:80%"> [<?=strtoupper($row_['allowance_name'])?> BONUS ]</span>
                        </div>

                        <div class='col-4 text-right'>
                            <span> <i class="ri-add-line"></i></span>
                            <span><?=$row_['total_benefit']?></span>
                        </div>
                <?}?>
                </div>
            </div>

            <div class="col-sm-12 col-12 content-box round history_detail mb20">
            <div class="box-header">
                <h4><i class="ri-calendar-event-line"></i>  <?=$val['day']?></h4>
            </div>
            
            <div class="box-body">
                <?
                    $detail_sql = "SELECT * FROM soodang_pay WHERE day = '{$val['day']}' AND mb_id = '{$member['mb_id']}' and allowance_name='{$val['cate']}' ";
                    $detail_result = sql_query($detail_sql);
                    while($rows = sql_fetch_array($detail_result) ){?>
                    <div class="block row">
                        <div class='col-9 text-left'>
                            <span class='b_hist_exp'><?=$rows['rec']?></span>
                        </div>
                        <div class='col-3 text-right'>
                            <span> <i class="ri-add-line"></i></span>
                            <span><?=$rows['benefit']?></span>
                        </div>
                    </div>    
                <?}?>
            </div>

        </div>
    </main>


<div class="gnb_dim"></div>
</section>


<script>
$(function(){
    
    
    $('.back_btn').click(function () {
        //location.href='page.php?id=bonus_history';
        /*
        pageContainerElement.page({ domCache: false });
        $.domCache().remove();
        $.mobile.page.prototype.options.domCache = false;
        */
    });
    
});
</script>

<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>