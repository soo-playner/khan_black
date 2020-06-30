<?
	// $menubar = 1;
	include_once('./_common.php');
	include_once(G5_THEME_PATH.'/_include/gnb.php');
	if( $member['mb_id'] == 'admin'){
		$tree_id = $config['cf_admin'];
		$tree_no = 1;
	}else{
		$tree_id = $member['mb_id'];
		$tree_no = $member['mb_no'];
	}

	login_check($member['mb_id']);

	///bbs/level_structure_upgraded.list.php 로드
	///bbs/level_structure_upgraded.search.php 검색
	///bbs/level_structure_upgraded.mem.php
	///util/level_structure.leg.php 스택
?>



<link rel="stylesheet" href="<?=G5_THEME_URL?>/_common/css/level_structure.css">
<style>
#now_id{width:calc(100% - 175px); margin-right:10px;}
</style>


<script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.11/lodash.min.js"></script>
<script>

var levelMap = {
	0 : '10',
	1 : '',
	2 : 'lvl-three dl_3depth',
	3 : 'lvl-four dl_4depth',
	4 : 'lvl-five dl_5depth',
	5 : 'lvl-six dl_6depth',
	9 : 'lvl-ten dl_10depth'
};


var depthMap = {
		0 : 'dl_1depth',
		1 : 'dl_2depth',
		2 : 'dl_3depth',
		3 : 'dl_4depth',
		4 : 'dl_5depth',
		5 : 'dl_6depth',
		6 : 'dl_7depth'
	};

