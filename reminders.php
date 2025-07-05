<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: userlogin.php");
    exit();
}

$userId = $_SESSION['user_id'];
$cropOptions = '';
$sql = "SELECT DISTINCT crop_name FROM Crops WHERE user_id = '$userId'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cropOptions .= "<option value='{$row['crop_name']}'>{$row['crop_name']}</option>";
    }
} else {
    $cropOptions = "<option value=''>No crops found</option>";
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reminders</title>
    <style>
        /* Add your CSS styling here, reusing styles from other pages as needed */
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
        .card { background-color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .card h2 { margin-top: 0; }
        .card select, .card button { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; }
        .card button { background-color: #4CAF50; color: white; border: none; cursor: pointer; }
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
                    <img src="<?php echo isset($_SESSION['image']) ? $_SESSION['image'] : 'uploads/default.png'; ?>" alt="Profile" class="profile-icon">
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
            <h2>Select Crop</h2>
            <form method="GET" action="">
                <select name="crop_name" required>
                    <?php echo $cropOptions; ?>
                </select>
                <button type="submit">Show Reminders</button>
            </form>
        </div>

        <?php if (isset($_GET['crop_name'])): ?>
            <?php
            include 'db.php';
            $cropName = htmlspecialchars($_GET['crop_name']);
            $sql = "SELECT reminder_date, message FROM Reminders 
                    JOIN Crops ON Reminders.crop_id = Crops.id 
                    WHERE Crops.user_id = '$userId' AND Crops.crop_name = '$cropName' 
                    ORDER BY reminder_date ASC";
            $result = $conn->query($sql);
            ?>
            <div class="card">
                <h2>Reminders for <?php echo $cropName; ?></h2>
                <?php if ($result->num_rows > 0): ?>
                    <ul>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <li><?php echo "{$row['reminder_date']}: {$row['message']}"; ?></li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>No reminders found for this crop.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="footer">
        <p>&copy; 2024 Farmer's reminders. All rights reserved.</p>
    </div>
</body>
</html>
