<?php

$sub_menu = "600600";
include_once('./_common.php');


auth_check($auth[$sub_menu], 'r');


// sales_day가 오늘날짜와 다르면 업데이트 
$sql= " select sales_day g5_member where sales_day<>'".date('Y-m-d')."'";
$salesday=sql_fetch($sql);


if(($salesday['sales_day']) || ($update=='y') ){

	$sql= " UPDATE g5_member  SET mb_my_sales=0, noo_my_sales=0,  mon_my_sales=0 ,day_my_sales=0, mb_habu_sum=0, noo_habu_sum=0, mon_habu_sum=0, habu_day_sales=0, sales_day=''";
	sql_query($sql);

	echo $sql."<br>sales_day가 오늘날짜와 다르거나 강제업데이트 0으로 update...<br><br>";
}

/*
$sql = "SELECT mb_id, SUM(pv) AS pv FROM g5_shop_order WHERE DATE_FORMAT(od_receipt_time,'%Y-%m-%d')=DATE_FORMAT(NOW(),'%Y-%m-%d')";

if($mb_id){
	$sql.=" and mb_id='".$mb_id."'";
}
*/


$sql = "SELECT mb_id, SUM(pv) AS pv FROM g5_shop_order WHERE DATE_FORMAT(od_receipt_time,'%Y-%m-%d')=DATE_FORMAT(NOW(),'%Y-%m-%d') and mb_id='".$mb_id."'";
$result = sql_query($sql);

echo $sql;

if($to_date==''){
	$to_date=date('Y-m-d');
}



for ($i=0; $row=sql_fetch_array($result); $i++) {   
	
		$comp=$row['mb_id'];
		$today_sales=$row['pv'];	
		$history_cnt=0;


		while(  ($comp!='admin')  || ($comp!='coolrunning')  ){   

			$sql = " SELECT mb_id,mb_name,  mb_brecommend FROM g5_member WHERE mb_id= '".$comp."'";

			$recommend = sql_fetch($sql);
			$mb_id=$recommend['mb_id'];
			$mb_name=$recommend['mb_name'];
			$sql3='';
			

			
			echo $comp;
				//위로 연속 올라가는 부분 추천인으로 올라갈지 바이너리추천인으로 올라갈지 결정
				$recom=$recommend['mb_brecommend'];

				if(   ($mb_name=='본사')  || ($mb_id=='')  ) { echo "admin , 본사 혹은 ''을 만나 정지됨"; break;}

					$sql3 = " update g5_member set sales_day='".$to_date."',";
					if($history_cnt==0)
					{ 
						$sql3 .= " mb_my_sales=mb_my_sales	+ ".$today_sales;
					}else{
						$sql3 .= " habu_day_sales=habu_day_sales+".$today_sales;
					}
					$sql3 .= " where mb_id='".$comp."'";

					sql_query($sql3);

					echo $history_cnt.'--'.$sql3.'<br>';



			$comp=$recom;
			$history_cnt++;


		} // while

		$history_cnt=0;
		$today_sales=0;

} //for

//alert('매출계산이 완료되었습니다');

?>

