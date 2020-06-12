<?php

$sub_menu = "600600";
include_once ('../common.php');


//auth_check($auth[$sub_menu], 'r');

$sql_price = "select btc_cost from coin_cost";
$result = sql_query($sql_price);
$ret = sql_fetch_array($result);
$exchange_rate =  $ret['btc_cost'];
$result_hap;

function make_habu($gubun){
	$noo=0;
	$mon=0;
	$today=0;
	$gubun = strtolower($gubun);

	$sql= " delete from ".$gubun."noo"; // 
	sql_query($sql);

	$sql= " delete from ".$gubun."thirty"; // 
	sql_query($sql);

	$sql= " delete from ".$gubun."today"; //
	sql_query($sql);

	habu_sales_calc($gubun,'coolrunning',0); 
}


function habu_sales_calc($gubun, $recom, $deep){
	global $result_hap;
//	if($deep>6)return;
	global $fr_date, $to_date;
	$deep++; // 대수

	$start_day	= '2017-07-01';
	$to_date		= '2018-06-06';

	if ($to_date){
		$day	= $to_date;
	}else{
		$day	= '2018-07-06'; //  = date('Y-m-d');
	}
	$yy= strtotime($day);
	$min30=date("Y-m-d", strtotime("-30 day", $yy));
	
    $res= sql_query("select * from g5_member where mb_".$gubun."recommend='".$recom."' ");

	echo "select * from g5_member where mb_".$gubun."recommend='".$recom."' ";
    echo "<br>";
	for ($j=0; $rrr=sql_fetch_array($res); $j++) { 	

		$recom=$rrr['mb_id'];  
		echo $recom.'<br>'; 
		$noo_search = " and date_format(o.od_receipt_time,'%Y-%m-%d')>='$start_day' and date_format(o.od_receipt_time,'%Y-%m-%d')<='$day'";
		$sql = sql_fetch("select sum(pv)as hap from g5_shop_order as o where mb_id='".$recom."' $noo_search");
		$noo = $noo + $sql['hap'];
		echo "noo : ".$noo."  hap : ".$sql[hap]."<br>";
		echo "select sum(pv)as hap from g5_shop_order as o where mb_id='".$recom."' $noo_search".'  '.$noo."<br>"	;
		$mysql=sql_fetch("select (pv)as hap from g5_shop_order as o where o.mb_id='".$mbid."'");
		$mysales=$mysql['hap'];
		list($noo_r,$mon_r,$today_r)=habu_sales_calc($gubun, $recom, $deep);	 
		$noo_r+=$mysales;
		$mon_r+=$mysales;
		$today_r+=$mysales;
		$noo+=$noo_r;
		$mon+=$mon_r;  
		$today+=$today_r; 
		if( ($noo>0) || ($noo_r>0)) {
			if($j==0){
				$rec=$noo;
			}else{
				$rec=$noo_r;	
			}
			$inbnoo = "insert ".$gubun."noo2 SET noo=".$rec.", mb_id='".$recom."',  day = '".$to_date."'";
			sql_query($inbnoo);	
			echo $inbnoo.' depth '.$deep."<br>";
			$result_hap=$result_hap+$rec;
		}
		if(($mon>0) && ($mon_r>0) ) {
			if($j==0){
				$rec=$mon;
			}else{
				$rec=$mon_r;	
			}
			$inthirty = "insert ".$gubun."thirty2 SET thirty=".$rec.", mb_id='".$recom."',  day = '".$to_date."'";
			sql_query($inthirty);
		}
		if(($today>0)&& ($today_r>0)) {
			if($j==0){
				$rec=$today;
			}else{
				$rec=$today_r;
			}
				$intoday = "insert ".$gubun."today2 SET todayy=".$rec.", mb_id='".$recom."',  day = '".$to_date."'";
				sql_query($intoday);
				//echo $intoday."<br>";
			}
		
	} // for j	

	 return array($noo,$mon,$today);
}  




 
// 새로운 공휴일 처리함수
function plus_day($day,$add)
{
	if($add<7 && $add>0){ $add+=2; }
		else if($add<=14 && $add>7){ $add+=4;}
		else if($add<=21 && $add>14){ $add+=6;  }
		else if($add<=28 && $add>21){ $add+=8;}
		else if($add<=31 && $add>28){ $add+=10;  }

		$year=date(Y);
		$holiday = array(date("Y")."-01-01",);
		$sql = " select * from holiday where YEAR(h_day)=$year";
		$result = sql_query($sql);
		$il= strtotime($day);
		$nal=date("Y-m-d", strtotime("+$add day", $il));
		for ($i=0; $row=sql_fetch_array($result); $i++) {
			if($nal==$row['h_day']){
				$il=strtotime($nal);
				$nal=date("Y-m-d", strtotime("+1 day", $il));
				$add++;
			}
		}    
		$exdate = explode("-", $day); 
		 $exyear = $exdate[0]; 
		 $exmonth = $exdate[1]; 
		 $exday = $exdate[2]; 
		 
		 $exweek = array(0=>'일',1=>'월',2=>'화',3=>'수',4=>'목',5=>'금',6=>'토'); 

		 



		  $exweek2 = date("w",mktime(0,0,0,(int) $exmonth,(int) $exday+$add,(int) $exyear)); 
		 if ($exweek2 == 0) {
		   $add++;
		 }

		// 토요일 이라면 월요일로...
		if ($exweek2 == 6) { 
		   $add++;
			$add++;
		 }

   return date("Y-m-d", mktime(0,0,0,(int) $exmonth,(int) $exday+$add,(int) $exyear));
 

}




