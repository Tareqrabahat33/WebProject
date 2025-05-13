<?php
$name = $email = $password = "";
$nameErr = $emailErr = $passwordErr = "";
$isValid = true;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    // If all inputs are valid
    if ($isValid) {
        // Here you can proceed with further processing (like saving data to a database)
        echo "Form submitted successfully!";
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