<?php

$sub_menu = "600200";
include_once('./_common.php');
include_once('./bonus_inc.php');

auth_check($auth[$sub_menu], 'r');

// 지난주 날짜 구하기 
/* $today=$bonus_day;
$timestr        = strtotime($today);
$week           = date('w', strtotime($todate));
$weekfr         = $timestr - ($week * 86400);
$weekla         = $weekfr + (6 * 86400);
$week_frdate    = date('Y-m-d', $weekfr - (86400 * 5)); // 지난주 시작일자
$week_todate    = date('Y-m-d', $weekla - (86400 * 5)); // 지난주 종료일자 */



// 레벨 수당
$bonus_row = bonus_pick($code);
if($bonus_row['limited'] != ''){
    $bonus_limit = $bonus_row['limited']/100;
}else{
    $bonus_limit = 0;
}

// 수당 배열인경우
/* $bonus_rate_array_cnt = mb_substr_count($bonus_row['rate'],',');
if($bonus_rate_array_cnt > 0){
    
}else{
    $bonus_rate = $bonus_row['rate']*0.01;  
} */

$bonus_rate = explode(',',$bonus_row['rate']);
$bonus_condition = $bonus_row['source'];
$bonus_condition_tx = bonus_condition_tx($bonus_condition);
$bonus_layer = $bonus_row['layer'];
$bonus_layer_tx = bonus_layer_tx($bonus_layer);


//지난주 매출 합계 
$total_order_query = "SELECT SUM(od_cart_price) AS hap FROM g5_shop_order WHERE od_date BETWEEN '{$week_frdate}' AND '{$week_todate}' "; 
$total_order_reult = sql_fetch($total_order_query);
$total_order = $total_order_reult['hap'];



//회원 리스트를 읽어 온다.

$sql_common = " FROM g5_member ";
$sql_search=" WHERE center_use > 0 ".$pre_condition.$admin_condition;
$sql_mgroup=" ORDER BY mb_no asc ";

$pre_sql = "select *
                {$sql_common}
                {$sql_search}
                {$sql_mgroup}";

$pre_result = sql_query($pre_sql);
$result_cnt = sql_num_rows($pre_result);

// 디버그 로그 
if($debug){
	echo "대상회원 - <code>";
    print_r($pre_sql);
	echo "</code><br>";
}


ob_start();

// 설정로그 
echo "<strong>".strtoupper($code)." 지급비율 : ". $bonus_row['rate']."%   </strong> |    지급조건 :".$pre_condition.' | '.$bonus_condition_tx." | ".$bonus_layer_tx."<br>";
echo "<br><strong> 현재일 : ".$bonus_day." |  지난주(지급산정기준) : <span class='red'>".$week_frdate."~".$week_todate."</span> | 지난주 매출 합계 : <span class='blue big'>".$total_order."</span></strong><br>";
echo "<br><br>기준대상자(센터회원) : <span class='red'>".$result_cnt."</span>";
echo "</span><br><br>";
echo "<div class='btn' onclick='bonus_url();'>돌아가기</div>";
?>

<html><body>
<header>정산시작</header>    
<div>
<?
$leader_bonus = $total_order*$bonus_rate[0]*0.01;
echo "<br><br><span class='title block'> 리더수당  - 지난주 총매출 : ".$total_order." | 리더수당 : ".$leader_bonus." (".$bonus_rate[0]."%)</span><br>";

echo "<br><br><span class='title block coral'> 센터수당 </span><br>";


/*
$sql = "SELECT od_date AS od_time, m.mb_no, m.mb_id, m.mb_name, m.mb_level, m.mb_deposit_point, m.mb_balance, m.grade,SUM(pv) AS hap 
            FROM g5_shop_order AS o, g5_member AS m
            WHERE o.mb_id = m.mb_id AND m.mb_center != '0' AND od_date BETWEEN '{$week_frdate}' AND '{$week_todate}'
            GROUP BY m.mb_id ORDER BY m.mb_no asc";
$result = sql_query($sql);
*/

//회원 리스트를 읽어 온다.
$sql_search=" WHERE center_use = 1 ".$pre_condition.$admin_condition;
$sql_mgroup=" ORDER BY mb_no asc ";
$sql = "select * FROM g5_member
                {$sql_search}
                {$sql_mgroup}";

$result = sql_query($sql);

// 디버그 로그 
if($debug){
	echo "<code>";
    print_r($sql);
	echo "</code><br>";
}


