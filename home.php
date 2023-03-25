<!DOCTYPE html>
<html>
  <head>
    <title>Forum</title>
    <link rel="stylesheet" href="home_style.css">
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
      $sql = "SELECT * FROM user_perms WHERE user_id = ? AND permission = 'admin'";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("s", $user_id);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($result->num_rows > 0) {
          $is_admin = true;
      }

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(isset($_POST['delete_post'])) {
          $post_id = $_POST['delete_post'];

          $sql = "SELECT * FROM posts WHERE _id = ?";
          $stmt = $conn->prepare($sql);
          $stmt->bind_param("s", $post_id);
          $stmt->execute();
          $result = $stmt->get_result();

          if ($result->num_rows == 1) {
            $post = $result->fetch_assoc();
            $author_id = $post['author_id'];

            if ($author_id == $user_id) {
              $sql = "DELETE FROM posts WHERE _id = ?";
              $stmt = $conn->prepare($sql);
              $stmt->bind_param("s", $post_id);
              $stmt->execute();
              $sql = "DELETE FROM comments WHERE post_id = ?";
              $stmt = $conn->prepare($sql);
              $stmt->bind_param("s", $post_id);
              $stmt->execute();
            }
          }
        } else {
          $post_id = $_POST['post_id'];
          $content = $_POST['content'];
          $topic = $_POST['topic'];
          $date_created = date("d-m-Y H:i");

          $sql = "INSERT INTO posts (_id, name, author_id, content, date_created, topic) VALUES (UUID(), ?, ?, ?, ?, ?)";
          $stmt = $conn->prepare($sql);
          $stmt->bind_param("sssss", $content, $user_id, $content, $date_created, $topic);
          $stmt->execute();
        }
      }
      ?>

      <nav>
        <ul>
          <li><a class="active" href="#">Home</a></li>
          <li><a href="#">Neuigkeiten</a></li>
          <li><a href="#">Portal</a></li>
          <li style="float:right"><a href="account.php">account</a></li>
          <li><a href="logout.php">logout</a></li>
        </ul>
      </nav>


      <h1>Forum</h1>

      <h2>Create Post</h2>
      <form method="post" action="">
        <label>Topic:</label>
        <input type="text" name="topic" required><br><br>
        <label>Content:</label>
        <textarea name="content" required></textarea><br><br>
        <input type="submit" value="Create Post">
      </form>

      <h2>Posts</h2>

      <?php
      $sql = "SELECT * FROM posts ORDER BY date_created DESC";
      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $post_id = $row['_id'];
        $post_name = $row['name'];
        $post_content = $row['content'];
        $post_topic = $row['topic'];
        $post_date = $row['date_created'];
        $author_id = $row['author_id'];
        $sql2 = "SELECT * FROM users WHERE _id = ?";
        $stmt = $conn->prepare($sql2);
        $stmt->bind_param("s", $author_id);
        $stmt->execute();
        $result2 = $stmt->get_result();
        $author = $result2->fetch_assoc();
        $author_name = $author['first_name'] . ' ' . $author['last_name'];
      
        echo "<div class='post_ground'>";
        echo "<div class='post' id='post2' name='post2'>";

        echo "<h1>Topic: $post_topic</h1>";
        echo "<p>Author: $author_name</p>";
        echo "<p>Date: $post_date</p>";
        echo "<h4>$post_content</h4>";
      
        if ($author_id == $user_id || $is_admin) {
        echo "<form method='post' action=''>";
        echo "<input type='hidden' name='delete_post' value='$post_id'>";
        echo "<input type='submit' value='Delete Post'>";
        echo "</form>";
        }
        echo "<form method='post' action='comment.php'>";
        echo "<input type='hidden' name='post_id' value='$post_id'>";
        echo "<input type='submit' value='Kommentar verfassen'>";
        echo "</form>";	

        echo "<h4>Comments</h4>";
      
        $sql3 = "SELECT * FROM comments WHERE post_id = ?";
        $stmt = $conn->prepare($sql3);
        $stmt->bind_param("s", $post_id);
        $stmt->execute();
        $result3 = $stmt->get_result();
      
        if ($result3->num_rows > 0) {
        while($row2 = $result3->fetch_assoc()) {
          $comment_content = $row2['content'];
          $comment_date = $row2['date_created'];
          $comment_author_id = $row2['author_id'];
      
          $sql4 = "SELECT * FROM users WHERE _id = ?";
          $stmt = $conn->prepare($sql4);
          $stmt->bind_param("s", $comment_author_id);
          $stmt->execute();
          $result4 = $stmt->get_result();
          $comment_author = $result4->fetch_assoc();
          $comment_author_name = $comment_author['first_name'] . ' ' . $comment_author['last_name'];
      
          echo "<p>$comment_content</p>";
          echo "<p>Author: $comment_author_name</p>";
          echo "<p>Date: $comment_date</p>";
        }
        } else {
        echo "<p>No comments yet</p>";
        }
        echo"<br>";
        echo"<br>";
        echo "</div>";
        echo "</div>";
      }
      }
      
      $conn->close();
      ?>
    </main>
  </body>
</html>	  