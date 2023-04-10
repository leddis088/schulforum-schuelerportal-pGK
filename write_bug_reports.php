<!DOCTYPE html>
<html>
    <head>
        <title>write a bug report</title>
        <link rel="stylesheet" href="style.css">
        <script>
            function toggleOtherTextField() {
                let selectElement = document.getElementById('topic');
                let otherTextField = document.getElementById('otherTextField');
                if (selectElement.value == 'other') {
                    otherTextField.style.display = 'block';
                } else {
                    otherTextField.style.display = 'none';
                }
            }
        </script>
    </head>
    <nav>
        <li><a class="active" href="bug_report.php">Back</a></li>
    </nav>
    <body>
        <h1>Bug reports</h1>
        <form method="post" action="">
            <label for="topic">Topic</label>
            <select id="topic" name="topic" onchange="toggleOtherTextField()">
            <option value="login error">login error</option>
            <option value="register error">register error</option>
            <option value="portal error">portal error</option>
            <option value="account error">account error</option>
            <option value="post error">post error</option>
            <option value="post deletion error">post deletion error</option>
            <option value="comment error">comment error</option>
            <option value="comment deletion error">comment deletion error</option>
            <option value="security problems">security problems</option>
            <option value="other">other</option>
            </select><br><br>
            <textarea type="text" name="otherTextField" id="otherTextField" style="display:none;"></textarea><br><br>
            <label for="content">Content:</label>
            <textarea name="content" id="content" required></textarea><br><br>
            <input type="submit" value="Submit Bug Report">
        </form>

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
                $topic = $_POST['topic'];
                $content = $_POST['content'];
                $date_created = date("Y-m-d H:i:s");
                $author_id = $user_id;

                if ($topic == 'other') {
                    $topic = $_POST['otherTextField'];
                }

                $sql = "INSERT INTO bug_reports (_id, author_id, content, date_created, bug_id) VALUES (UUID(), ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $author_id, $content, $date_created, $topic);
                $stmt->execute();
            }
        ?>
    </body>
</html>