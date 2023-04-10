<!DOCTYPE html>
<html>
<head>
  <title>Welcome</title>
</head>
<body>
<?php
  session_start();

  $servername = "localhost";
  $dbusername = "forum_user";
  $dbpassword = "Felix123!";
  $dbname = "forum_db";

  $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }

  if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
  } else {
    $stmt = $conn->prepare("SELECT first_name, last_name, username FROM users WHERE _id = ?");
    $stmt->bind_param("s", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
  }

  echo "<h1>" . $user['first_name'] . " " . $user['last_name'] . " (" . $user['username'] . ") hat sich erfolgreich eingeloggt!</h1>";

  echo '<script>
          setTimeout(function() {
            window.location.href = "forum.php";
          }, 3000);
        </script>';

$conn->close();

?>
</body>
</html>