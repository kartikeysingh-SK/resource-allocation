<?php
$host = 'localhost';
$dbname = 'resource_allocation';
$username = 'root';
$password = ''; // Default XAMPP MySQL password is blank

header('Content-Type: text/html; charset=utf-8');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    echo "<h2 style='color: green;'>✓ Database connected successfully!</h2>";

    // Query test: Count the inserted resources
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM resources");
    $rowCount = $stmt->fetchColumn();

    echo "<p>Connected to database <strong>$dbname</strong>.</p>";
    echo "<p>Total seed resources found: <strong>$rowCount</strong></p>";

} catch (PDOException $e) {
    echo "<h2 style='color: red;'>✗ Connection failed!</h2>";
    echo "<p><strong>Error message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}