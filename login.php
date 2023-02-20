<?php
  session_start(); // Start the session
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
</head>
<body>

  <h1>Login</h1>

  <?php
    // If the user submits the form
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Connect to the database
      $db = mysqli_connect('localhost', 'forum_user', 'Felix123!', 'forum_db');

      // Get the username and password from the form
      $username = mysqli_real_escape_string($db, $_POST['username']);
      $password = mysqli_real_escape_string($db, $_POST['password']);

      // Find the user in the database
      $query = "SELECT * FROM users WHERE username='$username'";
      $result = mysqli_query($db, $query);
      $user = mysqli_fetch_assoc($result);

      // Verify the password
      if ($user && password_verify($password, $user['password'])) {
        // If the password is correct, log the user in and redirect to the home page
        $_SESSION['username'] = $username;
        header('Location: home.html');
        exit();
      } else {
        // If the password is incorrect, display an error message
        echo "<p>Invalid username or password</p>";
      }
    }
  ?>

  <form method="POST">
    <label>Username:</label>
    <input type="text" name="username" required>
    <br>
    <label>Password:</label>
    <input type="password" name="password" required>
    <br>
    <button type="submit">Login</button>
  </form>

  <p>Don't have an account? <a href="register.php">Register</a></p>

</body>
</html>