<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
  <title>Login</title>
  <link rel="stylesheet" href="home_style.css">
  <link rel="sqlfile" href="home_style.css">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Loginpage Schulforum</title>
</head> 
<body>
 
    <?php
    $serverName = "localhost";
    $userName = "root";
    $password = "felix123";
    $database = "forum-db";
    ?>
 
<form action="?login=1" method="post">
E-Mail:<br>
<input type="name" size="40" maxlength="25" name="name"><br><br>

Dein Passwort:<br>
<input type="password" size="35"  maxlength="250" name="passwort"><br>
<input type="submit" value="Abschicken">
</form> 
</body>
</html>

<?php
$con = mysqli_connect("localhost","root","felix123","forum-db");
$con = mysqli_connect('localhost', 'root', 'felix123');
$txtUsers = $_POST['txtUsers'];
$txtPW = $_POST['txtPW'];
$sql = "INSERT INTO `users` (`Users`, `PW`) VALUES ('0', '$txtName', '$txtPW');"
?>
