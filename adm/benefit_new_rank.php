<?php
$sub_menu = "600600";
include_once('/home/sdevftv/html/common.php'); //실서버용 경로


//auth_check($auth[$sub_menu], 'r');

function self_sales($recom){

	//$sql_search = " and date_format(o.od_time,'%Y-%m-%d')>='$fr_date' and date_format(o.od_time,'%Y-%m-%d')<='$to_date' group by md_id";

    $res= sql_fetch("select sum(pv)as hap from g5_shop_order as o where mb_id='".$recom."'");
	
	return $res['hap'];    
	
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

$cond = array(array('price_cond'=>'','source'=>'','source_cond1'=>'','source_cond2'=>'','source_in1'=>'','source_in2'=>'','mb_level_cond1'=>'','mb_level_cond2'=>'','mb_level_in1'=>'','mb_level_in2'=>'','partner_cnt'=>'','partner_cont'=>'','history_cnt'=>'','history_cond1'=>'','history_cond2'=>'','history_in1'=>'','history_in2'=>'','base_source'=>'','per'=>'','allowance_name'=>'','mat'=>'','level1'=>'','level2'=>'','bigsmall1'=>'','bigsmall2'=>'','history'=>'','benefit'=>'','benefit_limit1'=>'','source11'=>'','source_cond11'=>'','source_cond12'=>'','source_in11'=>'','source_in12'=>'','sales_reset'=>'','max_reset1'=>'','max_reset2'=>'','mb_level_in11'=>'','mb_level_cond11'=>'','mb_level_cond12'=>'','bf_limit1'=>''));

$benefitSql = "SELECT * from soodang_set where immediate=3 order by partner_cnt desc, no";
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

//$day='2010-03-08';
$day  = $_GET['d'];
$start_day='2017-01-01'; // 누적 수당계산 시작일

if($fr_date==''){ 
	$fr_date=$day;
}
if($to_date==''){ 
	$to_date=$day;
}

function habu_rank($recom){
		$onestar=0;
		$twostar=0;
		$threestar=0;
		$fourstar=0;
		$fivestar=0;
		$sixstar=0;
	
    $res= sql_query("select * from g5_member where mb_recommend='".$recom."' ");
	$id = array();
    for ($j=0; $rrr=sql_fetch_array($res); $j++) { 
		$id[$j] = $rrr['mb_id'];
		list($one,$two,$three,$four,$five,$six) = habu_rank_sub($id[$j]);


			if($one>=1)$one=1;
			if($two>=1)$two=1;
			if($three>=1)$three=1;
			if($four>=1)$four=1;
			if($five>=1)$five=1;
			if($six>=1)$six=1;

			$onestar+=$one;   		
			$twostar+=$two; 
			$threestar+=$three; 
			$fourstar+=$four; 
			$fivestar+=$five; 
			$sixstar+=$six;
	} // for j	
	return array($onestar,$twostar,$threestar,$fourstar,$fivestar,$sixstar);  
}
function habu_rank_sub($id){
		$resh= sql_query("select * from g5_member where mb_recommend='".$id."' ");
		for ($j=0; $rrrh=sql_fetch_array($resh); $j++) { 
				$recomh=$rrrh['mb_id'];  
 				$odsql= sql_fetch("select mb_level from g5_member where mb_id='".$recomh."'");			  
 				switch($odsql['mb_level']){
				case 3:
					$onestar++;
					break;
				case 4:
					$twostar++;
					break;
				case 5:
					$threestar++;
					break;
				case 6:
					$fourstar++;
					break;
				case 7:
					$fivestar++;
					break;
				case 8:
					$sixstar++;
					break;
			  }
			
			list($one,$two,$three,$four,$five,$six)=habu_rank_sub($recomh);	 

			if($one>=1)$one=1;
			if($two>=1)$two=1;
			if($three>=1)$three=1;
			if($four>=1)$four=1;
			if($five>=1)$five=1;
			if($six>=1)$six=1;

			$onestar+=$one;   		
			$twostar+=$two; 
			$threestar+=$three; 
			$fourstar+=$four; 
			$fivestar+=$five; 
			$sixstar+=$six;

		}//for 종료
		$mylevel= sql_fetch("select mb_level from g5_member where mb_id='".$id."'");			  
		switch($mylevel['mb_level']){
			case 3:
				$onestar++;
				break;
			case 4:
				$twostar++;
				break;
			case 5:
				$threestar++;
				break;
			case 6:
				$fourstar++;
				break;
			case 7:
				$fivestar++;
				break;
			case 8:
				$sixstar++;
				break;
		}
		return array($onestar,$twostar,$threestar,$fourstar,$fivestar,$sixstar);    
}  
	echo "승급 분석 시작...<br>";

	$degrade=1; //강등여부 0 이면 강등은 안하고 승진만 1이면 승진.강등 진행됨


	//$sql = "SELECT mb_id, mb_name, mb_hp, mb_level, mb_recommend, mb_brecommend, sales_day, habu_day_sales FROM g5_member WHERE ((habu_day_sales>0)  or (day_my_sales>0) ) "; 
	// 오늘 자기매출이든 하부매출이든 있는 사람만 불러온다.
	$sql = "SELECT m.mb_id, m.mb_name, m.mb_hp, m.mb_level, m.mb_recommend, m.mb_brecommend , b2.todayy FROM g5_member as m, btoday2 as b2 WHERE m.mb_id=b2.mb_id and b2.todayy>0 and b2.day = '".$to_date."'"; 
	$result = sql_query($sql);
	$rec='';
	echo $sql;

	for ($i=0; $row=sql_fetch_array($result); $i++) {   
		$degrade=0;

		$mbid=$row['mb_id'];
		
		$get_rank_date = "Select rank_day from rank where mb_id='".$mbid."' order by rank_day desc";
		$rank_date_row= sql_fetch($get_rank_date);
		$rank_date = $rank_date_row['rank_day'];
		$chage_date = strtotime("$rank_date +30 days");
		if(date("Y-m-d",$chage_date)<= $to_date &&  $mbid != 'landlord' && $mbid != 'Coolrunning'){
			$degrade=1;
		}
		$mbname=$row['mb_name'];
		$mblevel=$row['mb_level'];
		$twoleg=0;
		$recom_sql = sql_fetch("select count(mb_id) as count_recom from g5_member where mb_recommend = '".$mbid."'");
		$pool_sql = sql_fetch("select it_pool1, it_pool2, it_pool3, it_pool4, it_pool5, it_pool6, it_pool7, it_pool8, it_gpu from g5_member where mb_id='".$mbid."'");
		$noosql= sql_fetch("select (noo)as hap from noo2 where mb_id='".$mbid."' and day='".$to_date."'");
		$thirtysql= sql_fetch("select (thirty)as hap from thirty2 where mb_id='".$mbid."' and day='".$to_date."'");
		//$mysql=sql_fetch("select (pv)as hap from g5_shop_order as o where o.mb_id='".$mbid."'");
		$noohap=$noosql['hap'];
		$thirtyhap=$thirtysql['hap'];
		$it_p1 = $pool_sql['it_pool1'];
		$it_p2 = $pool_sql['it_pool2'];
		$it_p3 = $pool_sql['it_pool3'];
		$it_p4 = $pool_sql['it_pool4'];
		$it_p5 = $pool_sql['it_pool5'];
		$it_p6 = $pool_sql['it_pool6'];
		$it_p7 = $pool_sql['it_pool7'];
		$it_p8 = $pool_sql['it_pool8'];
		$it_gpu = $pool_sql['it_gpu'];
		$recom_tot = $recom_sql['count_recom'];
		$note='';
		list($one,$two,$three,$four,$five,$six)=habu_rank($mbid);
		if($mbid=='llj0817B'){
			echo $mbid." 1star :".$one.' 2star : '.$two.' 3star : '.$three.' 4star : '.$four.' 5star : '.$five.' 6star : '.$six.'누적 : '.$noohap.' 직추천 : '.$recom_tot.' pool1 : '.$it_p1;
		}

		//$note=$mbid.'-현재 내직급: '.$mblevel.'  누적매출+자기매출: '.$noohap.' 30일간매출 : '.$thirtyhap.' 하부 1스타:'.$one.' / 2스타:'. $two.' / 3스타:'.$three.' / 4스타:'.$four.' / 5스타:'.$five.' / 6스타:'.$six;

		//모두들 1스타조건(2레그 3000조건)을 충족하고 있는가?


			if($mblevel>=3){
				$twoleg=0;
			}else{
				$twoleg=1;
			}


		if($twoleg==1){
			echo $mbname.'('.$mbid.')은 2레그 X 혹은 현재 직급: '.$mblevel.' 누적매출: '.$noohap.' 30일간매출 : '.$thirtyhap.'<br>';
		}else{ // 무조건 2레그 이상이어야만 작동된다.
				//2스타 조건
				if(  ($noohap>=15000) && ($it_p1>=1) && ($recom_tot>=3) ){
					
					if( ($one>=3) || ($two>=3) || ($three>=3) || ($four>=3) || ($five>=3) || ($six>=3)  ){
						if(  ($mblevel>4) && ($degrade==1) && ($thirtyhap < 15000) ){ // 강등해라
								
							$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 4 강등됨, 누적매출: '.$noohap.' 30일간매출 : '.$thirtyhap;

							$sql= " UPDATE g5_member SET mb_level=4, mb_adult='".$mblevel."', rank_day='$to_date', rank_note='$note' where mb_id='".$mbid."'";
							sql_query($sql);

							$sql= " insert rank SET mb_id='".$mbid."' , old_level='".$mblevel."', rank=4, rank_day='$to_date', rank_note='$note'";
							sql_query($sql);

						}else if($mblevel==4){  // 같은 등급이면...
							// 아무것도 안함
							$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 4 동일함으로 기록하지 않음, 누적매출: '.$noohap.' 30일간매출 : '.$thirtyhap;
						}else if($mblevel<4)  { // 현재 승진이라면...
							$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 4 승진함, 누적매출: '.$noohap.' 30일간매출 : '.$thirtyhap;
							
							$sql= " UPDATE g5_member SET mb_level=4, mb_adult='".$mblevel."', rank_day='$to_date', rank_note='$note' where mb_id='".$mbid."'";;
							sql_query($sql);

							$sql= " insert rank SET mb_id='".$mbid."' , old_level='".$mblevel."', rank=4, rank_day='$to_date', rank_note='$note'";
							sql_query($sql);
							echo $sql;
						}
					}
				}

				//3스타 조건
				if( ($noohap>=75000) && ($it_p2>=1) && ($recom_tot>=4)  ) {
					
					if( ($two>=3) || ($three>=3) || ($four>=3) || ($five>=3) || ($six>=3) ){
						if(  ($mblevel>5) && ($degrade==1) && ($thirtyhap < 70000)  ){ // 강등해라
								$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 5 강등됨, 누적매출: '.$noohap.' 30일간매출 : '.$thirtyhap;
								$sql= " UPDATE g5_member SET mb_level=5, mb_adult='".$mblevel."', rank_day='$to_date', rank_note='$note' where mb_id='".$mbid."'";;
								sql_query($sql);

								$sql= " insert rank SET mb_id='".$mbid."' , old_level='".$mblevel."', rank=5, rank_day='$to_date', rank_note='$note'";
								sql_query($sql);

						}else if($mblevel==5){  // 같은 등급이면...
							// 아무것도 안함
							$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 5 동일함으로 기록하지 않음, 누적매출: '.$noohap.' 30일간매출 : '.$thirtyhap;

						}else if($mblevel<5)  { // 현재 승진이라면...
							$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 5 승진함, 누적매출: '.$noohap.' 30일간매출 : '.$thirtyhap;
							$sql= " UPDATE g5_member SET mb_level=5, mb_adult='".$mblevel."', rank_day='$to_date', rank_note='$note' where mb_id='".$mbid."'";
							sql_query($sql);

							$sql= " insert rank SET mb_id='".$mbid."' , old_level='".$mblevel."', rank=5, rank_day='$to_date', rank_note='$note'";
							sql_query($sql);
						}
					}
				}

				//4스타 조건
				if( ($noohap>=350000) && ($it_p3>=1) && ($recom_tot>=5) ){
					
					if( ($three>=3) || ($four>=3) || ($five>=3) || ($six>=3) ){
						if(  ($mblevel>6) && ($degrade==1)  && ($thirtyhap < 3000000)){ // 강등해라
							$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 6 강등됨, 누적매출: '.$noohap.' 30일간매출 : '.$thirtyhap;
							$sql= " UPDATE g5_member SET mb_level=6, mb_adult='".$mblevel."', rank_day='$to_date', rank_note='$note' where mb_id='".$mbid."'";
							sql_query($sql);

							$sql= " insert rank SET mb_id='".$mbid."' , old_level='".$mblevel."', rank=6, rank_day='$to_date', rank_note='$note'";
							sql_query($sql);

						}else if($mblevel==6){ // 같은 등급이면...
							// 아무것도 안함
							$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 6 동일함으로 기록하지 않음, 누적매출: '.$noohap.' 30일간매출 : '.$thirtyhap;
						}else if($mblevel<6) { // 현재 승진이라면...
							$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 6 승진함, 누적매출: '.$noohap.' 30일간매출 : '.$thirtyhap;
							$sql= " UPDATE g5_member SET mb_level=6, mb_adult='".$mblevel."', rank_day='$to_date', rank_note='$note' where mb_id='".$mbid."'";
							sql_query($sql);

							$sql= " insert rank SET mb_id='".$mbid."' , old_level='".$mblevel."', rank=6, rank_day='$to_date', rank_note='$note'";
							sql_query($sql);
						}
					}
				}	

				//5스타 조건
				if( ($noohap>=3000000) &&  ($it_p4>=1) && ($it_gpu>=1) && ($recom_tot>=6) ) {					
					if( ($four>=3) || ($five>=3) || ($six>=3) && ($thirtyhap < 3000000) ){
						if(  ($mblevel>7) && ($degrade==1)  ){ // 강등해라
							$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 7 강등됨, 누적매출: '.$noohap.' 30일간매출: '.$thirtyhap;
							$sql= " UPDATE g5_member SET mb_level=7, mb_adult='".$mblevel."', rank_day='$to_date', rank_note='$note' where mb_id='".$mbid."'";
							sql_query($sql);

							$sql= " insert rank SET mb_id='".$mbid."' , old_level='".$mblevel."', rank=7, rank_day='$to_date', rank_note='$note'";
							sql_query($sql);

						}else if($mblevel==7){  // 같은 등급이면...
							// 아무것도 안함
							$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 7 동일함으로 기록하지 않음, 누적매출: '.$noohap.' 30일간매출 : '.$thirtyhap;
						}else if($mblevel<7) { // 현재 승진이라면...
							$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 7 승진함, 누적매출: '.$noohap.' 30일간매출: '.$thirtyhap;
							$sql= " UPDATE g5_member SET mb_level=7, mb_adult='".$mblevel."', rank_day='$to_date', rank_note='$note' where mb_id='".$mbid."'";
							sql_query($sql);

							$sql= " insert rank SET mb_id='".$mbid."' , old_level='".$mblevel."', rank=7, rank_day='$to_date', rank_note='$note'";
							sql_query($sql);
						}
					}
				}	

				//6스타 조건
				if( ($noohap>15000000) && ($it_p4>=1) && ($it_gpu>=1) && ($recom_tot>=8) ){
					
					if( ($five>=4) || ($six>=4) ){
						if(  ($mblevel>8) && ($degrade==1) && ($it_p4>=1) && ($it_gpu>=1) ){ // 강등해라
							$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 8 강등됨, 누적매출: '.$noohap.' 30일간매출 : '.$thirtyhap;
							$sql= " UPDATE g5_member SET mb_level=8, mb_adult='".$mblevel."', rank_day='$to_date', rank_note='$note' where mb_id='".$mbid."'";
							sql_query($sql);

							$sql= " insert rank SET mb_id='".$mbid."' , old_level='".$mblevel."', rank=8, rank_day='$to_date', rank_note='$note'";
							sql_query($sql);
						}else if($mblevel==8){  // 같은 등급이면...
							// 아무것도 안함
							$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 8 동일함으로 기록하지 않음, 누적매출: '.$noohap.' 30일간매출 : '.$thirtyhap;

						}else if($mblevel<8) { // 현재 승진이라면...
							$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 8 승진함, 누적매출: '.$noohap.' 30일간매출 : '.$thirtyhap;
							$sql= " UPDATE g5_member SET mb_level=8, mb_adult='".$mblevel."', rank_day='$to_date', rank_note='$note' where mb_id='".$mbid."'";
							sql_query($sql);

							$sql= " insert rank SET mb_id='".$mbid."' , old_level='".$mblevel."', rank=8, rank_day='$to_date', rank_note='$note'";
							sql_query($sql);
						}
					}
				}	
				//7스타 조건
				if( ($noohap>65000000) && ($it_p4>=1) && ($it_gpu>=1) && ($recom_tot>=11) ){
					
					if( ($five>=4) || ($six>=4) ){
						if(  ($mblevel>9) && ($degrade==1) && ($it_p4>=1) && ($it_gpu>=1) ){ // 강등해라
							$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 9 강등됨, 누적매출: '.$noohap.' 30일간매출 : '.$thirtyhap;
							$sql= " UPDATE g5_member SET mb_level=9, mb_adult='".$mblevel."', rank_day='$to_date', rank_note='$note' where mb_id='".$mbid."'";
							sql_query($sql);

							$sql= " insert rank SET mb_id='".$mbid."' , old_level='".$mblevel."', rank=9, rank_day='$to_date', rank_note='$note'";
							sql_query($sql);
						}else if($mblevel==9){  // 같은 등급이면...
							// 아무것도 안함
							$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 9 동일함으로 기록하지 않음, 누적매출: '.$noohap.' 30일간매출 : '.$thirtyhap;

						}else if($mblevel<9) { // 현재 승진이라면...
							$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 9 승진함, 누적매출: '.$noohap.' 30일간매출 : '.$thirtyhap;
							$sql= " UPDATE g5_member SET mb_level=9, mb_adult='".$mblevel."', rank_day='$to_date', rank_note='$note' where mb_id='".$mbid."'";
							sql_query($sql);

							$sql= " insert rank SET mb_id='".$mbid."' , old_level='".$mblevel."', rank=8, rank_day='$to_date', rank_note='$note'";
							sql_query($sql);
						}
					}
				}	
		}

	} //for

?>
