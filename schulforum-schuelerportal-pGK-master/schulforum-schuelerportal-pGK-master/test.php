<?php
session_start();

if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true){
    echo "Welcome to the site, " . $_SESSION['username'] . "!";
} else{
    echo "Please log in to access the site.";
}

$servername = "localhost";
$username = "forum_user";
$password = "Felix123!";
$dbname = "forum_db";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Connected successfully";

mysqli_close($conn);

?>