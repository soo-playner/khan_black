<?
	include_once('./_common.php');
	include_once(G5_THEME_PATH.'/_include/gnb.php');
	//print_r($member);

	login_check($member['mb_id']);

/*
	if($_GET['start_id']){
		$start_id = $_GET['start_id'];
	}else{
		if($member['mb_id'] == 'admin'){
			$start_id = $config['cf_admin'];
		}else{
			$start_id = $member['mb_id'];
		}
	}
*/

	if($_GET['start_id']){
		$start_id = $_GET['start_id'];
	}else{
		$start_id = $member['mb_id'];
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

/*
$get_point_cnt1 = "CREATE TEMPORARY TABLE IF NOT EXISTS tb_real2 select * from iwol where mb_id='".$brst['b_recomm']."' and pv>=0 and kind in (0,2,22,9,99);";
sql_query($get_point_cnt1);

$get_point_cnt2 = "select count(A.iwolday) as total_row from tb_real2 A left join iwol B on A.iwolday=B.iwolday where B.mb_id='".$brst_r['b_recomm2']."' and B.pv>=0 and B.kind in (0,2, 22, 9, 99) ";
$cnt_rst = sql_fetch($get_point_cnt2);
$total_count = $cnt_rst['total_row'];
*/

$rows = 12;
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함


$get_point1 = "CREATE TEMPORARY TABLE IF NOT EXISTS tb_real select * from iwol where mb_id='".$brst['b_recomm']."' and pv>0 and kind in (0,2,22,9,99);";
sql_query($get_point1);

$get_point2 = "(select A.iwolday, A.mb_id as l_mbid, A.pv as l_pv, B.mb_id as r_mbid, B.pv as r_pv from tb_real A left join iwol B on A.iwolday=B.iwolday where B.mb_id='".$brst_r['b_recomm2']."' and B.pv>=0 and B.kind in (0,2,9,22,99) order by A.no desc limit $from_record, $rows)";


$b_recom_arr =  array();
array_push($b_recom_arr, $start_id);
array_push($b_recom_arr, $start_id);
array_push($b_recom_arr, $brst['b_recomm']);
array_push($b_recom_arr, $brst_r['b_recomm2']);

$leg_l = "select sum(pv) as pv from iwol where mb_id = '".$brst['b_recomm']."' order by no desc" ;
$today_l = "select habu_day_sales, mb_my_sales from g5_member where mb_id = '".$brst['b_recomm']."'" ;
$iwol_l_value = sql_fetch($leg_l);
$today_l_value = sql_fetch($today_l);
$total_l_value = $iwol_l_value['pv']+$today_l_value['habu_day_sales']+$today_l_value['mb_my_sales'];

$leg_r = "select sum(pv) as pv from iwol where mb_id = '".$brst_r['b_recomm2']."' order by no desc";
$today_r = "select habu_day_sales, mb_my_sales from g5_member where mb_id = '".$brst_r['b_recomm2']."'" ;
$iwol_r_value = sql_fetch($leg_r);
$today_r_value = sql_fetch($today_r);
$total_r_value = $iwol_r_value['pv']+$today_r_value['habu_day_sales']+$today_r_value['mb_my_sales'];

if($brst['b_recomm'])
$sql2 = "select mb_id as b_recomm from g5_member where mb_brecommend='".$brst['b_recomm']."' and mb_brecommend_type='L'";
if($brst['b_recomm'])
$sql2_r = "select mb_id as b_recomm2 from g5_member where mb_brecommend='".$brst['b_recomm']."' and mb_brecommend_type='R'";
$brst2 = sql_fetch($sql2);
$brst2_r = sql_fetch($sql2_r);
//echo " 3".$brst2[b_recomm]." 4".$brst2[b_recomm2];
array_push($b_recom_arr,$brst2['b_recomm']);
array_push($b_recom_arr,$brst2_r['b_recomm2']);


if($brst_r['b_recomm2'])
$sql3 = "select mb_id as b_recomm from g5_member where mb_brecommend='".$brst_r['b_recomm2']."' and mb_brecommend_type='L'";
if($brst_r['b_recomm2'])
$sql3_r = "select mb_id as b_recomm2 from g5_member where mb_brecommend='".$brst_r['b_recomm2']."' and mb_brecommend_type='R'";
$brst3 = sql_fetch($sql3);
$brst3_r = sql_fetch($sql3_r);
//echo " 5".$brst3[b_recomm]." 6".$brst3[b_recomm2];
array_push($b_recom_arr,$brst3['b_recomm']);
array_push($b_recom_arr,$brst3_r['b_recomm2']);

if($brst2['b_recomm'])
$sql4 = "select mb_id as b_recomm from g5_member where mb_brecommend='".$brst2['b_recomm']."' and mb_brecommend_type='L'";
if($brst2['b_recomm'])
$sql4_r = "select mb_id as b_recomm2 from g5_member where mb_brecommend='".$brst2['b_recomm']."' and mb_brecommend_type='R'";

$brst4 = sql_fetch($sql4);
$brst4_r = sql_fetch($sql4_r);
//echo " 7".$brst4[b_recomm]." 8".$brst4[b_recomm2];
array_push($b_recom_arr,$brst4['b_recomm']);
array_push($b_recom_arr,$brst4_r['b_recomm2']);

if($brst2_r['b_recomm2'])
$sql5 = "select mb_id as b_recomm from g5_member where mb_brecommend='".$brst2_r['b_recomm2']."' and mb_brecommend_type='L'";
if($brst2_r['b_recomm2'])
$sql5_r = "select mb_id as b_recomm2 from g5_member where mb_brecommend='".$brst2_r['b_recomm2']."' and mb_brecommend_type='R'";
$brst5 = sql_fetch($sql5);
$brst5_r = sql_fetch($sql5_r);
//echo " 9".$brst5[b_recomm]." 10".$brst5[b_recomm2];
array_push($b_recom_arr,$brst5['b_recomm']);
array_push($b_recom_arr,$brst5_r['b_recomm2']);

if($brst3['b_recomm'])
$sql6 = "select mb_id as b_recomm from g5_member where mb_brecommend='".$brst3['b_recomm']."' and mb_brecommend_type='L'";
if($brst3['b_recomm'])
$sql6_r = "select mb_id as b_recomm2 from g5_member where mb_brecommend='".$brst3['b_recomm']."' and mb_brecommend_type='R'";
$brst6 = sql_fetch($sql6);
$brst6_r = sql_fetch($sql6_r);
//echo " 11".$brst6[b_recomm]." 12".$brst6[b_recomm2];
array_push($b_recom_arr,$brst6['b_recomm']);
array_push($b_recom_arr,$brst6_r['b_recomm2']);

if($brst3_r['b_recomm2'])
$sql7 = "select mb_id as b_recomm from g5_member where mb_brecommend='".$brst3_r['b_recomm2']."' and mb_brecommend_type='L'";
if($brst3_r['b_recomm2'])
$sql7_r = "select mb_id as b_recomm2 from g5_member where mb_brecommend='".$brst3_r['b_recomm2']."' and mb_brecommend_type='R'";
$brst7 = sql_fetch($sql7);
$brst7_r = sql_fetch($sql7_r);
//echo " 13".$brst7[b_recomm]." 14".$brst7[b_recomm2];
array_push($b_recom_arr,$brst7['b_recomm']);
array_push($b_recom_arr,$brst7_r['b_recomm2']);


$list_info = array();
$list_pinfo = array();
$left_point = array();
$right_point = array();

for($i=1;$i<=15;$i++){
	$sql_left = "select mb_id as b_recom_left from g5_member where mb_brecommend='".$b_recom_arr[$i]."' and mb_brecommend_type='L'";
	$sql_right = "select mb_id as b_recom_right from g5_member where mb_brecommend='".$b_recom_arr[$i]."' and mb_brecommend_type='R'";

	$left = sql_fetch($sql_left);
	$right = sql_fetch($sql_right);

	$sql = "select it_pool1, it_pool2, it_pool3, it_pool4, it_pool5, it_gpu, mb_level,grade, (select sum(pv) from iwol where mb_id ='$left[b_recom_left]' ) as left_p, (select sum(pv) from iwol where mb_id ='$right[b_recom_right]' ) as right_p  from g5_member where  mb_id ='$b_recom_arr[$i]' ";


	$rem_info = sql_fetch($sql);
	$my_rank = $rem_info['mb_level']+1;
	$my_grade= $rem_info['grade'];

	if($my_rank > 7){$my_rank = 7;}

	$my_rank_img = '<img src="'.G5_THEME_URL.'/_images/user_icon.png" width="20"><br>';

	array_push($list_info, $my_rank_img);
	array_push($list_pinfo, $my_pool_lv);
	array_push($left_point, floor($rem_info['left_p']/1000));
	array_push($right_point, floor ($rem_info['right_p']/1000));

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

			<div class="btn_input_wrap">

				<form id="sForm" name="sForm" method="post" >
				<div class='row'>
					<div class='col-8'><input type="text" placeholder="Member Search" name="binary_seach" id="binary_seach" data-i18n='[placeholder]binary.회원찾기'/></div>
					<div class='col-4'><button type="button" class="btn wd blue search-button"  id="search_btn"><span data-i18n='binary.검색'>Search</span></button></div>
				</div>		
				</form>

				<div class="search_container">
					<div class="search_result" id="search_result"></div>
					<div class="result_btn">Close</div>
				</div>
			</div>


			<div class="bin_top"><h5 data-i18n='binary.후원계보'> Member Stack </h5>

				<div class="leg-view-container">
					<div class="gray">
						<?$leg_stack = array();?>
						<?
							if($start_id!=$member['mb_id']){
								$get_list_higher  = "select mb_brecommend from g5_member where mb_id='".$start_id."'";
								$higher_id = sql_fetch($get_list_higher);
								 array_push($leg_stack, $higher_id['mb_brecommend']);
						?>

						<?
							while(true){
								if($higher_id['mb_brecommend'] != $member['mb_id']){
								$get_list_higher  = "select mb_brecommend from g5_member where mb_id='".$higher_id['mb_brecommend']."'";
								$higher_id = sql_fetch($get_list_higher);
						?>
								<? array_push($leg_stack, $higher_id['mb_brecommend']);?>

						<?
								}
								else{
									break;
								}
							}
							 $reverse_stack  = array_reverse($leg_stack);
							$cnt = count($reverse_stack) ;

							for($i=0;$cnt > $i; $i++){
								if($i == $cnt - 1){ ?>
								<span class="leg-name" name="<?echo $reverse_stack[$i];?>"><?echo $reverse_stack[$i];?> </span>
								<?}else{?>
							<span class="leg-name" name="<?echo $reverse_stack[$i];?>"><?echo $reverse_stack[$i];?><i class="fas fa-arrow-right"></i> </span>
							<?}?>
							<?
							}
						}
						?>
					</div>
				</div>

			</div>


				<div class="tree-container">
					<div class="tree">

						<div class="lvl1"> <!--1단계-->
							<div class="lvl" id="1" align="center">
								<?echo $list_info[0] ?>
								<?echo $b_recom_arr[1]?><br>
								<?echo $list_pinfo[0]?><br>
								<!-- L : <?echo $left_point[0]?> / R : <?echo $right_point[0]?> -->
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
							<?echo $list_pinfo[$i-1]?><br>
							 <!-- L : <?echo $left_point[$i-1]?> / R : <?echo $right_point[$i-1]?> -->
							</div>

							<?}else{?>
							<div class="lvl-open" id="<?echo $i ;?>" >
								<select>
									<option selected value="" data-i18n='binary.회원선택하기'>Select Member</option>
								</select>
								<button class="addMem"><span data-i18n='binary.등록하기'>Add member</span></button>
							</div>
							<?}//else end
							}//for end?>
						</div>


						<!--line-->
						<div class="line_2_con">
							<div class="line_2">
							   <div class="line1-1"></div>
								<div class="line2"></div>
							</div>
							<div class="line_2">
								<div class="line1-1"></div>
								<div class="line2"></div>
							</div>
						</div>

						<div class="lvl3"> <!--3단계-->
						<?for($i=4; $i<8 ;$i++){
							if($b_recom_arr[$i]){
						?>
							<div class="lvl" id="<?echo $i ;?>" >
								<?echo $list_info[$i-1] ?>
								<?echo $b_recom_arr[$i]?><br>
								<?echo $list_pinfo[$i-1]?><br>
								 <!-- L : <?echo $left_point[$i-1]?> / R : <?echo $right_point[$i-1]?> -->
							</div>
							<?//if end}
							}
							else{?>
							<div class="lvl-open" id="<?echo $i ;?>" >
								<select>
									<option selected="" value="" data-i18n='binary.회원선택하기'>Select Member</option>
								</select>
								<button class="addMem"><span data-i18n='binary.등록하기'>등록하기</span></button>
							</div>
							<?}//else end
							}//for end?>
						</div>
					</div>

					<div class="page-scroll">
						<span id="left_top" data-i18n='binary.왼쪽 맨 아래로'>Left bottom</span>
						<span id="go_top" data-i18n='binary.맨 위로 가기'>Back to top</span>
						<span id="go_up_one" data-i18n='binary.한 단계 위로 가기'>One level up</span>
						<span id="right_top" data-i18n='binary.오른쪽 맨 아래로'>Right bottom</span>
					</div>
				</div>

				<!--
				<?
				$mb_id = $start_id;
				$distance=0;

				while($mb_id!=$start_id){
					$get_recommend  = "select mb_recommend from g5_member where mb_id='".$mb_id."'";
					$rst_recom = sql_fetch($get_recommend);
					$mb_id = $rst_recom['mb_recommend'];
					$distance++;
				}

				$now_member = get_member($start_id); //get member info
				$member_nation = "select * from pinna_nation_code where code = ".$now_member['nation_number'];
				//SELECT * FROM  `pinna_nation_code`
				$nation_rst = sql_fetch($member_nation);

				$get_recom = "select count(mb_recommend) as recom_cnt from g5_member where mb_recommend = '".$start_id."'";
				$recom_rst = sql_fetch($get_recom);

				?>


				<div class="member-info">
				<div class="member-details">
					<h5><span data-i18n="tree.info" >Member Information</span> - <?echo $now_member['mb_id'];?> </h5>
					<table class="table table-striped table-bordered">
						<tbody>
						<tr>
							<th scope="row" data-i18n="tree.fName" >First Name</th>
							<td><?echo $now_member['first_name'];?> </td>
						</tr>
						<tr>
							<th scope="row" data-i18n="tree.lName" >Last Name</th>
							<td><?echo $now_member['last_name'];?></td>
						</tr>
						<tr>
							<th scope="row" data-i18n="tree.country" >Country</th>
							<td><?echo $nation_rst['nationv_en'];?></td>
						</tr>
						<tr>
							<th scope="row" data-i18n="tree.spon" >Sponsor</th>
							<td><?echo $now_member['mb_recommend'];?></td>
						</tr>

						<tr>
							<th scope="row" data-i18n="tree.pack" >Packages</th>
							<td>
								<?echo $my_pool_lv?>
							</td>
						</tr>

						<tr>
							<th scope="row" data-i18n="tree.rank">Rank</th>
							<td><?echo $my_rank_img?></td>
						</tr>

						<tr>
							<th scope="row" data-i18n="tree.stat">Status</th>
							<td>Active</td>
						</tr>
						<tr>
							<th scope="row" data-i18n="tree.left">Left Volume</th>
							<td><?echo round($total_l_value/1000)?></td>
						</tr>
						<tr>
							<th scope="row" data-i18n="tree.right" >Right Volume</th>
							<td><?echo round($total_r_value/1000)?></td>
						</tr>
						<tr>
							<th scope="row" data-i18n="tree.dist" >Distance From Me</th>
							<td><?echo $distance?></td>
						</tr>

						<tr>
							<th scope="row" data-i18n="tree.sponsored" >Total Sponsored</th>
							<td><?echo $recom_rst['recom_cnt'];?></td>
						</tr>
						<tr>
							<th scope="row" data-i18n="tree.members" >Total Binary Members</th>
							<td><?echo $now_member['mb_b_child'];?> </td>
						</tr>
						<tr>
							<th scope="row" data-i18n="tree.enroll" >Enrollment Date</th>
							<td><?echo $now_member['mb_open_date'];?></td>
						</tr>

						<tr>
							<th scope="row" data-i18n="tree.place" >Placement Date</th>
							<td><?echo $now_member['mb_bre_time'];?></td>
						</tr>
						</tbody>
					</table>
				</div>
				-->


					<!-- <div class="member-volume">
						<h5 data-i18n="binary.바이너리 볼륨" >Binary Volume</h5>

							<?
								$res_point = sql_query($get_point2);
							?>
							<table class="table table-hover">
								<thead>
									<tr>
										<th scope="col" >Day</th>
										<th scope="col" >Left Point</th>
										<th scope="col" >Right Point</th>
									</tr>
								</thead>
								<tbody>
								<?while($list=sql_fetch_array($res_point)){?>
								<tr>
								<td><?echo $list['iwolday'];?></td>
								<td><?echo round( $list['l_pv']/1000,2);?></td>
								<td><?echo round($list['r_pv']/1000,2);?></td>
								</tr>
								<?}?>
								</tbody>
							</table>

								<?php
									$qstr = 'start_id='.$start_id;
									$pagelist = get_paging_new($config['cf_write_pages'], $page, $total_page, $_SERVER['SCRIPT_NAME'].'?'.$qstr.'&amp;domain='.$domain.'&amp;page=');
									if ($pagelist) {
										echo $pagelist;
									}
								?>

							<nav class="pagination-container"></nav>
					  </div> -->


		</section>

		<div class="gnb_dim"></div>

		</section>

		<!-- SELECT TEMPLATE -->
		<select style="display:none;" id="dup" >
			<option value=""></option>
		</select>


	<script>
		$(function() {
			$(".top_title h3").html("<img src='<?=G5_THEME_URL?>/_images/top_binary.png' alt='아이콘'><span data-i18n='binary.바이너리조직도'> Binary Structure</span>");
			$('#wrapper').css("background", "#fff");
		});
	</script>


	<script>
	var b_recom_arr = JSON.parse('<? echo json_encode($b_recom_arr);?>');
	var $div = $('<div>');
	var data1 = {};

	$(function() {

		// 리스트 호출 바로윗단계기준 호출
		/*
		$( ".lvl-open" ).each(function( index ) {
			var upperId = Math.floor($(this).attr("id")/2);
			console.log("upperId : " +  upperId);

			var id = $(this).attr("id");

			if(b_recom_arr['upperId']){ // 상위 회원이 있을때
				$.ajax({
					url: g5_url+'/util/binary_tree_mem.php',
					type: 'GET',
					async: false,
					data: {
						mb_id: b_recom_arr['upperId']
					},
					dataType: 'json',
					success: function(result) {
						 //console.log(result);
						$div.empty();
						$.each(result, function( index, obj ) {
							var opt = $('#dup > option').clone();
							opt.attr('value', obj.mb_id);
							opt.html(obj.mb_id + '(' + obj.first_name + ' ' + obj.last_name + ')');
							$div.append(opt);
						});
						$('#'+id+'.lvl-open').find('select').append($div.html());
					}
				});

			}
		});
		*/

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
							opt.html(obj.mb_id + '(' + obj.first_name + ' ' + obj.last_name + ')');
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
//		var left_bottom = $('.8').val();
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

});

function go_member(go_id){
	//location.replace(g5_url + "/page.php?id=binary&start_id="+data.result);
	location.replace(g5_url + "/page.php?id=binary&start_id="+go_id);
}
</script>


<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>
