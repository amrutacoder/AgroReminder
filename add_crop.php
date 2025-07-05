<?php
session_start();
include 'db.php';
include('smtp/PHPMailerAutoload.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: userlogin.php");
    exit();
}

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
        
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

$userId = $_SESSION['user_id'];
$cropNameErr = $sowingDateErr = $successMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cropName = $sowingDate = "";
    $valid = true;

    if (empty($_POST["crop_name"])) {
        $cropNameErr = "Crop name is required";
        $valid = false;
    } else {
        $cropName = htmlspecialchars($_POST["crop_name"]);
    }

    if (empty($_POST["sowing_date"])) {
        $sowingDateErr = "Sowing date is required";
        $valid = false;
    } else {
        $sowingDate = htmlspecialchars($_POST["sowing_date"]);
    }

    if ($valid) {
        // Fetch crop data from JSON file
        $jsonFile = 'crops_data.json'; // Ensure this path is correct
        if (file_exists($jsonFile)) {
            $cropData = json_decode(file_get_contents($jsonFile), true);
            $cropDetails = isset($cropData[strtolower($cropName)]) ? $cropData[strtolower($cropName)] : null;

            if ($cropDetails) {
                $sql = "INSERT INTO Crops (user_id, crop_name, sowing_date) VALUES ('$userId', '$cropName', '$sowingDate')";
                if ($conn->query($sql) === TRUE) {
                    $cropId = $conn->insert_id;
                    $emailBody = "You have added a new crop: $cropName.<br>Here are your reminders:<br>";
                    foreach ($cropDetails['reminders'] as $reminder) {
                        $reminderDate = date('Y-m-d', strtotime($sowingDate . ' + ' . $reminder['interval']));
                        $message = $reminder['message'];
                        $sqlReminder = "INSERT INTO Reminders (user_id, crop_id, reminder_date, message) VALUES ('$userId', '$cropId', '$reminderDate', '$message')";
                        $conn->query($sqlReminder);
                        $emailBody .= "Reminder Date: $reminderDate - $message<br>";
                    }
                    $userEmail=$_SESSION['email']; 
                    sendEmail($userEmail, "Crop Reminder Details", $emailBody);
                    $successMsg = "Crop added successfully with reminders!";
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            } else {
                $cropNameErr = "Crop details not found.";
            }
        } else {
            echo "Error: JSON file not found.";
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Crop</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; }
        .navbar { background-color: #333; overflow: hidden; }
        .navbar a { float: left; display: block; color: white; text-align: center; padding: 14px 20px; text-decoration: none; }
        .navbar a:hover { background-color: #ddd; color: black; }
        .dropdown { float: right; overflow: hidden; }
        .dropdown .dropbtn { cursor: pointer; font-size: 16px; border: none; outline: none; color: white; padding: 14px 20px; background-color: inherit; }
        .dropdown-content { display: none; position: absolute; right: 0; background-color: #f9f9f9; min-width: 160px; box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2); z-index: 1; }
        .dropdown-content a { float: none; color: black; padding: 12px 16px; text-decoration: none; display: block; text-align: left; }
        .dropdown-content a:hover { background-color: #ddd; }
        .dropdown:hover .dropdown-content { display: block; }
        .container { max-width: 600px; margin: 20px auto; padding: 20px; }
        .footer { background-color: #333; color: white; text-align: center; padding: 10px 0; position: fixed; left: 0; bottom: 0; width: 100%; }
        .error { color: red; }
        .success { color: green; }
        .card { background-color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .card h2 { margin-top: 0; }
        .card input[type="text"], .card input[type="date"] { width: 100%; padding: 10px; margin: 10px 0 20px 0; border: 1px solid #ccc; border-radius: 5px; }
        .card button { background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .card button:hover { background-color: #45a049; }
        .profile-icon { width: 32px; height: 32px; border-radius: 50%; }
    </style>
</head>
<body style="background-image: url('uploads/agri.jpg'); background-size: cover; background-repeat: no-repeat; background-position: center; height: 500px;">
    <div class="navbar">
        <a href="index.php">Home</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="dropdown" style="float:right;">
                <button class="dropbtn">
                <img src="<?php echo $user['image'] ? $user['image'] : 'uploads/akash_the_coder.png'; ?>" alt="Profile" class="profile-icon">
                </button>
                <div class="dropdown-content">
                    <a href="profile.php">Profile</a>
                    <a href="reminders.php">My Reminders</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        <?php else: ?>
            <a href="login.php" style="float:right;">Login</a>
            <a href="register.php" style="float:right;">Register</a>
        <?php endif; ?>
    </div>

    <div class="container">
        <div class="card">
            <h2>Add Crop</h2>
            <?php if (!empty($successMsg)): ?>
                <p class="success"><?php echo $successMsg; ?></p>
            <?php endif; ?>
            <form method="POST" action="">
                <label for="crop_name">Crop Name:</label>
                <input type="text" id="crop_name" name="crop_name" value="<?php echo isset($_POST['crop_name']) ? htmlspecialchars($_POST['crop_name']) : ''; ?>">
                <span class="error"><?php echo $cropNameErr; ?></span><br>

                <label for="sowing_date">Sowing Date:</label>
                <input type="date" id="sowing_date" name="sowing_date" value="<?php echo isset($_POST['sowing_date']) ? htmlspecialchars($_POST['sowing_date']) : ''; ?>">
                <span class="error"><?php echo $sowingDateErr; ?></span><br>

                <button type="submit">Add Crop</button>
            </form>
        </div>
    </div>

    <div class="footer">
        <p>&copy; 2024 Farmer's reminders. All rights reserved.</p>
    </div>
</body>
</html>
