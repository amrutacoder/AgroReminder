<?php
include 'db.php';
include('smtp/PHPMailerAutoload.php');

function sendEmail($to, $subject, $body) {
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
        $mail->Body    = $body;
        $mail->AddAddress($to);
        $mail->SMTPOptions=array('ssl'=>array(
            'verify_peer'=>false,
            'verify_peer_name'=>false,
            'allow_self_signed'=>false
        ));

        $mail->send();
        header("Location:add_crop.php");
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

$today = date('Y-m-d');
$sql = "SELECT Reminders.reminder_date, Reminders.message, Users.email 
        FROM Reminders 
        JOIN Users ON Reminders.user_id = Users.id 
        WHERE Reminders.reminder_date = '$today'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        sendEmail($row['email'], "Crop Reminder", $row['message']);
    }
}

$conn->close();
?>
