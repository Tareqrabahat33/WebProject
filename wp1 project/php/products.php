<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// products.php - Product Management System for Louis BOATton

// Database connection
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password
$dbname = "louis_boatton";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create products table if it doesn't exist
function createProductsTable($conn) {
    $sql = "CREATE TABLE IF NOT EXISTS products (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        model VARCHAR(255) NOT NULL,
        length VARCHAR(20) NOT NULL,
        year INT(4) NOT NULL,
        cabins INT(2) NOT NULL,
        description TEXT NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        status VARCHAR(50),
        rent_price DECIMAL(10,2) NOT NULL,
        buy_price DECIMAL(10,2) NOT NULL,
        category VARCHAR(50) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) === FALSE) {
        echo "Error creating table: " . $conn->error;
    }
}

// Initialize table
createProductsTable($conn);

// Handle different operations based on request method and action parameter
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

// Make sure nothing is output before our JSON response
ob_start();

switch ($action) {
    case 'add':
        addProduct($conn);
        break;
    case 'edit':
        editProduct($conn);
        break;
    case 'delete':
        deleteProduct($conn);
        break;
    case 'list':
        getProducts($conn);
        break;
    case 'get':
        getProduct($conn);
        break;
    default:
        // For default case, confirm we have a valid response
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Invalid action: ' . $action]);
        break;
}

// Function to add a new product
function addProduct($conn) {
    // Check if request is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
        return;
    }
    
    // Get form data
    $model = $_POST['model'];
    $length = $_POST['length'];
    $year = $_POST['year'];
    $cabins = $_POST['cabins'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $rentPrice = $_POST['rent_price'];
    $buyPrice = $_POST['buy_price'];
    $category = $_POST['category'];
    
    // Handle image upload
    $imagePath = 'images/default-yacht.png'; // Default image
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = "../images/";
        $fileName = basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $fileName;
        
        // Check if file already exists
        if (file_exists($targetFile)) {
            $fileName = time() . '_' . $fileName;
            $targetFile = $targetDir . $fileName;
        }
        
        // Upload file
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $imagePath = "images/" . $fileName;
        }
    }
    
    // Insert into database
    $sql = "INSERT INTO products (model, length, year, cabins, description, image_path, status, rent_price, buy_price, category) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiissddss", $model, $length, $year, $cabins, $description, $imagePath, $status, $rentPrice, $buyPrice, $category);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Product added successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error adding product: ' . $stmt->error]);
    }
    
    $stmt->close();
}

// Function to edit an existing product
function editProduct($conn) {
    // Check if request is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
        return;
    }
    
    // Get form data
    $id = $_POST['id'];
    $model = $_POST['model'];
    $length = $_POST['length'];
    $year = $_POST['year'];
    $cabins = $_POST['cabins'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $rentPrice = $_POST['rent_price'];
    $buyPrice = $_POST['buy_price'];
    $category = $_POST['category'];
    
    // Check if image is updated
    $imageSql = "";
    $imagePath = '';
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = "../images/";
        $fileName = basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $fileName;
        
        // Check if file already exists
        if (file_exists($targetFile)) {
            $fileName = time() . '_' . $fileName;
            $targetFile = $targetDir . $fileName;
        }
        
        // Upload file
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $imagePath = "images/" . $fileName;
            $imageSql = ", image_path = ?";
        }
    }
    
    // Update database
    $sql = "UPDATE products SET model = ?, length = ?, year = ?, cabins = ?, 
            description = ?, status = ?, rent_price = ?, buy_price = ?, category = ?" . $imageSql . " WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    
    if (!empty($imagePath)) {
        $stmt->bind_param("ssiissddsssi", $model, $length, $year, $cabins, $description, $status, $rentPrice, $buyPrice, $category, $imagePath, $id);
    } else {
        $stmt->bind_param("ssiissddsi", $model, $length, $year, $cabins, $description, $status, $rentPrice, $buyPrice, $category, $id);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Product updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error updating product: ' . $stmt->error]);
    }
    
    $stmt->close();
}

// Function to delete a product
function deleteProduct($conn) {
    // Check if ID is provided
    if (!isset($_GET['id']) && !isset($_POST['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Product ID not provided']);
        return;
    }
    
    $id = isset($_GET['id']) ? $_GET['id'] : $_POST['id'];
    
    // Delete from database
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Product deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting product: ' . $stmt->error]);
    }
    
    $stmt->close();
}

// Function to get all products
function getProducts($conn) {
    // Get category filter if provided
    $category = isset($_GET['category']) ? $_GET['category'] : '';
    
    // Build query
    $sql = "SELECT * FROM products";
    
    if (!empty($category) && $category !== 'all') {
        $sql .= " WHERE category = ?";
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    try {
        // Prepare and execute query
        $stmt = $conn->prepare($sql);
        
        if (!empty($category) && $category !== 'all') {
            $stmt->bind_param("s", $category);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Fetch products
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        
        // Always return JSON for AJAX requests
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'products' => $products]);
        
        $stmt->close();
    } catch (Exception $e) {
        // Handle any errors by returning valid JSON with error message
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        return;
    }
}

// Function to get a single product
function getProduct($conn) {
    // Check if ID is provided
    if (!isset($_GET['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Product ID not provided']);
        return;
    }
    
    $id = $_GET['id'];
    
    // Get product from database
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        echo json_encode(['status' => 'success', 'product' => $product]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Product not found']);
    }
    
    $stmt->close();
}

// Close connection
$conn->close();
?>