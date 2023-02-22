<?php

$servername = "localhost";
$username = "forum_user";
$password = "Felix123!";
$dbname = "forum_db";

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!$conn->select_db($dbname)) {

    $sql = "CREATE DATABASE $dbname";
    if ($conn->query($sql) === TRUE) {
        echo "Database created successfully";
    } else {
        echo "Error creating database: " . $conn->error;
    }
}

$conn->select_db($dbname);

$sql = "CREATE TABLE IF NOT EXISTS username (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(30) NOT NULL
    )";
$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS first_name (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(30) NOT NULL
    )";
$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS last_name (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(30) NOT NULL
    )";
$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS class (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(30) NOT NULL
    )";
$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS password (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        value VARCHAR(255) NOT NULL
    )";
$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS rank (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(30) NOT NULL
    )";
$conn->query($sql);

$sql = "SELECT * FROM username WHERE name='root'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {

    $username = 'root';
    $first_name = 'root';
    $last_name = 'root';
    $class = 'none';
    $password = 'felix123';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $rank = 'admin';

    $sql = "INSERT INTO username (name) VALUES ('$username')";
    $conn->query($sql);

    $sql = "INSERT INTO first_name (name) VALUES ('$first_name')";
    $conn->query($sql);

    $sql = "INSERT INTO last_name (name) VALUES ('$last_name')";
    $conn->query($sql);

    $sql = "INSERT INTO class (name) VALUES ('$class')";
    $conn->query($sql);

    $sql = "INSERT INTO password (value) VALUES ('$hashed_password')";
    $conn->query($sql);

    $sql = "INSERT INTO rank (name) VALUES ('$rank')";
    $conn->query($sql);

    echo "Root user created successfully";
}

$conn->close();
?>