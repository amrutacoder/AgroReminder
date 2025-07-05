<?php
include 'db.php';
session_start();
$otp1=$_POST['otp'];
$otp2=$_SESSION['otp'];
$email1=$_SESSION['email'];
$email2=$_POST['email'];
$name=$_POST['name'];
$password=$_POST['password'];
if($email1==$email2){
	if($otp1==$otp2){
		$image=$_FILES["image"]["name"];
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
		$sql = "INSERT INTO users (name, email, password,image) VALUES ('$name', '$email1', '$password','$image')";
		$conn->query($sql);
		header("Location:userregister.php");
	}
	else{
		echo "error";
	}
}
else{
	echo "enter same email";
}
?>