<?php
include_once('./_common.php');
if(!$_GET['keyword']){
	print "[]";
	return ;
}

$sql = "select * from (
	select  mb_no,
			mb_id,
			mb_recommend_no,
			mb_recommend,
			mb_name,
			depth
	from    (select * from g5_member
			 order by mb_recommend_no, mb_no) products_sorted,
			(select @pv := '$member[mb_no]') initialisation
	where   find_in_set(mb_recommend_no, @pv) > 0
	and     @pv := concat(@pv, ',', mb_no)
) a where a.mb_id like '%$_GET[keyword]%'";

header('Content-Type: application/json');

$sth = sql_query($sql);
$rows = array();
while($r = mysqli_fetch_assoc($sth)) {
	$rows[] = $r;
}

print json_encode($rows);
?>
