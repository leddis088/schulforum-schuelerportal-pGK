<?php
$servername = "localhost";
$username = "forum_user";
$password = "Felix123!";
$dbname = "forum_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    session_start();
    if (isset($_SESSION["user_id"])) {
        $user_id = $_SESSION["user_id"];
        $post_name = htmlspecialchars($_POST["post_name"]);
        $post_content = htmlspecialchars($_POST["post_content"]);
        $post_topic = htmlspecialchars($_POST["post_topic"]);

        $post_id = uniqid();
        $date_created = date("Y-m-d H:i:s");
        $stmt = $conn->prepare("INSERT INTO posts (_id, name, author_id, content, date_created, topic) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $post_id, $post_name, $user_id, $post_content, $date_created, $post_topic);
        $stmt->execute();
    }
}

$sql = "SELECT posts._id, posts.name, users.username, posts.content, posts.date_created, posts.topic FROM posts INNER JOIN users ON posts.author_id=users._id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='post'>";
        echo "<h2>" . htmlspecialchars($row["name"]) . "</h2>";
        echo "<p>" . htmlspecialchars($row["content"]) . "</p>";
        echo "<p class='author'>Posted by " . htmlspecialchars($row["username"]) . " on " . htmlspecialchars($row["date_created"]) . "</p>";

        $post_id = $row["_id"];
        $sql_comments = "SELECT comments.content, comments.date_created, users.username FROM comments INNER JOIN users ON comments.author_id=users._id WHERE comments.post_id='$post_id'";
        $result_comments = $conn->query($sql_comments);

        if ($result_comments->num_rows > 0) {
            echo "<h3>Comments</h3>";
            while ($row_comment = $result_comments->fetch_assoc()) {
                echo "<div class='comment'>";
                echo "<p>" . htmlspecialchars($row_comment["content"]) . "</p>";
                echo "<p class='author'>Comment by " . htmlspecialchars($row_comment["username"]) . " on " . htmlspecialchars($row_comment["date_created"]) . "</p>";
                echo "</div>";
            }
        }

        session_start();
        if (isset($_SESSION["user_id"])) {
            echo "<form method='post'>";
            echo "<input type='hidden' name='post_id' value='" . htmlspecialchars($row["_id"]) . "'>";
            echo "<label for='comment_content'>Add a comment:</label>";
            echo "<textarea name='comment_content'></textarea>";
            echo "<input type='submit' value='Add Comment'>";
            echo "</form>";
        }

        echo "</div>";
    }
}
?>

<html>
<head>
    <link rel="stylesheet" href="home_style.css">
    <link rel="icon" type="image/x-icon" href
    <title>Forum</title>
</head>
<body>
    <div>
        <nav>
            <ul>
                <li><a class="active" href="#">Home</a></li>
                <li><a href="#">Neuigkeiten</a></li>
                <li><a href="#">Portal</a></li>
                <li style="float:right"><a href="login.php">Login</a></li>
            </ul>
        </nav>
    </div>
    <div class="main">
        <?php
        session_start();
        if (isset($_SESSION["user_id"])) {
            echo "<form method='post'>";
            echo "<label for='post_name'>Title:</label>";
            echo "<input type='text' name='post_name'>";
            echo "<label for='post_topic'>Topic:</label>";
            echo "<input type='text' name='post_topic'>";
            echo "<label for='post_content'>Content:</label>";
            echo "<textarea name='post_content'></textarea>";
            echo "<input type='submit' value='Add Post'>";
            echo "</form>";
        } else {
            echo "<p>Please <a href='login.php'>log in</a> to add a post.</p>";
        }
        ?>
    </div>
</body>
</html>
