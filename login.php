<!DOCTYPE html>
<html>
<head>
	<title>Login</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>
	<?php
  session_start();
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {	
      $username = $_POST['username'];
      $password = $_POST['password'];
      $servername = 'localhost';
      $dbusername = 'forum_user';
      $dbpassword = 'Felix123!';
      $dbname = 'forum_db';
    
      $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);
      
      $user_id = $conn->insert_id;

      if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
      }
    
      $sql = "SELECT * FROM users WHERE username = '$username'";
      $result = $conn->query($sql);
    
      if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['hash'])) {
          $_SESSION["user_id"] = $user['_id']; // <-- Update this line
          header("Location: home.php");
          exit();
      } else {
          echo 'Nutzername oder Passwort falsch!';
      }      
      } else {
        echo 'Nutzername oder Passwort falsch!';
      }
    
      $conn->close();
    
    }
    ?>

    <h1>Login</h1>
    <form method="post" action="">
      <label>Username:</label>
      <input type="text" name="username" required><br><br>
      <label>Password:</label>
      <input type="password" name="password" required><br><br>
      <input type="submit" value="Login">
    </form>
    <br>
  <a href="register.php">Register</a>
  
  </body>
</html>    