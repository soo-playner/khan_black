<?php
$sub_menu = "600900";
include_once('./_common.php');
include_once('./bonus_inc.php');

auth_check($auth[$sub_menu], 'r');

if($_GET['debug']){
	$debug = 1;
}

// 바이너리매칭 수당

$bonus_row = bonus_pick($code);

$bonus_limit = $bonus_row['limited']/100;
$bonus_rate = $bonus_row['rate']*0.01;
$bonus_day = $_GET['to_date'];

$bonus_condition = $bonus_row['source'];
$bonus_condition_tx = bonus_condition_tx($bonus_condition);

$bonus_layer = $bonus_row['layer'];
$bonus_layer_tx = bonus_layer_tx($bonus_layer);

$min30= date("Y-m-d", strtotime( "-30 day", strtotime($bonus_day)) );



//회원 리스트를 읽어 온다.
if($_GET['test_id']){
    $pre_sql = "select * from g5_member where mb_id = '".$test_id."'";
}else{
    $pre_sql = "select * from {$g5['member_table']} where (1)".$pre_condition .' '. $admin_condition." order by mb_no asc";
}

$pre_result = sql_query($pre_sql);
$result_cnt = sql_num_rows($pre_result);

if(!$debug){
    delete_sales();
    habu_sales_calc('',$config['cf_admin'],0);
    habu_sales_calc('b',$config['cf_admin'],0);
}


ob_start();


// 설정로그 
echo "<strong>".strtoupper($code)." 지급비율 : ". $bonus_row['rate']."%   </strong> |    지급조건 :".$pre_condition.' | '.$bonus_condition_tx." | ".$bonus_layer_tx."<br>";
echo "<strong>".$bonus_day."</strong><br>";
echo "<br><span class='red'> 기준대상자(매출발생자) : ".$result_cnt." (관리자제외) </span><br><br>";
echo "<div class='btn' onclick='bonus_url();'>돌아가기</div>";

if($debug){
	echo "<code>";
	print_r($pre_sql);
	echo "</code><br>";
}

?>

<html><body>
<header>정산시작</header>    
<div>


<?
//산하매출기록 초기화
function delete_sales(){
global $bonus_day;
    $sql_sales_del = " TRUNCATE table noo2 ";
     sql_query($sql_sales_del);
    /*
    $sql_sales_del = " TRUNCATE table thirty2";
        sql_query($sql_sales_del);
    */
    $sql_sales_del = " TRUNCATE table today2";
        sql_query($sql_sales_del);
    
    $sql_sales_del = " TRUNCATE table bnoo2";
        sql_query($sql_sales_del);
    /*
    $sql_sales_del = " TRUNCATE table bthirty2";
        sql_query($sql_sales_del);
    */
    $sql_sales_del = " TRUNCATE table btoday2";
        sql_query($sql_sales_del);
}            