var gradeMap = {
		0 : 'gr_0',
		1 : 'gr_1',
		2 : 'gr_2',
		3 : 'gr_3',
		4 : 'gr_4',
		5 : 'gr_5',
		6 : 'gr_6',
	};

	var $selected;
	var mb_no = '<?=$tree_no?>';
	//var xhr;

	$(function() {
		// 상세보기

		$(document).on('click','.lvl' ,function(e) {
			$(this).toggleClass('lvl-is-open');
			$selected = $(this).next();
			if($selected.css('max-height') != '0px' ){
				$selected.css('max-height','0px');
			}else{
				$selected.css('max-height', $selected.prop('scrollHeight') + 'px');
			}
			// console.log($(this).attr('mb_no'));
			if($(this).hasClass('lvl-is-open')){
				$.get( "/util/level_structure_upgraded.mem.php", {
					mb_no: $(this).attr('mb_no')
				}).done(function( data ) {
					if(data){
						$selected.find('.name').text(data.mb_id);
						$selected.find('.sponsor').text(data.mb_recommend);
						$selected.find('.enroll').text(daΩta.enrolled);
						if(data.mb_level > 1 && data.mb_level < 9){
							$selected.find('.rank').text((data.mb_level -2) + ' Star');
						}
						/* $selected.find('.email').text(data.mb_email);
						$selected.find('.pool1').text(data.it_pool1);
						$selected.find('.pool2').text(data.it_pool2);
						$selected.find('.pool3').text(data.it_pool3);
						$selected.find('.gpu').text(data.it_gpu); */
					}
				}).fail(function(e) {
					console.log( e );
				});
			}
		});

		 $(document).on('click','.lvl-username' ,function(e) {
			console.log($(this).text());

			getList($(this).text(), 'name');
			getLeg('<?=$tree_id?>', $(this).text());
			$('.search_container').removeClass("active");
		 });
/*/
		 $(document).on('click','.lv' ,function(e) {
			var search_mb_id = $(this).parent().find('.lvl-username').text();
			getList(search_mb_id, 'name');
			getLeg('<?=$tree_id?>', $(this).attr('mb_id'));
			e.stopPropagation();
		});
*/

		$(document).on('click','._lvl > .lv' ,togglebar);
		$(document).on('click','._lvl > .toggle' ,togglebar);

		function togglebar() {
			var con = $(this).parents('.lvl-container');
			var level = con.attr('class').replace('lvl-container ','');

			if(con.hasClass('closed')){
				con.nextUntil( "." + level ).removeClass('closed').show();
				con.removeClass('closed');
				$(this).parent().find('.toggle').css('color','#ccc');
			}else{
				$(this).parent().find('.toggle').css('color','black');
				con.nextUntil( "." + level ).hide();
				con.addClass('closed');
			}
			e.stopPropagation();
		}


		$(document).on('click','.go' ,function(e) {
			var search_mb_id = $(this).parent().parent().find('.lvl-username').text();

			console.log(search_mb_id);

			getList(search_mb_id, 'name');
			getLeg('<?=$tree_id?>', $(this).attr('mb_id'));
			e.stopPropagation();
		});

		// 검색결과 클릭
		$(document).on('click','.mbId' ,function(e) {
			getList($(this).text(), 'name');
			//getLeg('<?=$tree_id?>', $(this).text());
			$('.structure_search_container').removeClass("active");
		});

		// 엔터키
		$('#now_id').keydown(function (key) {
			if(key.keyCode == 13){
				key.preventDefault();
				//$('button.search-button').trigger('click');
				member_search();
			}
		});

		// 조직도 데이터 가져오기
		getList(Number(mb_no),'num');
		getLeg('<?=$tree_id?>', "<?=$tree_id?>");

	});


	function depthFirstTreeSort(arr, cmp) {

		function makeTree(arr) {
			var tree = {};
			for (var i = 0; i < arr.length; i++) {
				if (!tree[arr[i].mb_recommend_no]) tree[arr[i].mb_recommend_no] = [];
				tree[arr[i].mb_recommend_no].push(arr[i]);
			}
			return tree;
		}


		function depthFirstTraversal(tree, id, cmp, callback) {

			var children = tree[id];

			if (children) {
				children.sort(cmp);
				for (var i = 0; i < children.length; i++) {
					callback(children[i]);
					if(children[i].mb_no != mb_no){

							depthFirstTraversal(tree, children[i].mb_no, cmp, callback);
						}
					/*
					if(mb_no > 2){
						depthFirstTraversal(tree, children[i].mb_no, cmp, callback);
					}else{
						if(children[i].mb_no != mb_no){
							console.log(tree );
							depthFirstTraversal(tree, children[i].mb_no, cmp, callback);
						}
					}
					*/
				}

			}
		}

		var i = 0;
		var tree = makeTree(arr);
		depthFirstTraversal(tree, arr[0].mb_recommend_no, cmp, function(node) {
			arr[i++] = node;
		});
	}

	// function nameCmp(a, b) { return a.mb_id.localeCompare(b.mb_id); }
	nameCmp = function(a, b){ return a.mb_no < b.mb_no; }


	// 검색하는 부분
	function getMember(){
		console.log('get_member');
		var findemb_id = $("#now_id").val();

		getList( findemb_id, 'name' );
		getLeg('<?=$tree_id?>', mb_id);
	}

	function numberWithCommas(x) {
    	return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}


	function member_search(){
		console.log('member_search');
			if($("#now_id").val() == ""){
				//alert("Please enter a keyword.");
				commonModal('Notice','Please enter a keyword.',80);
				$("#now_id").focus();
				return;
			}

			$.get("/util/level_structure_upgraded.search.php", {
				keyword: $("#now_id").val()
			}).done(function( data ) {
				$('.structure_search_container').addClass("active");
				var vHtml = $('<div>');
				$.each(data, function( index, member ) {
					var line = $('<div>').append($('<strong>').addClass('mbId').html(member.mb_id));

					if(member.mb_name != ''){
						line.append('<br>');
						line.append( '(' + member.mb_name + ')');
					}else{
						line.css('line-height','50px');
					}
					vHtml.append(line);
				});
				$("#structure_search_result").html(vHtml.html());

				$(".structure_search_container .result_btn").click(function(){
				//console.log('close');
				$('.structure_search_container').removeClass("active");
		});
			}).fail(function(e) {
				console.log( e );
			});
		}

