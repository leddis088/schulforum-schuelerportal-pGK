<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
</head>
<body>

  <h1>Login</h1>

  <?php

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

      $db = mysqli_connect('localhost', 'forum_user', 'Felix123!', 'forum_db');

      $username = mysqli_real_escape_string($db, $_POST['username']);
      $password = mysqli_real_escape_string($db, $_POST['password']);

      $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
      $result = mysqli_query($db, $query);
      $user = mysqli_fetch_assoc($result);

      if ($user) {
        session_start();
        $_SESSION['username'] = $username;
        header('Location: welcome.php');
        exit();
      } else {

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
