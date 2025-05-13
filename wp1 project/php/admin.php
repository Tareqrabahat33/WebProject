<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "product_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission for adding products
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    $sql = "INSERT INTO products (name, price, description) VALUES ('$name', '$price', '$description')";
    if ($conn->query($sql) === TRUE) {
        echo "New product added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM products WHERE id=$id";
    $conn->query($sql);
}

// Handle update
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM products WHERE id=$id");
    $product = $result->fetch_assoc();
}

// Handle the update submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    $sql = "UPDATE products SET name='$name', price='$price', description='$description' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        echo "Product updated successfully";
        header("Location: admin.php");
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>
<!-- 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Page</title>
</head>
<body>
    <h1>Admin Page</h1>

    <h2>Add Product</h2>
    <form method="post" action="">
        <label>Name:</label>
        <input type="text" name="name" required>
        <br>
        <label>Price:</label>
        <input type="text" name="price" required>
        <br>
        <label>Description:</label>
        <textarea name="description" required></textarea>
        <br>
        <input type="submit" name="add" value="Add Product">
    </form>

    <h2>Existing Products</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['price']}</td>
                    <td>{$row['description']}</td>
                    <td>
                        <a href='admin.php?edit={$row['id']}'>Edit</a>
                        <a href='admin.php?delete={$row['id']}'>Delete</a>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No products found</td></tr>";
        }
        ?>
    </table>

    <?php if (isset($product)): ?>
        <h2>Edit Product</h2>
        <form method="post" action="">
            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
            <label>Name:</label>
            <input type="text" name="name" value="<?php echo $product['name']; ?>" required>
            <br>
            <label>Price:</label>
            <input type="text" name="price" value="<?php echo $product['price']; ?>" required>
            <br>
            <label>Description:</label>
            <textarea name="description" required><?php echo $product['description']; ?></textarea>
            <br>
            <input type="submit" name="update" value="Update Product">
        </form>
    <?php endif; ?>

    <a href="products.php">View Products</a>
</body>
</html>

<?php
$conn->close();
?> -->