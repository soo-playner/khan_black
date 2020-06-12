<?php
$sub_menu = "600920";
include_once("./_common.php");
include_once(G5_SMS5_PATH.'/sms5.lib.php');
include_once(G5_LIB_PATH.'/PHPExcel-1.8/Classes/PHPExcel.php');


$benefit = "SELECT allowance_name FROM soodang_pay WHERE (1) GROUP BY allowance_name ";
$rrr = sql_query($benefit);

$allowcnt=0;
for ($i=0; $allowance_name=sql_fetch_array($rrr); $i++) {   
	$nnn="allowance_chk".$i;
	$html.= "<input type='checkbox' name='".$nnn."' id='".$nnn."'";
	
	if($$nnn!=''){
		$html.=" checked='true' ";
	}		

	$html.=" value='".$allowance_name['allowance_name']."'>".$allowance_name['allowance_name']."&nbsp;&nbsp;";


	if(${"allowance_chk".$i}!=''){
		if($allowcnt==0){
			$sql_search .= " and ( (allowance_name='".${"allowance_chk".$i}."')";
		}else{
			$sql_search .= "  or ( allowance_name='".${"allowance_chk".$i}."' )";
		}

		
			$qstr.='&'.$nnn.'='.$allowance_name['allowance_name'];
		
		$allowcnt++;

	}

}

if ($allowcnt>0) $sql_search .= ")";


$token = get_token();

$fr_date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3", $fr_date);
$to_date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3", $to_date);

$qstr.='&fr_date='.$fr_date.'&to_date='.$to_date.'&chkc='.$chkc.'&chkm='.$chkm.'&chkr='.$chkr.'&chkd='.$chkd.'&chke='.$chke.'&chki='.$chki;
$qstr.='&diviradio='.$diviradio.'&r='.$r;
$qstr.='&stx='.$stx.'&sfl='.$sfl;
$qstr.='&aaa='.$aaa;

$sql_common = " from soodang_pay where (1) ";

if(!$fr_date){
	$fr_date=date("Y-m-d");
	$to_date=$fr_date;
	
}

if(($allowance_name) ){
	$sql_search .= " and (";
		if($chkc){
		$sql_search .= " allowance_name='".$allowance_name."'";
		}
 $sql_search .= " )";
 
}/*else if($dv_gubun){
	 $sql_search .= " and dv_gubun='".$dv_gubun."'";
}
*/

if($_GET[start_dt]){
	$sql_search .= " and day >= '".$_GET[start_dt]."'";
	$qstr .= "&start_dt=".$_GET[start_dt];
}
if($_GET[end_dt]){
	$sql_search .= " and day <= '".$_GET[end_dt]."'";
	$qstr .= "&end_dt=".$_GET[end_dt];
}

if ($stx) {
    $sql_search .= " and ( ";
	if(($sfl=='mb_id') || ($sfl=='mb_id')){
            $sql_search .= " ({$sfl} = '{$stx}') ";
          
	}else{
            $sql_search .= " ({$sfl} like '%{$stx}%') ";
          
    }
    $sql_search .= " ) ";
}

$sql = " select count(*) as cnt
            {$sql_common}
            {$sql_search}
             ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];


$sql_order='order by day desc';

$sql = "	select *
            {$sql_common}
            {$sql_search}
            {$sql_order}
           ";
$result = sql_query($sql);

$send_sql = $sql;
$listall = '<a href="'.$_SERVER['PHP_SELF'].'" class="ov_listall">전체목록</a>';


$colspan = 16;



if (!$total_count) alert_just('데이터가 없습니다..', G5_URL."/adm/mp_soodang.php");


function column_char($i) { return chr( 65 + $i ); }
 
// 자료 생성
$headers = array('수당날짜','수당이름','직급','이름','회원아이디','추천인','발생수당 (USD)','발생수당 (BTC)', '수당근거', '시세', '유보여부');

$rp=0;
while($row=sql_fetch_array($result))
{

	$rows[$rp] = array($row['day'],$row['day'],$row['day'],$row['day'],$row['day'],$row['day'],$row['day'],$row['day'],$row['day'],$row['day']);
	$rp = $rp+1;
	echo 'rp'.$rp.'<br>';
}
$data = array_merge(array($headers), $rows);
 
// 스타일 지정
$widths = array( 20, 20, 30, 10,10, 20, 20, 30, 10, 10);
$header_bgcolor = 'FFABCDEF';
$filename = "bonus list.xls";
// 엑셀 생성
$last_char = column_char( count($headers) - 1 );
 
$excel = new PHPExcel();
$excel->setActiveSheetIndex(0)->getStyle( "A1:${last_char}1" )->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($header_bgcolor);
$excel->setActiveSheetIndex(0)->getStyle( "A:$last_char" )->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
foreach($widths as $i => $w) $excel->setActiveSheetIndex(0)->getColumnDimension( column_char($i) )->setWidth($w);
$excel->getActiveSheet()->fromArray($data,NULL,'A1');
$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');


    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$filename.'"');
    header('Cache-Control: max-age=0');

    ob_end_clean();
    //$objWriter->save('php://output');


$writer->save('php://output');

?>