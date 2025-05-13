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

$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>
<!-- 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Page</title>
</head>
<body>
    <h1>Products</h1>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Description</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['price']}</td>
                    <td>{$row['description']}</td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No products found</td></tr>";
        }
        ?>
    </table>
    <a href="admin.php">Admin Page</a>
</body>
</html>

<?php
$conn->close();
?> -->