<?
include 'db.php';
session_start();
$name=$_POST['name'];
$email=$_POST['email'];
$password=$_POST['password'];
$image=$_POST['image'];
$sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
$conn->query($sql);
?>