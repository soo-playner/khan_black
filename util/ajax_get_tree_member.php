<?php
include_once('./_common.php');

$sql = "select * from {$g5['member_table']} where  mb_id in (select c_id from g5_member_bclass where mb_id='".$member['mb_id']."')  ";

if ($binary_seach) {
	$sql .= " and mb_id like '{$binary_seach}%' ";
}
$sql .= " order by mb_name";

$result = sql_query($sql);
?>

<style>
	.result_member:hover{
		background:#0079d3;
		color:white;
	}
	.result_btn{
		padding:10px;
		top:305px;
	}
</style>

		<table style="width:100%">
			<tr>
				<td height="20" style="padding-left:10px;line-height:20px;border-bottom:2px solid #ccc;"><b>RESEULT</b></td>
			</tr>
			<?
			for ($i=0; $row=sql_fetch_array($result); $i++) {
			?>
				<tr>
					<td bgcolor="#f5f5f5"  style="padding:10px;border-bottom:4px solid white;" class="result_member">
						<span style="cursor:pointer" onclick="go_member('<?=$row['mb_id']?>');"><?=$row['mb_name']?> [<?=$row['mb_id']?>]</span>
					</td>
				</tr>
			<?
			 }
				if ($i == 0)
					echo "<tr><td height=30 align=center>Not exist matching member.</td></tr>";
			?>
			</table>