<?php

$sub_menu = "600600";
include_once('/home/sdevftv/html/common.php'); //실서버용 경로
//include_once(G5_ADMIN_PATH.'/admin.lib.php');

//auth_check($auth[$sub_menu], 'r');

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

		habu_sales_calc($gubun,'Coolrunning',0); 
	}

	function habu_sales_calc($gubun, $recom, $deep){

		global $fr_date, $to_date;
		$deep++; // 대수

		$start_day = '2017-07-01';

		if ($to_date){
			$day = $to_date;
		}else{
			$day = '2018-07-06'; //  = date('Y-m-d');
		}
		$yy= strtotime($day);
		$min30 = date("Y-m-d", strtotime("-30 day", $yy));
		$res= sql_query("select * from g5_member where mb_".$gubun."recommend='".$recom."' ");
		for ($j=0; $rrr=sql_fetch_array($res); $j++) { 
			$recom=$rrr['mb_id'];  
			//echo $recom.'<br>'; 
			$noo_search = " and date_format(o.od_receipt_time,'%Y-%m-%d')>='$start_day' and date_format(o.od_receipt_time,'%Y-%m-%d')<='$day'";
			$sql_str = "select sum(pv)as hap from g5_shop_order as o where mb_id='".$recom."' $noo_search";
			$sql= sql_fetch($sql_str);
			$noo+=$sql['hap'];
			echo $sql_str."<br>";
			$mon_search = " and date_format(o.od_receipt_time,'%Y-%m-%d')>='$min30' and date_format(o.od_receipt_time,'%Y-%m-%d')<='$day'";
			$sql_str = "select sum(pv)as hap from g5_shop_order as o where mb_id='".$recom."' $mon_search";
			$sql= sql_fetch($sql_str);
			$mon+=$sql['hap'];
			echo $sql_str."<br>";				
			$day_search = " and date_format(o.od_receipt_time,'%Y-%m-%d')='$day'";
			$sql_str = "select sum(pv)as hap from g5_shop_order as o where mb_id='".$recom."' $day_search";
			$sql= sql_fetch($sql_str);
			$today+=$sql['hap'];
			echo $sql_str."<br>";
				/*
				if($sql['hap']>0){echo "<br>".$sql['hap']."---select sum(pv)as hap from g5_shop_order as o where mb_id='".$recom."' $day_search".'<br>';
				}
				*/
				$mysql=sql_fetch("select (pv)as hap from g5_shop_order as o where o.mb_id='".$mbid."'");
				$mysales=$mysql['hap'];
				echo $mysql."<br>"; 
				list($noo_r, $mon_r, $today_r)=habu_sales_calc($gubun, $recom, $deep);	 
					$noo_r+=$mysales;
					$mon_r+=$mysales;
					$today_r+=$mysales;
					$noo+=$noo_r;
					$mon+=$mon_r;  
					$today+=$today_r; 
				echo '해당 추천인 : '.$recom.' noo '.$noo.' t '.$today.' tr '.$today_r."<br>";
				if( ($noo>0) && ($noo_r>0)) {
					if($j==0){
						$rec=$noo_r;
					}else{
						$rec=$noo_r;	
					}
					$inbnoo = "insert ".$gubun."noo2 SET noo=".$rec.", mb_id='".$recom."',  day = '".$to_date."'";
					sql_query($inbnoo);	
					echo $inbnoo."'<br>";
				}
				
				if(($mon>0) && ($mon_r>0) ) {
					if($j==0){
						$rec=$mon_r;
					}else{
						$rec=$mon_r;	
					}
					$inthirty = "insert ".$gubun."thirty2 SET thirty=".$rec.", mb_id='".$recom."', day = '".$to_date."'";
					sql_query($inthirty);
					echo $inthirty."'<br>";
				}
				
			if(($today>0)&& ($today_r>0)) {
				if($j==0){
					$rec=$today_r;
				}else{
					$rec=$today_r;
				}

				$intoday = "insert ".$gubun."today2 SET todayy=".$rec.", mb_id='".$recom."', day = '".$to_date."'";
				sql_query($intoday);
				echo $intoday."'<br>";
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
			
		if($cnt >= 2 && $cnt2==2){
			$hap = my_bchild_sub($mb_id);
		}
		else{
			return 0;
		}
		return $hap;
	} 
	function today_sales($mbid,$day){
		$day_search = " and date_format(o.od_receipt_time,'%Y-%m-%d')='$day'";
		$sql= sql_fetch("select sum(pv)as hap from g5_shop_order as o where mb_id='".$mbid."' $day_search");
		if($sql['hap']=='')
		{
			$hap=0;
		}else{
			$hap=$sql['hap'];
		}
		return $hap;
	}

	function btoday_select($mbid){
		global $fr_date, $to_date;
		$res= sql_fetch("select todayy from btoday2 where mb_id='".$mbid."' ANd day='".$to_date."'");
		if($res['todayy']=='')
		{
			$hap=0;
		}else{
			$hap=$res['todayy'];
		}

		return $hap;
	} 

	function minus_iwol($mbid, $day){
		$res2= sql_fetch("select sum(pv)as hap from iwol where mb_id='".$mbid."'");
		$hap=$res2['hap'];
		if($hap>0){
			$temp_sql1 = " insert iwol set iwolday='".$day."'";
			$temp_sql1 .= " ,mb_id		= '".$mbid."'";
			$temp_sql1 .= " ,pv		= '".-($hap)."'";
			$temp_sql1 .= " ,kind		= 1";
			$temp_sql1 .= " ,note		= '이월매출사용'";
			sql_query($temp_sql1);
			echo $temp_sql1.'***1***<br>';
		}
	} 

	function plus_iwol($mbid, $day){
			
			$hap=(btoday_select($mbid)+today_sales($mbid,$day));  //자기매출과 하부매출을 합하여 
//			$hap=(btoday_select($mbid));  //자기매출과 하부매출을 합하여 
			echo $hap.'&&&&&&&&<br>';
			if($hap>0){
				$temp_sql1 = " insert iwol set iwolday='".$day."'";
				$temp_sql1 .= " ,mb_id		= '".$mbid."'";
				$temp_sql1 .= " ,pv			= '".($hap)."'";
				$temp_sql1 .= " ,kind		= 100";
				$temp_sql1 .= " ,note		= '금일 발생한 매출이월'";
				sql_query($temp_sql1);
				echo $temp_sql1.'***100***<br>';
			}
	} 

	function habu_iwol($mbid, $day, $cycle){
		$hap1=(btoday_select($mbid,$day)+today_sales($mbid,$day));  //자기매출과 하부매출을 합하여 
		$res2= sql_fetch("select sum(pv)as hap from iwol where mb_id='".$mbid."'");
		$hap2=$res2['hap'];
		echo '#########'.$hap1.'+'.$hap2.'<'.$cycle.' && '.$hap1.'>0';
		return array($hap1,$hap1+$hap2);    	
		//return ($hap2);    	
	} 

	function my_bchild($mb_id,$day,$cycle){
		//만약 오늘 매출이 없으면 마이너스 이월을 하지 않는다.
		//
		$id1='';
		$id2='';
		$hap1=0;
		$hap2=0;
		$today_hap1=0;
		$today_hap2=0;
		$res= sql_query("select mb_id from g5_member where mb_brecommend='".$mb_id."' order by mb_no"); 
		for ($j=0; $rrr=sql_fetch_array($res); $j++) { 
			if($j==0){
				$id1=$rrr['mb_id']; 
				list($today_hap1, $hap1)=habu_iwol($id1,$day,$cycle); 
				if($today_hap1==''){
					$today_hap1=0;
				}
			}
			if($j==1){
				$id2=$rrr['mb_id']; 
				list($today_hap2, $hap2)=habu_iwol($id2,$day,$cycle); 
				if($today_hap2==''){
					$today_hap2=0;
				}
			}
			
		} 
		if($today_hap1==0 && $today_hap2==0){ $hap1=0; $hap2=0;}
		if($hap1>0) { // cycle보다 같거나 크면 -이월한다
			minus_iwol($id1,$day); 		
		}
		if($hap2>0){
			minus_iwol($id2,$day); 
		}
		return array($id1, $hap1, $id2, $hap2);
	} 

	//**** 이월이 있다면 함께 DB에 저장 한다.
	function iwol_process($day,$mb_recommend, $mbid, $mb_name, $kind, $pv, $note){

		
		$iwol= sql_fetch("select count(*) as cnt from iwol where mb_id='".$mbid."' and kind<>1 and date_format(iwolday,'%Y-%m-%d')='$day'");
		echo '### '.$pv.'---'.$iwol['cnt'].'<br>';
		if( ($pv>0) && ($iwol['cnt']==0) ){   
			
			$temp_sql1 = " insert iwol set iwolday='".$day."'";
			$temp_sql1 .= " ,mb_id		= '".$mbid."'";
			//$temp_sql1 .= " ,mb_name		= '".$mbname."'";
			$temp_sql1 .= " ,kind		= '".$kind."'";
			$temp_sql1 .= " ,pv		= '".$pv."'";
			$temp_sql1 .= " ,note		= '".$note."'";
			$temp_sql1 .= " ,mb_brecommend		= '".$mb_recommend."'";
			sql_query($temp_sql1);
			echo $temp_sql1.'******2******<br>';
		}

	}

	function iwol_process_nocalc($day,$mb_recommend, $mbid, $mb_name, $kind, $pv, $note){
		if( ($pv>0) ){   				
			$temp_sql1 = " insert iwol set iwolday='".$day."'";
			$temp_sql1 .= " ,mb_id		= '".$mbid."'";
			//$temp_sql1 .= " ,mb_name		= '".$mbname."'";
			$temp_sql1 .= " ,kind		= '".$kind."'";
			$temp_sql1 .= " ,pv		= '".$pv."'";
			$temp_sql1 .= " ,note		= '".$note."'";
			$temp_sql1 .= " ,mb_brecommend		= '".$mb_recommend."'";
			sql_query($temp_sql1);
			echo $temp_sql1.'******2******<br>';
		}

	}

	// benefit_level은 per 금액을 넣어서 바이너리매칭보너스에서 사용한다
	//per - 퍼센티지로 한다.
	function save_benefit_b($day,$mbid, $mbname, $recom, $allowance_name, $sales_day, $habu_day_sales, $benefit, $benefit_level, $rec_adm, $rec,$exchange_rate,$mblevel){
		$iwol= sql_fetch("select count(*) as cnt from iwol where mb_brecommend='".$mbid."' and date_format(iwolday,'%Y-%m-%d')='$day'");
		if($iwol['cnt']==0){ 

			//수당을 비트로 환산 한다.
			$benefit_bit = round($benefit/$exchange_rate,8);
			//회원 잔고에 더해 준다.
			$balance_up = "update g5_member set mb_balance = round(mb_balance+ ".$benefit_bit.",8) where mb_id = '".$mbid."';";
			sql_query($balance_up);

			$temp_sql1 = " insert soodang_pay set day='".$day."'";
			$temp_sql1 .= " ,mb_id		= '".$mbid."'";
			$temp_sql1 .= " ,mb_name		= '".$mbname."'";
			$temp_sql1 .=" , mb_level = ".$mblevel;
			$temp_sql1 .= " ,mb_recommend		= '".$recom."'";
			$temp_sql1 .= " ,allowance_name		= '".$allowance_name."'";
			//$temp_sql1 .= " ,day_sales		=  '".$sales_day."'";
			//$temp_sql1 .= " ,habu_day_sales 		=  '".$habu_day_sales."'";
			$temp_sql1 .= " ,benefit			=  ".$benefit_bit;
			$temp_sql1 .= " ,benefit_usd		=  '".($benefit)."'";
			$temp_sql1 .= " ,exchange_rate      =  ".$exchange_rate;
			$temp_sql1 .= " ,benefit_level		=  '".($benefit_level)."'";
			$temp_sql1 .= " ,rec		= '".$rec."'";
			$temp_sql1 .= " ,rec_adm	= '".$rec_adm."'";
			sql_query($temp_sql1);
			echo $temp_sql1.'********3********<br>';
		}
	}
	function save_benefit($day,$mbid, $mbname, $recom, $allowance_name, $sales_day, $habu_day_sales, $benefit, $rec_adm, $rec, $exchange_rate, $mblevel ){
		//수당을 비트로 환산 한다.
		$benefit_bit = round($benefit/$exchange_rate,8);
		//회원 잔고에 더해 준다.
		$balance_up = "update g5_member set mb_balance = round(mb_balance+ ".$benefit_bit.",8) where mb_id = '".$mbid."';";
		sql_query($balance_up);

		$temp_sql1 = " insert soodang_pay set day='".$day."'";
		$temp_sql1 .= " ,mb_id		= '".$mbid."'";
		$temp_sql1 .= " ,mb_level = ".$mblevel;
		$temp_sql1 .= " ,mb_name		= '".$mbname."'";
		$temp_sql1 .= " ,mb_recommend		= '".$recom."'";
		$temp_sql1 .= " ,allowance_name		= '".$allowance_name."'";
		//$temp_sql1 .= " ,day_sales		=  '".$sales_day."'";
		//$temp_sql1 .= " ,habu_day_sales 		=  '".$habu_day_sales."'";
		$temp_sql1 .= " ,benefit			=  ".$benefit_bit;
		$temp_sql1 .= " ,benefit_usd		=  '".($benefit)."'";
		$temp_sql1 .= " ,exchange_rate      =  ".$exchange_rate;
		$temp_sql1 .= " ,rec		= '".$rec."'";
		$temp_sql1 .= " ,rec_adm	= '".$rec_adm."'";
		if($benefit_bit!=0 || $benefit!=0){
			sql_query($temp_sql1);
		}

		//echo $temp_sql1.'<br>';

	}

	function self_sales_rank($recom){

		//$sql_search = " and date_format(o.od_time,'%Y-%m-%d')>='$fr_date' and date_format(o.od_time,'%Y-%m-%d')<='$to_date' group by md_id";

		$res= sql_fetch("select sum(pv)as hap from g5_shop_order as o where mb_id='".$recom."'");
		
		return $res['hap'];    
		
	} 

	function my_bchild_sub_rank($mb_id){
		$hap2=0;
		//자기매출제외
		//$hap=self_sales($mb_id); //먼저 자기매출
		$res= sql_query("select mb_id from g5_member where mb_recommend='".$mb_id."' order by mb_no"); 
		for ($j=0; $rrr=sql_fetch_array($res); $j++) { 
			$hap2+=self_sales($rrr['mb_id']); // 하부매출을 구한다
			$hap2 = $hap2+ my_bchild_sub_rank($rrr['mb_id']);		
		} 	
		return $hap2;
	} 
   
	function my_bchild_hap_rank($mb_id){
		$cnt = 0;
		$hap = 0;
		$bcnt =0;

		$res= sql_query("select count(mb_id) as cnt from g5_member where mb_recommend='".$mb_id."' order by mb_no"); 
		$ret = sql_fetch_array($res);
		$cnt = $ret['cnt']; 

		$res2= sql_query("select count(mb_id) as bcnt from g5_member where mb_brecommend='".$mb_id."' order by mb_no"); 
		$ret2 = sql_fetch_array($res2);
		$cnt2 = $ret2['bcnt']; // 하부매출을 구한다
			
		if($cnt >= 2 && $cnt2=2){
			$hap = my_bchild_sub_rank($mb_id);
		}
		else{
			return 0;
		}
		return $hap;
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
	
	$sql_price = "select btc_cost from coin_cost";
	$result = sql_query($sql_price);
	$ret = sql_fetch_array($result);
	$exchange_rate =  $ret['btc_cost'];

//	$to_date =	date("Y-m-d",time() - 3600*24);
//	$fr_date = 	date("Y-m-d",time() - 3600*24);

	$to_date =	'2019-03-29';
	$fr_date = 	'2019-03-29';

	$check_exec = "SELECT DAY FROM soodang_pay WHERE (allowance_name LIKE  '%direct%' OR allowance_name LIKE  '%binary%') and day='".$to_date."'";
	$rst = sql_fetch($check_exec);
	if($rst){
		echo "You execute this already";
		die;
	}
	$price=pv;

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
	make_habu('');	// 추천으로 구함
	make_habu('b'); //누적,30일, 하루매출을 바이너리로 구함

	if($price=='pv') {
		// PV가로
		$price_cond=", SUM(pv) AS hap";
	} 
	else if($price=='bv') {
		//BV가로 계산
		$price_cond=", SUM(bv) AS hap";
	}
	else{
		// 판매가로 
		$price_cond=", SUM(od_receipt_price + od_receipt_cash) AS hap";
	}
	$sql_common = " FROM g5_shop_order AS o, g5_member AS m ";
	$sql_search=" WHERE o.mb_id=m.mb_id AND o.mb_id=m.mb_id";
	if($mb_id) {
		$sql_member=" AND m.mb_id='".$mb_id."'"; 
	}
	else{ 
		$sql_member=""; 
	}

	//$searchdate=" AND DATE_FORMAT(o.od_receipt_time,'%Y-%m-%d')>='".$fr_date."' AND DATE_FORMAT(o.od_receipt_time,'%Y-%m-%d')<='".$to_date."' GROUP BY mb_id, DATE_FORMAT(o.od_receipt_time,'%Y-%m')";

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
	//make_class();
	$history_cnt=0;
	$rec='';
	for ($i=0; $row=sql_fetch_array($result); $i++) {   
		$today_sales2=0;
		$today=$row['od_receipt_time'];
		$comp=$row['mb_id'];
		$confirm_exit1=0;
		$first=0; 
		$firstname='';
		$firstid='';
		$today_sales=$row['hap'];
		while(  ($comp!='admin')  || ($comp!='coolrunning')  ){   
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
			if(($mb_name=='본사')  || ($mbid=='')  ) {
				echo "admin , 본사 혹은 ''을 만나 정지됨"; 
				break;
			}
			$sql_sale = " update g5_member set sales_day='".$today."',";
			if($history_cnt==0){ 
				//$sql_sale .= " mb_my_sales=mb_my_sales	+ ".$today_sales;
			}else{
				//$sql_sale .= " habu_day_sales=habu_day_sales+".$today_sales;
			}
			$sql_sale .= " where mb_id='".$comp."'";
			sql_query($sql_sale);
			//************************* 1스타 직급 승급 ************************************			
			if($mblevel<3){  //현재 내 직급이 1star 이하이면
				if(my_bchild_hap($mbid)>=3000){
					$sql3 = " update g5_member set mb_level=3";
					$sql3 .= ", rank_day='".$to_date."'";
					$sql3 .= " where mb_id='".$comp."'";
					sql_query($sql3);
					$sql33 = " insert rank set";
					$sql33 .= " rank_day='".$to_date."'";
					$sql33 .= " , rank=3";
					$sql33 .= " , old_level=".$mblevel;
					$sql33 .= " , rank_note='1스타 승급함, benefit_immediate에서 계산됨'";
					$sql33 .= " where mb_id='".$comp."'";
					sql_query($sql33);
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
			if($cond[$i]['per']!=''){ // 수당 퍼센트가 있는 경우 실행
			if(($cond[$i]['mb_level_cond1']=='==')){
				if($mblevel==$cond[$i]['mb_level_in1']){
					$temp_cond_level1=0;
				}else{
					$temp_cond_level1=1;
				}
			}
			else if(($cond[$i]['mb_level_cond1']=='>=') && ($cond[$i]['mb_level_cond2']=='')){
				if($mblevel>=$cond[$i]['mb_level_in1']){
					$temp_cond_level1=0;
				}else{
					$temp_cond_level1=1;
				}
			}else if(  ($cond[$i]['mb_level_cond1']=='') && ($cond[$i]['mb_level_cond2']=='<=')  ){
				if($mblevel<=$cond[$i]['mb_level_in2']){
					$temp_cond_level1=0;
				}
				else{
					$temp_cond_level1=1;
				}
			}else if(  ($cond[$i]['mb_level_cond1']=='>=') && ($cond[$i]['mb_level_cond2']=='<=')  ){
				if(($mblevel>=$cond[$i]['mb_level_in1']) && ($mblevel<=$cond[$i]['mb_level_in2'])  ){
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
// echo $comp.'-대수: '.$temp_cond_history.'-본인직급: '.$temp_cond_level1.' -하부직급: '.$temp_cond_mat.'-매출크기: '.$bf_limit1.'<br>';
// ***** 걸린 조건을 모두 충족한다면 계산하라 
		if(($temp_cond_history==0) && ($temp_cond_bigsmall1==0) && ($temp_cond_bigsmall2==0) && ($temp_cond_level1==0) && ($temp_cond_leve2==0) && ($temp_cond_mat==0) && ($limit_reset==0)){
			//if($save_benefit==1){ // 저장하라면 
			$benefit=($today_sales)*($cond[$i]['per']/100);
			$rec_adm=$cond[$i]['allowance_name'].': 조건 '.$firstid.'('.$first.')<='.$mbid.'('.$pool_level.')   '.$firstname.'('.$firstid.') 으로부터 '.$history_cnt.'대 -'.$cond[$i]['base_source'].':'.$today_sales.'*'.($cond[$i]['per']/100).'='.$benefit.' / <br/>';
			$hist = $history_cnt-1;	
			$rec = 'From member '.$firstid.' ( level '.$hist.')';
			//echo $rec;
			   //**** 수당이 있다면 함께 DB에 저장 한다.
			$benefit_bit = round($benefit/$exchange_rate, 8);
			$balance_up = "update g5_member set mb_balance = round(mb_balance+ ".$benefit_bit.",8) where mb_id = '".$mbid."';";
			sql_query($balance_up);

			//**** 수당이 있다면 함께 DB에 저장 한다.
			$temp_sql1 = " insert soodang_pay set day='".$to_date."'";
			$temp_sql1 .= " ,mb_id		= '".$mbid."'";
			$temp_sql1 .= " ,mb_name		= '".$mbname."'";
			$temp_sql1 .= " ,mb_recommend		= '".$recom."'";
			$temp_sql1 .= " ,allowance_name		= '".$cond[$i]['allowance_name']."'";
			$temp_sql1 .= " ,andor		= '".$cond[$i]['andor']."'";
			$temp_sql1 .= " ,mb_level =".$mblevel;
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
			//echo $temp_sql1;
			if($benefit_bit!=0||$benefit!=0)
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
	$cond_binary = array(array('price_cond'=>'','source'=>'','source_cond1'=>'','source_cond2'=>'','source_in1'=>'','source_in2'=>'','mb_level_cond1'=>'','mb_level_cond2'=>'','mb_level_in1'=>'','mb_level_in2'=>'','partner_cnt'=>'','partner_cont'=>'','history_cnt'=>'','history_cond1'=>'','history_cond2'=>'','history_in1'=>'','history_in2'=>'','base_source'=>'','per'=>'','allowance_name'=>'','mat'=>'','level1'=>'','level2'=>'','bigsmall1'=>'','bigsmall2'=>'','history'=>'','benefit'=>'','benefit_limit1'=>'','source11'=>'','source_cond11'=>'','source_cond12'=>'','source_in11'=>'','source_in12'=>'','sales_reset'=>'','max_reset1'=>'','max_reset2'=>'','mb_level_in11'=>'','mb_level_cond11'=>'','mb_level_cond12'=>'','bf_limit1'=>''));

	$benefitSql = "SELECT * from pinna_soodang_set where immediate=2 order by partner_cnt desc, no";
	$rrr = sql_query($benefitSql);

	for ($i=0; $row=sql_fetch_array($rrr); $i++) {   

		$cond_binary[$i]['price_cond']=$row['price_cond'];
		$cond_binary[$i]['source']=$row['source'];
		$cond_binary[$i]['source_cond1']=$row['source_cond1'];
		$cond_binary[$i]['source_cond2']=$row['source_cond2'];
		$cond_binary[$i]['source_in1']=$row['source_in1'];
		$cond_binary[$i]['source_in2']=$row['source_in2'];
		$cond_binary[$i]['mb_level_cond1']=$row['mb_level_cond1'];
		$cond_binary[$i]['mb_level_cond2']=$row['mb_level_cond2'];
		$cond_binary[$i]['mb_level_in1']=$row['mb_level_in1'];
		$cond_binary[$i]['mb_level_in2']=$row['mb_level_in2'];
		$cond_binary[$i]['partner_cnt']=$row['partner_cnt'];
		$cond_binary[$i]['partner_cont']=$row['partner_cont'];
		$cond_binary[$i]['history_cnt']=$row['history_cnt'];
		$cond_binary[$i]['history_cond1']=$row['history_cond1'];
		$cond_binary[$i]['history_cond2']=$row['history_cond2'];
		$cond_binary[$i]['history_in1']=$row['history_in1'];
		$cond_binary[$i]['history_in2']=$row['history_in2'];
		$cond_binary[$i]['base_source']=$row['base_source'];
		$cond_binary[$i]['immediate']=$row['immediate'];
		$cond_binary[$i]['per']=$row['per'];
		$cond_binary[$i]['andor']=$row['andor'];
		$cond_binary[$i]['allowance_name']=$row['allowance_name'];

		$cond_binary[$i]['benefit_limit1']=$row['benefit_limit1'];
		$cond_binary[$i]['source11']=$row['source11'];
		$cond_binary[$i]['source_cond11']=$row['source_cond11'];
		$cond_binary[$i]['source_cond12']=$row['source_cond12'];
		$cond_binary[$i]['source_in11']=$row['source_in11'];
		$cond_binary[$i]['source_in12']=$row['source_in12'];
		
		$cond_binary[$i]['sales_reset']=$row['sales_reset'];
		$cond_binary[$i]['iwolyn']=$row['iwolyn'];
		$cond_binary[$i]['max_reset1']=$row['max_reset1'];
		$cond_binary[$i]['max_reset2']=$row['max_reset2'];
		$cond_binary[$i]['cycle']=$row['cycle'];

		$cond_binary[$i]['mb_level_in11']=$row['mb_level_in11'];
		$cond_binary[$i]['mb_level_cond11']=$row['mb_level_cond11'];
		$cond_binary[$i]['mb_level_cond12']=$row['mb_level_cond12'];

		$cond_binary[$i]['recom_kind']=$row['recom_kind'];


		if( ($row['sales_reset']>0) && ($row['cycle']>0)  && ($row['max_reset1']<>'')  && ($row['max_reset2']<>'') ){  $cond_binary[$i]['limit_reset']=1;  }  //극점 사용여부


		if( ($row['partner_cnt']>0) && ($row['partner_cont']>0) ){$cond_binary[$i]['mat']=1;}  // 메트릭스
		
		if(  ($row['source_in1']!=0) || ($row['source_in2']!=0) ){ $cond_binary[$i]['bigsmall1']=1;  }// 대소실적조건1

		if(  ($row['source_in11']!=0) || ($row['source_in12']!=0) ){ $cond_binary[$i]['bigsmall2']=1;  }// 대소실적조건12

		if(  ($row['mb_level_in1']!=0) || ($row['mb_level_in2']!=0) ){ $cond_binary[$i]['level1']=1; } //본인직급 

		if(  ($row['mb_level_cond11']!=0) || ($row['mb_level_cond12']!=0) ){ $cond_binary[$i]['level2']=1; } //하부직급

		if(  ($row['history_cond1']!='') || ($row['history_cond2']!='') ){ $cond_binary[$i]['history']=1;}  //대수 level

		if( $row['benefit_limit1']>0  ){$cond_binary[$i]['bf_limit1']=1;}  //매출자보다 수당받을자의 매출이 같거나 큰가?



	}




	$start_day = '2017-07-01';

	if ($to_date){
		$day       = $to_date;
	}else{
		$day    = date('Y-m-d');
	}


	if($iwol_yn_chk==1){
		$sql= " delete from iwol where date_format(iwolday,'%Y-%m-%d')='$to_date'"; 
		echo "이월DB지우고 다시생성<br><br>";
		sql_query($sql);
		
	}


	$history_cnt=0;
	$no_benefit=0;
	$rec='';

	// 직급이 최소 1스타(3) 이상
	$sql = "SELECT mb_id, mb_name, mb_hp, mb_level, mb_recommend, mb_brecommend FROM g5_member WHERE mb_level>=3";
	$result = sql_query($sql);

	echo $sql;

	for($i=0; $recommend=sql_fetch_array($result); $i++) {   
		$binary_trig=0;
		$today_sales=0;
		$today_sales2=0;
		$leg_success=0; // 메트릭스 성공찾기 클리어
		$mbid=$recommend['mb_id'];
		$mbname=$recommend['mb_name'];
		echo $mbid.'<br>';
		$mblevel=$recommend['mb_level'];
		if(   ($mb_name=='본사')  || ($mbid=='')  ) break;
		$iwol_pass =0; 
		$benefit_pass =0; 
		for ($i=0; $i<count($cond_binary); $i++) {
			if($cond_binary[$i]['level1']=='1'){ $temp_cond_level1=1; } else {$temp_cond_level1=0;} //본인직급
			if($cond_binary[$i]['limit_reset']==1){ 	$limit_reset=1; } else {$limit_reset=0;} //바이너리보너스조건
			if($cond_binary[$i]['recom_kind']=='mb_recommend'){
				$recom=$recommend['mb_recommend'];
			}else{
				$recom=$recommend['mb_brecommend'];
			}
			$temp_sql1 = '';
			if($cond_binary[$i]['per']!=''){ // 수당 퍼센트가 비어있으면 패스~
			//******   본인직급 조건이 있다면  성공여부 기록 
			if(($cond_binary[$i]['mb_level_cond1']=='==')){
				if($mblevel==$cond_binary[$i]['mb_level_in1']){
					$temp_cond_level1=0;
				}else{
					$temp_cond_level1=1;
				}
			}
			else if(  ($cond_binary[$i]['mb_level_cond1']=='>=') && ($cond_binary[$i]['mb_level_cond2']==''))
			{
				if($mblevel>=$cond_binary[$i]['mb_level_in1']){
					$temp_cond_level1=0;
				}else{
					$temp_cond_level1=1;
				}
			}else if(  ($cond_binary[$i]['mb_level_cond1']=='') && ($cond_binary[$i]['mb_level_cond2']=='<=')  ){
				if($mblevel<=$cond_binary[$i]['mb_level_in2']){
					$temp_cond_level1=0;
				}else{
					$temp_cond_level1=1;
				}
			}else if(  ($cond_binary[$i]['mb_level_cond1']=='>=') && ($cond_binary[$i]['mb_level_cond2']=='<=')  ){
				if(  ($mblevel>=$cond_binary[$i]['mb_level_in1']) && ($mblevel<=$cond_binary[$i]['mb_level_in2'])  ){
					$temp_cond_level1=0;
				}else{
					$temp_cond_level1=1;
				}
			}

			//******  바이너리보너스 계산
				if(($limit_reset==1) && ($temp_cond_level1==0) ){
					$note='';
					$id1='';
					$id2='';
					$big=0;
					$small=0;
					$hap1=0;
					$hap2=0;
					list($id1,$hap1,$id2,$hap2) = my_bchild($mbid,$to_date,$cond_binary[$i]['cycle']);
					echo $mbid.': '.$id1.'---'.$hap1.'---'.$id2.'---'.$hap2.'<br>';
					if(($hap1>0) || ($hap2>0)){
						if( $hap1<=$hap2 )
						{ //$hap1이 소실적이라면
							//if($hap1>=0 ){ //소실적이 극점?
							if($hap1*0.1>=($cond_binary[$i]['sales_reset']) ){ //소실적이 극점?
								$today_sales=$cond_binary[$i]['sales_reset'];
							//	$binary_trig=($cond_binary[$i]['sales_reset']/$cond_binary[$i]['cycle']);
								$firstname=$mbname;
								$firstid=$mbid;
		
								if( $cond_binary[$i]['max_reset1'] =='대.소실적 모두이월'){
									$note_adm='극점초과(대.소실적 모두이월) (1) 소실적:'.$hap1.'('.$id1.') / 대실적:'.$hap2.'('.$id2.')';
									echo $note='Binary Cycle Bonus for '.$maxcycle.' cycles as a '.$deslv.' member';
									$no_benefit=1;$binary_firstname=$mbname;$binary_firstid=$mbid;
									if($today_sales>0) 
										save_benefit_b($to_date, $mbid, $mbname, $recom, $cond_binary[$i]['allowance_name'], '', 0, $today_sales, $hap1, $cond_binary[$i]['allowance_name'].' '.$note_adm, $note, $exchange_rate, $mblevel );
									iwol_process($to_date, $mbid, $id1, $mbname, 1, $hap1-$cond_binary[$i]['sales_reset'] , $note_adm);
									iwol_process($to_date, $mbid, $id2, $mbname, 1,$hap2-$cond_binary[$i]['sales_reset'] , $note_adm);
								} 
								else if($cond_binary[$i]['max_reset1']=='대실적만 이월'){
									$note_adm='소실적 극점 발생 (대실적만 이월) (2) 소실적:'.$hap1.	'('.$id1.') / 대실적:'.$hap2.	'('.$id2.') 이월금:'.($hap2-($cond_binary[$i]['sales_reset']*10));
									$small_note='소실적 소멸 발생. Point : '.$hap1;
									echo $note='Binary Bonus for '.$deslv.' member';
									$no_benefit=1;$binary_firstname=$mbname;$binary_firstid=$mbid;
									if($today_sales>0) 
										save_benefit_b($to_date, $mbid, $mbname, $recom, $cond_binary[$i]['allowance_name'], '', 0, $today_sales, $hap1, $cond_binary[$i]['allowance_name'].' '.$note_adm, $note, $exchange_rate, $mblevel);
									iwol_process($to_date, $mbid, $id2, $mbname, 22, $hap2-($cond_binary[$i]['sales_reset']*10), $note_adm); 
									iwol_process($to_date, $mbid, $id1, $mbname, 22, 0 , $small_note);//제로이월
									//iwol_process($to_date, $mbid, $id2, $mbname, 2, $hap2, $note_adm); 

								} 
								else if($cond_binary[$i]['max_reset1']=='소실적만 이월'){
									$note_adm='극점초과(소실적만 이월) (3) 소실적:'.$hap1.	'('.$id1.') / 대실적:'.$hap2.	'('.$id2.') 이월금:'.($hap1-$cond_binary[$i]['sales_reset']);
									echo  $note='Binary Cycle Bonus for '.$maxcycle.' cycles as a '.$deslv.' member';
									$no_benefit=1;$binary_firstname=$mbname;$binary_firstid=$mbid;
									if($today_sales>0) 
										save_benefit_b($to_date,$mbid, $mbname, $recom, $cond_binary[$i]['allowance_name'], '', 0, $today_sales, $hap1, $cond_binary[$i]['allowance_name'].' '.$note_adm, $note, $exchange_rate, $mblevel);
									iwol_process($to_date, $mbid,  $id1, $mbname, 3, $hap2-$hap1 , $note_adm); 
								}
							} 
							else{//극점이 아니면
								$today_sales=$hap1*$cond_binary[$i]['per']/100;
							//	$binary_trig=($cond_binary[$i]['sales_reset']/$cond_binary[$i]['cycle']);
								$firstname=$mbname;
								$firstid=$mbid;
								if( $cond_binary[$i]['max_reset1'] =='대.소실적 모두이월'){
									$note_adm='극점초과(대.소실적 모두이월) (1) 소실적:'.$hap1.'('.$id1.') / 대실적:'.$hap2.'('.$id2.')';
									echo $note='Binary Cycle Bonus for '.$maxcycle.' cycles as a '.$deslv.' member';
									$no_benefit=1;$binary_firstname=$mbname;$binary_firstid=$mbid;
									if($today_sales>0) 
										save_benefit_b($to_date, $mbid, $mbname, $recom, $cond_binary[$i]['allowance_name'], '', 0, $today_sales, $hap1, $cond_binary[$i]['allowance_name'].' '.$note_adm, $note, $exchange_rate, $mblevel);
									iwol_process($to_date, $mbid, $id1, $mbname, 1, $hap1-$cond_binary[$i]['sales_reset'] , $note_adm);
									iwol_process($to_date, $mbid, $id2, $mbname, 1,$hap2-$cond_binary[$i]['sales_reset'] , $note_adm);
								} 
								else if($cond_binary[$i]['max_reset1']=='대실적만 이월'){
									$note_adm='소실적 발생 (대실적만 이월) (2) 소실적:'.$hap1.	'('.$id1.') / 대실적:'.$hap2.	'('.$id2.') 이월금:'.($hap2-$hap1);
									$small_note='소실적 소멸 발생. Point : '.$hap1;
									echo $note='Binary Bonus for '.$deslv.' member';
									$no_benefit=1;$binary_firstname=$mbname;$binary_firstid=$mbid;
									if($today_sales>0) 
										save_benefit_b($to_date, $mbid, $mbname, $recom, $cond_binary[$i]['allowance_name'], '', 0, $today_sales, $hap1, $cond_binary[$i]['allowance_name'].' '.$note_adm, $note,$exchange_rate, $mblevel);
									iwol_process($to_date, $mbid, $id2, $mbname, 2, $hap2-$hap1, $note_adm); 
									iwol_process($to_date, $mbid, $id1, $mbname, 2, 0 , $small_note);
									//iwol_process($to_date, $mbid, $id2, $mbname, 2, $hap2, $note_adm); 

								} 
								else if($cond_binary[$i]['max_reset1']=='소실적만 이월'){
									$note_adm='극점초과(소실적만 이월) (3) 소실적:'.$hap1.	'('.$id1.') / 대실적:'.$hap2.	'('.$id2.') 이월금:'.($hap1-$cond_binary[$i]['sales_reset']);
									echo  $note='Binary Cycle Bonus for '.$maxcycle.' cycles as a '.$deslv.' member';
									$no_benefit=1;$binary_firstname=$mbname;$binary_firstid=$mbid;
									if($today_sales>0) 
										save_benefit_b($to_date,$mbid, $mbname, $recom, $cond_binary[$i]['allowance_name'], '', 0, $today_sales, $hap1, $cond_binary[$i]['allowance_name'].' '.$note_adm, $note, $exchange_rate, $mblevel);
									iwol_process($to_date, $mbid,  $id1, $mbname, 3, $hap2-$hap1 , $note_adm); 
								}
							}
						}  //$hap1이 소실적이라면
						else{ //$hap2가 소실적이라면
							if(($hap2*0.1)>=($cond_binary[$i]['sales_reset']) ){ //소실적이 극점?
								$today_sales=$cond_binary[$i]['sales_reset']*$cond_binary[$i]['per']/100; 
								$binary_trig=($cond_binary[$i]['sales_reset']/$cond_binary[$i]['cycle']);
								$firstname=$mbname;
								$firstid=$mbid;
								if($cond_binary[$i]['max_reset1']=='대.소실적 모두이월'){
									$note_adm='극점초과(대.소실적 모두이월) (8) 소실적:'.$hap2.	'('.$id2.') / 대실적:'.$hap1.	'('.$id1.')';

									$no_benefit=1;$binary_firstname=$mbname;$binary_firstid=$mbid;
									if($today_sales>0) 
										save_benefit_b($to_date,$mbid, $mbname, $recom, $cond_binary[$i]['allowance_name'], '', 0, $today_sales, $hap2, cond_binary[$i]['allowance_name'].' '.$note_adm,$note, $exchange_rate, $mblevel);
									//echo $note='Binary Cycle Bonus for '.$maxcycle.' cycles as a '.$deslv.' member';
									iwol_process($to_date, $mbid, $id1, $mbname, 8,$hap1-$cond_binary[$i]['sales_reset'] , $note_adm);
									iwol_process($to_date, $mbid, $id2, $mbname, 8,$hap2-$cond_binary[$i]['sales_reset'] , $note_adm);
								}else if($cond_binary[$i]['max_reset1']=='대실적만 이월'){
									$note_adm='소실적 극점 발생 (대실적만 이월) (9) 소실적:'.$hap2.	'('.$id2.') / 대실적:'.$hap1.	'('.$id1.') 이월금:'.($hap1-($cond_binary[$i]['sales_reset']*10));
									$small_note='소실적 소멸 발생. Point : '.$hap2;
									echo $note='Binary Bonus for '.$deslv.' member';
									$no_benefit=1;$binary_firstname=$mbname;$binary_firstid=$mbid;
									if($today_sales>0) 
										save_benefit_b($to_date, $mbid, $mbname, $recom, $cond_binary[$i]['allowance_name'], '', 0, $today_sales,  $hap2, $cond_binary[$i]['allowance_name'].' '.$note_adm, $note, $exchange_rate, $mblevel);
									iwol_process($to_date, $mbid, $id1, $mbname, 99, $hap1-($cond_binary[$i]['sales_reset']*10) , $note_adm); 
									iwol_process($to_date, $mbid, $id2, $mbname, 99, 0 , $small_note);
									//iwol_process($to_date, $mbid, $id1, $mbname, 9, $hap1 , $note_adm); //대실적 1/2

								}else if($cond_binary[$i]['max_reset1']=='소실적만 이월'){
									$note_adm ='극점초과(소실적만 이월) (10) 소실적:'.$hap2.	'('.$id2.') / 대실적:'.$hap1.	'('.$id2.') 이월금:'.($hap2-$cond_binary[$i]['sales_reset']);
									//echo $note='Binary Cycle Bonus as a '.$maxcycle.' cycles as a '.$deslv.' member';
									iwol_process($to_date, $mbid, $id2, $mbname, 10, $hap2-$cond_binary[$i]['sales_reset'] , $note_adm); 
								}
							}
							else {
								$today_sales=$hap2*$cond_binary[$i]['per']/100; 
								$binary_trig=($cond_binary[$i]['sales_reset']/$cond_binary[$i]['cycle']);
								$firstname=$mbname;
								$firstid=$mbid;
								if($cond_binary[$i]['max_reset1']=='대.소실적 모두이월'){
									$note_adm='극점초과(대.소실적 모두이월) (8) 소실적:'.$hap2.	'('.$id2.') / 대실적:'.$hap1.	'('.$id1.')';
									$no_benefit=1;$binary_firstname=$mbname;$binary_firstid=$mbid;
									if($today_sales>0) 
										save_benefit_b($to_date,$mbid, $mbname, $recom, $cond_binary[$i]['allowance_name'], '', 0, $today_sales, $hap2, cond_binary[$i]['allowance_name'].' '.$note_adm,$note, $exchange_rate, $mblevel);
									//echo $note='Binary Cycle Bonus for '.$maxcycle.' cycles as a '.$deslv.' member';
									iwol_process($to_date, $mbid, $id1, $mbname, 8,$hap1-$cond_binary[$i]['sales_reset'] , $note_adm);
									iwol_process($to_date, $mbid, $id2, $mbname, 8,$hap2-$cond_binary[$i]['sales_reset'] , $note_adm);
								}else if($cond_binary[$i]['max_reset1']=='대실적만 이월'){
									$note_adm='소실적 발생 (대실적만 이월) (9) 소실적:'.$hap2.	'('.$id2.') / 대실적:'.$hap1.	'('.$id1.') 이월금:'.($hap1-$hap2);
									$small_note='소실적 소멸 발생. Point : '.$hap2;
									echo $note='Binary Bonus for '.$deslv.' member';
									$no_benefit=1;$binary_firstname=$mbname;$binary_firstid=$mbid;
									if($today_sales>0) 
										save_benefit_b($to_date, $mbid, $mbname, $recom, $cond_binary[$i]['allowance_name'], '', 0, $today_sales,  $hap2, $cond_binary[$i]['allowance_name'].' '.$note_adm, $note, $exchange_rate, $mblevel);
									iwol_process($to_date, $mbid, $id1, $mbname, 9, $hap1-$hap2 , $note_adm); 
									iwol_process($to_date, $mbid, $id2, $mbname, 9, 0 , $small_note);
									//iwol_process($to_date, $mbid, $id1, $mbname, 9, $hap1 , $note_adm); //대실적 1/2

								}else if($cond_binary[$i]['max_reset1']=='소실적만 이월'){
									$note_adm ='극점초과(소실적만 이월) (10) 소실적:'.$hap2.	'('.$id2.') / 대실적:'.$hap1.	'('.$id2.') 이월금:'.($hap2-$cond_binary[$i]['sales_reset']);
									//echo $note='Binary Cycle Bonus as a '.$maxcycle.' cycles as a '.$deslv.' member';
									iwol_process($to_date, $mbid, $id2, $mbname, 10, $hap2-$cond_binary[$i]['sales_reset'] , $note_adm); 
								}
							}
						}  //$hap2가 소실적이라면 
					}// if(($hap1>0) || ($hap2>0)){
					echo $mbname.'('.$mbid.'): '.$note.'직급: '.$mblevel.'= 직급조건: '.$cond_binary[$i]['mb_level_in1'].'<br>';
				} // 바이너리보너스 계산
			}// 수당per 가 있으면 
		} // for
		$rec='';
		$rec='';
		$history_cnt=0;
		$today_sales=0;

	} //for

	//alert('수당계산이 완료되었습니다');

	$cond_match = array(array('price_cond'=>'','source'=>'','source_cond1'=>'','source_cond2'=>'','source_in1'=>'','source_in2'=>'','mb_level_cond1'=>'','mb_level_cond2'=>'','mb_level_in1'=>'','mb_level_in2'=>'','partner_cnt'=>'','partner_cont'=>'','history_cnt'=>'','history_cond1'=>'','history_cond2'=>'','history_in1'=>'','history_in2'=>'','base_source'=>'','per'=>'','allowance_name'=>'','mat'=>'','level1'=>'','level2'=>'','bigsmall1'=>'','bigsmall2'=>'','history'=>'','benefit'=>'','benefit_limit1'=>'','source11'=>'','source_cond11'=>'','source_cond12'=>'','source_in11'=>'','source_in12'=>'','sales_reset'=>'','max_reset1'=>'','max_reset2'=>'','mb_level_in11'=>'','mb_level_cond11'=>'','mb_level_cond12'=>'','bf_limit1'=>''));

	$benefitSql = "SELECT * from pinna_soodang_set where immediate=2 order by partner_cnt desc, no";
	$rrr = sql_query($benefitSql);

	for ($i=0; $row=sql_fetch_array($rrr); $i++) {   

		$cond_match[$i]['price_cond']=$row['price_cond'];
		$cond_match[$i]['source']=$row['source'];
		$cond_match[$i]['source_cond1']=$row['source_cond1'];
		$cond_match[$i]['source_cond2']=$row['source_cond2'];
		$cond_match[$i]['source_in1']=$row['source_in1'];
		$cond_match[$i]['source_in2']=$row['source_in2'];
		$cond_match[$i]['mb_level_cond1']=$row['mb_level_cond1'];
		$cond_match[$i]['mb_level_cond2']=$row['mb_level_cond2'];
		$cond_match[$i]['mb_level_in1']=$row['mb_level_in1'];
		$cond_match[$i]['mb_level_in2']=$row['mb_level_in2'];
		$cond_match[$i]['partner_cnt']=$row['partner_cnt'];
		$cond_match[$i]['partner_cont']=$row['partner_cont'];
		$cond_match[$i]['history_cnt']=$row['history_cnt'];
		$cond_match[$i]['history_cond1']=$row['history_cond1'];
		$cond_match[$i]['history_cond2']=$row['history_cond2'];
		$cond_match[$i]['history_in1']=$row['history_in1'];
		$cond_match[$i]['history_in2']=$row['history_in2'];
		$cond_match[$i]['base_source']=$row['base_source'];
		$cond_match[$i]['immediate']=$row['immediate'];
		$cond_match[$i]['per']=$row['per'];
		$cond_match[$i]['andor']=$row['andor'];
		$cond_match[$i]['allowance_name']=$row['allowance_name'];

		$cond_match[$i]['benefit_limit1']=$row['benefit_limit1'];
		$cond_match[$i]['source11']=$row['source11'];
		$cond_match[$i]['source_cond11']=$row['source_cond11'];
		$cond_match[$i]['source_cond12']=$row['source_cond12'];
		$cond_match[$i]['source_in11']=$row['source_in11'];
		$cond_match[$i]['source_in12']=$row['source_in12'];
		
		$cond_match[$i]['sales_reset']=$row['sales_reset'];
		$cond_match[$i]['iwolyn']=$row['iwolyn'];
		$cond_match[$i]['max_reset1']=$row['max_reset1'];
		$cond_match[$i]['max_reset2']=$row['max_reset2'];
		$cond_match[$i]['cycle']=$row['cycle'];

		$cond_match[$i]['mb_level_in11']=$row['mb_level_in11'];
		$cond_match[$i]['mb_level_cond11']=$row['mb_level_cond11'];
		$cond_match[$i]['mb_level_cond12']=$row['mb_level_cond12'];

		$cond_match[$i]['recom_kind']=$row['recom_kind'];


		if( ($row['sales_reset']>0) && ($row['cycle']>0)  && ($row['max_reset1']<>'')  && ($row['max_reset2']<>'') ){  $cond_match[$i]['limit_reset']=1;  }  //극점 사용여부


		if( ($row['partner_cnt']>0) && ($row['partner_cont']>0) ){$cond_match[$i]['mat']=1;}  // 메트릭스
		
		if(  ($row['source_in1']!=0) || ($row['source_in2']!=0) ){ $cond_match[$i]['bigsmall1']=1;  }// 대소실적조건1

		if(  ($row['source_in11']!=0) || ($row['source_in12']!=0) ){ $cond_match[$i]['bigsmall2']=1;  }// 대소실적조건12

		if(  ($row['mb_level_in1']!=0) || ($row['mb_level_in2']!=0) ){ $cond_match[$i]['level1']=1; } //본인직급 

		if(  ($row['mb_level_cond11']!=0) || ($row['mb_level_cond12']!=0) ){ $cond_match[$i]['level2']=1; } //하부직급

		if(  ($row['history_cond1']!='') || ($row['history_cond2']!='') ){ $cond_match[$i]['history']=1;}  //대수 level

		if( $row['benefit_limit1']>0  ){$cond_match[$i]['bf_limit1']=1;}  //매출자보다 수당받을자의 매출이 같거나 큰가?
	}

	$day=$to_date;
	if($fr_date==''){ 
		$fr_date=$day;
	}
	if($to_date==''){ 
		$to_date=$day;
	}

	$allowance_name='바이너리보너스';
	// 직급이 최소 1스타(3) 이상이고 하부에 오늘 매출이 있는 사람만 
	$sql = "SELECT m.mb_id, m.mb_name, m.mb_hp, m.mb_level, m.mb_recommend, m.mb_brecommend , s.allowance_name ,s.benefit_usd, s.benefit, s.benefit_level FROM g5_member as m, soodang_pay as s WHERE m.mb_id=s.mb_id and benefit_level>2 and s.day='$to_date'";
	$result = sql_query($sql);
	//make_class();
	echo $sql;
	$rec='';
	for ($i=0; $row=sql_fetch_array($result); $i++) {   
		$comp=$row['mb_id'];
		$pay=$row['benefit_usd'];
		//$benefit=$row['benefit_usd']/$row['benefit_level'];//기존 보너스 룰 : 바이너리 수당에 cycle당 수량 - 1사이클 금액을 나눈것.
		$benefit=$row['benefit_usd'];//신규 보너스 룰 : 바이너리 소실적을 넣어준 것을 가져 온다.
		$binary_firstname=$row['mb_name'];
		$binary_firstid=$comp;
		$history_cnt=0;
		$paid_cnt = 0;
		$binary_fristlv = $row['mb_level'];

			while(  ($comp!='Coolrunning')  ){ 
				$sql = " SELECT mb_id, mb_name, mb_hp, mb_level, mb_recommend, mb_brecommend FROM g5_member WHERE mb_id= '".$comp."'";
				$recommend = sql_fetch($sql);
				$leg_success=0; // 메트릭스 성공찾기 클리어
				$mbid=$recommend['mb_id'];
				$mbname=$recommend['mb_name'];
				$mblevel=$recommend['mb_level'];
				if(   ($mb_name=='본사')  || ($mbid=='')  ) break;
					for ($i=0; $i<count($cond_match); $i++) {
					//위로 연속 올라가는 부분 추천인으로 올라갈지 바이너리추천인으로 올라갈지 결정
						if($cond_match[$i]['recom_kind']=='mb_recommend'){
								$recom=$recommend['mb_recommend'];
						}else{
								$recom=$recommend['mb_brecommend'];
						}
						//위$cond_match에서 설정한 내용에 대해 다시 변수설정하여 트리거 걸릴지 말지 설정
					
						if($cond_match[$i]['level1']=='1'){ $temp_cond_level1=1; } else {$temp_cond_level1=0;} //본인직급
					
						if($cond_match[$i]['history']=='1'){ $temp_cond_history=1; } else {$temp_cond_history=0;} //대수조건
							$temp_sql1 = '';
							if($cond_match[$i]['per']!=''){ // 수당 퍼센트가 비어있으면 패스~

									  //******   본인직급 조건이 있다면  성공여부 기록 
									if(   ($cond_match[$i]['mb_level_cond1']=='==')   ){

											if($mblevel==$cond_match[$i]['mb_level_in1']){
												$temp_cond_level1=0;
											}else{
												$temp_cond_level1=1;
											}

									}else if(  ($cond_match[$i]['mb_level_cond1']=='>=') && ($cond_match[$i]['mb_level_cond2']=='')  ){

											if($mblevel>=$cond_match[$i]['mb_level_in1']){
												
												$temp_cond_level1=0;
											}else{
												$temp_cond_level1=1;
											}

									}else if(  ($cond_match[$i]['mb_level_cond1']=='') && ($cond_match[$i]['mb_level_cond2']=='<=')  ){

											if($mblevel<=$cond_match[$i]['mb_level_in2']){
												
												$temp_cond_level1=0;
											}else{
												$temp_cond_level1=1;
											}

									}else if(  ($cond_match[$i]['mb_level_cond1']=='>=') && ($cond_match[$i]['mb_level_cond2']=='<=')  ){
									if(  ($mblevel>=$cond_match[$i]['mb_level_in1']) && ($mblevel<=$cond_match[$i]['mb_level_in2'])  ){
										$temp_cond_level1=0;
										}else{
											$temp_cond_level1=1;
										}
									}
									// 바이너리매칭 대수조건
									if(   ($cond_match[$i]['history_cond1']=='==')   ){

										if(($history_cnt)==$cond_match[$i]['history_in1']){
											$bm=0;
										}else{
											$bm=1;
										}
									}else if(  ($cond_match[$i]['history_cond1']=='>=') && ($cond_match[$i]['history_cond1']=='')  ){
											if(($history_cnt)>=$cond_match[$i]['history_in1']){
												$bm=0;
											}else{
												$bm=1;
											}

									}else if(  ($cond_match[$i]['history_cond1']=='') && ($cond_match[$i]['history_cond1']=='<=')  ){

											if(($history_cnt)<=$cond_match[$i]['history_in1']){
												$bm=0;
											}else{
												$bm=1;
											}

									}else if(  ($cond_match[$i]['history_cond1']=='>=') && ($cond_match[$i]['history_cond2']=='<=')  ){
											if( (($history_cnt)>=$cond_match[$i]['history_in1']) && ($history_cnt<=$cond_match[$i]['history_in2']) ){
												$bm=0;
											}else{
												$bm=1;
											}
									
									}


							if( ($bm==0) && ($temp_cond_level1==0)  ){//매칭 수당.
							if( ($pay>0) && ($cond_match[$i]['base_source']=='Cycle') ) {
							echo $mbname.'('.$mbid.'): 대수'.$history_cnt.'=='.$cond_match[$i]['history_in1'].'   직급 : '.$cond_match[$i]['mb_level_in1'].'=='.$mblevel.'  '.$bm,'---'.$temp_cond_level1.' /  '.$binary_firstname.'('.$binary_firstid.') 로부터 '.$cond_match[$i]['allowance_name'].' 발생<br>';

							$rec_adm=$binary_firstname.'('.$binary_firstid.') 로부터 '.$cond_match[$i]['allowance_name'].' 발생, 내 직급: '.$mb_level.', 대수: '.($history_cnt).' Cycle 수: '.$benefit*$cond_match[$i]['per']/100;

							$rec=$cond_match[$i]['allowance_name'].' from '.$binary_firstid.'( level '.$history_cnt.')';
											//echo $mbname.'('.$mbid.'): '.$rec.'<br>';
							save_benefit($to_date, $mbid, $mbname, $recom, $cond_match[$i]['allowance_name'], $recommend['sales_day'], $recommend['habu_day_sales'], ($benefit*$cond_match[$i]['per']/100) , $cond_match[$i]['allowance_name'].' '.$rec_adm, $rec,$exchange_rate, $mblevel);
						}
					}
				}// 수당per 가 있으면 
			} // for
			//echo $rec;
			$rec='';
			$comp=$recom;
			$history_cnt++;
		} // while
		$rec='';
			

	} //for

	//alert('수당계산이 완료되었습니다');






	$cond_rank = array(array('price_cond'=>'','source'=>'','source_cond1'=>'','source_cond2'=>'','source_in1'=>'','source_in2'=>'','mb_level_cond1'=>'','mb_level_cond2'=>'','mb_level_in1'=>'','mb_level_in2'=>'','partner_cnt'=>'','partner_cont'=>'','history_cnt'=>'','history_cond1'=>'','history_cond2'=>'','history_in1'=>'','history_in2'=>'','base_source'=>'','per'=>'','allowance_name'=>'','mat'=>'','level1'=>'','level2'=>'','bigsmall1'=>'','bigsmall2'=>'','history'=>'','benefit'=>'','benefit_limit1'=>'','source11'=>'','source_cond11'=>'','source_cond12'=>'','source_in11'=>'','source_in12'=>'','sales_reset'=>'','max_reset1'=>'','max_reset2'=>'','mb_level_in11'=>'','mb_level_cond11'=>'','mb_level_cond12'=>'','bf_limit1'=>''));

	$benefitSql = "SELECT * from pinna_soodang_set where immediate=3 order by partner_cnt desc, no";
	$rrr = sql_query($benefitSql);

	for ($i=0; $row=sql_fetch_array($rrr); $i++) {   

		$cond_rank[$i]['price_cond']=$row['price_cond'];
		$cond_rank[$i]['source']=$row['source'];
		$cond_rank[$i]['source_cond1']=$row['source_cond1'];
		$cond_rank[$i]['source_cond2']=$row['source_cond2'];
		$cond_rank[$i]['source_in1']=$row['source_in1'];
		$cond_rank[$i]['source_in2']=$row['source_in2'];
		$cond_rank[$i]['mb_level_cond1']=$row['mb_level_cond1'];
		$cond_rank[$i]['mb_level_cond2']=$row['mb_level_cond2'];
		$cond_rank[$i]['mb_level_in1']=$row['mb_level_in1'];
		$cond_rank[$i]['mb_level_in2']=$row['mb_level_in2'];
		$cond_rank[$i]['partner_cnt']=$row['partner_cnt'];
		$cond_rank[$i]['partner_cont']=$row['partner_cont'];
		$cond_rank[$i]['history_cnt']=$row['history_cnt'];
		$cond_rank[$i]['history_cond1']=$row['history_cond1'];
		$cond_rank[$i]['history_cond2']=$row['history_cond2'];
		$cond_rank[$i]['history_in1']=$row['history_in1'];
		$cond_rank[$i]['history_in2']=$row['history_in2'];
		$cond_rank[$i]['base_source']=$row['base_source'];
		$cond_rank[$i]['immediate']=$row['immediate'];
		$cond_rank[$i]['per']=$row['per'];
		$cond_rank[$i]['andor']=$row['andor'];
		$cond_rank[$i]['allowance_name']=$row['allowance_name'];

		$cond_rank[$i]['benefit_limit1']=$row['benefit_limit1'];
		$cond_rank[$i]['source11']=$row['source11'];
		$cond_rank[$i]['source_cond11']=$row['source_cond11'];
		$cond_rank[$i]['source_cond12']=$row['source_cond12'];
		$cond_rank[$i]['source_in11']=$row['source_in11'];
		$cond_rank[$i]['source_in12']=$row['source_in12'];
		
		$cond_rank[$i]['sales_reset']=$row['sales_reset'];
		$cond_rank[$i]['iwolyn']=$row['iwolyn'];
		$cond_rank[$i]['max_reset1']=$row['max_reset1'];
		$cond_rank[$i]['max_reset2']=$row['max_reset2'];
		$cond_rank[$i]['cycle']=$row['cycle'];

		$cond_rank[$i]['mb_level_in11']=$row['mb_level_in11'];
		$cond_rank[$i]['mb_level_cond11']=$row['mb_level_cond11'];
		$cond_rank[$i]['mb_level_cond12']=$row['mb_level_cond12'];

		$cond_rank[$i]['recom_kind']=$row['recom_kind'];
		if( ($row['sales_reset']>0) && ($row['cycle']>0)  && ($row['max_reset1']<>'')  && ($row['max_reset2']<>'') ){  $cond_rank[$i]['limit_reset']=1;  }  //극점 사용여부
		if( ($row['partner_cnt']>0) && ($row['partner_cont']>0) ){$cond_rank[$i]['mat']=1;}  // 메트릭스
		if(  ($row['source_in1']!=0) || ($row['source_in2']!=0) ){ $cond_rank[$i]['bigsmall1']=1;  }// 대소실적조건1
		if(  ($row['source_in11']!=0) || ($row['source_in12']!=0) ){ $cond_rank[$i]['bigsmall2']=1;  }// 대소실적조건12
		if(  ($row['mb_level_in1']!=0) || ($row['mb_level_in2']!=0) ){ $cond_rank[$i]['level1']=1; } //본인직급 
		if(  ($row['mb_level_cond11']!=0) || ($row['mb_level_cond12']!=0) ){ $cond_rank[$i]['level2']=1; } //하부직급
		if(  ($row['history_cond1']!='') || ($row['history_cond2']!='') ){ $cond_rank[$i]['history']=1;}  //대수 level
		if( $row['benefit_limit1']>0  ){$cond_rank[$i]['bf_limit1']=1;}  //매출자보다 수당받을자의 매출이 같거나 큰가?
	}
	$day = $to_date;
	$start_day='2017-01-01'; // 누적 수당계산 시작일

	if($fr_date==''){ 
		$fr_date=$day;
	}
	if($to_date==''){ 
		$to_date=$day;
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
		$pool_sql = sql_fetch("select it_pool1, it_pool2, it_pool3, it_pool4, it_pool5, VVIP225000,  VVIP337500, VVIP450000,  it_gpu from g5_member where mb_id='".$mbid."'");
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
		$it_p6 = $pool_sql['VVIP225000'];
		$it_p7 = $pool_sql['VVIP337500'];
		$it_p8 = $pool_sql['VVIP450000'];
		$it_gpu = $pool_sql['it_gpu'];
		$recom_tot = $recom_sql['count_recom'];
		$note='';
		list($one,$two,$three,$four,$five,$six)=habu_rank($mbid);
		echo $mbid.":".$one.$two.$three.$four.$five.$six;

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
			//2스타 -> 1스타 강등.
			if(  ($noohap>=3000) && ($it_p1>=1) && ($recom_tot>=2) ){
				if(($mblevel>3) && ($degrade==1) ){ // 강등해라
					$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 3 강등됨, 누적매출: '.$noohap;
					$sql= " UPDATE g5_member SET mb_level=3, mb_adult='".$mblevel."', rank_day='$to_date', rank_note='$note' where mb_id='".$mbid."'";
					sql_query($sql);

					$sql= " insert rank SET mb_id='".$mbid."' , old_level='".$mblevel."', rank=3, rank_day='$to_date', rank_note='$note'";
					sql_query($sql);

				}
			}

			if(  ($noohap>=15000) && ($it_p2>=1) && ($recom_tot>=3) ){
				if( ($one>=3) || ($two>=3) || ($three>=3) || ($four>=3) || ($five>=3) || ($six>=3)  ){
					if(  ($mblevel>4) && ($degrade==1) ){ // 강등해라
						$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 4 강등됨, 누적매출: '.$noohap;
						$sql= " UPDATE g5_member SET mb_level=4, mb_adult='".$mblevel."', rank_day='$to_date', rank_note='$note' where mb_id='".$mbid."'";
						sql_query($sql);
						$sql= " insert rank SET mb_id='".$mbid."' , old_level='".$mblevel."', rank=4, rank_day='$to_date', rank_note='$note'";
						sql_query($sql);

					}else if($mblevel==4){  // 같은 등급이면...
						// 아무것도 안함
						$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 4 동일함으로 기록하지 않음, 누적매출: '.$noohap;
					}else if($mblevel<4) { // 현재 승진이라면...
						$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 4 승진함, 누적매출: '.$noohap;
						
						$sql= " UPDATE g5_member SET mb_level=4, mb_adult='".$mblevel."', rank_day='$to_date', rank_note='$note' where mb_id='".$mbid."'";;
						sql_query($sql);

						$sql= " insert rank SET mb_id='".$mbid."' , old_level='".$mblevel."', rank=4, rank_day='$to_date', rank_note='$note'";
						sql_query($sql);
						echo $sql;
					}
				}
			}

			//3스타 조건
			if( ($noohap>=75000) && ($it_p3>=1) && ($recom_tot>=4)  ) {
				
				if( ($two>=3) || ($three>=3) || ($four>=3) || ($five>=3) || ($six>=3) ){
					if(  ($mblevel>5) && ($degrade==1) ){ // 강등해라
							$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 5 강등됨, 누적매출: '.$noohap;
							$sql= " UPDATE g5_member SET mb_level=5, mb_adult='".$mblevel."', rank_day='$to_date', rank_note='$note' where mb_id='".$mbid."'";;
							sql_query($sql);

							$sql= " insert rank SET mb_id='".$mbid."' , old_level='".$mblevel."', rank=5, rank_day='$to_date', rank_note='$note'";
							sql_query($sql);

					}else if($mblevel==5){  // 같은 등급이면...
						// 아무것도 안함
						$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 5 동일함으로 기록하지 않음, 누적매출: '.$noohap;

					}else if($mblevel<5)  { // 현재 승진이라면...
						$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 5 승진함, 누적매출: '.$noohap;
						$sql= " UPDATE g5_member SET mb_level=5, mb_adult='".$mblevel."', rank_day='$to_date', rank_note='$note' where mb_id='".$mbid."'";
						sql_query($sql);

						$sql= " insert rank SET mb_id='".$mbid."' , old_level='".$mblevel."', rank=5, rank_day='$to_date', rank_note='$note'";
						sql_query($sql);
					}
				}
			}

			//4스타 조건
			if( ($noohap>=350000) && ($it_p4>=1) && ($recom_tot>=5) ){
				
				if( ($three>=3) || ($four>=3) || ($five>=3) || ($six>=3) ){
					if(  ($mblevel>6) && ($degrade==1)  ){ // 강등해라
						$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 6 강등됨, 누적매출: '.$noohap;
						$sql= " UPDATE g5_member SET mb_level=6, mb_adult='".$mblevel."', rank_day='$to_date', rank_note='$note' where mb_id='".$mbid."'";
						sql_query($sql);

						$sql= " insert rank SET mb_id='".$mbid."' , old_level='".$mblevel."', rank=6, rank_day='$to_date', rank_note='$note'";
						sql_query($sql);

					}else if($mblevel==6){ // 같은 등급이면...
						// 아무것도 안함
						$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 6 동일함으로 기록하지 않음, 누적매출: '.$noohap;
					}else if($mblevel<6) { // 현재 승진이라면...
						$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 6 승진함, 누적매출: '.$noohap;
						$sql= " UPDATE g5_member SET mb_level=6, mb_adult='".$mblevel."', rank_day='$to_date', rank_note='$note' where mb_id='".$mbid."'";
						sql_query($sql);

						$sql= " insert rank SET mb_id='".$mbid."' , old_level='".$mblevel."', rank=6, rank_day='$to_date', rank_note='$note'";
						sql_query($sql);
					}
				}
			}	

			//5스타 조건
			if( ($noohap>=3000000) &&  ($it_p5>=1) && ($it_gpu>=1) && ($recom_tot>=6) ) {					
				if( ($four>=3) || ($five>=3) || ($six>=3)  ){
					if(  ($mblevel>7) && ($degrade==1)  ){ // 강등해라
						$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 7 강등됨, 누적매출: '.$noohap.' 30일간매출: '.$thirtyhap;
						$sql= " UPDATE g5_member SET mb_level=7, mb_adult='".$mblevel."', rank_day='$to_date', rank_note='$note' where mb_id='".$mbid."'";
						sql_query($sql);

						$sql= " insert rank SET mb_id='".$mbid."' , old_level='".$mblevel."', rank=7, rank_day='$to_date', rank_note='$note'";
						sql_query($sql);

					}else if($mblevel==7){  // 같은 등급이면...
						// 아무것도 안함
						$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 7 동일함으로 기록하지 않음, 누적매출: '.$noohap;
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
			if( ($noohap>15000000) && ($it_p6>=1) && ($it_gpu>=1) && ($recom_tot>=8) ){
				
				if( ($five>=4) || ($six>=4) ){
					if(  ($mblevel>8) && ($degrade==1) && ($it_p4>=1) && ($it_gpu>=1) ){ // 강등해라
						$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 8 강등됨, 누적매출: '.$noohap;
						$sql= " UPDATE g5_member SET mb_level=8, mb_adult='".$mblevel."', rank_day='$to_date', rank_note='$note' where mb_id='".$mbid."'";
						sql_query($sql);

						$sql= " insert rank SET mb_id='".$mbid."' , old_level='".$mblevel."', rank=8, rank_day='$to_date', rank_note='$note'";
						sql_query($sql);
					}else if($mblevel==8){  // 같은 등급이면...
						// 아무것도 안함
						$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 8 동일함으로 기록하지 않음, 누적매출: '.$noohap;

					}else if($mblevel<8) { // 현재 승진이라면...
						$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 8 승진함, 누적매출: '.$noohap;
						$sql= " UPDATE g5_member SET mb_level=8, mb_adult='".$mblevel."', rank_day='$to_date', rank_note='$note' where mb_id='".$mbid."'";
						sql_query($sql);

						$sql= " insert rank SET mb_id='".$mbid."' , old_level='".$mblevel."', rank=8, rank_day='$to_date', rank_note='$note'";
						sql_query($sql);
					}
				}
			}	
			//7스타 조건
			if( ($noohap>65000000) && ($it_p7>=1) && ($it_gpu>=1) && ($recom_tot>=11) ){
				
				if(  ($six>=4) ){
					if(  ($mblevel>9) && ($degrade==1) && ($it_p4>=1) && ($it_gpu>=1) ){ // 강등해라
						$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 9 강등됨, 누적매출: '.$noohap;
						$sql= " UPDATE g5_member SET mb_level=9, mb_adult='".$mblevel."', rank_day='$to_date', rank_note='$note' where mb_id='".$mbid."'";
						sql_query($sql);

						$sql= " insert rank SET mb_id='".$mbid."' , old_level='".$mblevel."', rank=9, rank_day='$to_date', rank_note='$note'";
						sql_query($sql);
					}else if($mblevel==9){  // 같은 등급이면...
						// 아무것도 안함
						$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 9 동일함으로 기록하지 않음, 누적매출: '.$noohap;

					}else if($mblevel<9) { // 현재 승진이라면...
						$note=$mbname.'('.$mbid.') 회원님, 현재 내직급: '.$mblevel.' 적용직급: 9 승진함, 누적매출: '.$noohap;
						$sql= " UPDATE g5_member SET mb_level=9, mb_adult='".$mblevel."', rank_day='$to_date', rank_note='$note' where mb_id='".$mbid."'";
						sql_query($sql);

						$sql= " insert rank SET mb_id='".$mbid."' , old_level='".$mblevel."', rank=8, rank_day='$to_date', rank_note='$note'";
						sql_query($sql);
					}
				}
			}	
		}

		if($note!=''){
		//	echo $mbname.'('.$mbid.') 회원님, -현재 직급: '.$mblevel.' 누적매출: '.$noohap.' 30일간매출 : '.$thirtyhap.'<br>';
		//}else{
			echo $note.'<br>';
		}

	} //for
//}//end if
$update_end = "update pinna_soodang_status set run_status = 'X'";
sql_query($update_end);
//alert('직급계산이 완료되었습니다');
//day while 반복
?>