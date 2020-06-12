<?php
$sub_menu = "600600";
include_once('./_common.php');

include_once('./inc.member.class.php');

auth_check($auth[$sub_menu], 'r');


 
// 새로운 공휴일 처리함수
function plus_day($day,$add)
{


		if($add<7 && $add>0){ $add+=2; }

		else if($add<=14 && $add>7){ $add+=4;}

		else if($add<=21 && $add>14){ $add+=6;  }

		else if($add<=28 && $add>21){ $add+=8;}

		else if($add<=31 && $add>28){ $add+=10;  }




		$year=date(Y);
		$holiday = array("2018-01-01",);
		$sql = " select * from holiday where YEAR(h_day)=$year";
		$result = sql_query($sql);
		/*
		if($result){
		  $count = mysql_num_rows($result);
		  if($count == 0){
			alert('공휴일 자료가 없습니다. 공휴일 관리에서 올해의 공휴일을 등록하세요!(배당지급시 공휴일은 건너 뛰어 계산합니다.)');
		  }
		  
		}
		*/

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








function habu_sales($recom,$fr_date,$to_date){

	$sql_search = " and date_format(o.od_time,'%Y-%m-%d')>='$fr_date' and date_format(o.od_time,'%Y-%m-%d')<='$to_date'";

    $res= sql_query("select * from g5_member where mb_recommend='".$recom."' ");

    for ($j=0; $rrr=sql_fetch_array($res); $j++) { 
 			  $recom=$rrr['mb_id'];  	
 			  $odsql= sql_fetch("select sum(pv)as hap from g5_shop_order as o where mb_id='".$recom."' $sql_search");
 			  $odhap+=$odsql['hap'];

			$od=habu_sales($recom,$fr_date,$to_date);	 
			$odhap+=$od;   		
	} // for j	
	 return $odhap;    
}  






function self_sales($recom,$fr_date,$to_date){

	$sql_search = " and date_format(o.od_time,'%Y-%m-%d')>='$fr_date' and date_format(o.od_time,'%Y-%m-%d')<='$to_date' group by md_id";

    $res= sql_fetch("select sum(pv)as hap from g5_shop_order as o where mb_id='".$recom."' $sql_search");
	
	return $res['hap'];    
	
} 


function self_iwol($recom,$fr_date,$to_date){

	$sql_search = " and date_format(iwolday,'%Y-%m-%d')>='$fr_date' and date_format(iwolday,'%Y-%m-%d')<='$to_date'  group by md_id";

    $res= sql_fetch("select sum(pv)as hap from iwol where mb_id='".$recom."' $sql_search");
	
	return $res['hap'];    
	
} 


function habu_iwol($mbid){
    $res1= sql_fetch("select mb_my_sales+habu_day_sales as hap from g5_member where mb_id='".$mbid."'");
	$res2= sql_fetch("select sum(pv)as hap from iwol where mb_id='".$mbid."'");
	return $res1['hap']+$res['hap'];    	
} 



function my_bchild($mb_id){
	$id1='';
	$id2='';
    $res= sql_query("select mb_id from g5_member where mb_recommend='".$mb_id."' order by mb_no"); 
	for ($j=0; $rrr=sql_fetch_array($res); $j++) { 
		if($j==0){$id1=$rrr['mb_id']; $hap1=habu_iwol($id1); }
		if($j==1){$id2=$rrr['mb_id']; $hap2=habu_iwol($id2); }
	} 
	return array($id1,$hap1,$id2,$hap2);
} 



/*
function my_bchild_sales($mb_id, $self_habu, $fr_date, $to_date){

    $res= sql_query("select * from g5_member where mb_recommend='".$mb_id."' order by mb_no"); 
   // $child =array(array('mb_id'=>'','hap'=>'','iwol'=>'','bigorsmall'=>''));

	
	for ($j=0; $rrr=sql_fetch_array($res); $j++) { 
	
		$hap=0;
		$iwol=0;
		$iiiid=$rrr['mb_id'];

		if($self_habu=='self'){
			$hap=self_sales($iiiid,$fr_date,$to_date); 
			$iwol=self_iwol($iiiid,$fr_date,$to_date); 
		}else{ 
			
			$hap=habu_sales($iiiid,$fr_date,$to_date); 
			$iwol=self_iwol($iiiid,$fr_date,$to_date); 
		}

		$child[$j]['mb_id']=$iiiid;
		$child[$j]['hap']=$hap;
		$child[$j]['iwol']=$iwol;

	} 




	return array($child);
} 
*/




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
function iwol_process($day,$mb_recommend, $mbid, $mb_name, $kind, $pv, $note){

	
	$iwol= sql_fetch("select count(*) as cnt from iwol where mb_id='".$mbid."' and date_format(iwolday,'%Y-%m-%d')='$day'");
	if( ($pv>0) && ($iwol['cnt']==0) ){   

		$temp_sql1 = " insert iwol set iwolday='".$day."'";
		$temp_sql1 .= " ,mb_id		= '".$mbid."'";
		//$temp_sql1 .= " ,mb_name		= '".$mbname."'";
		$temp_sql1 .= " ,kind		= '".$kind."'";
		$temp_sql1 .= " ,pv		= '".$pv."'";
		$temp_sql1 .= " ,note		= '".$note."'";
		$temp_sql1 .= " ,mb_recommend		= '".$mb_recommend."'";
		sql_query($temp_sql1);
	}

}


function save_benefit($day,$mbid, $mbname, $recom, $allowance_name, $sales_day, $habu_day_sales, $benefit, $rec){

	$iwol= sql_fetch("select count(*) as cnt from iwol where mb_recommend='".$mbid."' and date_format(iwolday,'%Y-%m-%d')='$day'");
	if($iwol['cnt']==0){ 

		$temp_sql1 = " insert soodang_pay set day='".$day."'";
		$temp_sql1 .= " ,mb_id		= '".$mbid."'";
		$temp_sql1 .= " ,mb_name		= '".$mbname."'";
		$temp_sql1 .= " ,mb_recommend		= '".$recom."'";
		$temp_sql1 .= " ,allowance_name		= '".$allowance_name."'";
		
		$temp_sql1 .= " ,day_sales		=  '".$sales_day."'";
		$temp_sql1 .= " ,habu_day_sales 		=  '".$habu_day_sales."'";
		$temp_sql1 .= " ,benefit		=  '".($benefit)."'";
		$temp_sql1 .= " ,rec		= '".$rec."'";

		sql_query($temp_sql1);

	}
}



$cond = array(array('price_cond'=>'','source'=>'','source_cond1'=>'','source_cond2'=>'','source_in1'=>'','source_in2'=>'','mb_level_cond1'=>'','mb_level_cond2'=>'','mb_level_in1'=>'','mb_level_in2'=>'','partner_cnt'=>'','partner_cont'=>'','history_cnt'=>'','history_cond1'=>'','history_cond2'=>'','history_in1'=>'','history_in2'=>'','base_source'=>'','per'=>'','allowance_name'=>'','mat'=>'','level1'=>'','level2'=>'','bigsmall1'=>'','bigsmall2'=>'','history'=>'','benefit'=>'','benefit_limit1'=>'','source11'=>'','source_cond11'=>'','source_cond12'=>'','source_in11'=>'','source_in12'=>'','sales_reset'=>'','max_reset1'=>'','max_reset2'=>'','mb_level_in11'=>'','mb_level_cond11'=>'','mb_level_cond12'=>'','bf_limit1'=>''));

$benefitSql = "SELECT * from soodang_set where immediate=2 order by partner_cnt desc, no";
$rrr = sql_query($benefitSql);

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


	if( ($row['sales_reset']>0) && ($row['cycle']>0)  && ($row['max_reset1']<>'')  && ($row['max_reset2']<>'') ){  $cond[$i]['limit_reset']=1;  }  //극점 사용여부


	if( ($row['partner_cnt']>0) && ($row['partner_cont']>0) ){$cond[$i]['mat']=1;}  // 메트릭스
	
	if(  ($row['source_in1']!=0) || ($row['source_in2']!=0) ){ $cond[$i]['bigsmall1']=1;  }// 대소실적조건1

	if(  ($row['source_in11']!=0) || ($row['source_in12']!=0) ){ $cond[$i]['bigsmall2']=1;  }// 대소실적조건12

	if(  ($row['mb_level_in1']!=0) || ($row['mb_level_in2']!=0) ){ $cond[$i]['level1']=1; } //본인직급 

	if(  ($row['mb_level_cond11']!=0) || ($row['mb_level_cond12']!=0) ){ $cond[$i]['level2']=1; } //하부직급

	if(  ($row['history_cond1']!='') || ($row['history_cond2']!='') ){ $cond[$i]['history']=1;}  //대수 level

	if( $row['benefit_limit1']>0  ){$cond[$i]['bf_limit1']=1;}  //매출자보다 수당받을자의 매출이 같거나 큰가?



}




