<?php
include_once('./_common.php');
//include_once(G5_LIB_PATH.'/mailer.lib.php');
$now_datetime = date('Y-m-d H:i:s');
$now_date = date('Y-m-d');
if ($_GET['debug']) $debug = 1;

$uid = $_POST['uid'];
$status = $_POST['status'];
$refund = $_POST['refund'];
$coin = $_POST['coin'];
$amt = $_POST['amt'];
$func = $_POST['func'];

$count = 1;
$drain = 1;


if ($debug) {
	$uid = 4;
	$status = 1;
	$refund = 'Y';
	$func = 'deposit';
	$coin = 'eth';
	$amt = '0';
}

if ($func == 'withrawal') {
	if ($status == '4' && $refund == 'Y') {
		$get_row = "SELECT * from {$g5['withdrawal']} where uid = {$uid} ";
		if ($debug) {
			print_r($get_row);
		}
		$ret = sql_fetch($get_row);
		$mb_id = $ret['mb_id'];
		$amt_total = $ret['amt_total'];

		$sql1 = "update g5_member set mb_shift_amt = mb_shift_amt + {$amt_total} where mb_id='{$mb_id}' ";

		if ($debug) {
			print_r($sql1);
			echo "<br>";
		} else {
			sql_query($sql1);
		}
	}

	$sql = "UPDATE {$g5['withdrawal']} set status = '{$status}' ";
	$sql .= ", update_dt = now() ";
	$sql .= " where uid = {$uid} ";


} else if ($func == 'deposit') {

	if ($status == '1' && $coin != '') {
		$get_row = "SELECT * from {$g5['deposit']} where uid = {$uid} ";
		if ($debug) {
			print_r($get_row);
			echo "<br>";
		}
		$ret = sql_fetch($get_row);
		$mb_id = $ret['mb_id'];

		$coin_target = "mb_" . strtolower($coin) . "_account";

		if($amt > 0 && $refund == 'Y'){
			$sql1 = "update g5_member set {$coin_target} = ($coin_target + $amt), mb_deposit_point = mb_deposit_point + {$amt}  where mb_id='{$mb_id}' ";

			if ($debug) {
				print_r($sql1);
				echo "<br>";
			} else {
				sql_query($sql1);
			}
		}
	

		// 추천인 산하 후원인 자리 검색
		$recomm_sql = "SELECT mb_recommend FROM g5_member WHERE mb_id = '{$mb_id}' ";
		$recomm_result = sql_fetch($recomm_sql);
		$recomm = $recomm_result['mb_recommend'];

		
		// echo "<br><h1>.$recomm.</h1><br>";

		$brecomme = array_brecommend($recomm, 1);
		$target_key = min(array_keys($brecomme));
		$now_brecom = $brecomme[$target_key];
		
		if ($debug) {echo "<br> 후원자찾기 :: ";print_R($now_brecom);}

		if($now_brecom['cnt'] == 0){
			$now_type = 'L';
			$mb_lr = '1';
		}else{
			$now_type = 'R';
			$mb_lr = '2';
		}

		// 후원인 기록 
		$recom_update_sql = "UPDATE g5_member set mb_brecommend='{$now_brecom['id']}', mb_brecommend_type='{$now_type}',mb_bre_time='{$now_datetime}',mb_lr = {$mb_lr} WHERE mb_id = '{$mb_id}' ";
		
		if ($debug){
			echo "<br><br>후원인 기록 :: ";
			print_R($recom_update_sql);
			$recom_update_result =1;
		}else{
			$recom_update_result = sql_query($recom_update_sql);
		}

		//$recom_update_result = sql_query($recom_update_sql);

		// 아바타 생성
		if($now_type == 'R' && $recom_update_result){

			
			if ($debug){
				echo "<br><br>";
				echo $now_brecom['id'];
				echo "<br><br>";
			}

			// 아바타인지 회원마스터인지 판별
			if(strpos($now_brecom['id'],'_')){
				$master_id_raw = explode('_',$mb['mb_id']);
				$master_id = $master_id_raw[0];
			}else{
				$master_id = $now_brecom['id'];
			}
			

			$mem_sql = "SELECT * from g5_member where mb_id='{$master_id}' ";
			$mb = sql_fetch($mem_sql);

			if ($debug){
				echo "<br>대상찾기:".$mem_sql;
			}
			
			$mem_avatar_num = $mb['avatar_last'];
			$avatar_last_num = $mem_avatar_num+1;

			


			$avata_id = $mb['mb_id']."_".sprintf("%02d",$avatar_last_num);

			/* $avata_sql = "CREATE table g5_member_avatar AS SELECT * FROM {$g5['member_table']} WHERE mb_id='{$mb['mb_id']}';
			UPDATE g5_member_avatar SET mb_no='', mb_id='{$avata_id}', mb_brecommend='', mb_bre_time='', mb_brecommend_type='', mb_datetime='{$now_datetime}', mb_open_date='{$now_date}' WHERE mb_id='{$mb['mb_id']}';
			INSERT INTO g5_member SELECT * FROM g5_member_avatar;
			DROP table g5_member_avatar; "; */

			$avata_sql= "INSERT IGNORE INTO {$g5['member_table']}
				( mb_id,mb_password,mb_recommend,mb_name, mb_lr,mb_recommend_no,depth, mb_datetime,mb_open_date ) value
				( '{$avata_id}','{$mb['mb_password']}','{$mb['mb_recommend']}','{$mb['mb_name']}',1,{$mb['mb_recommend_no']},'{$mb['depth']}', '{$now_datetime}', '{$now_date}' )";

			
			if ($debug){
				echo "<br><br>아바타 생성 :: ";
				print_R($avata_sql);
				$avata_result =1;
			}else{
				$avata_result = sql_query($avata_sql);
			}

			// 아바타 생성기록
			if($avata_result){
				$avatar_log = "INSERT into g5_avatar_log (mb_id,avatar_id,create_dt,memo,count) value ('{$mb['mb_id']}', '{$avata_id}', '{$now_datetime}', '후원조건달성생성', $avatar_last_num )";
				
				if ($debug){
					echo "<br><br>아바타 로그 :: ";
					print_R($avatar_log);
				}else{
					sql_query($avatar_log);
				}
				
			}

			// 수당기록 
			// $bonus_sql = "insert into soodang_pay (allowance_name, day, mb_id, mb_no, benefit, level, grade, mb_name,rec_adm,datetime) value ('cycle','{$now_date}','{$mb['mb_id']}',) ";
			
			$now_deposit = $mb['mb_deposit_point'] + $mb['mb_deposit_calc'];
			$bonus_sql = "INSERT {$g5['bonus']} set allowance_name ='cycle'
							, day = '{$now_date}'
							, mb_id = '{$mb['mb_id']}'
							, mb_no = {$mb['mb_no']}
							, benefit = '0.12'
							, mb_level = {$mb['mb_level']}
							, grade = {$mb['grade']}
							, mb_name = '{$mb['mb_name']}'
							, rec = 'cycle bouns from {$mb_id}'
							, rec_adm = 'cycle bouns from {$mb_id}'
							, origin_balance = {$mb['mb_balance']}
							, origin_deposit = {$now_deposit}
							, datetime = '{$now_datetime}' ";

			if ($debug){
				echo "<br><br>수당기록 :: ";
				print_R($bonus_sql);
				$bonus_result =1;
			}else{
				$bonus_result = sql_query($bonus_sql);
			}


			// 대상자 업데이트
			if($bonus_result){
				$origin_mem_update = "UPDATE g5_member set avatar_last = {$avatar_last_num},mb_balance = (mb_balance +0.12 )  WHERE mb_id ='{$mb['mb_id']}' ";

				if ($debug){
					echo "<br><br>대상자 업데이트 :: ";
					print_R($origin_mem_update);
					$origin_up_result =1;
				}else{
					$origin_up_result = sql_query($origin_mem_update);
				}
			}else{
				echo (json_encode(array("result" => "failed", "code" => "0005", "sql" => "대상자 업데이트 오류")));
			}


			// 아바타 입금요청 생성
			if($origin_up_result){
				$deposit_sql = "INSERT INTO wallet_deposit_request(mb_id, txhash, create_dt,create_d,status,coin) VALUES('{$avata_id}','AVATA','$now_datetime','$now_date',0,'eth')";
				
				if ($debug){
					echo "<br><br>아바타 입금요청 생성 :: ";
					print_R($deposit_sql);
				}else{
					$deposit_result = sql_query($deposit_sql);
				}
			}else{
				echo (json_encode(array("result" => "failed", "code" => "0005", "sql" => "아바타 자동입금기록 오류")));
				
			}

		} // 아바타생성프로세스

	} // 승인인경우
	
	$sql = "UPDATE {$g5['deposit']} set status = '{$status}' ";
	$sql .= ", in_amt = {$amt}";
	$sql .= ", update_dt = '{$now_datetime}' ";
	$sql .= " where uid = {$uid} ";
	
	if ($debug) {
		echo "<br><br>";
		print_r($sql);
		echo "<br>";
	} else {
		$result = sql_query($sql);
	}
	
	if ($result && $status != '1') {
		echo (json_encode(array("result" => "success", "code" => "1000")));
	}else if($recom_update_result){

		if($deposit_result){
			echo (json_encode(array("result" => "success", "code" => "0002", "sql" => "{$mb_id} :: 자동 후원인 등록 성공\n{$mb['mb_id']} :: 수당지급 / 아바타생성 / 업데이트 성공"),JSON_UNESCAPED_UNICODE));
		}else{
			echo (json_encode(array("result" => "success", "code" => "0001", "sql" => "자동후원인등록 성공")));
		}
	}
	
} else {
	echo (json_encode(array("result" => "failed", "code" => "9999", "sql" => "func can't find ERROR ")));
}


$brcomm_arr = [];
// 후원인 빈자리 찾기
function array_brecommend($recom_id, $count)
{
	global $brcomm_arr;

	// $new_arr = array();
	$b_recom_sql = "SELECT mb_id from g5_member WHERE mb_brecommend='{$recom_id}' ";
	$b_recom_result = sql_query($b_recom_sql);
	$cnt = sql_num_rows($b_recom_result);

	if ($cnt < 2) {
		// print_R($count.' :: '.$recom_id.' :: '.$cnt);
		// echo "<br><br>";
		if(!$brcomm_arr[$count]){
			$brcomm_arr[$count]['id'] = $recom_id;
			$brcomm_arr[$count]['cnt'] = $cnt;
		}
	} else {
		++$count;
		while ($row = sql_fetch_array($b_recom_result)) {
			array_brecommend($row['mb_id'], $count);
		}
	}
	return $brcomm_arr;
}


