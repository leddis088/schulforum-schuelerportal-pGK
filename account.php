<?php
session_start();

$servername = "localhost";
$username = "forum_user";
$password = "Felix123!";
$dbname = "forum_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION["user_id"];
$sql = "SELECT * FROM users WHERE _id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $username = $row["username"];
    $class = $row["class"];
    $first_name = $row["first_name"];
    $last_name = $row["last_name"];
    $profile_picture = $row["profile_picture"];
} else {
    die("Error: User not found");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["username"])) {
        $new_username = $_POST["username"];
        $sql = "UPDATE users SET username='$new_username' WHERE _id='$user_id'";
        if ($conn->query($sql) === TRUE) {
            $username = $new_username;
        } else {
            die("Error updating username: " . $conn->error);
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["change_password"])) {
        $current_password = $_POST["current_password"];
        $new_password = $_POST["new_password"];
        $confirm_new_password = $_POST["confirm_new_password"];

        $sql = "SELECT hash FROM users WHERE _id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $hash = $row["hash"];

        if (password_verify($current_password, $hash)) {
            if ($new_password === $confirm_new_password) {
                $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET hash=? WHERE _id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $new_hash, $user_id);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    echo "Password changed successfully!";
                } else {
                    echo "Error updating password.";
                }
            } else {
                echo "New passwords do not match.";
            }
        } else {
            echo "Incorrect current password.";
        }
    }

    if (isset($_POST["first_name"])) {
        $new_first_name = $_POST["first_name"];
        $sql = "UPDATE users SET first_name='$new_first_name' WHERE _id='$user_id'";
        if ($conn->query($sql) === TRUE) {
            $first_name = $new_first_name;
        } else {
            die("Error updating class: " . $conn->error);
        }
    }

    if (isset($_POST["last_name"])) {
        $new_last_name = $_POST["last_name"];
        $sql = "UPDATE users SET last_name='$new_last_name' WHERE _id='$user_id'";
        if ($conn->query($sql) === TRUE) {
            $last_name = $new_last_name;
        } else {
            die("Error updating class: " . $conn->error);
        }
    }

    if (isset($_POST["class"])) {
        $new_class = $_POST["class"];
        $sql = "UPDATE users SET class='$new_class' WHERE _id='$user_id'";
        if ($conn->query($sql) === TRUE) {
            $class = $new_class;
        } else {
            die("Error updating class: " . $conn->error);
        }
    }

    if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] == UPLOAD_ERR_OK) {
        $file_name = $_FILES["profile_picture"]["name"];
        $file_tmp = $_FILES["profile_picture"]["tmp_name"];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_exts = array("jpg", "jpeg", "png", "gif");

        if (in_array($file_ext, $allowed_exts)) {
            $new_file_name = "$user_id.$file_ext";
            $new_file_path = "uploads/$new_file_name";
            if (move_uploaded_file($file_tmp, $new_file_path)) {
                $sql = "UPDATE users SET profile_picture='$new_file_path' WHERE _id='$user_id'";
                if ($conn->query($sql) === TRUE) {
                    $profile_picture = $new_file_path;
                } else {
                    die("Error updating profile picture: " . $conn->error);
                }
            } else {
                die("Error uploading file");
            }
        } else {
            die("Invalid file type");
        }
    }

    if (isset($_POST["delete"])) {
        $sql = "DELETE FROM users WHERE _id='$user_id'";
        if ($conn->query($sql) === TRUE) {
            session_destroy();
            header("Location: login.php");
        } else {
            die("Error deleting account: " . $conn->error);
        }
    }
}

if (!$profile_picture) {
    $initials = strtoupper(substr($first_name, 0, 1) . substr($last_name, 0, 1));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Account Settings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>
<li style="float:right"><a href="home.php">Back</a></li>
<h1>Account Settings</h1>
    <form method="post" enctype="multipart/form-data">
        <label>Username:</label>
        <input type="text" name="username" value="<?php echo $username; ?>"><br><br>
        <label>first_name:</label>
        <input type="text" name="first_name" value="<?php echo $first_name; ?>"><br><br>
        <label>Current Password:</label>
        <input type="password" name="current_password"><br><br>
        <label> New Password:</label>
        <input type="password" name="new_password">
        <input type="password" name="confirm_new_password"><br>
        <input type="submit" name="change_password" value="Change Password"><br><br>
        <label>last_name:</label>
        <input type="text" name="last_name" value="<?php echo $last_name; ?>"><br><br>
        <label>Class:</label>
        <input type="text" name="class" value="<?php echo $class; ?>"><br><br>
        <label>Profile Picture:</label>
        <img src="<?php echo $profile_picture ? $profile_picture : "uploads/$user_id.jpg"; ?>" width="50" height="50"><br>
        <input type="file" name="profile_picture"><br><br>
        <input type="submit" value="Save Changes">
    </form>
    <br>
    <form method="post" onsubmit="return confirm('Are you sure you want to delete your account?')">
        <input type="hidden" name="delete">
        <input type="submit" value="Delete Account">
    </form>
</body>
</html>
<?php
$conn->close();
?>