excute();
function  excute(){

    global $result;
    global $g5, $bonus_day, $bonus_condition, $code, $bonus_rate,$pre_condition_in,$bonus_limit,$week_frdate,$week_todate;
    global $debug;


    for ($i=0; $row=sql_fetch_array($result); $i++) {   
   
        $center_bonus = $bonus_rate[1]*0.01;
        $mb_no=$row['mb_no'];
        $mb_id=$row['mb_id'];
        $mb_name=$row['mb_name'];
        $mb_level=$row['mb_level'];
        $mb_deposit=$row['mb_deposit_point'];
        $mb_balance=$row['mb_balance'];
        $grade=$row['grade'];
        
        echo "<br><br><span class='title' style='font-size:30px;'>".$mb_id."</span><br>";

        $recom= 'mb_center'; //센터멤버
        $sql = " SELECT mb_no, mb_id, mb_name, grade, mb_level, mb_balance, mb_deposit_point FROM g5_member WHERE {$recom} = '{$mb_id}' ";
        $sql_result = sql_query($sql);
        $sql_result_cnt = sql_num_rows($sql_result);

        echo "센터하부회원 : <span class='red'> ".$sql_result_cnt."</span> 명 <br>";
        
        while( $center = sql_fetch_array($sql_result) ){   
        
            $recom_id = $center['mb_id'];
            $week_bonus_sql = "SELECT SUM(od_cart_price) AS hap FROM g5_shop_order WHERE od_date BETWEEN '{$week_frdate}' AND '{$week_todate}' AND mb_id = '{$recom_id}' ";
            $week_bonus_result = sql_fetch($week_bonus_sql);

            // 디버그 로그 
            if($debug){
                echo "<br>--<br><code>";
                print_r($week_bonus_sql);
                echo "</code><br>";
            }
            if($week_bonus_result['hap']){
                $recom_week_bonus = $week_bonus_result['hap'];
            }else{
                $recom_week_bonus = 0;
            }
            
            if($pre_condition_in){	

                //$hist = $history_cnt-1;	
                $benefit=($recom_week_bonus*$center_bonus);// 매출자 * 수당비율

                $balance_limit = $bonus_limit * $mb_deposit; // 수당한계선
                $benefit_limit = $mb_balance + $benefit; // 수당합계
                
                $rec=$code.' Bonus from  '.$recom_id;
                $rec_adm= ''.$recom_week_bonus.'*'.$center_bonus.'='.$benefit;


                // 디버그 로그
                if($debug){
                    echo "<code>";
                    echo "현재수당 : ".$mb_balance."  | 수당한계 :". $balance_limit;
                    echo "</code><br>";
                }
                
                if($benefit_limit > $balance_limit && $balance_limit != 0){
                    $benefit_limit = $balance_limit;
                    $rec_adm = "benefit overflow";
                    echo $recom_id." | ".$recom_week_bonus.'*'.$center_bonus;
                    echo "<span class=blue> ▶▶▶ 수당 지급 : ".$benefit."</span>";
                    echo "<span class=red> ▶▶ 수당 초과 (한계까지만 지급)".$benefit_limit." </span><br>";
                    
                }else{
                    // 수당 로그
                    echo $recom_id." | ".$recom_week_bonus.'*'.$center_bonus;
                    echo "<span class=blue> ▶▶▶ 수당 지급 : ".$benefit."</span><br>";
                }

                

                //**** 수당이 있다면 함께 DB에 저장 한다.
                if($benefit > 0){
                    $bonus_sql = " insert `{$g5['bonus']}` set day='".$bonus_day."'";
                    $bonus_sql .= " ,mb_no			= ".$mb_no;
                    $bonus_sql .= " ,mb_id			= '".$mb_id."'";
                    $bonus_sql .= " ,mb_name		= '".$mb_name."'";
                    $bonus_sql .= " ,mb_level      = ".$mb_level;
                    $bonus_sql .= " ,grade      = ".$grade;
                    $bonus_sql .= " ,allowance_name	= '".$code."'";
                    $bonus_sql .= " ,benefit		=  ".$benefit;	
                    $bonus_sql .= " ,rec			= '".$rec."'";
                    $bonus_sql .= " ,rec_adm		= '".$rec_adm."'";
                    $bonus_sql .= " ,origin_balance	= '".$mb_balance."'";
                    $bonus_sql .= " ,origin_deposit	= '".$mb_deposit."'";
                    $bonus_sql .= " ,datetime		= '".date("Y-m-d H:i:s")."'";


                    // 디버그 로그
                    if($debug){
                        echo "<code>";
                        print_R($bonus_sql);
                        echo "</code>";
                    }else{
                        sql_query($bonus_sql);
                    }


                    $balance_up = "update g5_member set mb_balance = ".$benefit_limit."  where mb_id = '".$mb_id."'";

                    // 디버그 로그
                    if($debug){
                        echo "<code>";
                        print_R($balance_up);
                        echo "</code>";
                    }else{
                        sql_query($balance_up);
                    }
                }
                $rec='';
            }
        } // while

    
    } // for
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