_
	function getList(member_no, type){

		$.get( "/util/level_structure_upgraded.list.php", {
			mb_no: member_no,
			type : type
		}).done(function( data ) {
			//tt = data;
			console.log(data );
			var minObj = _.minBy(data, function(o) { return Number(o.depth); });

			_.forEach(data, function(member) {
				member.treelvl = member.depth - minObj.depth;
				member.gradelvl = member.grade;
			});

			depthFirstTreeSort(data, nameCmp);

			$('#total').text(data.length);

			var vHtml = $('<div>');
			$.each(data, function( index, member ) {


				var row = $('#dup .lvl-container').clone();

				if(member.mb_block == '0'){
					var status = "Active";
				}else{
					var status = "Block";
				}

				row.addClass(depthMap[member.treelvl]);
				row.addClass(gradeMap[member.gradelvl]);

				row.find('.lvl-username').text(member.mb_id);
				row.find('.level').text('P '+ member.mb_level);

				/* 펼침 추가정보
				row.find('.lv').addClass('s_v'+member.mb_level);
				row.find('.lv').text('V'+ member.mb_level);

				row.find('.recommend_num').text(member.cnt);
				row.find('.Blevel_num').text(member.treelvl);

				row.find('.deposit_num').text(member.mb_deposit_point);
				row.find('.name').text(member.mb_name);
				row.find('.mb_level').text('V'+ member.mb_level);
				row.find('.recommend_name').text(member.mb_recommend);
				row.find('.email').text(member.mb_email);

				row.find('.legsale_num').text( numberWithCommas(member.stacks));
				row.find('.sales_day').text(member.sales_day);

				row.find('.block').text(status);
				*/
				vHtml.append(row);

			});

			$('#levelStructure').html(vHtml.html());
			$("html, body").animate({ scrollTop: 0 }, "fast");

			/*상세보기*/
			$('.accordion_wrap dl dd').css("display", "none");

			$('.accordion_wrap dt').click(function() {
				$(this).next().stop().slideToggle();
			});


		}).fail(function(e) {
			console.log( e );
		})
	}


	// 찾는 아이디에서 조상까지의 경로를 표시
	function getLeg(lastParent, findId){

		$.get("/util/level_structure.leg.php", {
			lastParent : lastParent,
			findId : findId
		}).done(function( data ) {
			var reversed = data.reverse();
			//console.log(reversed);

			var vHtml = $('<div>');
			$.each(reversed, function( index, str ) {
				if(vHtml.html() == ''){
					vHtml.append($('<span>').addClass('mbId').text(str));
				}else{
					vHtml.append(" >> ").append($('<span>').addClass('mbId').text(str));
				}
			});
			$('.leg-view-container .gray').html(vHtml.html());
		}).fail(function(e) {
			console.log( e );
		});
	}
	</script>

	<style>
		.toggle{float:right;width:38px;height:38px;display:inline-block;font-size:18px;text-align:center;font-weight:300;color:#ccc}
		.toggle i{vertical-align:middle;line-height:36px;}
	</style>

		<section class="v_center structure_wrap">
			<!--<p data-i18n='structure.데이터 크기로 인해 한번에 5대씩 화면에 나타납니다'>Due to the amount of data, only 5 steps are shown</p>-->
			<div class="btn_input_wrap">
				<input type="text" id="now_id" placeholder="Member Search" data-i18n='[placeholder]structure.회원찾기'/>

				<button type="button" class="btn wide blue" id="binary_search" style='height: 50px;margin: 0;' data-i18n='structure.검색' onclick="member_search();">Search</button>
			</div>
				<div class="structure_search_container">
					<div class="structure_search_result" id="structure_search_result">

					</div>
					<div class="result_btn">Close</div>
				</div>
			<div class="bin_top" data-i18n="structure.추천 계보" >
				Member Stack
			</div>

			<div class="leg-view-container">
				<div class="gray"></div>
			</div>

			<div class="main-container">
				<div id="levelStructure" class="accordion_wrap" ></div>
			</div>

			<div style="display:none;" id="dup">
				<dl class="lvl-container" >
					<dt class="_lvl">
						<p class="lv"></p>
						<span  class="lvl-username">HAZ</span>
						<!-- <span class="level badge"></span> -->
						<span class='toggle'><i class="ri-line-height"></i></span>
					</dt>
				</dl>
		 	</div>

		</section>

		<div class="gnb_dim"></div>

	</section>



	<script>
		$(function() {
			$(".top_title h3").html("<img src='<?=G5_THEME_URL?>/_images/top_structure.png' alt='아이콘'><span data-i18n='structure.조직도'> Level Structure</span>");
		});
	</script>

<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>