function self_sales($recom){

    $res= sql_fetch("select sum(pv)as hap from g5_shop_order as o where o.mb_id='".$recom."'");
	echo "select sum(pv)as hap from g5_shop_order as o where o.mb_id='".$recom."'". $res['hap'].'<br>';    
	return $res['hap'];    
	
} 



function my_bchild_sub($mb_id){

	$hap2=0;

	//자기매출제외
	//$hap=self_sales($mb_id); //먼저 자기매출

    $res= sql_query("select mb_id from g5_member where mb_recommend='".$mb_id."' order by mb_no"); 
	for ($j=0; $rrr=sql_fetch_array($res); $j++) { 
		$hap2+=self_sales($rrr['mb_id']); // 하부매출을 구한다
		$hap2 = $hap2+ my_bchild_sub($rrr['mb_id']);		
	} 	
	return $hap2;
} 

function my_bchild_hap($mb_id){

	$cnt = 0;
	$hap = 0;
	$bcnt =0;
    $res= sql_query("select count(mb_id) as cnt from g5_member where mb_recommend='".$mb_id."' order by mb_no"); 
	$ret = sql_fetch_array($res);
	$cnt = $ret['cnt']; 

    $res2= sql_query("select count(mb_id) as bcnt from g5_member where mb_brecommend='".$mb_id."' order by mb_no"); 
	$ret2 = sql_fetch_array($res2);
	$cnt2 = $ret2['bcnt']; // 하부매출을 구한다
		
	if($cnt >= 2 and $cnt2=2){
		$hap = my_bchild_sub($mb_id);
	}
	else{
		return 0;
	}
	return $hap;
} 


