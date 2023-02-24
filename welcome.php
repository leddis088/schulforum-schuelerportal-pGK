<!DOCTYPE html>
<head>
  <title>Welcome</title>
</head>
<?php
  session_start();

  if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
  }

  echo "<h1>Hallo, " . $_SESSION['username'] ." du hast erfolgreich einen Account erstellt. Wir wünschen dir viel Spaß auf dem Schulforum von Tobias Löffler, Felix Riemer und Leon Kruspel!</h1>";

  echo "<form action='logout.php'><button type='submit'>Logout</button></form>";
?>
<p><a href="home.php">Zur Startseite</a></p>
