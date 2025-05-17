<?php
// Include database configuration
require_once 'config.php';

// Initialize variables
$name = $email = $password = "";
$nameErr = $emailErr = $passwordErr = "";
$isValid = true;
$registrationSuccess = false;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if this is a registration form
    if (isset($_POST["action"]) && $_POST["action"] == "register") {
        // Validate name
        if (empty($_POST["name"])) {
            $nameErr = "Name is required";
            $isValid = false;
        } else {
            $name = trim($_POST["name"]);
        }

        // Validate email
        if (empty($_POST["email"])) {
            $emailErr = "Email is required";
            $isValid = false;
        } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
            $isValid = false;
        } else {
            $email = trim($_POST["email"]);
            
            // Check if email already exists
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $emailErr = "Email already registered. Please use a different email or login.";
                $isValid = false;
            }
            
            $stmt->close();
        }

        // Validate password
        if (empty($_POST["password"])) {
            $passwordErr = "Password is required";
            $isValid = false;
        } elseif (strlen($_POST["password"]) < 6) {
            $passwordErr = "Password must be at least 6 characters long";
            $isValid = false;
        } else {
            $password = trim($_POST["password"]);
        }

        // If all inputs are valid, register the user
        if ($isValid) {
            // Hash the password for security
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user into database
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashedPassword);
            
            if ($stmt->execute()) {
                $registrationSuccess = true;
                // Redirect to login page after successful registration
                header("Location: /wp1%20project/wp1%20project/login.html?registered=true");
                exit;
            } else {
                $generalError = "Error: " . $stmt->error;
            }
            
            $stmt->close();
        }
    }
    // Check if this is a login form
    elseif (isset($_POST["action"]) && $_POST["action"] == "login") {
        // Validate email
        if (empty($_POST["email"])) {
            $emailErr = "Email is required";
            $isValid = false;
        } else {
            $email = trim($_POST["email"]);
        }

        // Validate password
        if (empty($_POST["password"])) {
            $passwordErr = "Password is required";
            $isValid = false;
        } else {
            $password = trim($_POST["password"]);
        }

        // If inputs are valid, attempt login
        if ($isValid) {
            // Check if user exists and verify password
            $stmt = $conn->prepare("SELECT id, name, email, password, is_admin FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                
                if (password_verify($password, $user["password"])) {
                    // Start a session
                    session_start();
                    
                    // Store user data in session
                    $_SESSION["user_id"] = $user["id"];
                    $_SESSION["user_name"] = $user["name"];
                    $_SESSION["user_email"] = $user["email"];
                    $_SESSION["is_admin"] = (bool)$user["is_admin"];
                    $_SESSION["logged_in"] = true;
                    
                    // Redirect to main page after successful login
                    header("Location: /wp1%20project/wp1%20project/main_page.html");
                    exit;
                } else {
                    $passwordErr = "Incorrect password";
                }
            } else {
                $emailErr = "Email not found. Please register first.";
            }
            
            $stmt->close();
        }
    }
}
?>
<!-- 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Validation Form</title>
</head>
<body>
    <h1>Registration Form</h1>
    <form method="post" action="">
        <label for="name">Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>">
        <span style="color:red;"><?php echo $nameErr; ?></span>
        <br>

        <label for="email">Email:</label>
        <input type="text" name="email" value="<?php echo htmlspecialchars($email); ?>">
        <span style="color:red;"><?php echo $emailErr; ?></span>
        <br>

        <label for="password">Password:</label>
        <input type="password" name="password">
        <span style="color:red;"><?php echo $passwordErr; ?></span>
        <br>

        <input type="submit" value="Submit">
    </form>
</body>
</html> -->