<?php
include_once('./_common.php');
include_once(G5_THEME_PATH.'/_include/head.php');
include_once(G5_THEME_PATH.'/_include/gnb.php');


$bo_table = "g5_write_notice";
$bo_table_java = "notice";

$list_cnt = sql_fetch("select count(*) as cnt from {$bo_table} where wr_1 = '1' order by wr_datetime desc");
$cnt = $list_cnt['cnt'];

$sql = "select * from {$bo_table} where wr_1 = '1' order by wr_datetime desc";
$list = sql_query($sql);

$title = 'News';

?>

	<!--<link rel="stylesheet" href= "<?=G5_THEME_URL?>/theme/css/style.css">-->

	<script>
		var open = '<?=$_GET['open']?>';
		var $selected;
		$(function() {
			var alterClass = function() {
				var ww = document.body.clientWidth;
				if (ww < 400) {
				$('.news-table').addClass('table-responsive');
				} else if (ww >= 401) {
				$('.news-table').removeClass('table-responsive');
				};
			};

			$(window).resize(function(){
				alterClass();
			});

			alterClass();

			$(document).on('click','.question' ,function(e) {
                var table = "<?=$bo_table_java?>";

				$selected = $(this).next();
				if($(this).hasClass('qa-open')){// 닫기
					$(this).removeClass('qa-open');
					 $selected.css('height','0px');
				}else{ // 열기
					$(this).addClass('qa-open');
                   //0px $(this).find('.views').text(Number($(this).find('.views').text()) + 1);

					$.get( g5_url + "/util/news_read.php", {
						bo_table : table,
						no : $(this).attr('no')
					}, function(data) {
                        //$('#notReadCnt').text(data.not_read_cnt);

						$selected.find('p.writing').html(data.writing);
						$selected.find('p.files').empty();
						$selected.find('div.images').empty();

						$.each(data.file_list, function( index, obj ) {
							if(obj.filename != ''){
								if(obj.bf_type == 0){
									var btn = $('<a>');
									btn.attr('href','/bbs/download.php?bo_table='+ table +'&wr_id=' + obj.wr_id + '&no=' + obj.bf_no);
									btn.html(obj.filename);
									$selected.find('p.files').append("<span class='font_red' style='font-weight:600'>Download : </span>").append(btn).append('<br>');
								}else {
									// console.log(obj)
									var img = $('<img>');

									img.attr('src','<?=G5_DATA_URL?>/file/'+table+'/' + obj.bf_file);
									$selected.find('div.images').append(img).append('<br>');
                                }
							}
						});

						$selected.css('height', '100%');
					},'json');
				}
			});

			if(open) {
				$('.question').eq(0).trigger('click');
			}
		});
	</script>


<section class='breadcrumb'>
	<ol>
		<li class="active title" data-i18n="news.뉴스"><?=$title?></li>
		<li class='home'><i class="ri-home-4-line"></i><a href="<?php echo G5_URL; ?>" data-i18n='news.홈'>Home</a></li>
		<li><a href="/page.php?id=<?=$title?>" data-i18n="news.뉴스"><?=$title?></a></li>
	</ol>
	<?php if($is_admin){?>
        <div class='admin_btn'><a class="btn wd btn-primary" style="color:white;" href="/bbs/write.php?bo_table=notice">admin</a></div>
    <?php }?>
</section>


<main >
    <div class='container'>

	<div class="qa-container">
		<div class="title round">
			<div class='row'>
				<div class="col-sm-3 col-3 date " data-i18n="news.날짜">Date</div>
				<div class="col-sm-7 col-7 inner_title text-center" data-i18n="news.제목">Title</div>
				<div class="col-sm-2 col-2 views" data-i18n="news.조회수">Views</div>
			</div>
		</div>
	</div>

	<?for($i; $row = sql_fetch_array($list); $i++){?>
        <div class="col-sm-12 col-12 content-box round news">
			<div class="qa-container">
				<div class="question row" no="<?echo $row['wr_id']?>">
					<div class="col-sm-3 col-3 date"><?echo date("d-m-Y", strtotime($row['wr_last']))?></div>
					<div class="col-sm-7 col-7 inner_title" ><?echo $row['wr_subject']?></div>
					<div class="col-sm-2 col-2 views"><?echo $row['wr_hit']?></div>
				</div>

				<div class="answer">
					<div class="images"></div>
					<div class="files"></div>
					<p class="writing"></p>
				</div>

				<?if($cnt == 0){?>
					<div style="height:200px; text-align:center;line-height:200px;font-size:1.5em;"> No news announced.</div>
				<?}?>
			</div>
        </div>
	<?}?>
	</div>
    <div class="gnb_dim"></div>

</main>


<script>
    $(function() {
        // $(".top_title h3").html("<img src='<?=G5_THEME_URL?>/_images/top_news.png' alt='아이콘'> <span data-i18n='news.뉴스'>NEWS</span>");
    });
</script>

<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>
