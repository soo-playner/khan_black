<?php
include_once("../common.php");

$to_email = $_POST['user_email'];

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
    // $mail -> Username = "willsoftkr@gmail.com";    // 메일 계정
    // $mail -> Password = "willsoft0780";                // 메일 비밀번호
    $mail -> Username = "thebinarybinary@gmail.com";    // 메일 계정
    $mail -> Password = "willsoft0780";                // 메일 비밀번호
    $mail -> SMTPSecure = "ssl";                    // SSL을 사용함
    $mail -> Port = 465;                            // email 보낼때 사용할 포트를 지정
    $mail -> CharSet = "utf-8";                        // 문자셋 인코딩

    // 보내는 메일
    // $mail -> setFrom("willsoftkr@gmail.com", "The Binary");
    $mail -> setFrom("thebinarybinary@gmail.com", "D fine");

    // 받는 메일
    $mail -> addAddress($to_email, $to_id);

    //인증해시값
    $dateTime = new DateTime("now", new DateTimeZone("Asia/Seoul"));
    $date_time = $dateTime->format("Y-m-d H:i:s");
    $auth_md5 = hash("sha256", $date_time.$to_email);
    

    // 메일 내용
    $mail -> isHTML(true);                                               // HTML 태그 사용 여부
    $mail -> Subject = "D fine CERTIFICATION";              // 메일 제목
    // $mail -> Body = $auth_md5;    // 메일 내용


    // 본문 이미지 첨부 및 내용
    $image = '../theme/binary/_images/logo.png';
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
    margin-top: 20px;'>Click<a href='https://thebinary.io/mail/auth_mail.php?hash=$auth_md5'> HERE </a>to complete authentication</div></div>");



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

    $sql_select = "SELECT * FROM auth_email WHERE email = '$to_email' ORDER BY auth_start_date DESC LIMIT 0,1";
    $result_select = sql_query($sql_select);
    $count = sql_num_rows($result_select);
    
    if($count > 0){
        $row_select = sql_fetch_array($result_select);
        $sql_update = "UPDATE auth_email SET auth_check = '2' WHERE id ='{$row_select['id']}'";
        sql_query($sql_update);
    }

    // DB 저장
    $sql_insert = "INSERT INTO auth_email(email, auth_md5, auth_start_date) VALUES('$to_email','$auth_md5','$date_time')";
    $result_insert = sql_query($sql_insert);

} catch (Exception $e) {echo $e;}

?>