//산하 매출 기록 
function habu_sales_calc($gubun, $recom, $deep){

    global $bonus_day,$min30,$debug;
    $deep++; // 대수

    //$od_time = "date_format(od_time,'%Y-%m-%d')";
	
    $res= sql_query("select * from g5_member where mb_".$gubun."recommend='".$recom."' ");
    for ($j=0; $rrr=sql_fetch_array($res); $j++) { 
	
        $recom=$rrr['mb_id'];
       
        //누적매출
		$sql1= sql_fetch("select sum(pv)as hap from g5_shop_order where mb_id='".$recom."' ");
        $noo+=$sql1['hap'];
        
        //월간매출
        /*
		$mon_search = " and od_date >='$min30' and od_date <='$bonus_day'";
        $sql2= sql_fetch("select sum(pv)as hap from g5_shop_order where mb_id='".$recom."' $mon_search");
        $mon+=$sql2['hap'];
        */
        
        //일일매출
		$day_search = " and od_date ='$bonus_day'";
		$sql3= sql_fetch("select sum(pv)as hap from g5_shop_order where mb_id='".$recom."' $day_search");
        $today+=$sql3['hap'];
        
         // 디버그 로그
         if($debug){
            echo "<span class=red> | noo: ".$noo." | mon: ".$mon." | today: ".$today."</span><br>" ;
         }
        

		list($noo_r,$today_r)=habu_sales_calc($gubun, $recom, $deep);	 

        if($debug){
            echo "<code>";
            print_r($recom);
        }

        $noo_r+=$mysales;
		//$mon_r+=$mysales;
		$today_r+=$mysales;

        $noo+=$noo_r;
        //$mon+=$mon_r;  
        $today+=$today_r; 


			if( ($noo>0) && ($noo_r>0)) {
				if($j==0){
					$rec=$noo;
				}else{
					$rec=$noo_r;	
                }
                
                $inbnoo = "insert ".$gubun."noo2 SET noo=".$rec.", mb_id='".$recom."',  day = '".$bonus_day."'";
                
                // 디버그 로그
                if($debug){
                   echo " | noo: ".$rec;
                }else{
                    sql_query($inbnoo);	
                }
			}
			/*
			if(($mon>0) && ($mon_r>0) ) {
				if($j==0){
					$rec=$mon;
				}else{
					$rec=$mon_r;	
				}
                $inthirty = "insert ".$gubun."thirty2 SET thirty=".$rec.", mb_id='".$recom."',  day = '".$bonus_day."'";
                // 디버그 로그
                if($debug){
                    echo " | thrty: ".$rec;
                }else{
                    sql_query($inthirty);
                }
            }
            */
			
			if(($today>0) && ($today_r>0)) {
				if($j==0){
					$rec=$today;
				}else{
					$rec=$today_r;
                }
                
                if($j == count($rrr)) {
					$rec=$rec;
				}else{
					$rec=$today_r;
				}

                $intoday = "insert ".$gubun."today2 SET todayy=".$rec.", mb_id='".$recom."',  day = '".$bonus_day."'";
                // 디버그 로그
                if($debug){
                    echo " | today: ".$rec."</code>";
                }else{
                    sql_query($intoday);
                }
               
            }
        echo "</code>";
    }
	return array($noo,$today);
}



    for($i=0; $member=sql_fetch_array($pre_result); $i++) {

        $mb_no=$member['mb_no'];
        $mb_id=$member['mb_id'];
        $mb_name=$member['mb_name'];
        $mb_level=$member['mb_level'];
        $mb_deposit=$member['mb_deposit_point'];
        $mb_balance=$member['mb_balance'];
        $grade=$member['grade'];
        $recom=$member['mb_recommend'];

        $limit_point = $bonus_limit*$mb_deposit;

        if(($mb_name=='본사') || ($mb_id=='')  )
            break;

        list($id1,$hap1,$id2,$hap2) = my_bchild($mb_id,$bonus_day,0);

        echo '<br>▶ 실적 계산 기준  :: ' .$id1.'---'.$hap1.' // '.$id2.'---'.$hap2.' || 수당한계 : <span >'.$limit_point.'</span><br>';
        
        $note='Binary Bonus from member';
        $firstname=$mb_name;
        $firstid=$mb_id;
        
        if(($hap1>0) || ($hap2>0)){
            if( $hap1<$hap2 )
            { //$hap1이 소실적이라면
                if( ($hap1*$bonus_rate ) > $limit_point && $limit_point!=0){ //소실적이 극점?

                    $today_sales=$limit_point;
                    
                    // 수당 로그
                    echo "▶▶ 수당 계산 1-1 (수당초과) :: 대실적-<strong>".$hap2."</strong>(".$id2.") ||  소실적-<strong>".$hap1."</strong>(".$id1.") ||  수당: <span class=blue>".$bonus_rate."%</span> || 발생수당 : <strong>".$today_sales."</strong><br><br>";
                    
                    $note_adm=' 소실적 발생 (대실적만 이월) (1-1-1) 소실적:'.$hap1.	'('.$id1.') || 대실적:'.$hap2.	'('.$id2.') | 이월금:'.($hap2-$hap1);
                    $note_adm2=' 대실적 이월 (1-1-2) :'.$hap2.'('.$id2.') | 이월금:'.($hap2-$hap1);
                    $note_adm3=' 소실적 소멸 (1-1-3) :'.$hap1.'('.$id1.') | 이월금: 소멸';
                    $note = $note." ".$id1;

                    save_benefit($bonus_day, $mb_no, $mb_id, $mb_name, $grade, $mb_level, $recom,  $today_sales, $note_adm, $note, $mb_balance,$mb_deposit);
  
                        iwol_process($bonus_day, $mb_id, $id2, $mb_name, 111, $hap2-$hap1, $note_adm2);
                        iwol_process($bonus_day, $mb_id, $id1, $mb_name, 112, 0, $note_adm3); //소실적 소멸
                    
                }
                else if($hap1 == 0){ //소실적이 0일때
                    
                    $today_sales=$hap2*($bonus_rate);

                    // 수당 로그
                    echo " ▶▶ 수당 계산 1-3 ::  대실적-<strong>".$hap2."</strong>(".$id2.") ||  소실적-<strong>".$hap1."</strong>(".$id1.") ||  수당: <span class=blue>".$bonus_rate."%</span> || 발생수당 : <strong>".$today_sales."</strong><br><br>";
                    $note_adm='소실적 0 (대실적만 이월) (1-3-1) 대실적:'.$hap2.	'('.$id2.') || 소실적:'.$hap1.'('.$id1.') | 이월금:'.($hap2-$hap1);
                        
                        iwol_process($bonus_day, $mb_id, $id1, $mb_name, 13, $hap1-$hap2, $note_adm2);
                }

                else { //수당발생
                
                    $today_sales= $hap1* ($bonus_rate);

                     // 수당 로그
                    echo "▶▶ 수당 계산 1-2 :: 대실적-<strong>".$hap2."</strong>(".$id2.") ||  소실적-<strong>".$hap1."</strong>(".$id1.") ||  수당: <span class=blue>".$bonus_rate."%</span> ||  <span class=red>발생수당 : ".$hap1.'*'.$bonus_rate.'= '.$today_sales."</span><br><br>";
                    
                        $note_adm=" 소실적 발생 (대실적만 이월) (1-2-1) 소실적:".$hap1.	'('.$id1.') || 대실적:'.$hap2.	'('.$id2.') | 이월금:'.($hap2-$hap1);
                        $note_adm2=' 대실적 이월 (1-2-2) :'.$hap2.'('.$id2.') | 이월금:'.($hap2-$hap1);
                        $note_adm3=' 소실적 소멸 (1-2-3) :'.$hap1.'('.$id1.') | 이월금: 소멸';
                        $note = $note." ".$id1;

                        save_benefit($bonus_day, $mb_no, $mb_id, $mb_name, $grade, $mb_level, $recom, $today_sales, $note_adm, $note, $mb_balance,$mb_deposit);

                            iwol_process($bonus_day, $mb_id, $id2, $mb_name, 121, $hap2-$hap1, $note_adm2);
                            iwol_process($bonus_day, $mb_id, $id1, $mb_name, 122, 0, $note_adm3); //소실적 소멸
                        
                }
            }  //$hap1이 소실적이라면
            else if( $hap1>$hap2 ){ //$hap2가 소실적이라면

                if($hap2*($bonus_rate)>=$limit_point && $limit_point!=0){ //소실적이 극점?

                    $today_sales=$limit_point;
                    
                    echo " ▶▶ 수당 계산 2-1 (수당초과) :: 대실적-<strong>".$hap1."</strong>(".$id1.") ||  소실적-<strong>".$hap2."</strong>(".$id2.") ||  수당: <span class=blue>".$bonus_rate."%</span> || 발생수당 : <strong>".$today_sales."</strong><br><br>";

                        $note_adm=' 소실적 발생 (대실적만 이월) (2-1-1) 대실적:'.$hap1.	'('.$id1.') ||  소실적:'.$hap2.'('.$id2.') | 이월금:'.($hap1-$hap2);
                        $note_adm2=' 대실적 이월 (2-1-1) 대실적:'.$hap1.'('.$id1.') | 이월금:'.($hap1-$hap2);
                        $note_adm3=' 소실적 소멸 (2-1-2) 소실적:'.$hap2.'('.$id2.') | 이월금: 0';
                        $note = $note." ".$id2;
                    
                        save_benefit($bonus_day, $mb_no, $mb_id, $mb_name, $grade, $mb_level, $recom, $today_sales, $note_adm, $note, $mb_balance,$mb_deposit);

                            iwol_process($bonus_day, $mb_id, $id1, $mb_name, 211, $hap1-$hap2 , $note_adm2);
                            iwol_process($bonus_day, $mb_id, $id2, $mb_name, 212, 0, $note_adm3); //소실적 소멸
                        
                } else if($hap2 == 0){ //소실적이 0일때
                    
                    $today_sales=$hap2*($bonus_rate);

                    echo " ▶▶ 수당 계산 2-3 ::  대실적-<strong>".$hap1."</strong>(".$id1.") ||  소실적-<strong>".$hap2."</strong>(".$id2.") ||  수당: <span class=blue>".$bonus_rate."%</span> || 발생수당 : <strong>".$today_sales."</strong><br><br>";

                        $note_adm='소실적 0 (대실적만 이월) (2-3) 대실적:'.$hap1.	'('.$id1.') || 소실적:'.$hap2.'('.$id2.') | 이월금:'.($hap1-$hap2);

                        iwol_process($bonus_day, $mb_id, $id1, $mb_name, 23, $hap1-$hap2, $note_adm);
                        
                }else{ //소실적이 극점x
                    
                    $today_sales=$hap2*($bonus_rate);

                    echo " ▶▶ 수당 계산 2-2 ::  대실적-<strong>".$hap1."</strong>(".$id1.") ||  소실적-<strong>".$hap2."</strong>(".$id2.") ||  수당: <span class=blue>".$bonus_rate."%</span> ||  <span class=red>발생수당 :".$hap2.'*'.$bonus_rate.'= '.$today_sales."</span><br><br>";
                       
                        $note_adm='소실적 발생 (대실적만 이월) (2-2-1) 대실적:'.$hap1.	'('.$id1.') || 소실적:'.$hap2.'('.$id2.') | 이월금:'.($hap1-$hap2);
                        $note_adm2=' 대실적 이월 (2-2-1) :'.$hap1.'('.$id1.') | 이월금:'.($hap1-$hap2);
                        $note_adm3=' 소실적 소멸 (2-2-2) :'.$hap2.'('.$id2.') | 이월금: 0';
                        $note = $note." ".$id2;

                        save_benefit($bonus_day, $mb_no, $mb_id, $mb_name, $grade, $mb_level, $recom, $today_sales, $note_adm, $note, $mb_balance,$mb_deposit);
                            
                            iwol_process($bonus_day, $mb_id, $id1, $mb_name, 221, $hap1-$hap2, $note_adm2);
                            iwol_process($bonus_day, $mb_id, $id2, $mb_name, 222, 0, $note_adm3); //소실적 소멸
                }

            }else if( $hap1=$hap2 ){ //$hap1 과 hap2 가 같다면

                    $today_sales=$hap2*$bonus_rate;

                    echo " ▶▶ 수당 계산 3 :: 대실적-<strong>".$hap1."</strong>(".$id1.") ||  소실적-<strong>".$hap2."</strong>(".$id2.") <br>";
                    
                        $note_adm=' 대소실적같음 소멸 (3-1-1) 대실적:'.$hap1.'('.$id1.') || 소실적:'.$hap2.'('.$id2.')';
                        $note_adm2=' 대소실적 소멸 (3-1-2) 대실적:'.$hap1.'('.$id1.') | 이월금: 0';
                        $note_adm3=' 대소실적 소멸 (3-1-3) 소실적:'.$hap2.'('.$id2.') | 이월금: 0';
                        $note = $note." ".$id2;

                        save_benefit($bonus_day, $mb_no, $mb_id, $mb_name, $grade, $mb_level, $recom, $today_sales, $note_adm, $note, $mb_balance,$mb_deposit);
                        
                        iwol_process($bonus_day, $mb_id, $id1, $mb_name, 311, 0 , $note_adm2);
                        iwol_process($bonus_day, $mb_id, $id2, $mb_name, 312, 0, $note_adm3); //소실적 소멸       
            }
        } // for

        $rec='';
        $today_sales=0;
    } //for