/*
// 테이블에 매출이 있었던 회원 찾아서 누적및 월 합계 구해서 넣기 위해 기존 테이블을 비워줌
 	$sql= " delete from soodang_pay"; 
	sql_query($sql);
*/
/*
$sql= "UPDATE g5_member SET noo_my_sales=0,  mon_my_sales=0 ,day_sales=0, mb_habu_sum=0, noo_habu_sum=0, mon_habu_sum=0, habu_day_sales=0"; 
sql_query($sql);
*/

/*
if($benefit_yn_chk==1){
	$sql= " delete from dividend"; 
	sql_query($sql);
}
*/



//$fr_date="2015-05-01";
//$to_date="2015-05-31";

$day=date('Y-m-d');

$start_day='2018-01-01'; // 누적 수당계산 시작일

if($fr_date==''){ 
	$fr_date=$day;
}
if($to_date==''){ 
	$to_date=$day;
}

	//$to_date=date('Y-m-d');

	

	//$ym=substr($fr_date,0,7);




if($benefit_yn_chk==1){
 	$sql= " delete from iwol where date_format(iwolday,'%Y-%m-%d')='$to_date'"; 
	sql_query($sql);
}


/*
if($sales_yn_chk==1){

$sql= " UPDATE g5_member SET noo_my_sales=0,  mon_my_sales=0 ,day_sales=0, mb_habu_sum=0, noo_habu_sum=0, mon_habu_sum=0, habu_day_sales=0 ,mb_my_sales=0";
sql_query($sql);
}
*/


if($noo_start_day==''){
	$noo_start_day=date('Y-01');
}


// 직급이 최소 1스타(3) 이상이고 하부에 오늘 매출이 있는 사람만 
$sql = "SELECT mb_id, mb_name, mb_hp, mb_level, mb_recommend, mb_brecommend, mb_my_sales, habu_day_sales FROM g5_member WHERE habu_day_sales>0 and mb_level>=3 and date_format(sales_day,'%Y-%m-%d')='$to_date'";
$result = sql_query($sql);

//make_class();
//echo $sql;

$history_cnt=0;
$no_benefit=0;
$rec='';


