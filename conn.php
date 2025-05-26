<?php
$servername = "localhost";
$username = "miniemen_sep";
$password = "Miniemen123";
$db = "miniemen_spc";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $db);

// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
//echo "Connected successfully";

?>