// 본인 매출
function today_sales($mb_id, $day){
    
	$day_search = " and od_date = '$day'";
	$sql= sql_fetch("select sum(pv)as hap from g5_shop_order where mb_id='".$mb_id."' $day_search");
	if($sql['hap']=='')
	{
		$hap=0;
	}else{
		$hap=$sql['hap'];
    }
	return $hap;
}

// 하부매출
function btoday_select($mb_id,$day){
	$res= sql_fetch("select todayy from btoday2 where mb_id='".$mb_id."' and day='".$day."'");
	if($res['todayy']=='')
	{
		$hap=0;
	}else{
		$hap=$res['todayy'];
	}
	return $hap;
}

//이월된 매출
function habu_iwol($mb_id,$day){
   
	$hap1=(btoday_select($mb_id,$day)+today_sales($mb_id,$day));  //자기매출과 하부매출을 합하여
	$res2= sql_fetch("select pv as hap from iwol where mb_id='".$mb_id."' order by iwolday desc limit 0,1");
    $hap2=$res2['hap'];
    
	echo '▷ '.$mb_id.'/'.$day.' 산하매출: '.btoday_select($mb_id,$day)." + 본인매출: ".today_sales($mb_id,$day).' + 이월매출:'.$hap2.' <br>';

	return ($hap1+$hap2);
	//return ($hap2);
}