function clear_all_benefit_mem(){
	for ($i=0; $row=sql_fetch_array($rrr); $i++) {   
		$cond[$i]['price_cond']='';
		$cond[$i]['source']='';
		$cond[$i]['source_cond1']='';
		$cond[$i]['source_cond2']='';
		$cond[$i]['source_in1']='';
		$cond[$i]['source_in2']='';
		$cond[$i]['mb_level_cond1']='';
		$cond[$i]['mb_level_cond2']='';
		$cond[$i]['mb_level_in1']='';
		$cond[$i]['mb_level_in2']='';
		$cond[$i]['partner_cnt']='';
		$cond[$i]['partner_cont']='';
		$cond[$i]['history_cnt']='';
		$cond[$i]['history_cond1']='';
		$cond[$i]['history_cond2']='';
		$cond[$i]['history_in1']='';
		$cond[$i]['history_in2']='';
		$cond[$i]['base_source']='';
		$cond[$i]['per']='';
		$cond[$i]['allowance_name']='';
		$cond[$i]['benefit_limit1']='';
		$cond[$i]['benefit']=0;
		$cond[$i]['source11']='';
		$cond[$i]['source_cond11']='';
		$cond[$i]['source_cond12']='';
		$cond[$i]['source_in11']='';
		$cond[$i]['source_in12']='';
		$cond[$i]['iwolyn']='';
		$cond[$i]['mb_level_in11']='';
		$cond[$i]['mb_level_cond11']='';
		$cond[$i]['mb_level_cond12']='';
		$cond[$i]['cycle']='';
		$cond[$i]['sales_reset']='';
		$cond[$i]['max_reset1']='';
		$cond[$i]['max_reset2']='';
		$cond[$i]['recom_kind']='';
		$cond[$i]['bigsmall1']='';
		$cond[$i]['bigsmall2']='';
		$cond[$i]['level1']='';
		$cond[$i]['level2']='';
		$cond[$i]['andor']='';
		$cond[$i]['mat']='';
		$cond[$i]['history']='';
		$cond[$i]['bf_limit1']='';
	}
}

function clear_benefit_mem(){
	for ($i=0; $row=sql_fetch_array($rrr); $i++) {   
		$cond[$i]['bigsmall1']='';
		$cond[$i]['bigsmall2']='';
		$cond[$i]['level1']='';
		$cond[$i]['level2']='';
		$cond[$i]['mat']='';
		$cond[$i]['history']='';
	}
}

//**** 수당이 있다면 함께 DB에 저장 한다.
function iwol_process($to_date,$mbid, $mb_name, $kind, $pv, $note){
	$temp_sql1 = " insert iwol set iwolday='".$to_date."'";
	$temp_sql1 .= " ,mb_id		= '".$mbid."'";
	$temp_sql1 .= " ,mb_name		= '".$mbname."'";
	$temp_sql1 .= " ,kind		= '".$kind."'";
	$temp_sql1 .= " ,pv		= '".$pv."'";
	$temp_sql1 .= " ,note		= '".$note."'";
	sql_query($temp_sql1);
}

$cond = array(array('price_cond'=>'','source'=>'','source_cond1'=>'','source_cond2'=>'','source_in1'=>'','source_in2'=>'','mb_level_cond1'=>'','mb_level_cond2'=>'','mb_level_in1'=>'','mb_level_in2'=>'','partner_cnt'=>'','partner_cont'=>'','history_cnt'=>'','history_cond1'=>'','history_cond2'=>'','history_in1'=>'','history_in2'=>'','base_source'=>'','per'=>'','allowance_name'=>'','mat'=>'','level1'=>'','level2'=>'','bigsmall1'=>'','bigsmall2'=>'','history'=>'','benefit'=>'','benefit_limit1'=>'','source11'=>'','source_cond11'=>'','source_cond12'=>'','source_in11'=>'','source_in12'=>'','sales_reset'=>'','max_reset1'=>'','max_reset2'=>'','mb_level_in11'=>'','mb_level_cond11'=>'','mb_level_cond12'=>'','bf_limit1'=>''));

$benefit = "SELECT * from pinna_soodang_set where immediate=1 order by partner_cnt desc, no ";
$rrr = sql_query($benefit);

