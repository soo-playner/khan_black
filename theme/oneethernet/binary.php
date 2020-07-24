<?
	include_once('./_common.php');
	include_once(G5_THEME_PATH.'/_include/gnb.php');
	
	login_check($member['mb_id']);

	if($member['avatar_last'] > 0){
		$avatar_id = $member['mb_id'].'_'.sprintf("%02d", $member['avatar_last']);
		$avatar_list_sql = "SELECT * from g5_avatar_log WHERE mb_id = '{$member['mb_id']}' ";
		$avatar_list_result = sql_query($avatar_list_sql);
	}

	if($_GET['start_id']){
		$start_id = $_GET['start_id'];
	}else{
		if($member['avatar_last'] > 0){
			$start_id = $avatar_id;
		}else{
			$start_id = $member['mb_id'];
		}
	}

	function get_left_bottom($start_id){

		$sql = "select mb_id from g5_member where mb_brecommend='".$start_id."' and mb_brecommend_type='L'";
		$rst = sql_fetch($sql);
		$temp = $rst['mb_id'];

		if($temp==null || $temp==""){return '';}
		$left_bottom  = $temp;

		while(true){
			$sql2 = "select mb_id from g5_member where mb_brecommend='".$temp."' and mb_brecommend_type='L'";
			$rst2 = sql_fetch($sql2);

			if($rst2['mb_id']!=null &&  $rst2!=""){
				$temp = $rst2['mb_id'];
				$left_bottom  = $temp;
			}
			else
			{
				break;
			}

		}
		return $left_bottom;
	}

	function get_right_bottom($start_id){

		$sql = "select mb_id from g5_member where mb_brecommend='".$start_id."' and mb_brecommend_type='R' ";
		$rst = sql_fetch($sql);
		$temp = $rst['mb_id'];
		if($temp==null || $temp==""){return '';}
		$right_bottom  = $temp;
		while(true){
			$sql2 = "select mb_id from g5_member where mb_brecommend='".$temp."' and mb_brecommend_type='R' ";
			$rst2 = sql_fetch($sql2);

			if($rst2['mb_id']!=null && $rst2!=""){
				$temp = $rst2['mb_id'];
				$right_bottom  = $temp;
			}
			else
			{
				break;
			}

		}
		return $right_bottom;
	}

	$left_bottom = get_left_bottom($start_id);
	$right_bottom = get_right_bottom($start_id);

/* ____________________________________________________________________________*/



$sql = "select mb_id as b_recomm from g5_member where mb_brecommend='".$start_id."' and mb_brecommend_type='L'";
$sql_r = "select mb_id as b_recomm2 from g5_member where mb_brecommend='".$start_id."' and mb_brecommend_type='R'";

$brst = sql_fetch($sql);
$brst_r = sql_fetch($sql_r);

$b_recom_arr =  array();
array_push($b_recom_arr, $start_id);
array_push($b_recom_arr, $start_id);
array_push($b_recom_arr, $brst['b_recomm']);
array_push($b_recom_arr, $brst_r['b_recomm2']);


if($brst['b_recomm'])
	$sql2 = "select mb_id as b_recomm from g5_member where mb_brecommend='".$brst['b_recomm']."' and mb_brecommend_type='L'";

if($brst['b_recomm'])
	$sql2_r = "select mb_id as b_recomm2 from g5_member where mb_brecommend='".$brst['b_recomm']."' and mb_brecommend_type='R'";

$brst2 = sql_fetch($sql2);
$brst2_r = sql_fetch($sql2_r);

array_push($b_recom_arr,$brst2['b_recomm']);
array_push($b_recom_arr,$brst2_r['b_recomm2']);

$list_info = array();
$list_pinfo = array();


