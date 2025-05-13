<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "users";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $username;
            header("Location: we.php"); // إعادة التوجيه إلى صفحة الترحيب بعد تسجيل الدخول
            exit();
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "No user found with that username.";
    }
}

// إذا كان المستخدم مسجلاً الدخول، يمكنك توجيهه إلى صفحة الترحيب مباشرة
if (isset($_SESSION['username'])) {
    header("Location: we.php");
    exit();
}

$conn->close();
?>
<!-- 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <form method="post" action="">
        <label>Username:</label>
        <input type="text" name="username" required>
        <br>
        <label>Password:</label>
        <input type="password" name="password" required>
        <br>
        <input type="submit" value="Login">
    </form>
    <a href="register.php">Register</a>

    <?php if (isset($error_message)): ?>
        <p style="color:red;"><?php echo $error_message; ?></p>
    <?php endif; ?>
</body>
</html> -->