for ($i=0; $row=sql_fetch_array($result); $i++) {   


		$comp=$row['mb_id'];
		$confirm_exit1=0;
		$first=0; 
		$firstname='';
		$firstid='';
		$binary_trig=0;

		$binary_firstname='';
		$binary_firstid='';
		


		while(  ($comp!='admin')  ){ 
			
			$today_sales=0;
			$today_sales2=0;
			
			
			


			$sql = " SELECT mb_id, mb_name, mb_hp, mb_level, mb_recommend, mb_brecommend, sales_day, habu_day_sales  FROM g5_member WHERE mb_id= '".$comp."'";
			$recommend = sql_fetch($sql);

				$leg_success=0; // 메트릭스 성공찾기 클리어

				$my_today_sales=$recommend['mb_my_sales'];
				$habu_today_sales=$recommend['habu_day_sales'];
				
				$mbid=$recommend['mb_id'];
				$mbname=$recommend['mb_name'];
				
				$mblevel=$recommend['mb_level'];

				if(   ($mb_name=='본사')  || ($mbid=='')  ) break;


				$iwol_pass =0; 
				$benefit_pass =0; 


				//$iwol= sql_fetch("select count(*) as cnt from iwol where mb_id='".$mbid."' and date_format(iwolday,'%Y-%m-%d')='$day'");
		
				//if($iwol['cnt']==0){ $iwol_pass=1;}


				//if($confirm_exit1==1) break; //만약 루프탈출할 필요시 ...

					
							
					for ($i=0; $i<count($cond); $i++) {


					//위로 연속 올라가는 부분 추천인으로 올라갈지 바이너리추천인으로 올라갈지 결정
					if($cond[$i]['recom_kind']=='mb_recommend'){
							$recom=$recommend['mb_recommend'];
					}else{
							$recom=$recommend['mb_brecommend'];
					}

						//$sql= "UPDATE g5_member SET noo_my_sales=0,  mon_my_sales=0 ,day_sales=0, mb_habu_sum=0, noo_habu_sum=0, mon_habu_sum=0, habu_day_sales=0 where mb_id='".$comp."'"; 
						//sql_query($sql);

						//위$cond에서 설정한 내용에 대해 다시 변수설정하여 트리거 걸릴지 말지 설정
						if($cond[$i]['mat']=='1'){ $temp_cond_mat=1; } else {$temp_cond_mat=0;} // 메트릭스
						if($cond[$i]['bigsmall1']=='1'){ $temp_cond_bigsmall1=1; } else {$temp_cond_bigsmall1=0;} //대소실적
						if($cond[$i]['bigsmall2']=='1'){ $temp_cond_bigsmall2=1; } else {$temp_cond_bigsmall2=0;} //대소실적
						if($cond[$i]['level1']=='1'){ $temp_cond_level1=1; } else {$temp_cond_level1=0;} //본인직급
						if($cond[$i]['level2']=='1'){ $temp_cond_level2=1; } else {$temp_cond_level2=0;} //하부직급
						if($cond[$i]['history']=='1'){ $temp_cond_history=1; } else {$temp_cond_history=0;} //대수조건
						//if($cond[$i]['bf_limit1']=='1'){ $bf_limit1=1; } else {$bf_limit1=0;} //매출크기제한

						if($cond[$i]['limit_reset']==1){ 	$limit_reset=1; } else {$limit_reset=0;} //바이너리보너스조건
					
						
						//if($cond[$i]['b_bonus']=='1'){ $b_bonus=1; } else {$b_bonus=0;} //바이너리보너스 발생시 이후 계산조건



						/*
						//추천수당이라면 최초 매출자의 매출을 기록한다. 왜냐면 최초 매출자의 매출을 가지고 위로 올라가며 수당을 계산하기 때문에... 
						
						if( ($cond[$i]['base_source']=='추천수당') && ($history_cnt==0) ){
							$first= self_sales($comp, $start_day, $to_date);
							$firstname=$mbname;
							$firstid=$mbid;

						}

						//그런데 추천수당중 매출발생자 매출보다 커야만 받을수 있는 조건이 있다면 상위업라인들의 누적매출이 매출발생자 매출보다 같거나 커야만 지급한다. 
						if( $bf_limit1>0 ){

							$ccc=self_sales($comp, $start_day, $to_date);
							if($ccc>=$first){
								$bf_limit1=0;
							}else{
								$bf_limit1=1;
							}
						}
						*/



						

						$temp_sql1 = '';
						

						if($cond[$i]['per']!=''){ // 수당 퍼센트가 비어있으면 패스~

						
						//*********************		실적조건 있다면		*********************
							$tempnoobig=0;		//누적대실적
							$tempnoosmall=0;		//누적소실적
							$tempmhabunoo=0;		//누적하부실적
							$tempnoome=0;			//누적자기실적
							$tempmhabunoome=0;		//누적하부실적+자기실적

							$tempmonbig=0;		//금월대실적
							$tempmonsmall=0;		//금월소실적
							$tempmhabumon=0;		//금월하부실적
							$tempmonme=0;
							$tempmhabumonme=0;		//금월하부실적+자기실적		

							$tempdaybig=0;		//금일대실적
							$tempdaysmall=0;		//금일소실적
							$tempmhabuday=0;		//금일하부실적
							$tempdayme=0;
							$tempmhabudayme=0;		//금일하부실적+자기실적	

								if( ($cond[$i]['source']=='누적 대실적') || ($cond[$i]['base_source']=='누적 대실적') || ($cond[$i]['source']=='누적 소실적') || ($cond[$i]['base_source']=='누적 소실적') || ($cond[$i]['source']=='누적 하부실적') || ($cond[$i]['base_source']=='누적 하부실적') || ($cond[$i]['source']=='누적 하부실적+자기실적') || ($cond[$i]['base_source']=='누적 하부실적+자기실적') ||
								($cond[$i]['source']=='누적 자기실적') || ($cond[$i]['base_source']=='누적 자기실적') || ($cond[$i]['source11']=='누적 대실적') || ($cond[$i]['source11']=='누적 소실적') || ($cond[$i]['source11']=='누적 하부실적') || ($cond[$i]['source11']=='누적 하부실적+자기실적')||
								($cond[$i]['source11']=='누적 자기실적')  ){

										$big=0; //대실적 
										$small=0; //소실적

										//대실적과 소실적 조건이 있으면 미리 대.소실적을 구한다.
										if( ($cond[$i]['source']=='누적 대실적') || ($cond[$i]['base_source']=='누적 대실적') || ($cond[$i]['source']=='누적 소실적') || ($cond[$i]['base_source']=='누적 소실적') || ($cond[$i]['source11']=='누적 대실적') || ($cond[$i]['source11']=='누적 소실적') ) {
											
											//for($i=0;$i<count($arr);$i++){

												if($child[0]['hap']>=$child[1]['hap']){
													$child[0]['bigorsmall']='big';
													$big=$child[0]['bigorsmall'];
													$small=$child[1]['bigorsmall'];
												}else{
													$child[1]['bigorsmall']='big';
													$big=$child[1]['bigorsmall'];
													$small=$child[0]['bigorsmall'];
												}
											//}
										}

	
										if( ($cond[$i]['source']=='누적 대실적') || ($cond[$i]['source11']=='누적 대실적') || ($cond[$i]['base_source']=='누적 대실적')  ){
											 $tempnoobig=$big;
											 
											 if( $cond[$i]['source11']=='누적 대실적'){
												 $today_sales=$big;
											 }else{
												 $today_sales2=$big;
											 }
											
										}

										


										if( ($cond[$i]['source']=='누적 소실적') || ($cond[$i]['source11']=='누적 소실적') || ($cond[$i]['base_source']=='누적 소실적')){
											 
												$tempnoosmall=$small;

												if( $cond[$i]['source11']=='누적 소실적'){
													 $today_sales=$big;
												}else{
													 $today_sales2=$big;
												}
											
										}
										if( ($cond[$i]['source']=='누적 하부실적') || ($cond[$i]['source11']=='누적 하부실적') || ($cond[$i]['base_source']=='누적 하부실적')){
											  
												$tempmhabunoo=habu_sales($comp, $start_day, $to_date);
												if( $cond[$i]['source11']=='누적 하부실적'){
													 $today_sales=$big;
												}else{
													 $today_sales2=$big;
												}
											
										}

										if( ($cond[$i]['source']=='누적 자기실적') || ($cond[$i]['source11']=='누적 자기실적') || ($cond[$i]['base_source']=='누적 자기실적')){
											 
												$tempmnoome=self_sales($comp, $start_day, $to_date);

												if( $cond[$i]['source11']=='누적 자기실적'){
													 $today_sales=$big;
												}else{
													 $today_sales2=$big;
												}												
											 
										}


										if( ($cond[$i]['source']=='누적 하부실적+자기실적') || ($cond[$i]['source11']=='누적 하부실적+자기실적') || ($cond[$i]['base_source']=='누적 하부실적+자기실적')){

												$tempmhabunoome=(  habu_sales($comp, $start_day, $to_date)+self_sales($comp, $start_day, $to_date)  );	

												if( $cond[$i]['source11']=='누적 하부실적+자기실적'){
													 $today_sales=$big;
												}else{
													 $today_sales2=$big;
												}
											 
										}

										


							 }
							 



							//금월 대소실적 구함
								if( ($cond[$i]['source']=='금월 대실적') || ($cond[$i]['base_source']=='금월 대실적') || ($cond[$i]['source']=='금월 소실적') || ($cond[$i]['base_source']=='금월 소실적') || ($cond[$i]['source']=='금월 하부실적') || ($cond[$i]['base_source']=='금월 하부실적') || ($cond[$i]['source']=='금월 하부실적+자기실적') || ($cond[$i]['base_source']=='금월 하부실적+자기실적') ||
								($cond[$i]['source']=='금월 자기실적') || ($cond[$i]['base_source']=='금월 자기실적') || ($cond[$i]['source11']=='금월 대실적') || ($cond[$i]['source11']=='금월 소실적') || ($cond[$i]['source11']=='금월 하부실적') || ($cond[$i]['source11']=='금월 하부실적+자기실적')||
								($cond[$i]['source11']=='금월 자기실적')  ){
								
										$big=0; //대실적 
										$small=0; //소실적

										//대실적과 소실적 조건이 있으면 미리 대.소실적을 구한다.
										if( ($cond[$i]['source']=='금월 대실적') || ($cond[$i]['base_source']=='금월 대실적') || ($cond[$i]['source']=='금월 소실적') || ($cond[$i]['base_source']=='금월 소실적') || ($cond[$i]['source11']=='금월 대실적') || ($cond[$i]['source11']=='금월 소실적') ) {

												if($child[0]['hap']>=$child[1]['hap']){
													$child[0]['bigorsmall']='big';
													$big=$child[0]['bigorsmall'];
													$small=$child[1]['bigorsmall'];
												}else{
													$child[1]['bigorsmall']='big';
													$big=$child[1]['bigorsmall'];
													$small=$child[0]['bigorsmall'];
												}

											
										}

	
										if( ($cond[$i]['source']=='금월 대실적') || ($cond[$i]['source11']=='금월 대실적') || ($cond[$i]['base_source']=='금월 대실적')  ){
											 $tempnoobig=$big;	

												if( $cond[$i]['source11']=='금월 대실적'){
													 $today_sales=$big;
												}else{
													 $today_sales2=$big;
												}

											
										}
										if( ($cond[$i]['source']=='금월 소실적') || ($cond[$i]['source11']=='금월 소실적') || ($cond[$i]['base_source']=='금월 소실적')){
											 
												$tempnoosmall=$small;
												
												if( $cond[$i]['source11']=='금월 소실적'){
													 $today_sales=$big;
												}else{
													 $today_sales2=$big;
												}
											
										}
										if( ($cond[$i]['source']=='금월 하부실적') || ($cond[$i]['source11']=='금월 하부실적') || ($cond[$i]['base_source']=='금월 하부실적')){
											  
												$tempmhabunoo=habu_sales($comp, $min30, $to_date);	
												if( $cond[$i]['source11']=='금월 하부실적'){
													 $today_sales=$big;
												}else{
													 $today_sales2=$big;
												}
											
										}

										if( ($cond[$i]['source']=='금월 자기실적') || ($cond[$i]['source11']=='금월 자기실적') || ($cond[$i]['base_source']=='금월 자기실적')){
											 
												$tempmnoome=self_sales($comp, $min30, $to_date);
												if( $cond[$i]['source11']=='금월 자기실적'){
													 $today_sales=$big;
												}else{
													 $today_sales2=$big;
												}												
											 
										}


										if( ($cond[$i]['source']=='금월 하부실적+자기실적') || ($cond[$i]['source11']=='금월 하부실적+자기실적') || ($cond[$i]['base_source']=='금월 하부실적+자기실적')){

												$tempmhabunoome=(  habu_sales($comp, $min30, $to_date)+self_sales($comp, $start_day, $to_date)  );	
												if( $cond[$i]['source11']=='금월 하부실적+자기실적'){
													 $today_sales=$big;
												}else{
													 $today_sales2=$big;
												}
											 
										}

										


							 }

							 //금일 대소실적 구함
							 if( ($cond[$i]['source']=='금일 대실적') || ($cond[$i]['base_source']=='금일 대실적') || ($cond[$i]['source']=='금일 소실적') || ($cond[$i]['base_source']=='금일 소실적') || ($cond[$i]['source']=='금일 하부실적') || ($cond[$i]['base_source']=='금일 하부실적') || ($cond[$i]['source']=='금일 하부실적+자기실적') || ($cond[$i]['base_source']=='금일 하부실적+자기실적') ||
								($cond[$i]['source']=='금일 자기실적') || ($cond[$i]['base_source']=='금일 자기실적') || ($cond[$i]['source11']=='금일 대실적') || ($cond[$i]['source11']=='금일 소실적') || ($cond[$i]['source11']=='금일 하부실적') || ($cond[$i]['source11']=='금일 하부실적+자기실적')||
								($cond[$i]['source11']=='금일 자기실적')  ){

										$big=0; //대실적 
										$small=0; //소실적

										//대실적과 소실적 조건이 있으면 미리 대.소실적을 구한다.
										if( ($cond[$i]['source']=='금일 대실적') || ($cond[$i]['base_source']=='금일 대실적') || ($cond[$i]['source']=='금일 소실적') || ($cond[$i]['base_source']=='금일 소실적') || ($cond[$i]['source11']=='금일 대실적') || ($cond[$i]['source11']=='금일 소실적') ) {

												if($child[0]['hap']>=$child[1]['hap']){
													$child[0]['bigorsmall']='big';
													$big=$child[0]['bigorsmall'];
													$small=$child[1]['bigorsmall'];
												}else{
													$child[1]['bigorsmall']='big';
													$big=$child[1]['bigorsmall'];
													$small=$child[0]['bigorsmall'];
												}
										}

	
										if( ($cond[$i]['source']=='금일 대실적') || ($cond[$i]['source11']=='금일 대실적') || ($cond[$i]['base_source']=='금일 대실적')  ){
											 $tempnoobig=$big;	
												if( $cond[$i]['source11']=='금월 자기실적'){
													 $today_sales=$big;
												}else{
													 $today_sales2=$big;
												}
											
										}
										if( ($cond[$i]['source']=='금일 소실적') || ($cond[$i]['source11']=='금일 소실적') || ($cond[$i]['base_source']=='금일 소실적')){
											 
												$tempnoosmall=$small;	
											
										}
										if( ($cond[$i]['source']=='금일 하부실적') || ($cond[$i]['source11']=='금일 하부실적') || ($cond[$i]['base_source']=='금일 하부실적')){
											  
												$tempmhabunoo=habu_sales($comp, $fr_date, $to_date);	
											
										}

										if( ($cond[$i]['source']=='금일 자기실적') || ($cond[$i]['source11']=='금일 자기실적') || ($cond[$i]['base_source']=='금일 자기실적')){

												$tempmnoome=self_sales($comp, $fr_date, $to_date);	
																							
										}


										if( ($cond[$i]['source']=='금일 하부실적+자기실적') || ($cond[$i]['source11']=='금일 하부실적+자기실적') || ($cond[$i]['base_source']=='금일 하부실적+자기실적')){

												$tempmhabunoome=(  habu_sales($comp, $fr_date, $to_date)+self_sales($comp, 
												$start_day, $to_date)  );	
											 
										}

							 } //금일하부실적 끝

					

								// 실적조건1
								if(   ($cond[$i]['source_cond1']=='==') && ( $cond[$i]['source']!='' ) ){

										if($today_sales==$cond[$i]['source_in1']){
											
											$temp_cond_bigsmall1=0;
										}else{
											$temp_cond_bigsmall1=1;
										}

								}else if(  ($cond[$i]['source_cond1']=='>=') && ($cond[$i]['source_cond2']=='') && ( $cond[$i]['source']!='' ) ){
								
									//echo $today_sales.'  >= '.$cond[$i]['source_in1'].'<br>';
										if($today_sales>=$cond[$i]['source_in1']){
											
											$temp_cond_bigsmall1=0;
										}else{
											$temp_cond_bigsmall1=1;
										}

								}else if(  ($cond[$i]['source_cond1']=='') && ($cond[$i]['source_cond2']=='<=') && ( $cond[$i]['source']!='' ) ){

										if($today_sales<=$cond[$i]['source_in2']){
											
											$temp_cond_bigsmall1=0;
										}else{
											$temp_cond_bigsmall1=1;
										}

								}else if(  ($cond[$i]['source_cond1']=='>=') && ($cond[$i]['source_cond2']=='<=') && ( $cond[$i]['source']!='' )  ){
									

										if(  ($today_sales>=$cond[$i]['source_in1']) && ($today_sales<=$cond[$i]['source_in2'])  ){
											
											$temp_cond_bigsmall1=0;
										}else{
											$temp_cond_bigsmall1=1;
										}
								}



								// 실적조건2
								if(   ($cond[$i]['source_cond11']=='==') && ( $cond[$i]['source11']!='' ) ){

										if($today_sales==$cond[$i]['source_in11']){
											
											$temp_cond_bigsmall2=0;
										}else{
											$temp_cond_bigsmall2=1;
										}

								}else if(  ($cond[$i]['source_cond11']=='>=') && ($cond[$i]['source_cond12']=='') && ( $cond[$i]['source11']!='' ) ){
								
									//echo $today_sales.'  >= '.$cond[$i]['source_in1'].'<br>';
										if($today_sales>=$cond[$i]['source_in11']){
											
											$temp_cond_bigsmall2=0;
										}else{
											$temp_cond_bigsmall2=1;
										}

								}else if(  ($cond[$i]['source_cond11']=='') && ($cond[$i]['source_cond12']=='<=') && ( $cond[$i]['source11']!='' ) ){

										if($today_sales<=$cond[$i]['source_in12']){
											
											$temp_cond_bigsmall2=0;
										}else{
											$temp_cond_bigsmall2=1;
										}

								}else if(  ($cond[$i]['source_cond11']=='>=') && ($cond[$i]['source_cond12']=='<=') && ( $cond[$i]['source11']!='' )  ){
									

										if(  ($today_sales>=$cond[$i]['source_in11']) && ($today_sales<=$cond[$i]['source_in12'])  ){
											
											$temp_cond_bigsmall2=0;
										}else{
											$temp_cond_bigsmall2=1;
										}
								}

								//*********************		실적조건 끝		*********************








								///////********   메티릭스 조건


								 if($cond[$i]['partner_cnt']>0){
									 
									if(get_depth($comp)==$cond[$i]['partner_cnt']){

										$temp_cond_mat=0;

									}else{
										$temp_cond_mat=1;

									}


								 } // if




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

								// 바이너리매칭 대수조건

								if(   ($cond[$i]['history_cond1']=='==')   ){

										if(($no_benefit-1)==$cond[$i]['history_in1']){
											$bm=0;
										}else{
											$bm=1;
										}

								}else if(  ($cond[$i]['history_cond1']=='>=') && ($cond[$i]['history_cond1']=='')  ){
										if(($no_benefit-1)>=$cond[$i]['history_in1']){
											$bm=0;
										}else{
											$bm=1;
										}

								}else if(  ($cond[$i]['history_cond1']=='') && ($cond[$i]['history_cond1']=='<=')  ){

										if(($no_benefit-1)<=$cond[$i]['history_in1']){
											$bm=0;
										}else{
											$bm=1;
										}

								}else if(  ($cond[$i]['history_cond1']=='>=') && ($cond[$i]['history_cond2']=='<=')  ){
										if( (($no_benefit-1)>=$cond[$i]['history_in1']) && ($history_cnt<=$cond[$i]['history_in2']) ){
											$bm=0;
										}else{
											$bm=1;
										}
								
								}


								if($bm==0){

									$bm=0;

									if( ($binary_trig>0) && ($cond[$i]['base_source']=='Cycle') ) {
										
										
										$rec=$binary_firstname.'('.$binary_firstid.') 로부터 '.$cond[$i]['allowance_name'].' 발생, 내 직급: '.$mb_level.', 대수: '.($no_benefit-1).' Cycle 수: '.$binary_trig;

										save_benefit($fr_date,$mbid, $mbname, $recom, $cond[$i]['allowance_name'], $recommend['sales_day'], $recommend['habu_day_sales'], ($binary_trig*$cond[$i]['per']), $cond[$i]['allowance_name'].' '.$rec);
									}
								}




						  //******  바이너리보너스 계산
						 
						 if(($limit_reset==1)  ){

							
							$note='';
							$id1='';
							$id2='';
							$big=0;
							$small=0;
							
							$hap1=0;
							$hap2=0;

							list($id1,$hap1,$id2,$hap2)=my_bchild($mbid);

							//echo $cond[$i]['allowance_name'].'   '.$id1.': '.$hap1.'  ,  '.$id2.': '.$hap2.'<br>';
								
							if(($hap1>0) || ($hap2>0)){
								
								if( $hap1<=$hap2 ){ //$hap1이 소실적이라면

									if($hap1>=($cond[$i]['sales_reset']) ){ //소실적이 극점?

										
										$today_sales=($cond[$i]['sales_reset']/$cond[$i]['cycle'])*$cond[$i]['per'];
										$binary_trig=($cond[$i]['sales_reset']/$cond[$i]['cycle']);
										$firstname=$mbname;
										$firstid=$mbid;

				
										


										if( $cond[$i]['max_reset1'] =='대.소실적 모두이월'){

											$note='극점초과(대.소실적 모두이월) : 소실적-'.$hap1.'('.$id1.') / 대실적-'.$hap2.'('.$id2.')';

											$no_benefit=1;$binary_firstname=$mbname;$binary_firstid=$mbid;
											if($today_sales>0) save_benefit($fr_date,$mbid, $mbname, $recom, $cond[$i]['allowance_name'], $recommend['sales_day'], $recommend['habu_day_sales'], $today_sales, $cond[$i]['allowance_name'].' '.$note);

											iwol_process($fr_date, $mbid, $id1, $mbname, '극점초과', $hap1-$cond[$i]['sales_reset'] , $note);
											iwol_process($fr_date, $mbid, $id2, $mbname, '극점초과',$hap2-$cond[$i]['sales_reset'] , $note);


										}else if($cond[$i]['max_reset1']=='대실적만 이월'){

											$note='극점초과(대실적만 이월) : 소실적-'.$hap1.	'('.$id1.') / 대실적-'.$hap2.	'('.$id2.') 이월금:'.($hap2-$cond[$i]['sales_reset']);
											
											$no_benefit=1;$binary_firstname=$mbname;$binary_firstid=$mbid;
											if($today_sales>0) save_benefit($fr_date,$mbid, $mbname, $recom, $cond[$i]['allowance_name'], $recommend['sales_day'], $recommend['habu_day_sales'], $today_sales, $cond[$i]['allowance_name'].' '.$note);

											iwol_process($fr_date, $mbid, $id2, $mbname, '극점초과',$hap2-$cond[$i]['sales_reset'] , $note); 

										}else if($cond[$i]['max_reset1']=='소실적만 이월'){
											$note='극점초과(소실적만 이월) : 소실적-'.$hap1.	'('.$id1.') / 대실적-'.$hap2.	'('.$id2.') 이월금:'.($hap1-$cond[$i]['sales_reset']);

											$no_benefit=1;$binary_firstname=$mbname;$binary_firstid=$mbid;
											if($today_sales>0) save_benefit($fr_date,$mbid, $mbname, $recom, $cond[$i]['allowance_name'], $recommend['sales_day'], $recommend['habu_day_sales'], $today_sales, $cond[$i]['allowance_name'].' '.$note);

											iwol_process($fr_date,$mbid,  $id1, $mbname, '극점초과',$hap1-$cond[$i]['sales_reset'] , $note); 
										}


										
										
									}else{ //극점이 아니고
										if($hap1>=($cond[$i]['cycle'])){ // 1Cycle보다 크다

											$cnt=floor(($hap1)/($cond[$i]['cycle'])); // 몇Cycle인지 알아낸다

											$today_sales=$cnt*$cond[$i]['per'];
											$binary_trig=$cnt;
											$firstname=$mbname;
											$firstid=$mbid;


											if($cond[$i]['max_reset2']=='대.소실적 모두이월'){

												$note=$cnt.'Cycle달성(대.소실적 모두이월) : 소실적-'.$hap1.	'('.$id1.') / 대실적-'.$hap2.	'('.$id2.')';

												$no_benefit=1;$binary_firstname=$mbname;$binary_firstid=$mbid;
												if($today_sales>0) save_benefit($fr_date,$mbid, $mbname, $recom, $cond[$i]['allowance_name'], $recommend['sales_day'], $recommend['habu_day_sales'], $today_sales, $cond[$i]['allowance_name'].' '.$note);

												iwol_process($fr_date, $mbid, $id1, $mbname, $cnt.'Cycle성공', $hap1-($cnt*($cond[$i]['cycle']) )  , $note); 
												iwol_process($fr_date, $mbid, $id2, $mbname, $cnt.'Cycle성공', $hap2-($cnt*($cond[$i]['cycle']) )  , $note); 


											}else if($cond[$i]['max_reset2']=='대실적만 이월'){

												$note=$cnt.'Cycle달성(대실적만 이월) : 소실적-'.$hap1.	'('.$id1.') / 대실적-'.$hap2.	'('.$id2.') 이월금:'.($hap2-$cond[$i]['sales_reset']);

												$no_benefit=1;$binary_firstname=$mbname;$binary_firstid=$mbid;
												if($today_sales>0) save_benefit($fr_date,$mbid, $mbname, $recom, $cond[$i]['allowance_name'], $recommend['sales_day'], $recommend['habu_day_sales'], $today_sales, $cond[$i]['allowance_name'].' '.$note);

												iwol_process($fr_date, $mbid,  $id2, $mbname, $cnt.'Cycle성공', $hap2-$cond[$i]['sales_reset'] , $note); 

											}else if($cond[$i]['max_reset2']=='소실적만 이월'){
												$note=$cnt.'Cycle달성(소실적만 이월): 소실적-'.$hap1.	'('.$id1.') / 대실적-'.$hap2.	'('.$id2.') 이월금:'.($hap1-$cond[$i]['sales_reset']);

												$no_benefit=1;$binary_firstname=$mbname;$binary_firstid=$mbid;
												if($today_sales>0) save_benefit($fr_date,$mbid, $mbname, $recom, $cond[$i]['allowance_name'], $recommend['sales_day'], $recommend['habu_day_sales'], $today_sales, $cond[$i]['allowance_name'].' '.$note);

												iwol_process($fr_date,$mbid,  $id1, $mbname, $cnt.'Cycle성공', $hap1-$cond[$i]['sales_reset'] , $note); 
											}
											

										}else{ // 1Cycle보다 작다
											$note='0Cycle미달 : 소실적-'.$hap1.	'('.$id1.') / 대실적-'.$hap2.	'('.$id2.')';

												
				
												iwol_process($fr_date, $mbid, $id1, $mbname, 'Cycle미달', $hap1  , $note); 
												iwol_process($fr_date, $mbid, $id2, $mbname, 'Cycle미달', $hap2  , $note); 
										}
									}

								}else{ //$hap2가 소실적이라면

									if($hap2>=($cond[$i]['sales_reset']) ){ //소실적이 극점?



										$today_sales=($cond[$i]['sales_reset']/$cond[$i]['cycle'])*$cond[$i]['per']; 
										$binary_trig=($cond[$i]['sales_reset']/$cond[$i]['cycle']);
										$firstname=$mbname;
										$firstid=$mbid;
									

										if($cond[$i]['max_reset1']=='대.소실적 모두이월'){

											$note='극점초과(대.소실적 모두이월) : 소실적-'.$hap2.	'('.$id2.') / 대실적-'.$hap1.	'('.$id1.')';

												$no_benefit=1;$binary_firstname=$mbname;$binary_firstid=$mbid;
												if($today_sales>0) save_benefit($fr_date,$mbid, $mbname, $recom, $cond[$i]['allowance_name'], $recommend['sales_day'], $recommend['habu_day_sales'], $today_sales, $cond[$i]['allowance_name'].' '.$note);

											iwol_process($fr_date, $mbid, $id1, $mbname, '극점초과',$hap1-$cond[$i]['sales_reset'] , $note);
											iwol_process($fr_date, $mbid, $id2, $mbname, '극점초과',$hap2-$cond[$i]['sales_reset'] , $note);


										}else if($cond[$i]['max_reset1']=='대실적만 이월'){

											$note='극점초과(대실적만 이월) : 소실적-'.$hap2.	'('.$id2.') / 대실적-'.$hap1.	'('.$id1.') 이월금:'.($hap1-$cond[$i]['sales_reset']);

												$no_benefit=1;$binary_firstname=$mbname;$binary_firstid=$mbid;
												if($today_sales>0) save_benefit($fr_date,$mbid, $mbname, $recom, $cond[$i]['allowance_name'], $recommend['sales_day'], $recommend['habu_day_sales'], $today_sales, $cond[$i]['allowance_name'].' '.$note);

											iwol_process($fr_date, $mbid, $id1, $mbname, '극점초과',$hap1-$cond[$i]['sales_reset'] , $note); 

										}else if($cond[$i]['max_reset1']=='소실적만 이월'){
											$note='극점초과(소실적만 이월) : 소실적-'.$hap2.	'('.$id2.') / 대실적-'.$hap1.	'('.$id2.') 이월금:'.($hap2-$cond[$i]['sales_reset']);
											iwol_process($fr_date, $mbid, $id2, $mbname, '극점초과',$hap2-$cond[$i]['sales_reset'] , $note); 
										}


										
										
									}else{ //극점이 아니고
										if($hap2>=($cond[$i]['cycle'])){ // 1Cycle보다 크다

											$cnt=floor(($hap2)/($cond[$i]['cycle'])); // 몇Cycle인지 알아낸다
											$today_sales=$cnt*$cond[$i]['per'];
											$binary_trig=$cnt;
											$firstname=$mbname;
											$firstid=$mbid;

											if($cond[$i]['max_reset2']=='대.소실적 모두이월'){

												$note=$cnt.'Cycle달성(대.소실적 모두이월) : 소실적-'.$hap2.	'('.$id2.') / 대실적-'.$hap1.	'('.$id1.')';

												$no_benefit=1;$binary_firstname=$mbname;$binary_firstid=$mbid;
												if($today_sales>0) save_benefit($fr_date,$mbid, $mbname, $recom, $cond[$i]['allowance_name'], $recommend['sales_day'], $recommend['habu_day_sales'], $today_sales, $cond[$i]['allowance_name'].' '.$note);

												iwol_process($fr_date, $mbid, $id1, $mbname, $cnt.'Cycle성공', $hap1-($cnt*($cond[$i]['cycle']) )  , $note); 
												iwol_process($fr_date, $mbid, $id2, $mbname, $cnt.'Cycle성공', $hap2-($cnt*($cond[$i]['cycle']) )  , $note); 


											}else if($cond[$i]['max_reset2']=='대실적만 이월'){

												$note=$cnt.'Cycle달성(대실적만 이월) : 소실적-'.$hap2.	'('.$id2.') / 대실적-'.$hap1.	'('.$id1.') 이월금:'.($hap1-$cond[$i]['sales_reset']);

												$no_benefit=1;$binary_firstname=$mbname;$binary_firstid=$mbid;
												if($today_sales>0) save_benefit($fr_date,$mbid, $mbname, $recom, $cond[$i]['allowance_name'], $recommend['sales_day'], $recommend['habu_day_sales'], $today_sales, $cond[$i]['allowance_name'].' '.$note);

												iwol_process($fr_date, $mbid, $id1, $mbname, $cnt.'Cycle성공',$hap1-$cond[$i]['sales_reset'] , $note); 

											}else if($cond[$i]['max_reset2']=='소실적만 이월'){
												$note=$cnt.'Cycle달성(소실적만 이월): 소실적-'.$hap2.	'('.$id2.') / 대실적-'.$hap1.	'('.$id1.') 이월금:'.($hap2-$cond[$i]['sales_reset']);

												$no_benefit=1;$binary_firstname=$mbname;$binary_firstid=$mbid;
												if($today_sales>0) save_benefit($fr_date,$mbid, $mbname, $recom, $cond[$i]['allowance_name'], $recommend['sales_day'], $recommend['habu_day_sales'], $today_sales, $cond[$i]['allowance_name'].' '.$note);

												iwol_process($fr_date, $mbid, $id2, $mbname, $cnt.'Cycle성공',$hap2-$cond[$i]['sales_reset'] , $note); 
											}
											

										}else{ // 1Cycle보다 작다
											$note='1Cycle미달 : 소실적-'.$hap1.	'('.$id1.') / 대실적-'.$hap2.	'('.$id2.')';

												
												iwol_process($fr_date,$mbid,  $id1, $mbname, 'Cycle미달', $hap1  , $note); 
												iwol_process($fr_date,$mbid,  $id2, $mbname, 'Cycle미달', $hap2  , $note); 
										}
									}
								}  //$hap2가 소실적이라면 

							 }// if(($hap1>0) || ($hap2>0)){



								
						 } // 바이너리

											
							//	echo $cond[$i]['base_source'].'=='.$today_sales.'==='.$temp_cond_history.'   '.$temp_cond_bigsmall1.'    '.$temp_cond_level.'   '.$temp_cond_mat.'<br>';;


							 echo $comp.'-대수: '.$mblevel.'-'.$history_cnt.'-'.$temp_cond_history.' -대소실적: '.$temp_cond_bigsmall1.'-대소실적 : '.$temp_cond_bigsmall2.'-본인직급: '.$temp_cond_level1.' -하부직급: '.$temp_cond_level2.'-매트릭스: '.$temp_cond_mat.'-매출크기: '.$bf_limit1.'- 바이너리보너스: '.$limit_reset.'todaysales:'.$today_sales.'수익:'.$no_benefit.'trig:'.$binary_trig.$cond[$i]['base_source'].'<br>';
								


								// ***** 걸린 조건을 모두 충족한다면 계산하라 
						
								if(   ($temp_cond_history==0)  &&  ($temp_cond_bigsmall1==0) &&  ($temp_cond_bigsmall2==0)  &&  ($temp_cond_level1==0)  && ($temp_cond_leve2==0)  &&  ($temp_cond_mat==0) && ($bf_limit1==0) && ($limit_reset==0)    ){


										//if($save_benefit==1){ // 저장하라면 

		
			

											 switch(  $cond[$i]['base_source'] )
											 {
												 case '누적 대실적':
													 $today_sales= $tempnoobig;

													 break;
												 case '누적 소실적':
													 $today_sales= $tempnoosmall;

													 break;
												 case '누적 하부실적':
													$today_sales= $tempmhabunoo; 	
													 break;
												 case '누적 자기실적':
													 $today_sales= $tempmhabunoome; 	
													 break;
												 case '누적 하부실적+자기실적':
													 $today_sales= ($tempmhabunoo+$tempmhabunoome); 	
													 break;
							

												 case '금월 대실적':

													 //echo  $comp.': '.$cond[$i]['base_source'].'==='.$tempmonbig.'<br>';;

													 $today_sales= $tempmonbig;

													 break;
												 case '금월 소실적':
													 $today_sales= $tempmonsmall;

													 break;
												 case '금월 하부실적':
													// if($cond[$i]['immediate']!=1){ //즉시매출이 아닐경우 하부 합산한 매출 , 금월매출이면 하부라도 한사람분만 
														 $today_sales= $tempmhabumon; 
													// }
													 break;
												 case '금월 자기실적':
													//if($cond[$i]['immediate']!=1){  
														$today_sales= $tempmhabumonme;
													//}
													 break;
												 case '금월 하부실적+자기실적': 
														 $today_sales= ($tempmhabumon+$tempmhabumonme); 	
													 break;



												 case '금일 대실적':

													 //echo  $comp.': '.$cond[$i]['base_source'].'==='.$tempmonbig.'<br>';;

													 $today_sales= $tempdaybig;

													 break;
												 case '금일 소실적':
													 $today_sales= $tempdaysmall;

													 break;
												 case '금일 하부실적':
													
													 $today_sales= $tempmhabuday; 
													 
													 break;
												 case '금일 자기실적':
													//if($cond[$i]['immediate']!=1){  
														$today_sales= $tempmhabudayme;
													//}
													 break;
												 case '금일 하부실적+자기실적': 
														 $today_sales= ($tempmhabuday+$tempmhabudayme); 	
													 break;
												 case '추천수당': 
														 $today_sales= $first; 
													 break;
												 case 'Cycle': 
														 $today_sales= $cond[$i]['per']; 
													 break;

											 }
														
												

												

													/*$benefit=($today_sales)*($cond[$i]['per']/100);
													$rec.=$note;*/

													$rec=$cond[$i]['allowance_name'].':  '.$history_cnt.'대 -'.$cond[$i]['base_source'].': '.$today_sales.'*'.($cond[$i]['per']/100).'='.$benefit.' / <br/>';


											

												//echo $mbid.' ---'.$rec;

												/*if(($today_sales>0) && ($no_benefit==0) ){ 
													save_benefit($fr_date,$mbid, $mbname, $recom, $cond[$i]['allowance_name'], $recommend['sales_day'], $recommend['habu_day_sales'], $today_sales, $cond[$i]['allowance_name'].' '.$note);
												}
												*/


												/*

													   //**** 수당이 있다면 함께 DB에 저장 한다.
													$temp_sql1 = " insert soodang_pay set day='".$day."'";
													$temp_sql1 .= " ,mb_id		= '".$mbid."'";
													$temp_sql1 .= " ,mb_name		= '".$mbname."'";
													$temp_sql1 .= " ,mb_recommend		= '".$recom."'";
													$temp_sql1 .= " ,allowance_name		= '".$cond[$i]['allowance_name']."'";
													$temp_sql1 .= " ,andor		= '".$cond[$i]['andor']."'";
													
													$temp_sql1 .= " ,accu_my_sales		=  '".$recommend['noo_my_sales']."'";
													$temp_sql1 .= " ,mon_my_sales		=  '".$recommend['mon_my_sales']."'";
													$temp_sql1 .= " ,accu_habu_sum		=  '".$recommend['noo_habu_sum']."'";
													$temp_sql1 .= " ,mon_habu_sum		=  '".$recommend['mon_habu_sum']."'";
													$temp_sql1 .= " ,benefit		=  '".($benefit)."'";
													$temp_sql1 .= " ,rec		= '".$rec."'";
													
													

													sql_query($temp_sql1);
												*/
												

										

										/*
										
												//***************** 분할지급 **********************
												
												$paidcnt=0; // 추후 필요시 , 과거 이미 지급된 것이 있다면 그 이후부터 카운트

												$divid = explode(">", $cond[$i]['andor']); 
												if($paidcnt>0) {$cnt=$paidcnt+1;	}else{$cnt=1;}
					
												$ccc=round($benefit/count($divid));
												$tax=round($ccc*0.043); 
												

												for($y=0; $y<count($divid); $y++) {	

													
													
													 $newdate=date("Y-m-$divid[$y]",strtotime($day))   ; 

													 //echo $day.'--'.$divid[$y].'----'.$newdate.'-<br>';
													 $ttt= strtotime($newdate);
													 $rrr=$divid[$y]*$cnt;
													 $nal=date("Y-m-d", strtotime("+$rrr day", $ttt));
													 $nal2=plus_day($nal,0);
													 
													if($y>=$paidcnt){	

														$sql = " insert dividend set dv_datetime='".$nal2."'";
														$sql .= " ,mb_id		= '".$mbid."'";
														$sql .= " ,mb_name		= '".$mbname."'";
														$sql .= " ,mb_hp		= '".$mbhp."'";
														
														$sql .= " ,dv_gubun		= '".$cond[$i]['allowance_name']."'";
														$sql .= " ,dv_content		= '".$rec."'";
														$sql .= " ,dv_money 			= '$ccc'";
														$sql .= "	,dv_tax 			= '$tax'";
														$sql .= "	,dv_count 			= '$cnt'";
														$cnt++;

														sql_query($sql);
														
															
													}
										 
												$nal=$nal2;
												}     

											*/


				
										//}//if($save_benefit==1){ /

										$oldcomp=$comp;

										

								}else{//if(   ($temp_cond_history==0)  &&  ($temp_cond_bigsmall1==0) 
									
								}

								
							

						}// 수당per 가 있으면 
					} // for
							
				
					
					//echo $rec;
					$rec='';


			$comp=$recom;


			$history_cnt++;

			if($no_benefit>0)$no_benefit++;


		} // while

		$rec='';
		$history_cnt=0;
		$today_sales=0;
		

} //for

alert('수당계산이 완료되었습니다');

?>

