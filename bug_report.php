<!DOCTYPE html>
<html>
  <head>
    <title>Feedback Reports</title>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <main>
    <?php
      session_start();

      if (!isset($_SESSION["user_id"])) {
        header("Location: login.php");
        exit();
      }

      $user_id = $_SESSION["user_id"];

      $servername= 'localhost';
      $dbusername = 'forum_user';
      $dbpassword = 'Felix123!';
      $dbname = 'forum_db';

      $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

      if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
      }

      $is_admin = false;
      $sql = "SELECT * FROM user_perms WHERE _id = ? AND permission = 'admin'";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("s", $user_id);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($result->num_rows > 0) {
          $is_admin = true;
      }
      ?>

      <nav>
        <ul>
          <li><a class="active" href="forum.php">Back</a></li>
          <li><a href="write_bug_reports.php">report bug</a></li>
        </ul>
      </nav>

      <h1>Feedback Reports</h1>

      <?php
      $sql = "SELECT * FROM bug_reports ORDER BY date_created DESC";
      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          $bug_id = $row['bug_id'];
          $bug_content = $row['content'];
          $bug_date = $row['date_created'];
          $author_id = $row['author_id'];
          $sql2 = "SELECT * FROM users WHERE _id = ?";
          $stmt = $conn->prepare($sql2);
          $stmt->bind_param("s", $author_id);
          $stmt->execute();
          $result2 = $stmt->get_result();
          $author = $result2->fetch_assoc();
          $author_name = $author['first_name'] . ' ' . $author['last_name'];
        
          echo "<div class='bug_report'>";
          echo "<h4>Bug ID: $bug_id</h4>";
          echo "<p>Author: $author_name</p>";
          echo "<p>Date: $bug_date</p>";
          echo "<h2>$bug_content</h2>";
        
          if ($author_id == $user_id || $is_admin) {
            echo "<form method='post' action=''>";
            echo "<input type='hidden' name='delete_bug' value='$bug_id'>";
            echo "<input type='submit' value='Delete Bug Report'>";
            echo "</form>";
          }
          echo "</div>";
        }
      } else {
        echo "<p>No bug reports yet</p>";

      }
      ?>
      <?php
      if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["delete_bug"])) {
          $bug_id = $_POST["delete_bug"];

          $sql = "DELETE FROM bug_reports WHERE bug_id = ?";
          $stmt = $conn->prepare($sql);
          $stmt->bind_param("s", $bug_id);
          $stmt->execute();

          if ($stmt->affected_rows > 0) {
            echo "<script>alert('Bug report deleted successfully!'); window.location.href='bug_report.php';</script>";
          } else {
            echo "<script>alert('Error deleting bug report!');</script>";
          }
        }
      }

      $conn->close();
      ?>
    </main>
  </body>
</html>