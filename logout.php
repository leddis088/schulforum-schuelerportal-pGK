<?php
  session_start();

  unset($_SESSION['username']);

  session_destroy();

  echo "Bis zum naechsten Mal ";
?>
<html>
  <script>
  var counter = 10;
  setInterval(function() {
      counter--;
      if(counter < 0) {
          window.location = 'login.php';
      } else {
          document.getElementById("count").innerHTML = counter;
          }
  }, 1000);
  </script>
</html>
<?php
  exit();
?>
