<?php
$sub_menu = "600600";
include_once ('../common.php');

$rate = [];
$recom_rate_sql = "SELECT * from eos_daily_paid order by idx";
$result = sql_query($recom_rate_sql);
while( $row = sql_fetch_array($result)){
	array_push($rate,$row);
}
//print_r($rate[1]['recom_1']);

if ($to_date){
	$to_date       = $to_date;
}else{
	$to_date    = date('Y-m-d');
}

$mem_sql = "select * from g5_member order by mb_no";
$mem_list = sql_query($mem_sql);
$result_hap;

while($row = sql_fetch_array($mem_list)){
	$mbid = $row['mb_id'];
	$allowance_name = $rec_adm  = $rec = "Role Down Recom"; // 추천수당이름
	$grade = $row['grade']; // 회원등급 계산
	$benefit_rate = $rate[$row['grade']]; //회원등급별 수당률
	
	
	$recomend_count = sql_fetch( "select count(mb_id) as count from g5_member where  mb_recommend = '".$mbid."' and mb_deposit_point >= 0 ");
	$recomend_cnt = $recomend_count['count']; // 추천인수

	sales_calc($mbid, $recomend_cnt,$grade,$benefit_rate);
	
	//benefit($to_date, $m_row['mb_id'], $m_row['mb_no'], $m_row['mbname'], $recom, $allowance_name, $result_hap, $rec_adm, $rec, $mb_level);
	
}

function sales_calc($mbid, $recomend_cnt,$grade,$benefit_rate){
	
	/*2뎁스 추천인*/
	global $to_date;
	
	$sql = "select mb_id from g5_member where  mb_recommend = '".$mbid."' and mb_deposit_point >= 0";
	$result = sql_query($sql);
	while( $row = sql_fetch_array($result) ){

		$query= sql_query("select mb_id from g5_member where mb_recommend='".$row['mb_id']."' and mb_deposit_point >= 0 ");
		while( $row2 = sql_fetch_array($query) ){
			
			$daily_paid = sql_fetch("select benefit from soodang_pay where 1=1 and allowance_name = 'daily payout' and mb_id='".$row2['mb_id']."' limit 1");
			echo $row2['mb_id']; 
		}
	}

	
	
	//print_r("<br> 회원아이디 : ".$mbid. "  |  추천인수:  ".$recomend_cnt."  | 회원등급 :  ".$grade."    | 수당비율 :      ".$benefit_rate['recom_1']."/".$benefit_rate['recom_2']."    | 수당합계 :      ".$benefit);
}

function benefit_exec($to_date){
	
}



// 수당계산
function habu_sales_calc($gubun, $recom, $deep, $count, $rate){
	global $result_hap;
	if($deep>$count){
		//echo $deep.' return'.'<br>';
		return;
	}
	global $fr_date, $to_date;
	$deep++; // 대수	
	

	//echo "select * from g5_member where mb_".$gubun."recommend='".$recom."' "."<br>";
    $res= sql_query("select * from g5_member where mb_".$gubun."recommend='".$recom."' ");

    for ($j=0; $rrr=sql_fetch_array($res); $j++) { 		
		
		$percent_hist = $rate[$rrr['grade']]['recom_2'];
		//print_r("<br>". $rrr['mb_no']." / ".$rrr['mb_id']."/".$percent_hist);

		/*
		for ($i=0; $i<count($cond); $i++) {

			if( ($cond[$i]['recom_grade']==$count) && ($cond[$i]['recom_history']==$deep) ){
				$percent_hist = $cond[$i]['recom_per'];
			}		
		}
		*/



		$recom=$rrr['mb_id'];  
			//echo "select benefit from soodang_pay where 1=1 and day='".$to_date."' and allowance_name = 'daily payout' and mb_id='".$recom."'"."<br>";
		$daily_paid = sql_fetch("select benefit from soodang_pay where 1=1 and day='".$to_date."' and allowance_name = 'daily payout' and mb_id='".$recom."'");
		$daily_benefit = round($daily_paid['benefit']*$percent_hist,2);
			//echo '  ::: deep : '.$deep.' daily_benefit.. : '.$daily_benefit.' percent_hist '.$percent_hist."<br>";

		$result_hap+=$daily_benefit;

		list($noo, $mon_r, $today_r)=habu_sales_calc($gubun, $recom, $deep,$count, $cond);	 
		
		//echo 'noo.list : '.$noo."<br>";
		//$result_hap+=$noo;
		if( ($noo>0)) {
			if($j==0){
				$rec=$noo;
			}else{
				$rec=$noo;	
			}
			
			//echo $inbnoo.' depth '.$deep."<br>";
			//$result_hap=$rec;
		}		
	} // for j	
	return array($daily_benefit, $mon, $today);
}


function save_benefit($to_date, $mbid, $mbno,$mbname, $recom, $allowance_name, $benefit, $rec_adm, $rec, $mb_level ){
	
	print_r("<br>save __________".$mbid."/".$mbno."/".$mbname."/".$recom."/".$benefit."/".$rec_adm."/".$rec."/".$mb_level);

	$benefit = number_format($benefit,2);

	$balance_up = "update g5_member set mb_balance = Number_format(mb_balance+ ".$benefit.",2)  where mb_id = '".$mbid."';";
	
	//sql_query($balance_up);

	$temp_sql1 = " insert soodang_pay set day='".$to_date."'";
	$temp_sql1 .= " ,mb_id			= '".$mbid."'";
	$temp_sql1 .= " ,mb_no			= ".$mbno;
	$temp_sql1 .= " ,mb_level      = ".$mb_level;
	$temp_sql1 .= " ,mb_name	= '".$mbname."'";
	$temp_sql1 .= " ,mb_recommend	= '".$recom."'";
	$temp_sql1 .= " ,allowance_name	= '".$allowance_name."'";
	$temp_sql1 .= " ,benefit		=  ".$benefit;	
	$temp_sql1 .= " ,rec			= '".$rec."'";
	$temp_sql1 .= " ,rec_adm	= '".$rec_adm."'";

	//sql_query($temp_sql1);
	echo $temp_sql1;
}