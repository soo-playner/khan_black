<?php
$sub_menu = "600100";
include_once('./_common.php');
include_once('../admin.head.php');
include_once('./bonus_inc.php');

auth_check($auth[$sub_menu], 'r');
$token = get_token();

?>

<style>
    table {width:100%;}
    table tbody td, table tfoot td{border:0;}
    table tbody td { height:40px;border-bottom:1px solid #f5f5f5} 
    table tbody tr:first-child td{background:#efefef;padding-bottom:20px;border-bottom:2px solid #333;}  
    table tbody tr:nth-child(2) td{padding-top:30px;} 
    tfoot td{background:white}
    .bonus_input{box-shadow:none;text-shadow:none;padding:10px;border:0;background: whitesmoke}
	.btn_ly{text-align:center;}
    .btn_confirm.btn_submit:hover{background:black !important;}
    .bonus_source{height:36px;}
    hr{height:1px;float:left;width:60%;display:block;background:#333;margin:20px 0;}
</style>

<form name="allowance" id="allowance" method="post" action="./bonus_config_update.php" onsubmit="return frmconfig_check(this);" >
<div class="tbl_head02 tbl_wrap">
    <table >
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
	<p> 수당 설정 </p>
    <tr>
        <th scope="col" width="30">No</th>
        <th scope="col" width="40">사용설정</th>
        <th scope="col" width="80">수당명</th>	
        <th scope="col" width="80">수당코드</th>
        
        <th scope="col" width="80">수당한계 (%)</th>
		<th scope="col" width="100">수당비율 (%)</th>
		<th scope="col" width="80">수당지급제한(대수)</th>
        <th scope="col" width="100">수당지급방식</th>
        <th scope="col" width="100">수당설명</th>
    </tr>
    </thead>

    <tbody>
    <?for($i=0; $row=sql_fetch_array($list); $i++){?>
    <tr class='<?if($i == 0){echo 'first';}?>'>
   
    <td style="text-align:center"><input type="hidden" name="idx[]" value="<?=$row['idx']?>"><?=$row['idx']?></td>
    <td style="text-align:center"><input type='checkbox' class='checkbox' name='check' <?php echo $row['used']==1?'checked':''; ?>>
        <input type="hidden" name="used[]" class='used' value="<?=$row['used']?>">
    </td>
    <td style="text-align:center"><input class='bonus_input' name="name[]"  value="<?=$row['name']?>"></input></td>
    <td style="text-align:center"><input class='bonus_input' name="code[]"  value="<?=$row['code']?>"></input></td>
    
	<td style="text-align:center"><input class='bonus_input' name="limited[]"  value="<?=$row['limited']?>"></input></td>
	<td style="text-align:center"><input class='bonus_input' name="rate[]"  value="<?=$row['rate']?>"></input></td>
    <td style="text-align:center"><input class='bonus_input' name="layer[]"  value="<?=$row['layer']?>"></input></td>
    <td style="text-align:center">
        <select id="bonus_source" class='bonus_source' name="source[]">
            <?php echo option_selected(0, $row['source'], "ALL"); ?>
            <?php echo option_selected(1, $row['source'], "추천인[tree]"); ?>
            <?php echo option_selected(2, $row['source'], "바이너리[binary]"); ?>
        </select>
    </td>
    <td style="text-align:center"><input class='bonus_input' name="memo[]"  value="<?=$row['memo']?>"></input></td>
    </tr>
    <?}?>
    </tbody>
    
    <tfoot>
        <td colspan=9 height="100px" style="padding:50px 0" class="btn_ly">
            <input  style="align:center;padding:10px 30px;background:cornflowerblue;" type="submit" class="btn btn_confirm btn_submit" value="저장하기" id="com_send"></input>
        </td>
    </tfoot>
</table>

</div>
</form>

<script>

    function frmconfig_check(f){
        
    }

    $(document).ready(function(){

        $(".checkbox" ).on( "click",function(){
            if($("input:checkbox[name='check']").is(":checked") == true){
                console.log( $(this).next().val() );
                $(this).next().val(1);
            }else{
                $(this).next().val(0);
            }
        });
        
    });

</script>
</div>

<?php
include_once ('../admin.tail.php');
?>