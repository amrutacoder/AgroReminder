<?php
include 'db.php';

$nameErr = $emailErr = $phoneErr = $passwordErr = $confirmPasswordErr = $imageErr = "";
$name = $email = $phone = $password = $confirmPassword = $image = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $valid = true;

    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
        $valid = false;
    } else {
        $name = htmlspecialchars($_POST["name"]);
    }

    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
        $valid = false;
    } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format";
        $valid = false;
    } else {
        $email = htmlspecialchars($_POST["email"]);
    }
    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
        $valid = false;
    } else {
        $password = htmlspecialchars($_POST["password"]);
    }
    if ($_FILES["image"]["name"]) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["image"]["tmp_name"]);

        if ($check !== false) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image = $target_file;
            } else {
                $imageErr = "Sorry, there was an error uploading your file.";
                $valid = false;
            }
        } else {
            $imageErr = "File is not an image.";
            $valid = false;
        }
    }

    if ($valid) {
        
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    
    <style>
      body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; }
        .navbar { background-color: #333; overflow: hidden;  padding: 10px 0; position: fixed; left: 0; top : 0; width: 100%; }
        .navbar a, .navbar .dropdown { float: left; display: block; color: white; text-align: center; padding: 14px 20px; text-decoration: none; }
        .navbar a:hover, .dropdown:hover .dropbtn { background-color: #ddd; color: black; }
        .container { max-width: 600px; margin: 50px auto;  padding: 20px; border: 1px solid #ccc; border-radius: 10px; background-color: #f9f9f9; }
        h2 { text-align: center; }
        .error { color: red; }
        input[type="text"], input[type="email"], input[type="tel"], input[type="password"], input[type="file"] {
            width: 100%; padding: 10px; margin: 5px 0 20px 0; border: 1px solid #ccc; border-radius: 5px;
        }
        button { background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; width: 100%; }
        button:hover { background-color: #45a049; }
        .footer { background-color: #333; color: white; text-align: center; padding: 10px 0; position: fixed; left: 0; bottom: 0; width: 100%; }
    </style>
</head>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

    function showalert(){
        alert("Registerd Sucessfully")
    }
    function send_otp(){
	var email=jQuery('#email').val();
	jQuery.ajax({
		url:'send_otp.php',
		type:'post',
		data:{'email':email},    
		success:function(result){
            console.log(result)
			if(result=='yes'){
				jQuery('.second_box').show();
				jQuery('.first_box').hide();
			}
			if(result=='not_exist'){
				jQuery('#email_error').html('Please enter valid email');
			}
		}
	});
}

function submit_otp(){
    console.log("success")
	var otp=jQuery('#otp').val();
    var email=jQuery('#email').val();
	jQuery.ajax({
		url:'check_otp.php',
		type:'post',
		data:{'otp':otp,'email':email},
		success:function(result){
            console.log(result)
            console.log("fekuhvegrebivbc")
			if(result=='yes'){
				window.location='userregister.php'
			}
			if(result=='no'){
				jQuery('#otp_error').html('Please enter valid otp');
			}
		}
	});
}  
</script>

<body style="background-image: url('uploads/agri.jpg'); background-size: cover; background-repeat: no-repeat; background-position: center; height: 500px;">
    <div class="navbar">
        <a href="index.php">Home</a>
        <a href="userlogin.php">Login</a>
    </div>
    <br>
    <br>
    <br>
    <div class="container">
        <h2>Register</h2>
        <form method="POST" action="check_otp.php" enctype="multipart/form-data">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo $name; ?>">
            <span class="error"><?php echo $nameErr; ?></span><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $email; ?>">
            <span class="error"><?php echo $emailErr; ?></span><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" value="<?php echo $password; ?>">
            <span class="error"><?php echo $passwordErr; ?></span><br>
            
            <div class="form-group second_box">
            <input type="text" id="otp" class="form-control" placeholder="OTP" required="required" name="otp">
			<span id="otp_error" class="field_error"></span>
            </div>

            <div>
            <button type="button" class="btn btn-primary btn-block" onclick="send_otp()">Send OTP</button>
            </div>

            <label for="image">Profile Picture (optional):</label>
            <input type="file" id="image" name="image" require>
            <span class="error"><?php echo $imageErr; ?></span><br>

            <button type="submit">Register</button>
        </form>
    </div>
    <br>
    <br>
    <br>
    <div class="footer">
        <p>&copy; 2024 Farmer's reminders. All rights reserved.</p>
    </div>
</body>
</html>