// 하위매출 가져오기
function my_bchild($mb_id,$day){
    echo '<br><br> Run : <strong>'.$mb_id.'</strong>     | '.$day.' '."<br>";
    
	$id1='';
	$id2='';
	$hap1=0;
	$hap2=0;

	$res= sql_query("select mb_id from g5_member where mb_brecommend='".$mb_id."' order by mb_no");
	
	for ($j=0; $rrr=sql_fetch_array($res); $j++) {
		if($j==0){
			$id1=$rrr['mb_id'];
			$hap1=habu_iwol($id1, $day);
			if($hap1==''){ $hap1=0;}
		}
		if($j==1){
			$id2=$rrr['mb_id'];
			$hap2=habu_iwol($id2, $day);
			if($hap2==''){ $hap2=0;}
		}
	}
	
	return array($id1, $hap1, $id2, $hap2);
}



/* 이월 DB 저장 */
function iwol_process($bonus_day,$mb_brecommend, $mb_id, $mb_name, $kind, $pv, $note){
	
	if( $pv>=0){   // 소실적 제거용
		$temp_sql1 = " insert iwol set iwolday='".$bonus_day."'";
		$temp_sql1 .= " ,mb_id		= '".$mb_id."'";
		$temp_sql1 .= " ,mb_name		= '".$mb_name."'";
		$temp_sql1 .= " ,kind		= '".$kind."'";
		$temp_sql1 .= " ,pv		= '".$pv."'";
		$temp_sql1 .= " ,note		= '".$note."'";
		$temp_sql1 .= " ,mb_brecommend		= '".$mb_brecommend."'";
		sql_query($temp_sql1);
		
		//echo $temp_sql1;
		if($pv == '0'){
			echo '<br><span class=black> ▶▶▶▶ 이월금소멸 : '.$pv.'</span> <span style=margin-left:20px>['.$note.']</span>';
		}else{
			echo '<br><span class=blue> ▶▶▶▶ 이월금 : '.$pv.'</span> <span style=margin-left:20px>['.$note.']</span>';
		}
	}
}



