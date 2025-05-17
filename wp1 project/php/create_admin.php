<?php
// Include database configuration
require_once 'config.php';

// Check if the is_admin column exists in the users table, and add it if it doesn't
function ensureAdminColumnExists($conn) {
    // Check if column exists
    $checkCol = $conn->query("SHOW COLUMNS FROM users LIKE 'is_admin'");
    
    // If the column doesn't exist, add it
    if ($checkCol->num_rows === 0) {
        $addColumn = "ALTER TABLE users ADD COLUMN is_admin TINYINT(1) DEFAULT 0";
        
        if ($conn->query($addColumn) === TRUE) {
            echo "<div class='message success'>Table structure updated to support admin accounts.</div>";
        } else {
            echo "<div class='message error'>Error updating table structure: " . $conn->error . "</div>";
        }
    }
}

// Ensure the admin column exists before proceeding
ensureAdminColumnExists($conn);

// Initialize variables
$admin_email = '';
$admin_password = '';
$admin_name = '';
$message = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    if (empty($_POST["name"])) {
        $message = "Name is required";
    } else {
        $admin_name = trim($_POST["name"]);
    }

    // Validate email
    if (empty($_POST["email"])) {
        $message = "Email is required";
    } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format";
    } else {
        $admin_email = trim($_POST["email"]);
        
        // Check if email already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $admin_email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // If the user exists but is not an admin, make them an admin
            if ($user['is_admin'] == 0) {
                $update_stmt = $conn->prepare("UPDATE users SET is_admin = 1 WHERE email = ?");
                $update_stmt->bind_param("s", $admin_email);
                
                if ($update_stmt->execute()) {
                    $message = "Success! The user with email " . htmlspecialchars($admin_email) . " has been upgraded to admin status.";
                } else {
                    $message = "Error updating user: " . $conn->error;
                }
                
                $update_stmt->close();
            } else {
                $message = "This user is already an admin.";
            }
        } else {
            // If the email doesn't exist, proceed with new admin creation
            
            // Validate password
            if (empty($_POST["password"])) {
                $message = "Password is required";
            } elseif (strlen($_POST["password"]) < 6) {
                $message = "Password must be at least 6 characters long";
            } else {
                $admin_password = trim($_POST["password"]);
                
                // Hash the password for security
                $hashedPassword = password_hash($admin_password, PASSWORD_DEFAULT);
                
                // Insert new admin user into database
                $stmt = $conn->prepare("INSERT INTO users (name, email, password, is_admin) VALUES (?, ?, ?, ?)");
                $isAdmin = 1; // Set admin status to true
                $stmt->bind_param("sssi", $admin_name, $admin_email, $hashedPassword, $isAdmin);
                
                if ($stmt->execute()) {
                    $message = "Success! Admin account created with email: " . htmlspecialchars($admin_email);
                } else {
                    $message = "Error creating admin account: " . $conn->error;
                }
                
                $stmt->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin Account - Louis BOATton</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/auth-nav.css">
    <style>
        .admin-form-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-left: 4px solid var(--navy-blue);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: var(--navy-blue);
        }
        
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        .btn-admin {
            background-color: var(--navy-blue);
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        
        .btn-admin:hover {
            background-color: var(--gold);
            color: var(--navy-blue);
        }
        
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <nav>
        <div class="nav-brand">
            <img src="../images/BOATTON SCARP-01.png" alt="Louis BOATton Logo" height="60">
            <span class="brand-text">Louis BOATton</span>
        </div>
        <div class="nav-toggle" tabindex="0" aria-label="Toggle navigation" onclick="toggleNav()" onkeypress="if(event.key==='Enter'){toggleNav();}">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="nav-links" id="navLinks">
            <a href="../main_page.html">Home</a>
            <a href="../product_page.html">Products</a>
            <a href="../contact_page.html">Contact us</a>
        </div>
    </nav>

    <main class="container">
        <div class="page-header">
            <h1>Create Admin Account</h1>
            <p>This page is used to create or promote an administrator account for Louis BOATton website</p>
        </div>

        <section class="admin-form-container">
            <?php if (!empty($message)): ?>
                <div class="message <?php echo strpos($message, 'Success') !== false ? 'success' : 'error'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="name">Full Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                    <small>Password must be at least 6 characters long</small>
                </div>
                
                <button type="submit" class="btn-admin">Create Admin Account</button>
            </form>
            
            <div style="margin-top: 20px;">
                <p><a href="../main_page.html">&larr; Return to Home Page</a></p>
            </div>
        </section>
    </main>

    <script>
        function toggleNav() {
            var navLinks = document.getElementById('navLinks');
            navLinks.classList.toggle('active');
        }
    </script>
</body>
</html>