for ($i=0; $row=sql_fetch_array($rrr); $i++) {   
	$cond[$i]['price_cond']=$row['price_cond'];
	$cond[$i]['source']=$row['source'];
	$cond[$i]['source_cond1']=$row['source_cond1'];
	$cond[$i]['source_cond2']=$row['source_cond2'];
	$cond[$i]['source_in1']=$row['source_in1'];
	$cond[$i]['source_in2']=$row['source_in2'];
	$cond[$i]['mb_level_cond1']=$row['mb_level_cond1'];
	$cond[$i]['mb_level_cond2']=$row['mb_level_cond2'];
	$cond[$i]['mb_level_in1']=$row['mb_level_in1'];
	$cond[$i]['mb_level_in2']=$row['mb_level_in2'];
	$cond[$i]['partner_cnt']=$row['partner_cnt'];
	$cond[$i]['partner_cont']=$row['partner_cont'];
	$cond[$i]['history_cnt']=$row['history_cnt'];
	$cond[$i]['history_cond1']=$row['history_cond1'];
	$cond[$i]['history_cond2']=$row['history_cond2'];
	$cond[$i]['history_in1']=$row['history_in1'];
	$cond[$i]['history_in2']=$row['history_in2'];
	$cond[$i]['base_source']=$row['base_source'];
	$cond[$i]['immediate']=$row['immediate'];
	$cond[$i]['per']=$row['per'];
	$cond[$i]['andor']=$row['andor'];
	$cond[$i]['allowance_name']=$row['allowance_name'];
	$cond[$i]['benefit_limit1']=$row['benefit_limit1'];
	$cond[$i]['source11']=$row['source11'];
	$cond[$i]['source_cond11']=$row['source_cond11'];
	$cond[$i]['source_cond12']=$row['source_cond12'];
	$cond[$i]['source_in11']=$row['source_in11'];
	$cond[$i]['source_in12']=$row['source_in12'];
	$cond[$i]['sales_reset']=$row['sales_reset'];
	$cond[$i]['iwolyn']=$row['iwolyn'];
	$cond[$i]['max_reset1']=$row['max_reset1'];
	$cond[$i]['max_reset2']=$row['max_reset2'];
	$cond[$i]['cycle']=$row['cycle'];
	$cond[$i]['mb_level_in11']=$row['mb_level_in11'];
	$cond[$i]['mb_level_cond11']=$row['mb_level_cond11'];
	$cond[$i]['mb_level_cond12']=$row['mb_level_cond12'];
	$cond[$i]['recom_kind']=$row['recom_kind'];

	if(  ($row['mb_level_in1']!=0) || ($row['mb_level_in2']!=0) ){ $cond[$i]['level1']=1; } //본인직급 
	if(  ($row['mb_level_cond11']!=0) || ($row['mb_level_cond12']!=0) ){ $cond[$i]['level2']=1; } //하부직급
	if(  ($row['history_cond1']!='') || ($row['history_cond2']!='') ){ $cond[$i]['history']=1;}  //대수 level
	if( $row['benefit_limit1']>0  ){$cond[$i]['bf_limit1']=1;}  //매출자보다 수당받을자의 매출이 같거나 큰가?
}

	if ($to_date){
		$day       = $to_date;
	}else{
		$day    = date('Y-m-d');
	}

//make_habu('b'); // 누적,30일, 하루매출을 바이너리로 구함
make_habu('');
//echo 'result : '.$result_hap;
if($benefit_yn_chk==1){
 	$sql= " delete from soodang_pay"; 
	sql_query($sql);
}


/*
if($sales_yn_chk==1){

$sql= " UPDATE g5_member  SET mb_my_sales=0, noo_my_sales=0,  mon_my_sales=0 ,day_my_sales=0, mb_habu_sum=0, noo_habu_sum=0, mon_habu_sum=0, habu_day_sales=0, sales_day='".$fr_date."'";
sql_query($sql);

echo "update...";
}
*/
	$price=pv;
	if(   $price=='pv') {
		// PV가로
		$price_cond=", SUM(pv) AS hap";

	} else if(   $price=='bv') {
		//BV가로 계산
		$price_cond=", SUM(bv) AS hap";

	}else{
		// 판매가로 
		$price_cond=",SUM(od_receipt_price +od_receipt_cash) AS hap";
	}

$sql_common = " FROM g5_shop_order AS o, g5_member AS m ";

$sql_search=" WHERE o.mb_id=m.mb_id AND o.mb_id=m.mb_id";
if($mb_id) {$sql_member=" AND m.mb_id='".$mb_id."'"; }else{ $sql_member=""; }

