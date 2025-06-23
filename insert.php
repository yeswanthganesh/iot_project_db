<?php
$host = 'localhost';
$db = 'new_sensor';              // Your database name
$user = 'root';                  // Default XAMPP user
$pass = '';                      // Default XAMPP password is empty
$charset = 'utf8mb4';

// Set up DSN and options
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo "DB connection failed: " . $e->getMessage();
    exit;
}

// Get POST data
$reading     = $_POST['reading']     ?? null;
$temperature = $_POST['temperature'] ?? null;
$humidity    = $_POST['humidity']    ?? null;
$heatIndex   = $_POST['heatIndex']   ?? null;

// Validate data
if ($reading === null || $temperature === null || $humidity === null || $heatIndex === null) {
    http_response_code(400); // Bad Request
    echo "Missing one or more required POST parameters.";
    exit;
}

// Prepare and insert
try {
    $stmt = $pdo->prepare("INSERT INTO sensor_data (reading, temperature, humidity, heatIndex, created_at)
                           VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$reading, $temperature, $humidity, $heatIndex]);
    echo "Data inserted successfully.";
} catch (PDOException $e) {
    echo "Insert failed: " . $e->getMessage();
}
?>
