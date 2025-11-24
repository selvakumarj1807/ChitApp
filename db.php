<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "chitapp"; 

// Create connection

$conn = new mysqli($servername, $username, $password, $dbname);


// PDO Connection

//$connect = new PDO("mysql:host=127.0.0.1;dbname=tnmigrants", "root", "");


// Check connection

if ($conn->connect_error) {
   	die ("<script type='text/javascript'>  window.location='error.php'; </script>");
}
?>

