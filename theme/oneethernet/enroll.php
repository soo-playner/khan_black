<?
$menubar = 1;
$email_auth = 0;
$phone_auth = 0;


include_once(G5_THEME_PATH.'/_include/head.php');
include_once(G5_THEME_PATH.'/_include/gnb.php');


if($_GET['recom_referral'])
	$recom_sql = "select mb_id from g5_member where mb_no = '{$_GET['recom_referral']}'";
	$recom_result = sql_fetch($recom_sql);

	$mb_recommend = $recom_result['mb_id'];
?>


<script type="text/javascript">
	var captcha;
	var key;
	var verify = false;
	var recommned = "<?=$mb_recommend?>";
	var recommend_search = false;

	if(recommned){
		recommend_search = true;
	}
	//console.log(recommend_search);

$(function(){

	/*초기설정*/
	//$('.agreement_ly').hide();
	$('#verify_txt').hide();


	/* 핸드폰 SMS 문자인증 사용 */

	$('#nation_number').on('change',function(e){
		$('#reg_mb_hp').val( $(this).val() ) ;
	});

	var phone_auth = "<?=$phone_auth?>";
	if( phone_auth > 0){
		$('.verify_phone').hide();

		//SMS발송
		$('#sendSms').on('click', function(e){
			if(!$('#reg_mb_hp').val()){
				commonModal('Mobile authentication','<p>Please enter your Mobile Number</p>',80);
				return;
			}
			var reg_mb_hp = + ($('#reg_mb_hp').val().replace(/-/gi,''));
			$.ajax({
				url: '/bbs/register.sms.verify.php',
				type: 'post',
				async: false,
				data: {
					"nation_no": $('#nation_number').val(),
					"mb_hp": reg_mb_hp
				},
				dataType: 'json',
				success: function(result) {
					// console.log(result);
					smsKey = result.key;
					commonModal('SMS authentication','<p>Sent a authentication code to your Mobile.</p>',80);
				},
				error: function(e){
					console.log(e);
				}
			});
		});
	}


	/*이메일 체크*/
	 validateEmail = function (email) {
		var email = email;
		var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;

		if (email == '' || !re.test(email)) {
			alert("올바른 이메일 주소를 입력하세요")
			return false;
		}
	}


	/*메일발송*/
	$('#sendMail').on('click', function(e){
		//console.log('sendmail');
		if(!$('#reg_mb_email').val()){
			commonModal('Mail authentication','<p>Please enter your mail</p>',80);
			return;
		}
		$.ajax({
			url: '/bbs/register.mail.verify.php',
			type: 'GET',
			async: false,
			data: {
				"mb_email": $('#reg_mb_email').val()
			},
			dataType: 'json',
			success: function(result) {
				console.log(result);
				key = result.key;

			commonModal('Mail authentication','<p>Sent a authentication code to your mail.</p>',80);
			},
			error: function(e){
				console.log(e);
			}
		});
	});

	// 메일 인증 코드 성공
	$('#vCode').on('change', function(e){
		console.log( $('#vCode').val().trim() );
		if(key == sha256( $('#vCode').val().trim()) ){

			console.log( "verify OK" );
			verify = true;
			$('#verify_txt').show();
			$('#reg_mb_email').css('background-color','#ccc').prop('readonly', true);;

		}else{
			commonModal('Do not match','<p>Email verification code is incorrect. Please enter the correct code</p>',80);
		}
	});

	// 핀번호 (오직 숫자만)
	// document.getElementById('reg_tr_password').oninput = function(){
	// 	// if empty
	// 	if(!this.value) return;
	//
	// 	// if non numeric
	// 	let isNum = this.value[this.value.length - 1].match(/[0-9]/g);
	// 	if(!isNum) this.value = this.value.substring(0, this.value.length - 1);
	//
	// 	chkPwd_2($('#reg_tr_password').val(),$('#reg_tr_password_re').val());
	// }
	//
	// document.getElementById('reg_tr_password_re').oninput = function(){
	// 	// if empty
	// 	if(!this.value) return;
	//
	// 	// if non numeric
	// 	let isNum = this.value[this.value.length - 1].match(/[0-9]/g);
	// 	if(!isNum) this.value = this.value.substring(0, this.value.length - 1);
	//
	// 	chkPwd_2($('#reg_tr_password').val(),$('#reg_tr_password_re').val());
	// }


	// 아이디 중복 체크
	$('#id_check').click(function(){
		var registerId = $('#reg_mb_id').val();
		if(registerId.length < 5){
			dialogModal("ID CHECK", "Please put 5 letters or more", "failed");
		}else{
			$.ajax({
					type : "POST",
					dataType : "json",
					url : "/bbs/register_check_id.php",
					data : {
						"registerId" : registerId
					},
					success : function(res){
						if(res.code == '000'){
							dialogModal("ID CHECK", res.response, 'failed');
						}else{
							dialogModal("ID CHECK", res.response, 'success');
						}
					}
			});
		}
	});

	$("#reg_mb_id").bind("keyup",function(){
	 re = /[~!@\#$%^&*\()\-=+_']/gi;
	 var temp=$("#reg_mb_id").val();
	 if(re.test(temp)){ //특수문자가 포함되면 삭제하여 값으로 다시셋팅
	 $("#reg_mb_id").val(temp.replace(re,"")); } });



	/*이용약관동의*/
	$('.agreement_ly').click(function() {
		if($("#agree").is(":checked") == true){
			$('#agree').prop("checked", false);
			$('.agreement_ly > span').css('text-decoration','none') ;
		}else{
			$('#agree').prop("checked", true);
			$('.agreement_ly > span').css('text-decoration','underline') ;
		}
	});


	$('#reg_mb_password').on('keyup',function(e){
		chkPwd_1($('#reg_mb_password').val());
	});
	$('#reg_mb_password_re').on('keyup',function(e){
		chkPwd_1($('#reg_mb_password').val(), $('#reg_mb_password_re').val());
	});

	$('#wallet_addr_check').click(function(){
		var wallet_addr_len = $('#wallet_addr').val().length;
		console.log(wallet_addr_len);
		if(wallet_addr_len <= 40){
			commonModal('Mobile authentication','<p>Please check your Wallet Address again</p>',80);
		}
	});

});

/* 패스워드 확인*/
function chkPwd_1(str,str2){
	var pw = str;
	var pw_rule =0;
	var num = pw.search(/[0-9]/g);
	var eng = pw.search(/[a-z][A-Z]/ig);
	//var eng_large = pw.search(/[A-Z]/ig);
	var spe = pw.search(/[`~!@@#$%^&*|₩₩₩'₩";:₩/?]/gi);

	if(pw.length < 4){
		$("#pm_1").attr('class','x_li');
	}else{
		$("#pm_1").attr('class','o_li');
		pw_rule += 1;
	}

	if(eng < 0 || num < 0){
		$("#pm_3").attr('class','x_li');
	}else{
		$("#pm_3").attr('class','o_li');
		pw_rule += 1;
	}


	// if(spe < 0 ){
	// 	$("#pm_3").attr('class','x_li');
	// }else{
	// 	$("#pm_3").attr('class','o_li');
	// 	pw_rule += 1;
	// }


	if( pw_rule == 2 && str == str2){
		$("#pm_5").attr('class','o_li');
		pw_rule += 1;
	}else{
		$("#pm_5").attr('class','x_li');
	}

	if(pw_rule == '3'){
		return true;
	}else{
		return false;
	}
}

function chkPwd_2(str,str2){

	if(str.length < 6){
		$("#pt_1").attr('class','x_li');
	}else{
		$("#pt_1").attr('class','o_li');
	}

	if(str == str2){
		$("#pt_2").attr('class','o_li');
	}else{
		$("#pt_2").attr('class','x_li');
	}

	if(str == str2 && str.length == 6 && str2.length == 6){
		return true;
	}else{
		return false;
	}
}


	/*추천인/ 센터멤버 등록*/
	function getUser(etarget,type){
	var target = etarget;
	if(type  == 1){
		var target_type = "#referral";
	}else{
		var target_type = "#center";
	}
	//console.log(target + ' === ' + type);

	$.ajax({
			type:'POST',
			url:'/util/ajax.recommend.user.php',
			data: {
				mb_id : $(target).val(),
				type : type
			} ,
			success: function(data){
				var list = JSON.parse(data);

				if(list.length > 0){
					$(target_type).modal('show');
					var vHtml = $('<div>');
					$.each(list, function( index, obj ) {
						vHtml.append($('<div>').addClass('user').html(obj.mb_id));
					});

					$(target_type + ' .modal-body').html(vHtml.html());

					$(target_type + ' .modal-body .user').click(function(){
						console.log('user click');
						$( target + ' .modal-body .user').removeClass('selected');
						$(this).addClass('selected');
					});

					$(target_type + ' .modal-footer #btnSave').click(function(){
						//console.log('user select : ' + $( target_type + '.modal-body .user.selected').html());
						recommend_search = true;
						console.log(recommend_search);
						$(target).val($( target_type + ' .modal-body .user.selected').html());
						$(target_type).modal('hide');
					});

				}else {

					commonModal('Notice','MEMBER NOT FOUND',80);
				}
			}
		});

	} ///*추천인등록*/



	// submit 최종 폼체크
	function fregisterform_submit(){
		var f = $('#fregisterform')[0];
		//console.log(recommend_search);
		/*
		if(key != sha256($('#vCode').val())){
		 	commonModal('Do not match','<p>Please enter the correct code</p>',80);
		 	return false;
		}
		*/

		console.log( $('#reg_mb_email').val() + '/' + $('#reg_mb_email_re').val() );


		if (f.mb_password.value != f.mb_password_re.value) {
			commonModal('check password','<strong> Password does not match </strong>',80);

			f.mb_password_re.focus();
			return false;
		}

		if (f.mb_password.value.length > 0) {
			if (f.mb_password_re.value.length < 3) {
				commonModal('check password','<strong>password must contain 6 to 8 characters long</strong>',80);

				f.mb_password_re.focus();
				return false;
			}
		}

		if(!chkPwd_1($('#reg_mb_password').val(),$('#reg_mb_password_re').val())){
			commonModal('Check password Rule','<strong> Login Password does not match password Rule.</strong>',80);
			return false;
		}

		// if(!chkPwd_2($('#reg_tr_password').val(),$('#reg_tr_password_re').val())){
		// 	commonModal('Check password Rule','<strong> Transaction Password does not match password Rule.</strong>',80);
		// 	return false;
		// }

		/* 메일인증
		if(verify == false){
			commonModal('check e-mail verifiy','<strong>Enter the verification code to verify your email address</strong>',80);
			return false;
		}
		*/


		if( $('#reg_mb_email').val() != $('#reg_mb_email_re').val() ){
			commonModal('Check Email Address','<strong> Email Address does not match. </strong>',80);
			return false;
		}

		if (f.mb_recommend.value =='' || f.mb_recommend.value =='undefined') {
			commonModal('check recommend','<strong>check recommend.</strong>',80);
			return false;
		}


		if(!recommend_search){
			commonModal('Please check recommend search Button','<strong>Please check recommend search Button and choose recommend.</strong>',80);
			return false;
		}

		if (f.mb_id.value == f.mb_recommend.value) {
			commonModal('check recommend','<strong> can not recommend self. </strong>',80);
			f.mb_recommend.focus();
			return false;
		}

		if(!$('#agree').prop('checked')){
			commonModal('check the policy agreement!!','<strong>check the policy agreement!!</strong>',80);
			return false;
		}

		f.submit();

	}

	/*이용약관*/
	/*
	function agreementModal(title ){
		$('#agreement').modal('show');
		$('#agreement .modal-header .modal-title').html(title);
		$('#agreement .modal-body').load('<?=G5_THEME_URL?>/policy.html');
		$('#agreement .modal-body').css('height','auto');
		$('#closeModal').focus();
	}
	$(document).on('click','.agreeement_show',function(e) {
		agreementModal('agreement');

		$('#agreement .yes')	.on('click',function(e) {
			$('.agreement_ly').show();
			$('.agreement_btn').hide();
			$('#agree').attr("checked", true);
		});
	});

	$(document).on('click','#agree',function(e) {
		$('.agreement_btn').show();
	});
	*/

</script>



<div class="v_center">

	<div class="enroll_wrap">
		<form id="fregisterform" name="fregisterform" action="/bbs/register_form_update.php" method="post" enctype="multipart/form-data" autocomplete="off">

			<!-- <div>
				<select id="nation_number" name="nation_number" required >
					<option value="country" data-i18n="signUp.국가를 선택해주세요" >Select Country</option>
					<option value="1">001 - USA</option>
					<option value="81">081 - Japan</option>
					<option value="82">082 - Korea</option>
					<option value="84">084 - Vietnam</option>
					<option value="86">086 - China</option>
					<option value="62">062 - Indonesia</option>
					<option value="63">063 - Philippines</option>
					<option value="66">066 - Thailand</option>
				</select>
			</div> -->

			<!-- <section class='referzone blue'>
				<label>Center's Username</label>
				<div class="btn_input_wrap">
					<input type="text" placeholder="" name='mb_center' id="reg_mb_center" data-i18n='[placeholder]signUp.센터 이름' value="<?=$mb_center?>" />
					<div class='in_btn_ly2'>
						<button type='button' class="btn_round check b_white" onclick="getUser('#reg_mb_center',2);" style="width:100px;"><span data-i18n="signUp.검색">Search</span></button>
					</div>

				</div>
			</section> -->

			<section class='referzone'>
			<label class='text-white'>Referrer's Username</label>
				<div class="btn_input_wrap">
					<input type="text" name="mb_recommend" id="reg_mb_recommend"  value="<?=$mb_recommend ?>" placeholder="Referrers Username"  required data-i18n='[placeholder]signUp.추천인 이름'/>
					<div class='in_btn_ly2'>
						<button type='button' class="btn_round check " onclick="getUser('#reg_mb_recommend',1);"  style="width:100px;"><span data-i18n="signUp.검색">Search</span></button>
					</div>
					<!--<a href="javascript:getUser('#reg_mb_recommend',1);" class="btn_round check" data-i18n='register.추천인 검색'>Search Referrer</a>-->
				</div>
			</section>

			<p class="check_appear_title mt40"><span data-i18n='signUp.일반정보'>General Information</span></p>
			<div>
				<input type="text" minlength="5" maxlength="20" name="mb_id"  id="reg_mb_id"  placeholder="" data-i18n='[placeholder]signUp.아이디'/>
				<div class='in_btn_ly'><input type="button" id='id_check' class='btn_round check' value="ID Check" data-i18n='[value]signUp.중복확인'></div>
			</div>

			<ul class="clear_fix pw_ul">
				<li>
					<input type="password" name="mb_password" id="reg_mb_password"  minlength="4" placeholder="Login Password" data-i18n='[placeholder]signUp.로그인 비밀번호'/>
					<input type="password" name="mb_password_re" id="reg_mb_password_re" minlength="4" placeholder="Confirm login password" data-i18n='[placeholder]signUp.로그인 비밀번호 확인'/>

					<strong ><span class='mb10' style='display:block' data-i18n='signUp.강도 높은 비밀번호 설정 조건' >Your password must contain</span></strong>
					<ul>
						<li class="x_li" id="pm_1" data-i18n='signUp.4자 이상 20자 이하' >4 characters or more</li>
						<li class="x_li" id="pm_3" data-i18n='signUp.숫자+영문' >Digits + Characters</li>
						<!--<li class="x_li" id="pm_4" data-i18n='register.특수 기호' >Special Characters</li>-->
						<li class="x_li" id="pm_5" data-i18n='signUp.비밀번호 비교' >Compare Password</li>
					</ul>
				</li>
				<!-- <li>
					<input type="password" minlength="6" maxlength="6" id="reg_tr_password" name="reg_tr_password" placeholder="Pin-Code" data-i18n='[placeholder]signUp.핀코드'/>
					<input type="password" minlength="6" maxlength="6" id="reg_tr_password_re" name="reg_tr_password_re" placeholder="confirm Pin-Code" data-i18n='[placeholder]signUp.핀코드확인'/>

					<strong><span class='mb10' style='display:block' data-i18n='signUp.강도 높은 핀코드 설정 조건'>Your Pin-code must contain</span></strong>
					<ul>
						<li class="x_li" id="pt_1" data-i18n='signUp.6 자리' >6 digits</li>
						<li class="x_li" id="pt_2" data-i18n='signUp.핀코드 비교' >Compare Pin-code</li>
					</ul>
				</li> -->
			</ul>



			<section id="personal">
				<div class="check_appear mt40">
				<p class="check_appear_title"><span data-i18n='signUp.개인 정보 & 인증'>Personal Information & Authentication </span></p>
				<input class="input_addr" type="text" name="first_name" id="wallet_addr" placeholder="Name" data-i18n='[placeholder]signUp.이름'/>
				<div class='in_btn_ly'><input type="button" id='wallet_addr_check' class='btn_round check' value="ID Check" data-i18n='[value]signUp.지갑 확인'></div>
				<!--<input type="text" name="last_name" placeholder="Last Name (Must match the legal name on file)" data-i18n='[placeholder]register.성 (신분증에 기록된 이름과 동일해야 함)'/>-->
				<input type="email" name="mb_email" id="reg_mb_email" onChange="validateEmail(this.value);" placeholder="Email address" data-i18n='[placeholder]signUp.이메일 주소'/>

				<input type="email" name="mb_email_re" id="reg_mb_email_re" onChange="validateEmail(this.value);" placeholder="Email address" data-i18n='[placeholder]signUp.이메일 주소 확인'/>

				<!--// 메일 인증-->
				<?if($email_auth == true){?>
					<div class="clear_fix ecode_div">
						<div class="sendbtn">
						<a href="javascript:void(0);" class="btn" id="sendMail">
							<img src="<?=G5_THEME_URL?>/_images/email_send_icon.gif" alt="이메일코드">
							<span data-i18n="signUp.이메일인증번호">Email Authentication Code</span>
						</a>
						</div>

						<input type="text" name="vCode" placeholder="Enter Email Authtication Code" id="vCode" required class="input-search" maxlength="10" data-i18n='[placeholder]register.이메일 승인 번호' >

						<p id="verify_txt" class="text_right font_green mb20" data-i18n="signUp.인증 완료">Verification Complete</p>
					</div>
					<hr>
				<?}?>



				<!-- <div>
					<span style='display:block;margin-left:10px;' class='' data-i18n='signUp.핸드폰 번호'> Phone number</span>
					<input type="text" name="mb_hp"  id="reg_mb_hp"  pattern="[09]*" placeholder="Phone number" value='' data-i18n='[placeholder]signUp.핸드폰 번호'/>
					<label class='phone_num'><i class="ri-smartphone-line"></i></label>
				</div> -->

				<!-- // 폰인증 -->
				<!-- <?if($phone_auth > 1){?>
					<div class="clear_fix ecode_div">
					<div class="verify_phone">
						<input type="text" placeholder="Enter Phone Authtication Code"/>
						<a href="javascript:void(0)" class=""  id="sendSms">
							<img src="<?=G5_THEME_URL?>/_images/email_send_icon.gif" alt="이메일코드">
							Enter Phone Authtication Code
						</a>
						</div>
					</div>
				<?}?> -->
			</section>

			<!--
			<hr>
			<div class="agreement_btn"> <button type="button" class="agreeement_show btn"><span data-i18n='register.회원가입 약관보기'>Read Terms and Conditions</span></button></div>
			-->

			<div class="mb20 agreement_ly">
				<div class="checkbox_wrap"><input type="checkbox" name='agree' id="agree" class="checkbox"><label for="agree"></label></div>
				<span data-i18n='signUp.이용약관'>
	I have read and agreed to the Terms and Conditions. I fully understood the this business and I know the NO REFUND policy. I know the change of Refferer is NOT POSSIBLE.</span>
			</div>

				<!--
				<div style="height:100px; text-align: center; background:#eee;">
					캡챠영역
				</div>
				-->

			<div class="btn2_wrap mb40" style='width:100%;height:60px'>
				<!-- <input class="btn_basic mt20" type="button" value="취소" onClick="history.back(-1);">
				<input class="btn_basic mt20" type="button" value="신규 회원 등록하기" onClick="location.href='dashboard.php'"> -->

				<input class="btn btn_double default_btn enroll_cancel_pop_open pop_open" type="button" value="Cancel" data-i18n='[value]signUp.취소'>
				<input class="btn btn_double blue" type="button" onclick="fregisterform_submit();" value="Enroll new member" data-i18n='[value]signUp.신규 회원 등록하기'>
			</div>


		</form>
	</div>

</div>
</section>


<div class="gnb_dim"></div>



	<script>
		$(function() {
			$(".top_title h3").html("<span data-i18n='title.신규 회원등록'>Create a new account</span>");
		});
	</script>


<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>
