<!DOCTYPE html>
<html>
<head>
  <title>Welcome</title>
</head>
<body>
<?php
  session_start();

  if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
  }

  echo "<h1>Hallo, " . $_SESSION['username'] ." du hast erfolgreich einen Account erstellt. Wir wünschen dir viel Spaß auf dem Schulforum von Tobias Löffler, Felix Riemer und Leon Kruspel!</h1>";

  echo '<script>
          setTimeout(function() {
            window.location.href = "login.php";
          }, 5000);
        </script>';
?>
</body>
</html>