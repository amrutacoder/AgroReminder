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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .navbar { background-color: #333; overflow: hidden; }
        .navbar a { float: left; display: block; color: white; text-align: center; padding: 14px 20px; text-decoration: none; }
        .navbar a:hover { background-color: #ddd; color: black; }
        .container { max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; background-color: #f9f9f9; }
        .profile-pic { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; margin: 0 auto; display: block; }
        .profile-details { text-align: center; }
        .profile-details p { margin: 10px 0; }
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
        <img src="<?php echo $user['image'] ? $user['image'] : 'profile_pic_placeholder.jpg'; ?>" alt="Profile Picture" class="profile-pic">
        <div class="profile-details">
            <h2><?php echo $user['name']; ?></h2>
            <p>Name: <?php echo $user['name']; ?></p>
            <p>Email: <?php echo $user['email']; ?></p>
            <p>Password: <?php echo $user['password']; ?></p>
        </div>
        <a href="edit_profile.php"><button>Edit Profile</button></a>
    </div>
    <div class="footer">
        <p>&copy; 2024 Farmer's reminders. All rights reserved.</p>
    </div>
</body>
</html>