for($i=1;$i<=15;$i++){
	$sql_left = "select mb_id as b_recom_left from g5_member where mb_brecommend='".$b_recom_arr[$i]."' and mb_brecommend_type='L'";
	$sql_right = "select mb_id as b_recom_right from g5_member where mb_brecommend='".$b_recom_arr[$i]."' and mb_brecommend_type='R'";

	$left = sql_fetch($sql_left);
	$right = sql_fetch($sql_right);

	$sql = "select mb_level,grade, (select sum(pv) from iwol where mb_id ='$left[b_recom_left]' ) as left_p, (select sum(pv) from iwol where mb_id ='$right[b_recom_right]' ) as right_p  from g5_member where  mb_id ='$b_recom_arr[$i]' ";


	$rem_info = sql_fetch($sql);
	$my_rank = $rem_info['mb_level']+1;
	$my_grade= $rem_info['grade'];

	if($my_rank > 7){$my_rank = 7;}

	$my_rank_img = '<img src="'.G5_THEME_URL.'/_images/user_icon.png" width="20"><br>';

	array_push($list_info, $my_rank_img);

}



/* ____________________________________________________________________________*/

?>

<style>
	.material-icons{vertical-align:bottom;}
	.material-icons.grade1{color:black}
		.material-icons.grade2{color:red}
			.material-icons.grade3{color:blue}
				.material-icons.grade4{color:green}
