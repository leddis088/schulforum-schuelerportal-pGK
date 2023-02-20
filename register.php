<!DOCTYPE html>
<html>
<head>
  <title>Register</title>
</head>
<body>

  <h1>Register</h1>

  <?php
    // If the user submits the form
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Connect to the database
      $db = mysqli_connect('localhost', 'forum_user', 'Felix123!', 'forum_db');

      // Get the registration information from the form
      $first_name = mysqli_real_escape_string($db, $_POST['first_name']);
      $last_name = mysqli_real_escape_string($db, $_POST['last_name']);
      $username = mysqli_real_escape_string($db, $_POST['username']);
      $password = mysqli_real_escape_string($db, $_POST['password']);
      $class = mysqli_real_escape_string($db, $_POST['class']);

      // Hash the password
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);

      // Insert the user into the database with default rank "schüler"
      $query = "INSERT INTO users (first_name, last_name, username, password, class, rank) VALUES ('$first_name', '$last_name', '$username', '$hashed_password', '$class', 'schüler')";
      mysqli_query($db, $query);

      // Log the user in
      session_start();
      $_SESSION['username'] = $username;
      header('Location: welcome.php');
      exit();
    }
  ?>

  <form method="POST">
    <label>First Name:</label>
    <input type="text" name="first_name" required>
    <br>
    <label>Last Name:</label>
    <input type="text" name="last_name" required>
    <br>
    <label>Username:</label>
    <input type="text" name="username" required>
    <br>
    <label>Password:</label>
    <input type="password" name="password" required>
    <br>
    <label>Class:</label>
    <select name="class" required>
      <option value="1A">1A</option>
      <option value="1B">1B</option>
      <option value="2A">2A</option>
      <option value="2B">2B</option>
      <option value="3A">3A</option>
      <option value="4A">4A</option>
      <option value="4B">4B</option>
    </select>
    <br>
    <button type="submit">Register</button>
  </form>

  <p>Already have an account? <a href="login.php">Login</a></p>

</body>
</html>