$searchdate=" AND DATE_FORMAT(o.od_receipt_time,'%Y-%m-%d')>='".$to_date."' AND DATE_FORMAT(o.od_receipt_time,'%Y-%m-%d')<='".$to_date."' GROUP BY mb_id, DATE_FORMAT(o.od_receipt_time,'%Y-%m')";


$search_mon_date=" AND DATE_FORMAT(o.od_receipt_time,'%Y-%m-%d')>='".$min30."' AND DATE_FORMAT(o.od_receipt_time,'%Y-%m-%d')<='".$to_date."' GROUP BY mb_id, DATE_FORMAT(o.od_receipt_time,'%Y-%m')"; //

$sql_mgroup='GROUP BY m.mb_id';


$sql_orderby=' order by od_receipt_time asc';

$sql = "SELECT SUBSTRING(o.od_receipt_time,1,10) AS od_receipt_time, m.mb_id, m.mb_name,m.mb_5, m.mb_recommend, m.mb_hp 
			$price_cond 
            {$sql_common}
            {$sql_search}
			{$sql_member}
			{$searchdate}
";
$result = sql_query($sql);

echo $sql;
//make_class();


$history_cnt=0;

$rec='';


$onestar=3000; // 1스타 승급매출 조건


for ($i=0; $row=sql_fetch_array($result); $i++) {   

	
	$today_sales2=0;
	$today=$row['od_receipt_time'];
	$comp=$row['mb_id'];
	$confirm_exit1=0;
	$first=0; 
	$firstname='';
	$firstid='';
	$today_sales=$row['hap'];


		while(  ($comp!='admin')  || ($comp!='Coolrunning')  ){   

			$sql = " SELECT mb_id, mb_name, mb_hp, mb_level, pool_level, mb_recommend, mb_brecommend FROM g5_member WHERE mb_id= '".$comp."'";
			$recommend = sql_fetch($sql);

				$leg_success=0; // 메트릭스 성공찾기 클리어
				$mesales=0; // 최초 내 매출저장
				$limit1=0;

				$mbid=$recommend['mb_id'];
				$mbname=$recommend['mb_name'];
				$mbhp=$recommend['mb_hp'];
				$mblevel=$recommend['mb_level'];
				$pool_level=$recommend['pool_level'];



				//위로 연속 올라가는 부분 추천인으로 올라갈지 바이너리추천인으로 올라갈지 결정


				if(   ($mb_name=='본사')  || ($mbid=='')  ) { echo "admin , 본사 혹은 ''을 만나 정지됨"; break;}
					
					$sql_sale = " update g5_member set sales_day='".$today."',";
					if($history_cnt==0)
					{ 
						$sql_sale .= " mb_my_sales=mb_my_sales	+ ".$today_sales;
					}else{
						$sql_sale .= " habu_day_sales=habu_day_sales+".$today_sales;
					}
					$sql_sale .= " where mb_id='".$comp."'";
					sql_query($sql_sale);


					//************************* 1스타 직급 승급 ************************************			
					if($mblevel<3){  //현재 내 직급이 1star 이하이면

						if(my_bchild_hap($r)>=$onestar){

							$sql3 = " update g5_member set mb_level=3";
							$sql3 .= " , rank_day='".$to_date."'";
							$sql3 .= " where mb_id='".$comp."'";
							sql_query($sql3);

							$sql3 = " insert rank set ";
							$sql3 .= " rank_day='".$to_date."'";
							$sql3 .= " , rank=3";
							$sql3 .= " , old_level='".$mblevel."'";
							$sql3 .= " , rank_note='1스타 승급함, benefit_immediate에서 계산됨'";
							$sql3 .= " where mb_id='".$comp."'";
						//	sql_query($sql3);
							

								echo $mbid." 승급함<br>";
						}
					}

	



							
					for ($i=0; $i<count($cond); $i++) {

						if($cond[$i]['recom_kind']=='mb_recommend'){
								$recom=$recommend['mb_recommend'];
						}else{
								$recom=$recommend['mb_brecommend'];
						}


							
						if($cond[$i]['level1']=='1'){ $temp_cond_level1=1; } else {$temp_cond_level1=0;} //본인직급
						if($cond[$i]['level2']=='1'){ $temp_cond_level2=1; } else {$temp_cond_level2=0;} //하부직급
						if($cond[$i]['history']=='1'){ $temp_cond_history=1; } else {$temp_cond_history=0;} //대수조건
						if($cond[$i]['bf_limit1']=='1'){ $bf_limit1=1; } else {$bf_limit1=0;} //대수조건


						
						//추천수당이라면 최초 매출자의 매출을 기록한다. 왜냐면 최초 매출자의 매출을 가지고 위로 올라가며 수당을 계산하기 때문에... 
					
						if( ($cond[$i]['base_source']=='추천수당') && ($history_cnt==0) ){
							
							$firstname=$mbname;
							$firstid=$mbid;
							
							$first=$pool_level; 
							
						}
						



						//그런데 추천수당중 매출발생자 매출보다 커야만 받을수 있는 조건이 있다면 상위업라인들의 누적매출이 매출발생자 매출보다 같거나 커야만 지급한다. 
						if( $bf_limit1>0 ){
						
							if($first<=$pool_level){
								$limit1=0;
							}else{
								 echo '지급유보발생!! 발생자'.$firstid.'('.$first.')<='.$mbid.'지급유보자('.$pool_level.')';

								$limit1=1;
							}

						}



						

						$temp_sql1 = '';
						

						if($cond[$i]['per']!=''){ // 수당 퍼센트가 비어있으면 패스~



								  //******   본인직급 조건이 있다면  성공여부 기록 
								if(   ($cond[$i]['mb_level_cond1']=='==')   ){

										if($mblevel==$cond[$i]['mb_level_in1']){
											$temp_cond_level1=0;
										}else{
											$temp_cond_level1=1;
										}

								}else if(  ($cond[$i]['mb_level_cond1']=='>=') && ($cond[$i]['mb_level_cond2']=='')  ){

										if($mblevel>=$cond[$i]['mb_level_in1']){
											
											$temp_cond_level1=0;
										}else{
											$temp_cond_level1=1;
										}

								}else if(  ($cond[$i]['mb_level_cond1']=='') && ($cond[$i]['mb_level_cond2']=='<=')  ){

										if($mblevel<=$cond[$i]['mb_level_in2']){
											
											$temp_cond_level1=0;
										}else{
											$temp_cond_level1=1;
										}

								}else if(  ($cond[$i]['mb_level_cond1']=='>=') && ($cond[$i]['mb_level_cond2']=='<=')  ){


										if(  ($mblevel>=$cond[$i]['mb_level_in1']) && ($mblevel<=$cond[$i]['mb_level_in2'])  ){
							
											$temp_cond_level1=0;
										}else{
											$temp_cond_level1=1;
										}
								}


								 //******   하부직급 조건이 있다면  성공여부 기록 
								if(  ($cond[$i]['mb_level_cond11']!='') && ($cond[$i]['mb_level_cond12']>0)   ){

										//하위에 몇명의 원하는 직급이 있는지 파악
										$temp_cond_level2=0;


								}

								
							  
							  //******   대수 조건이 있다면 계산하라 


								if(   ($cond[$i]['history_cond1']=='==')   ){

										if($history_cnt==$cond[$i]['history_in1']){
											
											$temp_cond_history=0;
										}else{
											$temp_cond_history=1;
										}

								}else if(  ($cond[$i]['history_cond1']=='>=') && ($cond[$i]['history_cond1']=='')  ){
										if($history_cnt>=$cond[$i]['history_in1']){

											$temp_cond_history=0;
										}else{
											$temp_cond_history=1;
										}

								}else if(  ($cond[$i]['history_cond1']=='') && ($cond[$i]['history_cond1']=='<=')  ){

										if($history_cnt<=$cond[$i]['history_in1']){

											$temp_cond_history=0;
										}else{
											$temp_cond_history=1;
										}

								}else if(  ($cond[$i]['history_cond1']=='>=') && ($cond[$i]['history_cond2']=='<=')  ){
										if( ($history_cnt>=$cond[$i]['history_in1']) && ($history_cnt<=$cond[$i]['history_in2']) ){

											$temp_cond_history=0;
										}else{
											$temp_cond_history=1;
										}
								
								}






								//echo $cond[$i]['base_source'].'=='.$today_sales.'==='.$temp_cond_history.'   '.$temp_cond_bigsmall1.'    '.$temp_cond_level.'   '.$temp_cond_mat.'<br>';;


								 echo $comp.'-대수: '.$temp_cond_history.'-본인직급: '.$temp_cond_level1.' -하부직급: '.$temp_cond_mat.'-매출크기: '.$bf_limit1.'<br>';
								
								

								// ***** 걸린 조건을 모두 충족한다면 계산하라 
						
								if(   ($temp_cond_history==0)  &&  ($temp_cond_bigsmall1==0) &&  ($temp_cond_bigsmall2==0)  &&  ($temp_cond_level1==0)  && ($temp_cond_leve2==0)  &&  ($temp_cond_mat==0) && ($limit_reset==0)  ){


										//if($save_benefit==1){ // 저장하라면 

											
								$benefit=($today_sales)*($cond[$i]['per']/100);

								$rec_adm=$cond[$i]['allowance_name'].': 조건 '.$firstid.'('.$first.')<='.$mbid.'('.$pool_level.')   '.$firstname.'('.$firstid.') 으로부터 '.$history_cnt.'대 -'.$cond[$i]['base_source'].':'.$today_sales.'*'.($cond[$i]['per']/100).'='.$benefit.' / <br/>';
								$hist = $history_cnt-1;	
								$rec=$cond[$i]['allowance_name'].' Bonus from member '.$firstid.' ( level '.$hist.')';

												
												echo $rec;
												   //**** 수당이 있다면 함께 DB에 저장 한다.
												$benefit_bit = round($benefit/$exchange_rate,8);
												//$balance_up = "update g5_member set mb_balance = round(mb_balance+ ".$benefit_bit.",8)
												//where mb_id = '".$mbid."';";
												//sql_query($balance_up);

												   //**** 수당이 있다면 함께 DB에 저장 한다.
												$temp_sql1 = " insert soodang_pay set day='".$to_date."'";
												$temp_sql1 .= " ,mb_id		= '".$mbid."'";
												$temp_sql1 .= " ,mb_name		= '".$mbname."'";
												$temp_sql1 .= " ,mb_recommend		= '".$recom."'";
												$temp_sql1 .= " ,allowance_name		= '".$cond[$i]['allowance_name']."'";
												$temp_sql1 .= " ,andor		= '".$cond[$i]['andor']."'";
												
												$temp_sql1 .= " ,accu_my_sales		=  '".$recommend['noo_my_sales']."'";
												$temp_sql1 .= " ,mon_my_sales		=  '".$recommend['mon_my_sales']."'";
												$temp_sql1 .= " ,accu_habu_sum		=  '".$recommend['noo_habu_sum']."'";
												$temp_sql1 .= " ,mon_habu_sum		=  '".$recommend['mon_habu_sum']."'";
												$temp_sql1 .= " ,benefit			=  ".$benefit_bit;
												$temp_sql1 .= " ,benefit_usd		=  '".($benefit)."'";
												$temp_sql1 .= " ,exchange_rate      =  ".$exchange_rate;
												if($limit1>0){ $temp_sql1 .= " ,benefit_limit1		=  1";}
												
												$temp_sql1 .= " ,rec		= '".$rec."'";
												$temp_sql1 .= " ,rec_adm	= '".$rec_adm."'";
												echo $temp_sql1;
												sql_query($temp_sql1);
												

									
										$oldcomp=$comp;

										

								}//if(   ($temp_cond_history==0)  &&  ($temp_cond_bigsmall1==0) 


							

						}// 수당per 가 있으면 
					} // for
							
				
					
					//echo $rec;
					$rec='';


			$comp=$recom;


			$history_cnt++;


		} // while

		$rec='';
		$history_cnt=0;
		$today_sales=0;

} //for

//alert('수당계산이 완료되었습니다');

?>

