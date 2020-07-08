<?php

$sub_menu = "600200";
include_once('./_common.php');
include_once('./bonus_inc.php');

auth_check($auth[$sub_menu], 'r');


// 직추천 수당
$bonus_row = bonus_pick($code);

// $bonus_limit = $bonus_row['limited']/100;
// $bonus_rate = $bonus_row['rate']*0.01;
$bonus_rate = $bonus_row['rate'];

$bonus_condition = $bonus_row['source'];
$bonus_condition_tx = bonus_condition_tx($bonus_condition);

$bonus_layer = $bonus_row['layer'];
$bonus_layer_tx = bonus_layer_tx($bonus_layer);


//회원 리스트를 읽어 온다.
// $sql_common = " FROM g5_shop_order AS o, g5_member AS m ";
// $sql_search=" WHERE o.mb_id=m.mb_id AND od_date ='".$bonus_day."'";
// $sql_mgroup=' GROUP BY m.mb_id ORDER BY m.mb_no asc';

// $pre_sql = "select count(*) 
//             {$sql_common}
//             {$sql_search}
//             {$sql_mgroup}";

$pre_sql = "SELECT mb_no, mb_id, mb_name,grade,mb_level, mb_balance, mb_recommend, mb_brecommend, mb_deposit_point FROM g5_member WHERE mb_id NOT IN('admin') ORDER BY mb_no ASC";


if($debug){
    echo "<code>";
    print_r($pre_sql);
    echo "</code><br>";
}

$pre_result = sql_query($pre_sql);
$result_cnt = sql_num_rows($pre_result);

ob_start();

// 설정로그 
echo "<strong>".strtoupper($code)." 지급비율 : ". $bonus_row['rate']."ETH   </strong> |    지급조건 :".$pre_condition.' | '.$bonus_condition_tx." | ".$bonus_layer_tx."<br>";
echo "<strong>".$bonus_day."</strong><br>";
echo "<br><span class='red'> 기준대상자(매출발생자) : ".$result_cnt."</span><br><br>";
echo "<div class='btn' onclick='bonus_url();'>돌아가기</div>";

?>

<html><body>
<header>정산시작</header>    
<div>
<?

// $price_cond=", SUM(pv) AS hap";

// $sql = "SELECT od_date AS od_time, m.mb_no, m.mb_id, m.mb_recommend, m.mb_name, m.mb_level, m.mb_deposit_point, m.mb_balance, m.grade
//             $price_cond 
//             {$sql_common}
//             {$sql_search}
//             {$sql_mgroup}";

$sql = $pre_sql;
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
    global $g5, $bonus_day, $bonus_condition, $code, $bonus_rate,$pre_condition_in,$bonus_limit ;
    global $debug;


    for ($i=0; $row=sql_fetch_array($result); $i++) {   
   
        $mb_no=$row['mb_no'];
        $mb_id=$row['mb_id'];
        $mb_name=$row['mb_name'];
        $mb_level=$row['mb_level'];
        $mb_deposit=$row['mb_deposit_point'];
        //$mb_balance=$row['mb_balance'];
        $grade=$row['grade'];
        
        
        echo "<br><br><span class='title' style='font-size:30px;'>".$mb_id."</span><br>";

        // 추천, 후원 조건
        if($bonus_condition < 2){
            $recom= 'mb_recommend';
        }else{
            $recom= 'mb_brecommend';
        }
        
        // $sql = " SELECT mb_no, mb_id, mb_name,grade,mb_level, mb_balance, mb_recommend, mb_brecommend, mb_deposit_point FROM g5_member WHERE {$recom}= '{$mb_id}' ";
        // $sql = "SELECT mb_no, mb_id, mb_name,grade,mb_level, mb_balance, mb_recommend, mb_brecommend, mb_deposit_point,
        // (SELECT od_cart_price  FROM g5_shop_order WHERE A.mb_id = mb_id AND od_date = '{$bonus_day}') AS today_sale FROM g5_member AS A WHERE {$recom} = '{$mb_id}' ";
        $sql = "SELECT mb_no, mb_id, mb_name,grade,mb_level, mb_balance, mb_recommend, mb_brecommend, mb_deposit_point FROM g5_member WHERE mb_recommend = '{$mb_id}' ";
        $sql_result = sql_query($sql);
        $sql_result_cnt = sql_num_rows($sql_result);

        echo "직추천인 : <span class='red'> ".$sql_result_cnt."</span> 명 <br>";
        
        
        while( $recommend = sql_fetch_array($sql_result) ){   
            $recommend['today_sale'] = 1;
            $recom_id = $recommend['mb_id'];
            if($recommend['today_sale'] > 0){
                $today_sales=$recommend['today_sale'];
            }else{$today_sales = 0;}

            // 관리자 제외
            if($mb_level > 9 ){ break;} 

            if($pre_condition_in){	

                // 실시간 반영 
                $mem_sql="SELECT mb_balance FROM g5_member WHERE mb_id ='{$mb_id}' ";
                $mem_reult = sql_fetch($mem_sql);
                $mb_balance = $mem_reult['mb_balance'];

                // $benefit=($today_sales*$bonus_rate);// 매출자 * 수당비율
                $benefit= $bonus_rate;

                // $balance_limit = $bonus_limit * $mb_deposit; // 수당한계선
                $benefit_limit = $mb_balance + $benefit; // 수당합계
                
                $rec=$code.' Recommend Bonus from  '.$recom_id;
                $rec_adm= ''.$today_sales.'*'.$bonus_rate.'='.$benefit;


                // 디버그 로그
                if($debug){
                    echo "<code>";
                    echo "현재수당 : ".$mb_balance."  | 수당한계 :". $balance_limit;
                    echo "</code><br>";
                }
                
                // if($benefit_limit > $balance_limit){
                //     $benefit_limit = $balance_limit;
                //     $rec_adm = "benefit overflow";
                //     echo $recom_id." | ".$today_sales.'*'.$bonus_rate;
                //     echo "<span class=blue> ▶▶▶ 수당 지급 : ".$benefit."</span>";
                //     echo "<span class=red> ▶▶ 수당 초과 (한계까지만 지급)".$benefit_limit." </span><br>";
                    
                // }else{
                    // 수당 로그
                    echo $recom_id." | ".$today_sales.'*'.$bonus_rate;
                    echo "<span class=blue> ▶▶▶ 수당 지급 : ".$benefit."</span><br>";
                // }

                if($benefit > 0){
                    //**** 수당이 있다면 함께 DB에 저장 한다.
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