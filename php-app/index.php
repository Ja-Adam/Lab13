<?php
$host = 'db';  // Docker Compose service name
$db = 'mydatabase';
$user = 'db_user';
$pass = 'db_password';
$charset = 'utf8mb4';

$mysqli = new mysqli($host, $user, $pass, $db);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Create tables if not exist
$mysqli->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100)
)");

$mysqli->query("CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_name VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES users(id)
)");

// Insert sample data
$mysqli->query("INSERT INTO users (name, email) VALUES ('John Doe', 'john@example.com')");
$mysqli->query("INSERT INTO orders (user_id, product_name) VALUES (1, 'Product A')");
$mysqli->query("INSERT INTO orders (user_id, product_name) VALUES (1, 'Product B')");

// Display data
echo "<h1>Users</h1>";
$resultUsers = $mysqli->query("SELECT * FROM users");
while ($row = $resultUsers->fetch_assoc()) {
    echo "<p>User ID: {$row['id']}, Name: {$row['name']}, Email: {$row['email']}</p>";
}

echo "<h1>Orders</h1>";
$resultOrders = $mysqli->query("SELECT * FROM orders");
while ($row = $resultOrders->fetch_assoc()) {
    echo "<p>Order ID: {$row['id']}, User ID: {$row['user_id']}, Product: {$row['product_name']}</p>";
}

$mysqli->close();
?>
