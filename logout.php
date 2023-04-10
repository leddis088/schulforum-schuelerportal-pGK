<?php
  session_start();

  unset($_SESSION['username']);

  session_destroy();

  echo "Bis zum naechsten Mal ";
?>
<html>
  <head>
    <link rel="stylesheet" href="style.css">
  </head>
  <script>
  var counter = 3;
  setInterval(function() {
      counter--;
      if(counter < 0) {
          window.location = 'login.php';
      } else {
          document.getElementById("count").innerHTML = counter;
          }
  }, 100);
  </script>
</html>
<?php
  exit();
?>
