<!DOCTYPE html>
<html>
<head>
	<title>Login</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>
	<?php
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {	
      $username = $_POST['username'];
      $password = $_POST['password'];

      $servername = 'localhost';
      $dbusername = 'forum_user';
      $dbpassword = 'Felix123!';
      $dbname = 'forum_db';
  
      $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

      if ($conn->connect_error) {
        die('Die Verbindung zur Datenbank wurde unterbrochen!');
      }

      $sql = "SELECT * FROM users WHERE username = '$username'";
      $result = $conn->query($sql);
  
      if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {

          header('Location: home.html');
          exit();
        } else {
          echo 'Falsches Passwort!';
        }
      } else {
        echo 'Benutzername nicht gefunden!';
      }

      $conn->close();
  
    }
  ?>
  
  <h1>Login</h1>
  
  <form method="post" action="">
    <label>Benutzername:</label>
    <input type="text" name="username" required><br><br>
    <label>Passwort:</label>
    <input type="password" name="password" required><br><br>
    <input type="submit" value="Anmelden">
  </form>
  
  <br>
  
  <a href="register.php">Registrieren</a>
  