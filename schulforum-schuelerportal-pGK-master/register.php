<?php
session_start();

$servername = "localhost";
$username = "forum_user";
$password = "Felix123!";
$dbname = "forum_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $class = $_POST["class"];

    if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
        $error_message = "Password must have at least 8 characters and contain at least one letter and one number.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = "Username already exists! <a href=login.php>login</a>";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (_id, username, first_name, last_name, hash, class) VALUES (UUID(), ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $username, $first_name, $last_name, $hash, $class);
            $stmt->execute();

            $user_id = $conn->insert_id;

            $stmt = $conn->prepare("INSERT INTO user_perms (_id, user_id, permission) VALUES ('1', UUID(), 'user')");
            $stmt->bind_param("s", $user_id);
            $stmt->execute();

            $_SESSION["user_id"] = $user_id;
            header("Location: welcome.php");
            exit();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>
    <h1>Register</h1>
    <?php if (isset($error_message)) { ?>
        <p><?php echo $error_message; ?></p>
    <?php } ?>
    <form method="post">
        <label for="first_name">First Name</label>
        <input type="text" id="first_name" name="first_name">
        <br>
        <label for="last_name">Last Name</label>
        <input type="text" id="last_name" name="last_name">
        <br>
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" minlength="8" required>
        <button type="button" onclick="togglePassword()">Show</button>
        <br>
        <label for="class">Class</label>
        <select id="class" name="class" required>
        <option value="1A">1A</option>
        <option value="1B">1B</option>
        <option value="2A">2A</option>
        <option value="2B">2B</option>
        <option value="3A">3A</option>
        <option value="4A">4A</option>
        <option value="4B">4B</option>
    </select>
    <br>
    <button type="submit">Register</button>
</form>
<script>
function togglePassword() {
  var x = document.getElementById("password");
  if (x.type === "password") {
    x.type = "text";
  } else {
    x.type = "password";
  }
}
</script>
</body>
</html>