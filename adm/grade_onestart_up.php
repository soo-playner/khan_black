<?php

$sub_menu = "600600";
include_once('./_common.php');

$sql_mb = "select mb_level, mb_id from g5_member where mb_level = 0";
$ret = sql_query($sql_mb);

while($mb_row = sql_fetch_array($ret)){
	$mbid = $mb_row['mb_id'];
 	//현재 내 직급이 1star 이하이면

		if(my_bchild_hap($mbid)>=3000){

			$sql3 = " update g5_member set mb_level=3";
			$sql3 .= " , rank_day='".$to_date."'";
			$sql3 .= " where mb_id='".$mbid."'";
			sql_query($sql3);

			$sql3 = " insert rank set mb_level=3";
			$sql3 .= " , rank_day='".$to_date."'";
			$sql3 .= " , rank=3";
			$sql3 .= " , old_level='".$mblevel."'";
			$sql3 .= " , rank_note='1스타 승급함, benefit_immediate에서 계산됨'";
			$sql3 .= " where mb_id='".$mbid."'";
			sql_query($sql3);		
				echo $mbid." 승급함<br>";
		}
	
}

function my_bchild_hap($mb_id){

	$hap=0;
	$cnt=0;

	$res= sql_query("select mb_id from g5_member where mb_recommend='".$mb_id."' order by mb_no"); 
	for ($j=0; $rrr=sql_fetch_array($res); $j++) { 
		$hap+=self_sales($rrr['mb_id']); // 하부매출을 구한다
		$cnt++;
	} 
	
	if($cnt>=2){
		return $hap;
	}else{
		return 0;
	}
} 

function self_sales($recom){

    $res= sql_fetch("select sum(pv)as hap from g5_shop_order as o where o.mb_id='".$recom."'");
	echo "select sum(pv)as hap from g5_shop_order as o where o.mb_id='".$recom."'". $res['hap'].'<br>';    
	return $res['hap'];    
	
} 
?>