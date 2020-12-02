/*
$(function(){
		 
		//파일올리기
		 var fileTarget = $('.filebox .upload-hidden'); 
		 fileTarget.on('change', function(){ // 값이 변경되면 
			 if(window.FileReader){ // modern browser 
				 var filename = $(this)[0].files[0].name; 
			 } else { // old IE 
				 var filename = $(this).val().split('/').pop().split('\\').pop(); // 파일명만 추출 
			 } // 추출한 파일명 삽입 
			 $(this).siblings('.upload-name').val(filename); 
		 }); 
});
*/
var debug = '<?=$is_debug?>';

function move(bonus_per,main = 0) {
	var total_bonus_point = 0;
	if(bonus_per != undefined){
		var total_bonus_point = bonus_per;
	}

	if(debug){console.log(' total_bonus_point  =  '+total_bonus_point);}

	if(total_bonus_point >= '100'){total_bonus_point = '100'};

	var elem = document.getElementById("total_B_bar");
	var width = 1;
	var id = setInterval(frame, 20);
	function frame() {
		if(width >= 100){
			$('#total_B_bar').addClass('deg100');
			$('#total_B_bar').addClass('active');
			$('.bonus_per').addClass('active')
		}

		if(width >= 75){
			$('#total_B_bar').addClass('deg75');
		}
		if(width >= 50){
			$('#total_B_bar').addClass('deg50');
		}
		if(width >= 25){
			$('#total_B_bar').addClass('deg25');
		}

		if (width >= total_bonus_point) {
			clearInterval(id);

			// 수당초과시 팝업 
			if(total_bonus_point > 75 && main == 1){dialogModal('Total Bonus', 'Total Bonus more than 75%', 'warning');}

		} else {
			width++;
			elem.style.width = width + '%';
		}
	}
}

function go_to_url(target){
	location.href="/page.php?id="+ target;
}

function getCookie(name) {

	var i, x, y, ARRcookies = document.cookie.split(";");
	for (i = 0; i < ARRcookies.length; i++) {

			x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));

			y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);

			x = x.replace(/^\s+|\s+$/g, "");

			if (x == name) {

					return unescape(y);

			}
	}

}

function setCookie(name, value, days) {
	if (days) {
			var date = new Date();

			date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));

			var expires = "; expires=" + date.toGMTString();
	} else {
		var expires = "";
	}
		document.cookie = name + "=" + value + expires + "; path=/";
	}

	
function commonModal(title, htmlBody, bodyHeight){
	$('#commonModal').modal('show');
	$('#commonModal .modal-header .modal-title').html(title);
	$('#commonModal .modal-body').html(htmlBody);
	if(bodyHeight){
		$('#commonModal .modal-body').css('height',bodyHeight+'px');
	} 
	$('#closeModal').focus();
}

function confirmModal(title, htmlBody, bodyHeight){
	$('#confirmModal').modal('show');
	$('#confirmModal .modal-header .modal-title').html(title);
	$('#confirmModal .modal-body').html(htmlBody);
	if(bodyHeight){
		$('#confirmModal .modal-body').css('height',bodyHeight+'px');
	} 
	$('#confirmModal').focus();
}

// function serviceModal(){
// 	$('#commonModal').modal('show');
// 	$('#commonModal .modal-header .modal-title').html('Service is being prepared');
// 	$('#commonModal .modal-body').html('this Service Will be avaiable shortly');
// 	$('#commonModal .modal-body').css('height','auto');
	
// 	$('#closeModal').focus();
// }

// function LoginModal(){
// 	$('#commonModal').modal('show');
// 	$('#commonModal .modal-header .modal-title').html('Plese check login');
// 	$('#commonModal .modal-body').html('this Service Will be avaiable with login');
// 	$('#commonModal .modal-body').css('height','auto');
	
// 	$('#closeModal').focus();
// }

// function purchaseModal(title, htmlBody, category){
	
// 	$('#purchaseModal').modal('show');
// 	$('#purchaseModal .modal-header .modal-title').html(title);

// 	if(category == 'success'){
// 		$('#purchaseModal .modal-body').html("<div class=chkimg><img src='"+g5_url+"/img/comform_chk.gif'></div>" + htmlBody);
// 		$('#purchaseModal .modal-footer').html("<button type='button' class='btn btn-secondary' data-dismiss='modal' id='modal_return_url' onclick='dimHide();'>Close</button>");
// 	}
// 	else if(category == 'confirm'){
// 		$('#purchaseModal .modal-body').html("<div class=chkimg><img src='"+g5_url+"/img/notice.png'></div>" + htmlBody);
// 		$('#purchaseModal .modal-footer').html("<button type='button' class='btn btn-secondary' data-dismiss='modal' onclick='dimHide();'>Cancle</button> <button type='button' class='btn btn-secondary' id='modal_confirm' >OK</button>");
// 	}
// 	else if(category == 'failed'){
// 		$('#purchaseModal .modal-body').html("<div class=chkimg><img src='"+g5_url+"/img/notice_pop.gif'></div>" + htmlBody);
// 		$('#purchaseModal .modal-footer').html("<button type='button' class='btn btn-secondary' data-dismiss='modal' id='modal_return_back' onclick='dimHide();'>Close</button>");
// 	}
// 	$('#purchaseModal').focus();
	
// }

function dialogModal(title, htmlBody, category){
	
	$('#dialogModal').modal('show');
	$('#dialogModal .modal-header .modal-title').html(title);

	if(category == 'success'){
		$('#dialogModal .modal-body').html("<div class=chkimg><img src='"+g5_url+"/img/comform_chk.gif'></div>" + htmlBody);
		$('#dialogModal .modal-footer').html("<button type='button' class='btn wd btn_defualt closed' data-dismiss='modal' id='modal_return_url' onclick='dimHide();'>Close</button>");
	}
	else if(category == 'confirm'){
		$('#dialogModal .modal-body').html("<div class=chkimg><img src='"+g5_url+"/img/notice.png'></div>" + htmlBody);
		$('#dialogModal .modal-footer').html("<button type='button' class='btn btn-secondary cancle' data-dismiss='modal' onclick='dimHide();'>Cancle</button> <button type='button' class='btn btn-secondary confirm' id='modal_confirm' >OK</button>");
		
	}else if(category == 'warning'){
		$('#dialogModal .modal-body').html("<div class=chkimg><img src='"+g5_url+"/img/notice.png'></div>" + htmlBody);
		$('#dialogModal .modal-footer').html("<button type='button' class='btn wd btn_defualt closed' data-dismiss='modal' id='modal_return_url' onclick='dimHide();'>Close</button>");
	}
	else if(category == 'failed'){
		$('#dialogModal .modal-body').html("<div class=chkimg><img src='"+g5_url+"/img/notice_pop.gif'></div>" + htmlBody);
		$('#dialogModal .modal-footer').html("<button type='button' class='btn wd btn_defualt closed' data-dismiss='modal' id='modal_return_back' onclick='dimHide();'>Close</button>");
	}

	$('#dialogModal').focus();
	
}
