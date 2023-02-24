<!DOCTYPE html>
<html>
<head>
	<title>Forum</title>
	<link rel="stylesheet" href="">
</head>
<body>
	<?php
    error_reporting(E_ALL);
	session_start();

	if (!isset($_SESSION["user_id"])) {
	    header("Location: login.php");
	    exit();
	}

	$servername = "localhost";
	$username = "forum_user";
	$password = "Felix123!";
	$dbname = "forum_db";

	$conn = new mysqli($servername, $username, $password, $dbname);

	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
	    if (isset($_POST["post_name"]) && isset($_POST["post_content"]) && isset($_POST["post_topic"])) {
	        $post_name = htmlspecialchars($_POST["post_name"]);
	        $post_content = htmlspecialchars($_POST["post_content"]);
	        $post_topic = htmlspecialchars($_POST["post_topic"]);
	        $post_id = uniqid();
	        $date_created = date("Y-m-d H:i:s");
	        $stmt = $conn->prepare("INSERT INTO posts (_id, name, author_id, content, date_created, topic) VALUES (?, ?, ?, ?, ?, ?)");
	        $stmt->bind_param("ssssss", $post_id, $post_name, $_SESSION["user_id"], $post_content, $date_created, $post_topic);
	        $stmt->execute();
	    } else if (isset($_POST["comment_content"]) && isset($_POST["post_id"])) {
	        $comment_content = htmlspecialchars($_POST["comment_content"]);
	        $post_id = htmlspecialchars($_POST["post_id"]);
	        $comment_id = uniqid();
	        $date_created = date("Y-m-d H:i:s");
	        $stmt = $conn->prepare("INSERT INTO comments (_id, author_id, content, date_created, post_id) VALUES (?, ?, ?, ?, ?)");
	        $stmt->bind_param("sssss", $comment_id, $_SESSION["user_id"], $comment_content, $date_created, $post_id);
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
                    echo "<form method='post'>";
                    echo "<input type='hidden' name='post_id' value='" . htmlspecialchars($row["_id"]) . "'>";
                    echo "<label for='comment_content'>Add a comment:</label>";
                    echo "<textarea name='comment_content'></textarea>";
                    echo "<input type='submit' value='Add Comment'>";
                    echo "</form>";
                    echo "</div>";
                }
            }
    }
    
    $conn->close();
    ?>
    <nav>
        <ul>
            <li><a class="active" href="#">Home</a></li>
            <li><a href="#">Neuigkeiten</a></li>
            <li><a href="#">Portal</a></li>
            <li style="float:right"><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    <div class="main">
        <form method='post'>
            <label for='post_name'>Title:</label>
            <input type='text' name='post_name'>
            <label for='post_topic'>Topic:</label>
            <input type='text' name='post_topic'>
            <label for='post_content'>Content:</label>
            <textarea name='post_content'></textarea>
            <input type='submit' value='Add Post'>
        </form>
    </div>
    </body>
</html>    