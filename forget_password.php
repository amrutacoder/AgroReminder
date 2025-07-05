<?php
include 'db.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';
include('smtp/PHPMailerAutoload.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? $_POST['email'] : '';

    if (!empty($email)) {
        // Check if the email exists in the database
        $stmt = $conn->prepare("SELECT password FROM Users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $password = $row['password'];

            // Send the password via email
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
                $mail->Subject = 'Your Password';
                $mail->Body = "Your password is: $password";
                $mail->AddAddress($email);
                $mail->SMTPOptions=array('ssl'=>array(
                    'verify_peer'=>false,
                    'verify_peer_name'=>false,
                    'allow_self_signed'=>false
                ));
                $mail->send();
                header("Location: fp.html");
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo 'Email address not found.';
        }

        $stmt->close();
    } else {
        echo 'Please enter a valid email address.';
    }

    $conn->close();
}
?>
