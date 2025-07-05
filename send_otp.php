<?php
include('smtp/PHPMailerAutoload.php');
session_start();
include 'db.php';
$email=$_POST['email'];
$name=$_POST['name'];
$password=$_POST['password'];
$otp=rand(11111,99999);
$html="Your otp verification code is ".$otp;
$_SESSION['email']=$email;
if(smtp_mailer($email,'OTP Verification',$html,$otp)==0){
 echo "no";
}
else{
    echo "yes";
}


function smtp_mailer($to,$subject, $msg,$otp){
	// require_once("smtp/class.phpmailer.php");
	// $mail = new PHPMailer(); 
	// $mail->IsSMTP(); 
	// $mail->SMTPDebug = 1; 
	// $mail->SMTPAuth = true; 
	// $mail->SMTPSecure = 'TLS'; 
	// $mail->Host = 'smtp.gmail.com';
	// $mail->Port = 587; 
	// $mail->IsHTML(true);
	// $mail->CharSet = 'UTF-8';
	// $mail->Username = 'ag284533@gmail.com';
    // $mail->Password = 'leoymcnjmqrzipef';
	// $mail->SetFrom("ag284533@gmail.com");
	// $mail->Subject = $subject;
	// $mail->Body =$msg;
	// $mail->AddAddress($to);
	// if(!$mail->Send()){
	// 	return 0;
	// }else{
	// 	return 1;
	// }

    $mail = new PHPMailer(true);
    try {
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587; 
        $mail->IsHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Username = 'ag284533@gmail.com';
        $mail->Password = 'leoymcnjmqrzipef';
        $mail->setFrom('ag284533@gmail.com', 'Farmer Portal');
        $mail->Subject = $subject;
        $mail->Body    = $msg;
        $mail->AddAddress($to);
        $mail->SMTPOptions=array('ssl'=>array(
            'verify_peer'=>false,
            'verify_peer_name'=>false,
            'allow_self_signed'=>false
        ));
        if(!$mail->Send()){
            	return 0;
        }else{
            $_SESSION['otp']=$otp;
            return 1;
        }
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>