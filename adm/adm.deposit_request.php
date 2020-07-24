<?php
$sub_menu = "700600";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$g5['title'] = "<span class='font_blue'>입금 요청 내역</psan>";

include_once('./adm.header.php');

function short_code($string, $char = 8){
	return substr($string,0,$char)." ... ".substr($string,-8);
}

$status_string = array('요청','승인','대기','불가','취소');
function status($val){
    global $status_string;
    return $status_string[$val];
}

/* 조건검색*/
if($_GET['fr_id']){
	$sql_condition .= " and A.mb_id = '{$_GET['fr_id']}' ";
	$qstr .= "&fr_id=".$_GET['fr_id'];
}

if($fr_date && $to_date){
	$sql_condition .= " and DATE_FORMAT(A.create_dt, '%Y-%m-%d') between '{$fr_date}' and '{$to_date}' ";
	$qstr = "fr_date=".$fr_date."&amp;to_date=".$to_date."&amp;to_id=".$fr_id;
}

if($_GET['update_dt']){
	$sql_condition .= " and DATE_FORMAT(A.update_dt, '%Y-%m-%d') = '".$_GET['update_dt']."'";
	$qstr .= "&update_dt=".$_GET['update_dt'];
}

if($_GET['status'] != ''){
	echo $_GET['status']."<Br><br>";
	$sql_condition .= " and A.status = '".$_GET['status']."'";
	$qstr .= "&status=".$_GET['status'];
}


if($_GET['ord']!=null && $_GET['ord_word']!=null){
	$sql_ord = "order by ".$_GET['ord_word']." ".$_GET['ord'];
}


$colspan = 8;
// $to_date = date("Y-m-d", strtotime(date("Y-m-d")."+1 day"));

$sql_common = " from {$g5['deposit']} as A";
$sql_search = " WHERE 1=1 ".$sql_condition;
$sql = " select count(create_d) as cnt
{$sql_common}
{$sql_search}";
$rows = sql_fetch($sql);
$total_count = $rows['cnt'];

$rows = 30;
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " select *
            {$sql_common}
            {$sql_search}
            order by create_dt desc
            limit {$from_record}, {$rows} ";
$result = sql_query($sql);
?>


