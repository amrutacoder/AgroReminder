<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: userlogin.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM Users WHERE id='$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit();
}

$nameErr = $emailErr = $imageErr = "";
$name = $user['name'];
$email = $user['email'];
$password = $user['password'];
$image = $user['image'];

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
        $sql = "UPDATE Users SET name='$name', email='$email', password='$password', image='$image' WHERE id='$user_id'";
        if ($conn->query($sql) === TRUE) {
            header("Location: profile.php");
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .navbar { background-color: #333; overflow: hidden;  padding: 10px 0; position: fixed; left: 0; top : 0; width: 100%; }
        .navbar a, .navbar .dropdown { float: left; display: block; color: white; text-align: center; padding: 14px 20px; text-decoration: none; }
        .navbar a:hover, .dropdown:hover .dropbtn { background-color: #ddd; color: black; }
        .container { max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; background-color: #f9f9f9; }
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
<body style="background-image: url('uploads/agri.jpg'); background-size: cover; background-repeat: no-repeat; background-position: center; height: 500px;">
    <div class="navbar">
        <a href="index.php">Home</a>
        <a href="logout.php">Logout</a>
    </div>
    <div class="container">
        <h2>Edit Profile</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo $name; ?>">
            <span class="error"><?php echo $nameErr; ?></span><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $email; ?>">
            <span class="error"><?php echo $emailErr; ?></span><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" value="<?php echo $password; ?>">
            

            <label for="image">Profile Picture:</label>
            <input type="file" id="image" name="image">
            <span class="error"><?php echo $imageErr; ?></span><br>

            <button type="submit">Save Changes</button>
        </form>
    </div>
    <div class="footer">
        <p>&copy; 2024 Farmer's reminders. All rights reserved.</p>
    </div>
</body>
</html>
