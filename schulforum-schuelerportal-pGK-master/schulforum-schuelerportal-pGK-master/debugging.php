<!DOCTYPE html>
<html>
  <head>
    <title>Forum</title>
    <link rel="stylesheet" href="home_style.css">
  </head>
  <body>
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

      if ($author_id == $user_id || $is_admin) {
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
    $content = $_POST['content'];
    $topic = $_POST['topic'];
    $date_created = date("Y-m-d H:i:s");

    $sql = "INSERT INTO posts (_id, name, author_id, content, date_created, topic) VALUES (UUID(), ?, (SELECT username FROM users WHERE _id = ?), ?, ?, ?)";
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
  <li style="float:right"><a href="logout.php">Logout</a></li>
</ul>
</nav>

    <h1>Forum</h1>

    <h2>Create Post</h2>
    <form method="post" action="">
      <label>Content:</label>
      <textarea name="content" required></textarea><br><br>
      <label>Topic:</label>
      <input type="text" name="topic" required><br><br>
      <input type="submit" value="Create Post">
    </form>

    <h2>Posts</h2>

    <?php
    $sql = "SELECT posts._id, posts.name, posts.author_id, posts.content, posts.date_created, posts.topic, users.username
            FROM posts INNER JOIN users
        ON posts.author_id = users._id
        ORDER BY posts.date_created DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $post_id = $row['_id'];
    $post_name = $row['name'];
    $post_content = $row['content'];
    $post_topic = $row['topic'];
    $post_date = $row['date_created'];
    $author_name = $row['username'];

    echo "<h3>$post_content</h3>";
    echo "<p>Topic: $post_topic</p>";
    echo "<p>Author: $author_name</p>";
    echo "<p>Date: $post_date</p>";

    if ($row['author_id'] == $user_id || $is_admin) {
      echo "<form method='post' action=''>";
      echo "<input type='hidden' name='delete_post' value='$post_id'>";
      echo "<input type='submit' value='Delete Post'>";
      echo "</form>";
    }

    echo "<form method='post' action='comment.php'>";
    echo "<input type='hidden' name='post_id' value='$post_id'>";
    echo "<input type='submit' value='Verfasse einen Kommentar'>";
    echo "</form>";

    echo "<h4>Comments</h4>";

    $sql2 = "SELECT * FROM comments WHERE post_id = ?";
    $stmt = $conn->prepare($sql2);
    $stmt->bind_param("s", $post_id);
    $stmt->execute();
    $result2 = $stmt->get_result();

    if ($result2->num_rows > 0) {
      while($row2 = $result2->fetch_assoc()) {
        $comment_content = $row2['content'];
        $comment_date = $row2['date_created'];
        $comment_author_id = $row2['author_id'];

        $sql3 = "SELECT username FROM users WHERE _id = ?";
        $stmt = $conn->prepare($sql3);
        $stmt->bind_param("s", $comment_author_id);
        $stmt->execute();
        $result3 = $stmt->get_result();
        $comment_author = $result3->fetch_assoc();
        $comment_author_name = $comment_author['username'];

        echo "<p>$comment_content</p>";
        echo "<p>Author: $comment_author_name</p>";
        echo "<p>Date: $comment_date</p>";
      }
    } else {
      echo "<p>No comments yet</p>";
    }
  }
}

$conn->close();
?>
  </body>
</html>