<style>
    .red{color:red}
    .text-center{text-align:center}
    .hash{min-width:120px;height:auto;display:block;}
    .reg_text{border:1px solid #ccc;padding:5px 10px;width:80%;}
    select{padding:5px;min-width:80px;width:80%;}
    table tr td{text-align:center}
    .row_dup td{background:bisque}
</style>


<script>
	$(function(){

		$('.regTb [name=status]').on('change',function(e){
            var refund = 'N';
            var coin = $(this).parent().parent().find('.coin').text();
            var amt = $(this).parent().parent().find('.input_amt_val').val();

            console.log( `${$(this).attr('uid')} / ${coin} / ${amt}`);

			if (confirm('상태값을 변경하시겠습니까?')) {
			} else {
				return false;
			}

			if($(this).val() == '1' && coin != '' && amt > 0){
				if (confirm('입금액을 반영하시겠습니까?')) {
					refund = 'Y';	
				} else {
					refund = 'N';
				}
			}

			$.post( "/adm/adm.request_proc.php", {
				uid : $(this).attr('uid'),
				status : $(this).val(),
                refund : refund,
                coin : coin,
                amt : amt,
                func : 'deposit'
			}, function(data) {
				if(data.result =='success'){
                    if(data.code == 0001 || data.code == 0002){
                        alert(data.sql);
                    }else{
					    alert('변경되었습니다.');
                    }
					location.reload();
				}else{
					alert("처리되지 않았습니다.");
				}
			},'json');
		});


		$.datepicker.regional["ko"] = {
			closeText: "close",
			prevText: "이전달",
			nextText: "다음달",
			currentText: "오늘",
			monthNames: ["1월(JAN)","2월(FEB)","3월(MAR)","4월(APR)","5월(MAY)","6월(JUN)", "7월(JUL)","8월(AUG)","9월(SEP)","10월(OCT)","11월(NOV)","12월(DEC)"],
			monthNamesShort: ["1월","2월","3월","4월","5월","6월", "7월","8월","9월","10월","11월","12월"],
			dayNames: ["일","월","화","수","목","금","토"],
			dayNamesShort: ["일","월","화","수","목","금","토"],
			dayNamesMin: ["일","월","화","수","목","금","토"],
			weekHeader: "Wk",
			dateFormat: "yymmdd",
			firstDay: 0,
			isRTL: false,
			showMonthAfterYear: true,
			yearSuffix: ""
		};
		$.datepicker.setDefaults($.datepicker.regional["ko"]);

		$("#create_dt_fr,#create_dt_to, #update_dt").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
    });
</script>

<link href="https://cdn.jsdelivr.net/npm/remixicon@2.3.0/fonts/remixicon.css" rel="stylesheet">
<div class="tbl_head01 tbl_wrap">
    <table class='regTb'>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col" width='5%'>no</th>
        <th scope="col" width='10%'>아이디</th>
        <th scope="col" width='20%'>Transaction_hash</th>
        <th scope="col" width='10%'>코인종류</th>
        <th scope="col" width='15%'>요청시간</th>
        <th scope="col" width='15%'>입금확인금액(각코인단위)</th>
        <th scope="col" width='10%'>승인여부</th>
        <th scope="col" width='15%'>상태변경일</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        $bg = 'bg'.($i%2);
        $duplicate_sql ="select COUNT(*) as cnt from wallet_deposit_request WHERE mb_id='{$row['mb_id']}' ";
        $duplicate_result = sql_fetch($duplicate_sql);
        $duplicate = $duplicate_result['cnt'];
        if($duplicate > 1){$row_dup = 'row_dup';}else{$row_dup = '';}
    ?>
   
    <tr class="<?php echo $bg; ?> <?=$row_dup?>">
        <td ><?php echo $row['uid'] ?></td>
        <td><a href='/adm/member_form.php?sst=&sod=&sfl=&stx=&page=&w=u&mb_id=<?=$row['mb_id']?>' target='_blank'><?=$row['mb_id'] ?></a></td>
        <td ><a href="https://etherscan.io/tx/<?=$row['txhash']?>" target='_blank' class='hash'>
        <?=short_code($row['txhash'],10)?><i class="ri-external-link-fill" style='font-size:16px;vertical-align:sub;float:right;margin-right:10px;'></i></a></td>
        <td class='red coin'><strong><?=strtoupper($row['coin']);?></strong></td>
        <td><?=$row['create_dt']?></td>
        <td><input type='text' class='reg_text input_amt_val' value='<?=$row['in_amt']?>'></td>
        <td>
            <!-- <?=status($row['status'])?> -->
            <select name="status" uid="<?=$row['uid']?>" class='sel_<?=$row['status']?>'>
                <option <?=$row['status'] == 0 ? 'selected':'';?> value=0>요청</option>
                <option <?=$row['status'] == 1 ? 'selected':'';?> value=1>승인</option>
                <option <?=$row['status'] == 2 ? 'selected':'';?> value=2>대기</option>
                <option <?=$row['status'] == 3 ? 'selected':'';?> value=3>불가</option>
                <option <?=$row['status'] == 4 ? 'selected':'';?> value=4>취소</option>
            </select>	
        </td>
        
        <td><?=$row['update_dt']?></td>
    </tr>

    <?php
    }
    if ($i == 0)
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">자료가 없거나 관리자에 의해 삭제되었습니다.</td></tr>';
    ?>
    </tbody>
    </table>
</div>

<?php
if (isset($domain))
    $qstr .= "&amp;domain=$domain";
$qstr .= "&amp;page=";

$pagelist = get_paging($config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr");
echo $pagelist;


include_once('./admin.tail.php');
?>