</style>

	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="<?=G5_THEME_URL?>/_common/css/binary.css">

		<section class="v_center binary_wrap">

			<!-- 검색미사용
			<div class="btn_input_wrap">

				<form id="sForm" name="sForm" method="post" >
				<div class='row'>
					<div class='col-8'><input type="text" placeholder="Member Search" name="binary_seach" id="binary_seach" style='font-size:16px;' data-i18n='[placeholder]binary.회원찾기'/></div>
					<div class='col-4'><button type="button" class="btn wd blue search-button"  id="search_btn"><span data-i18n='binary.검색'>Search</span></button></div>
				</div>		
				</form>

				<div class="search_container">
					<div class="search_result" id="search_result" style='overflow:scroll'></div>
					<div class="result_btn">Close</div>
				</div>
			</div> -->
			<div class="bin_top" style='margin:15px 0;'><h3> Member Binary Struture </h3></div>
			<!-- STACK 미사용
			<div class="bin_top"><h5 data-i18n='binary.후원계보'> Member Stack </h5>

				<div class="leg-view-container">
					<div class="gray">
						<?
						/* $leg_stack = array();
						if($start_id != $member['mb_id']){
							$get_list_higher  = "select mb_brecommend from g5_member where mb_id='".$start_id."'";
							$higher_id = sql_fetch($get_list_higher);
							array_push($leg_stack, $higher_id['mb_brecommend']);
					
							while(true){
								if($higher_id['mb_brecommend'] != $member['mb_id'] || $higher_id['mb_brecommend'] == '1eth'){
								$get_list_higher  = "select mb_brecommend from g5_member where mb_id='".$higher_id['mb_brecommend']."'";
								$higher_id = sql_fetch($get_list_higher);
								array_push($leg_stack, $higher_id['mb_brecommend']);
								}else{break;}
							}

							$reverse_stack  = array_reverse($leg_stack);
							$cnt = count($reverse_stack) ;

							for($i=0;$cnt > $i; $i++){
								if($i == $cnt - 1){
								echo "<span class='leg-name' name='".$reverse_stack[$i]."'>".$reverse_stack[$i]."</span>";
								}else{
								echo "<span class='leg-name' name='".$reverse_stack[$i]."'>".$reverse_stack[$i]."<i class='fas fa-arrow-right'></i></span>";
								}
							}	
						} */
						?>

										
					</div>
				</div>

			</div> -->


				<div class="tree-container">
					<div class="tree">

						<div class="lvl1"> <!--1단계-->
							<div class="lvl" id="1" align="center">
								<?echo $list_info[0] ?>
								<?echo $b_recom_arr[1]?><br>
							</div>
						</div>

						<!--line-->
						<div class="line_1">
							<div class="line1-1"></div>
							<div class="line2"></div>
						</div>

						<div class="lvl2"> <!--2단계-->
						<?for($i=2; $i<4;$i++){
							if($b_recom_arr[$i]){?>

							<div class="lvl" id="<?echo $i ;?>" >
							<?echo $list_info[$i-1] ?>
							<?echo $b_recom_arr[$i]?><br>
							</div>

							<?}else{?>
								<div class="lvl" id="<?echo $i ;?>" >
								not yet regist member
								</div>
							<?}
						}?>
						</div>


					</div>

					<div class="page-scroll">
						<span id="left_top" data-i18n='binary.왼쪽 맨 아래로'>Left bottom</span>
						<span id="go_top" data-i18n='binary.맨 위로 가기'>Back to top</span>
						<span id="go_up_one" data-i18n='binary.한 단계 위로 가기'>One level up</span>
						<span id="right_top" data-i18n='binary.오른쪽 맨 아래로'>Right bottom</span>
					</div>
				</div>
					
				
				<div class="member-info">
				<div class="member-details">
					<h5><span data-i18n="tree.info" >My Member Avatar List</span> - <span class='m_id'><?=$member['mb_id'];?></span> </h5>
					<table class="table table-striped table-bordered">
							<tbody>
							<tr class='mem_btn' data-mem='<?=$member['mb_id']?>'>
								<th scope="row"  style='text-align:center' >master</th>
								<th ><span class='m_id'><?=$member['mb_id']?></span></th>
								<td> 생성일 : <?=$member['mb_open_date']?> </td>
							</tr>
							
							<?if($member['avatar_last'] > 0){
								while($avatar_list = sql_fetch_array($avatar_list_result)){
								?>
									<tr class='mem_btn' data-mem='<?=$avatar_list['avatar_id']?>'>
										<th scope="row" style='text-align:center' ><?= sprintf("%02d", $avatar_list['count'])?></th>
										<th ><span class='m_id'><?=$avatar_list['avatar_id']?></span></th>
										<td> 생성일 : <?=$avatar_list['create_dt']?> </td>
									</tr>	
								<?}?>
							<?}?>

						</tbody>
					</table>
				</div>

		</section>

		<div class="gnb_dim"></div>

		</section>

		<!-- SELECT TEMPLATE -->
		<select style="display:none;" id="dup" >
			<option value=""></option>
		</select>


	<script>
		$(function() {
			// $(".top_title h3").html("<img src='<?=G5_THEME_URL?>/_images/top_binary.png' alt='아이콘'><span data-i18n='binary.바이너리조직도'> Binary Structure</span>");
			// $('#wrapper').css("background", "#fff");
		});
	</script>


	<script>
	var b_recom_arr = JSON.parse('<? echo json_encode($b_recom_arr);?>');
	var $div = $('<div>');
	var data1 = {};

	$(function() {
		
		// 리스트 호출 로그인멤버기준
		$( ".lvl-open" ).each(function( index ) {
			var upperId = Math.floor($(this).attr("id")/2);
			var id = $(this).attr("id");
			var mem_id = "<?=$member['mb_id']?>";

			console.log("upperId : " +  upperId + " | mem : "+ mem_id + " | " + b_recom_arr[upperId]);
			//console.log("success : "+ b_recom_arr[upperId]);
			if(b_recom_arr[upperId]){
				$.ajax({
					url: g5_url+'/util/binary_tree_mem.php',
					type: 'POST',
					async: false,
					data: {
						mb_id: mem_id
					},
					dataType: 'json',
					success: function(result) {

						console.log("success" +result);

						$div.empty();
						$.each(result, function( index, obj ) {
							var opt = $('#dup > option').clone();
							opt.attr('value', obj.mb_id);
							opt.html(obj.mb_id);
							$div.append(opt);
						});
						$('#'+id+'.lvl-open').find('select').append($div.html());
					}
				});
			}
		});


		// 후원인 추가 등록 버튼
		$('.addMem').click(function(){
			//console.log('후원인등록');

			var no = $(this).parent().attr('id');
			var upperId = Math.floor(no/2);

			if(!b_recom_arr[upperId]){ // 상위 회원이 없을때
				commonModal('Error',"Can not place this position.",80);
				return;
			}


			if(!$(this).siblings('select').val()){
				commonModal('Error',"Select Member",80);
				return;
			}

			var set_type = "";
			if(no%2 == 0){ // 나머지가 0이면 좌측 노드
				set_type = "L";
			}else{
				set_type = "R";
			}
			 //console.log(set_type);
			 //console.log($(this).siblings('select').val());
			data1 = {
				"set_id": b_recom_arr[upperId],
				"set_type": set_type,
				"recommend_id": $(this).siblings('select').val()
			};
			$('#confirmModal').modal('show');
		});


		// 후원인 추가 등록 확인 > 저장
		$('#confirmModal #btnSave').on('click',function(e){
			$.ajax({
				url: g5_url+'/util/binary_tree_add.php',
				type: 'POST',
				async: false,
				data: data1,
				dataType: 'json',
				success: function(result) {
					//console.log(result);
					location.reload();
				},
				error: function(e){
					console.log(e);
				}
			});
		});

		//상단 나열이름 클릭
		$('.leg-name').click(function(){
			var move_id = $(this).attr("name");
			if(move_id){
				location.replace(g5_url + "/page.php?id=binary&start_id="+move_id);
			}
		});

		//회원카드 클릭
		$('.lvl').click(function(){
			var id_check = $(this).attr("id");
			var add_id = Math.floor(id_check/2);
			var add_id2 = id_check%2;
			//나머지가 0이면 Left //나머지가 1이면 Right
			//alert (b_recom_arr[id_check]);
			if(id_check!=1){
				location.replace(g5_url + "/page.php?id=binary&start_id="+b_recom_arr[id_check]);
			}
			//alert (add_id);
		});


		//회원검색 SET
		$('button.search-button').click(function(){
			if($("#binary_seach").val() == ""){
				commonModal('Error','Please enter a keyword.',80);
				$("#binary_seach").focus();
			}else{
				$.post(g5_url + "/util/ajax_get_tree_member.php", $("#sForm").serialize(),function(data){
					dimShow();
					$('.search_container').addClass("active");
					$("#search_result").html(data);
				});
			}

		});


		$('.result_btn').click(function(){
			$('.search_container').removeClass('active');
			dimHide();
		});

		/*
			$('#binary_seach').on('keydown',function(e){
				if(e.which == 13) {
					e.preventDefault();
					$('#search_btn').trigger('click');
				}
			});
		*/


		// 하단 4단계 버튼


		$("#left_top").click(function(){
			//var left_bottom = $('.8').val();
			var left_bottom =  "<?=$left_bottom?>";
			if(left_bottom!=null && left_bottom!=""){
				location.replace(g5_url + "/page.php?id=binary&start_id="+left_bottom);
			}
			else
				//alert("Can't move left bottom");
				commonModal('Error',"Can't move left bottom.",80);
		});

		$("#go_top").click(function(){
			location.replace(g5_url + "/page.php?id=binary&start_id=<?=$member['mb_id']?>");
		});

		$("#go_up_one").click(function(){

			var id = "<?=$start_id?>";
			//console.log(id);
			$.ajax({
				type: "POST",
				url: g5_url + "/util/binary_tree_uptree.php",
				cache: false,
				async: false,
				dataType: "json",
				data:  {
					start_id : id
				},
				success: function(data) {
						//alert(data.result);
						if(data.result!="")
							location.replace(g5_url + "/page.php?id=binary&start_id="+data.result);
						else
							//alert("Now member is Top");
							commonModal('Notice',"Now member is Top",80);
				}
			});
		});

		$("#right_top").click(function(){
			var right_bottom = "<?=$right_bottom?>";
			if(right_bottom!=null && right_bottom!=""){
				location.replace(g5_url + "/page.php?id=binary&start_id="+right_bottom);
			}
			else
				//alert("Can't move left bottom");
				commonModal('Error',"Can't move left bottom.",80);
		});

		$('.mem_btn').on('click',function(){
			var target = $(this).data('mem');
			go_member(target);
		});

	});

	function go_member(go_id){
		//location.replace(g5_url + "/page.php?id=binary&start_id="+data.result);
		location.replace(g5_url + "/page.php?id=binary&start_id="+go_id);
	}
</script>


<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>
