<?php
include 'db.php';
session_start();

$emailErr = $passwordErr = "";
$email = $password = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $valid = true;

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

    if ($valid) {
        $sql = "SELECT * FROM Users WHERE email='$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // If passwords are stored in plain text, compare directly
            if ($password == $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email']=$user['email'];
                header("Location: index.php");
            } else {
                $passwordErr = "Invalid password";
            }
        } else {
            $emailErr = "No user found with this email";
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .navbar { background-color: #333; overflow: hidden; }
        .navbar a { float: left; display: block; color: white; text-align: center; padding: 14px 20px; text-decoration: none; }
        .navbar a:hover { background-color: #ddd; color: black; }
        .container { max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; background-color: #f9f9f9; }
        h2 { text-align: center; }
        .error { color: red; }
        input[type="email"], input[type="password"] {
            width: 100%; padding: 10px; margin: 5px 0 20px 0; border: 1px solid #ccc; border-radius: 5px;
        }
        button { background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; width: 100%; }
        button:hover { background-color: #45a049; }
        .footer { background-color: #333; color: white; text-align: center; padding: 10px 0; position: fixed; left: 0; bottom: 0; width: 100%; }
    </style>
</head>
<body style="background-image: url('uploads/agri.jpg'); background-size: cover; background-repeat: no-repeat; background-position: center; height: 500px;">
    <div class="navbar">
        <a href="index.php">Home</a>
        <a href="userlogin.php">Login</a>
    </div>
    <div class="container">
        <h2>Login</h2>
        <form method="POST" action="">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $email; ?>">
            <span class="error"><?php echo $emailErr; ?></span><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password">
            <a href="fp.html">Forget Password?</a></p>
            <span class="error"><?php echo $passwordErr; ?></span><br>

            <button type="submit">Login</button>
        </form>
        <p style="text-align:center; margin-top:20px;">Don't have an account? <a href="userregister.php">Register</a></p>
    </div>
    <div class="footer">
        <p>&copy; 22024 Farmer's reminders. All rights reserved.</p>
    </div>
</body>
</html>
