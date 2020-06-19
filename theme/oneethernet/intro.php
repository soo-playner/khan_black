
<style>
body{}
.container {
	margin:0;
	padding:0;
	width:100%;
	display:block; 
	height:100vh; 
	background:url('<?=G5_THEME_URL?>/_images/launcher.png') no-repeat 50% 50%;
	background-size:cover;
}

.adm_title{background:#f9a62e;color:white;padding:5px 30px;font-size:1.2em; border-radius:25px;margin-bottom:20px;display:inline-block}

#btnDiv {
  display: none;
  text-align: center;
  position:absolute;
  bottom:25%;
  width:100%;
  z-index:1000;
}
.btn_ly{width:85%;
  text-align:center;
  margin:0 auto;}

#myProgress {
  width: 100%;
}

#myBar {
  width: 1%;
  height: 3px;
  background-color: #00b6d3;
}

.animate-bottom {
  position: relative;
  -webkit-animation-name: animatebottom;
  -webkit-animation-duration: 1s;
  animation-name: animatebottom;
  animation-duration: 1s
}

@-webkit-keyframes animatebottom {
  from { bottom:-10%; opacity:0 } 
  to { bottom:25%; opacity:1 }
}

@keyframes animatebottom { 
  from{ bottom:-10%; opacity:0 } 
  to{ bottom:25%; opacity:1 }
}



@media screen and (max-width: 1600px) {
	
}

@media screen and (max-width: 1200px) {

}

@media screen and (max-width: 1024px) {
	
}
@media screen and (max-width: 993px) {

}

@media screen and (max-width: 767px){
	
}

@media screen and (max-width: 736px) {
	.container {max-width: 100%;}
}


@media (max-width: 414px) {
	
}

@media (max-width: 650px) {
	
}

@media (max-width: 768px) {
}

@media (min-width: 767px) {
	body{background:#0b0c13}
	.container{width:550px;margin:0 auto;}
	#btnDiv{width:550px;}
}
</style>



<script >
	var myVar;
	var maintenance = "<?=$maintenance?>";

	function myFunction() {
	  move()
	}

	function showPage() {
	  document.getElementById("myBar").style.display = "none";
	  document.getElementById("btnDiv").style.display = "block";
	}

	function move() {
	  var elem = document.getElementById("myBar");   
	  var width = 1;
	  var id = setInterval(frame, 5);
	  function frame() {
		if (width >= 100) {
		  clearInterval(id);
		  //showPage();

		  if(maintenance == 'N'){
			showPage();
		  }
		} else {
		  width++; 
		  elem.style.width = width + '%'; 
		}
	  }
	}
</script>

<html>


<body onload="myFunction();" style="margin:0;">

<div class="container">
	<div id="myBar"></div>
	
	<div id="btnDiv" class="animate-bottom">
		<div class='btn_ly'>
	  		<a href="/bbs/login_pw.php" class="btn btn_wd btn_primary login_btn">LOG IN</a>
	  		<a href="/bbs/register_form.php" class="btn btn_wd btn_secondary signup_btn">SIGN UP</a>
		</div>
	</div>
</div>
	


<!--
	<section id="wrapper" class="bg_white">
		<div class="v_center">
			<div class="login_wrap">
				<div class="logo_login_div">
					<img src="<?=G5_THEME_URL?>/_images/login_logo.png" alt="Haz logo">
					<?if(strpos($url,'adm')){echo "<br><span class='adm_title'>For Administrator</span>";}?>
				</div>


				<form name="flogin" action="<?php echo $login_action_url ?>" method="post">
					  <input type="hidden" id="url" name="url" value="<?=$url?>">
					<div>
						<label for="u_name"><span data-i18n="login.유저네임">Username</span></label>
						<input type="text" name="mb_id" id="u_name" />
					</div>
					<div>
						<label for="u_pw"><span data-i18n="login.비밀번호">Password</span></label>
						<input type="password" name="mb_password" id="u_pw" style="line-height:22px;" />
					</div>

				
					<div class="find_pw_div">
						<input type="button" value="Login" class="btn_basic_block" onclick="flogin_submit();" >
					
						<a href="<?=G5_THEME_URL?>/forgot_password.php">비밀번호 찾기</a>
					</div>
					
					<a href="index.php" class="fp_img_a">
						<img class="fp_img" src="<?=G5_THEME_URL?>/_images/login_fingerprint.png" alt="지문">
					</a>
					
					<?if(!strpos($url,'adm')){?>
					<div class="login_btn_bottom">
						<a href="/bbs/register_form.php" class="btn_basic_block btn_navy"><span data-i18n="login.신규 회원 등록하기">Create new account</span></a>
					
						<a href="mailto:cs@v7wallet.com" class="support_a">Contact Support</a>
					</div>
					<?}?>
				</form>
			</div>

		</div>
	</section>
-->

</html>