function save_benefit($bonus_day, $mb_no, $mb_id, $mb_name, $grade, $mb_level, $recom, $today_sales,$rec_adm, $rec,$mb_balance,$mb_deposit){
    global $g5, $debug, $code, $bonus_limit,$pre_condition_in;
    
   
    $benefit=$today_sales;// 매출자 * 수당비율
    $balance_limit = $bonus_limit * $mb_deposit; // 수당한계선
    $benefit_limit = $mb_balance + $benefit; // 수당합계
    
    if($pre_condition_in){

        if($benefit_limit > $balance_limit){
            $benefit_limit = $balance_limit;
            $rec_adm = "benefit overflow";
            echo " <span class=red> ▶▶ 수당 초과 (한계까지만 지급)".$benefit_limit." </span><br>"; 
        }
	
        //**** 수당이 있다면 함께 DB에 저장 한다.
        $bonus_sql = " insert `{$g5['bonus']}` set day='".$bonus_day."'";
        $bonus_sql .= " ,mb_no			= ".$mb_no;
        $bonus_sql .= " ,mb_id			= '".$mb_id."'";
        $bonus_sql .= " ,mb_name		= '".$mb_name."'";
        $bonus_sql .= " ,mb_level      = ".$mb_level;
        $bonus_sql .= " ,grade      = ".$grade;
        $bonus_sql .= " ,allowance_name	= '".$code."'";
        $bonus_sql .= " ,benefit		=  ".$today_sales;	
        $bonus_sql .= " ,rec			= '".$rec."'";
        $bonus_sql .= " ,rec_adm		= '".$rec_adm."'";
        $bonus_sql .= " ,origin_balance	= '".$mb_balance."'";
        $bonus_sql .= " ,origin_deposit	= '".$mb_deposit."'";
        $bonus_sql .= " ,datetime		= '".date("Y-m-d H:i:s")."'";

        //수당로그
        echo " <span class=red> ▶▶▶ 수당 지급 ".$benefit." </span><br>";
        $balance_up = "update g5_member set mb_balance = '".$benefit_limit."' where mb_id = '".$mb_id."'";
        
        // 디버그 로그
        if($debug){
            echo "<code>";
            print_R($bonus_sql);
            echo "</code>";
        }else{
            sql_query($bonus_sql);
        }

        // 디버그 로그
        if($debug){
            echo "<code>";
            print_R($balance_up);
            echo "</code>";
        }else{
            sql_query($balance_up);
        }

    }
}
?>


<?include_once('./bonus_footer.php');?>

<?
if($debug){}else{
    $html = ob_get_contents();
    //ob_end_flush();
    $logfile = G5_PATH.'/data/log/'.$code.'/'.$code.'_'.$bonus_day.'.html';
    fopen($logfile, "w");
    file_put_contents($logfile, ob_get_contents());
}

?>