<!DOCTYPE html>
<html>
  <head>
    <title>Forum</title>
    <link rel="stylesheet" href="style.css">
  </head>
  <nav>
      <li><a href="forum.php">Back</a></li>
  </nav>
    <?php
    session_start();

    if (!isset($_SESSION["user_id"])) {
      header("Location: login.php");
      exit();
    }

    $user_id = $_SESSION["user_id"];
    $is_admin = $_SESSION["is_admin"];
    $servername= 'localhost';
    $dbusername = 'forum_user';
    $dbpassword = 'Felix123!';
    $dbname = 'forum_db';

    $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

    if ($conn->connect_error) {
      die('Connection failed: ' . $conn->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      if (isset($_POST['delete_comment'])) {
        $comment_id = $_POST['comment_id'];
        $post_id = $_POST['post_id'];
        $sql = "DELETE FROM comments WHERE _id = ? AND (author_id = ? OR ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $comment_id, $user_id, $is_admin);
        $stmt->execute();
      } else {
        $content = $_POST['content'];
        if (isset($_POST['post_id'])) {
          $post_id = $_POST['post_id'];
        } elseif (isset($_GET['post_id'])) {
          $post_id = $_GET['post_id'];
        } else {
          die("Post ID not found");
        }
        $date_created = date("d-m-Y H:i");

        $sql = "INSERT INTO comments (_id, author_id, content, date_created, post_id) VALUES (UUID(), ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $user_id, $content, $date_created, $post_id);
        $stmt->execute();
      }
      header("Location: " . $_SERVER['PHP_SELF'] . "?post_id=" . $post_id);
      exit();
    }


    if (isset($_POST['post_id'])) {
      $post_id = $_POST['post_id'];
  } elseif (isset($_GET['post_id'])) {
      $post_id = $_GET['post_id'];
  } else {
      die("Post ID not found");
  }

    $sql = "SELECT * FROM posts WHERE _id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
      $post = $result->fetch_assoc();
      $post_content = $post['content'];
      $post_topic = $post['topic'];
      $post_date = $post['date_created'];
      $author_id = $post['author_id'];

      $sql2 = "SELECT * FROM users WHERE _id = ?";
      $stmt = $conn->prepare($sql2);
      $stmt->bind_param("s", $author_id);
      $stmt->execute();
      $result2 = $stmt->get_result();
      $author = $result2->fetch_assoc();
      $author_name = $author['first_name'] . ' ' . $author['last_name'];

      echo "<h1>$post_topic</h1>";
      echo "<p><i><b>Author: $author_name</i></b></p>";
      echo "<p><b>Date: $post_date</b></p>";
      echo "<pre>  </pre>";
      echo "<p>$post_content</p>";

      echo "<pre>  </pre>";
      echo "<pre>  </pre>";
      echo "<hr></hr>";
      echo "<h3>Comments</h3>";

      $sql3 = "SELECT * FROM comments WHERE post_id = ? ORDER BY date_created";
      $stmt = $conn->prepare($sql3);
      $stmt->bind_param("s", $post_id);
      $stmt->execute();
      $result3 = $stmt->get_result();
  if ($result3->num_rows > 0) {
    while($row = $result3->fetch_assoc()) {
      $comment_content = $row['content'];
      $comment_date = $row['date_created'];
      $comment_author_id = $row['author_id'];

      $sql4 = "SELECT * FROM users WHERE _id = ?";
      $stmt = $conn->prepare($sql4);
      $stmt->bind_param("s", $comment_author_id);
      $stmt->execute();
      $result4 = $stmt->get_result();
      $comment_author = $result4->fetch_assoc();
      $comment_author_name = $comment_author['first_name'] . ' ' . $comment_author['last_name'];
      echo "<hr></hr>";
      echo "<p><i><b>Author: $comment_author_name</i></b></p>";
      echo "<p><b>Date: $comment_date</b></p>";
      echo "<pre>  </pre>";
      echo "<p>$comment_content</p>";

    if ($comment_author_id == $user_id || $is_admin) {
      echo "<form method='post' action=''>";
      echo "<input type='hidden' name='delete_comment' value='true'>";
      echo "<input type='hidden' name='comment_id' value='" . $row['_id'] . "'>";
      echo "<input type='hidden' name='post_id' value='" . $post_id . "'>";
      echo "<input type='submit' value='Delete comment'>";
      echo "</form>";
      }
    }
      } else {
        echo "<p>No comments yet</p>";
      }
      
      echo "<h3>Add Comment</h3>";
      echo "<form method='post' action='" . $_SERVER['PHP_SELF'] . "?post_id=" . $post_id . "'>";
      echo "<input type='hidden' name='post_id' value='$post_id'>";
      echo "<label>Content:</label>";
      echo "<textarea name='content' required></textarea><br><br>";
      echo "<input type='submit' value='Add Comment'>";
      echo "</form>";
      
    } else {
      echo "<p>Post not found</p>";
    }
  
  $conn->close();
    ?>
  </body>
</html>