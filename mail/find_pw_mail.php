<?php
include_once("../common.php");

$mb_id = $_POST['mb_id'];
$to_email = $_POST['user_email'];
$auth_number = $_POST['auth_number'];

$sql = "SELECT * FROM g5_member WHERE mb_id = '{$mb_id}' AND mb_email = '{$to_email}'";
$result = sql_query($sql);
$cnt = sql_num_rows($result);
if($cnt < 1 ){
    echo json_encode(array("code"=>"00002"));
    return;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "./PHPMailer.php";
require "./SMTP.php";
require "./Exception.php";

$mail = new PHPMailer(true);

try {

    // 서버세팅
    $mail -> SMTPDebug = 2;    // 디버깅 설정
    $mail -> isSMTP();        // SMTP 사용 설정

    $mail -> Host = "smtp.gmail.com";                // email 보낼때 사용할 서버를 지정
    $mail -> SMTPAuth = true;                        // SMTP 인증을 사용함
    $mail -> Username = "willsoftkr@gmail.com";    // 메일 계정
    $mail -> Password = "willsoft0780";                // 메일 비밀번호
    $mail -> SMTPSecure = "ssl";                    // SSL을 사용함
    $mail -> Port = 465;                            // email 보낼때 사용할 포트를 지정
    $mail -> CharSet = "utf-8";                        // 문자셋 인코딩

    // 보내는 메일
    // $mail -> setFrom("willsoftkr@gmail.com", "The Binary");
    $mail -> setFrom("mcloud@gmail.com", "M cloud");

    // 받는 메일
    $mail -> addAddress($to_email, $to_id);

  
    // 메일 내용
    $mail -> isHTML(true);                                               // HTML 태그 사용 여부
    $mail -> Subject = "M cloud FIND PASSWORD";              // 메일 제목
    // $mail -> Body = $auth_md5;    // 메일 내용

    $hostname=$_SERVER["HTTP_HOST"];

    // 본문 이미지 첨부 및 내용
    $image = '../theme/mcloud/img/logo.png';
    $mail->AddEmbeddedImage($image, "keyImage");
    $mail->MsgHTML("<div><p><img src='cid:keyImage'
    style='max-width:80%;
    margin: 0 auto 20px;
    display:block;
    margin-top: 20px;'></p><br />
    <div
    style='width: 50%;

    margin: 0 auto 20px;
    display: block;
    margin-top: 20px;'>인증번호는 [ $auth_number ] 입니다.</div></div>");



    // Gmail로 메일을 발송하기 위해서는 CA인증이 필요하다.
    // CA 인증을 받지 못한 경우에는 아래 설정하여 인증체크를 해지하여야 한다.
    $mail -> SMTPOptions = array(
        "ssl" => array(
              "verify_peer" => false
            , "verify_peer_name" => false
            , "allow_self_signed" => true
        )
    );
    // 메일 전송
    $mail -> send();

} catch (Exception $e) {}

?>
