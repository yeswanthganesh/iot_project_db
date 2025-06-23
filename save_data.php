<?php
$host = 'localhost';
$db = 'new_sensor';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo "DB connection failed: " . $e->getMessage();
    exit;
}

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Validate JSON data
if (!isset($data['reading'], $data['temperature'], $data['humidity'], $data['heatIndex'])) {
    http_response_code(400);
    echo "Missing data.";
    exit;
}

$reading     = $data['reading'];
$temperature = $data['temperature'];
$humidity    = $data['humidity'];
$heatIndex   = $data['heatIndex'];

try {
    $stmt = $pdo->prepare("INSERT INTO sensor_data (reading, temperature, humidity, heatIndex, created_at)
                           VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$reading, $temperature, $humidity, $heatIndex]);
    echo "Inserted successfully.";
} catch (PDOException $e) {
    echo "Insert failed: " . $e->getMessage();
}
?>
