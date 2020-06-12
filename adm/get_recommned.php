<!--최대 직추천자 get_recommend.php-->
<?
include_once('/home/sdevftv/html/common.php'); //실서버용 경로
//1. 회원 아이디를 가지고 직 추천자 수를 구한다.
/*$sql = "Select mb_id from g5_member";
$list = sql_query($sql);
for($i; $row = sql_fetch_array($list); $i++){
$sql1 = "Select count(mb_id) as hap from g5_member where it_pool1>=1 and mb_recommend = '".$row['mb_id']."' and mb_open_date BETWEEN '2018-08-23' and '2018-10-15'";
//echo $sql1."<br>";
$rst = sql_fetch($sql1);
if($rst[hap]>0)echo $row['mb_id']."/".$rst[hap]."<br>";
}*/
?>
<!--풀패키지 구매자 get_recommend.php-->
<?

//1. 회원 아이디를 가지고 직 추천자 수를 구한다.
$sql = "Select mb_id from g5_member where it_pool1>=1 and it_pool2>=1 and it_pool3>=1 and it_pool4>=1 and it_gpu>=1";
$list = sql_query($sql);
for($i; $row = sql_fetch_array($list); $i++){
$sql1 = "Select mb_id from g5_member where it_pool1>=1 and mb_recommend = '".$row['mb_id']."' and it_pool1>=1 and it_pool2>=1 and it_pool3>=1 and it_pool4>=1 and it_gpu>=1";
//echo $sql1."<br>";
$rst = sql_fetch($sql1);
if($rst['mb_id'])echo "recom_id : ".$row['mb_id']." / sponsor_id : ".$rst['mb_id']."<br>";
}
?>