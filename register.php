<!DOCTYPE html>
<html>
<head>
  <title>Register</title>
</head>
<body>
  <h1>Register</h1>
  <?php
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $servername = 'localhost';
    $dbusername = 'forum_user';
    $dbpassword = 'Felix123!';
    $dbname = 'forum_db';

    $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

    if ($conn->connect_error) {
      die('Die Verbindung zur Datenbank wurde unterbrochen!');
    }

    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $class = trim($_POST['class']);

    if (empty($username) || empty($password) || empty($class)) {
      echo "Please fill the username and password fields.";
      exit();
    }

    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
      echo "Invalid username. Please use only letters, numbers, and underscores.";
      exit();
    }

    if (strlen($password) < 8) {
      echo "Password should be at least 8 characters long.";
      exit();
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
      echo "Username already exists. Please choose a different username.";
      echo "<button onclick=\"window.location.href='login.php'\">Go to Login</button>";
      exit();
    }

    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, username, password, class, rank) VALUES (?, ?, ?, ?, ?, 'schüler')");
    $stmt->bind_param("sssss", $first_name, $last_name, $username, $hash, $class);
    $stmt->execute();

    session_start();
    $_SESSION['username'] = $username;
    header('Location: welcome.php');
    exit();
  }
  ?>
  <form method="POST">
    <label>First Name:</label>
    <input type="text" name="first_name">
    <br>
    <label>Last Name:</label>
    <input type="text" name="last_name">
    <br>
    <label>Username:</label>
    <input type="text" name="username" required>
    <br>
    <label>Password:</label>
    <input type="password" name="password" id="password" required>
    <button type="button" onclick="togglePassword()">Show</button>
    <br>
    <label>Class:</label>
    <select name="class" required>
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
  <p>Already have an account? <a href="login.php">Login</a></p>
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