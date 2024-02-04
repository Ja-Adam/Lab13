<?php
$host = 'db';  // Docker Compose service name
$db = 'mydatabase';
$user = 'db_user';
$pass = 'db_password';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// Create tables if not exist
$pdo->exec("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100)
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_name VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES users(id)
)");

// Insert sample data
$pdo->exec("INSERT INTO users (name, email) VALUES ('John Doe', 'john@example.com')");
$pdo->exec("INSERT INTO orders (user_id, product_name) VALUES (1, 'Product A')");
$pdo->exec("INSERT INTO orders (user_id, product_name) VALUES (1, 'Product B')");

// Display data
echo "<h1>Users</h1>";
foreach ($pdo->query("SELECT * FROM users") as $row) {
    echo "<p>User ID: {$row['id']}, Name: {$row['name']}, Email: {$row['email']}</p>";
}

echo "<h1>Orders</h1>";
foreach ($pdo->query("SELECT * FROM orders") as $row) {
    echo "<p>Order ID: {$row['id']}, User ID: {$row['user_id']}, Product: {$row['product_name']}</p>";
}
?>
