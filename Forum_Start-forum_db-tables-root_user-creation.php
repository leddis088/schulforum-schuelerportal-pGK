<?php
$servername = "localhost";
$username = "forum_user";
$password = "Felix123!";

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "CREATE DATABASE IF NOT EXISTS forum_db";
if ($conn->query($sql) !== TRUE) {
    die("Error creating database: " . $conn->error);
}

$conn->select_db("forum_db");

$sql = "CREATE TABLE IF NOT EXISTS users (
    _id VARCHAR(255) PRIMARY KEY,
    username VARCHAR(30) NOT NULL,
    class VARCHAR(30) NOT NULL,
    first_name VARCHAR(30) NOT NULL,
    last_name VARCHAR(30) NOT NULL,
    hash VARCHAR(255) NOT NULL
)";
$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS posts (
    _id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    author_id VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    date_created VARCHAR(255) NOT NULL,
    topic VARCHAR(255) NOT NULL
)";
$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS user_perms (
    _id VARCHAR(255) PRIMARY KEY,
    user_id VARCHAR(255) NOT NULL,
    permission VARCHAR(255) NOT NULL
)";
$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS comments (
    _id VARCHAR(255) PRIMARY KEY,
    author_id VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    date_created VARCHAR(255) NOT NULL,
    post_id VARCHAR(255) NOT NULL
)";
$conn->query($sql);

$sql = "SELECT * FROM users WHERE username='root'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    $username = 'root';
    $class = 'none';
    $first_name = 'root';
    $last_name = 'root';
    $password = 'felix123';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (_id, username, first_name, last_name, hash, class)
        VALUES (UUID(), ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $username, $first_name, $last_name, $hashed_password, $class);
    $stmt->execute();

    $root_user_id = $conn->insert_id;

    $sql = "INSERT INTO user_perms (_id, user_id, permission) VALUES (UUID(), ?, 'admin')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $root_user_id);
    $stmt->execute();

    echo "Root user created successfully";
}

$conn->close();

?>