<?php
// Connect to MySQL
try {
    $pdo = new PDO("mysql:host=localhost;dbname=new_sensor", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Attempt to fetch latest row by recorded timestamp instead of non-existent ID
$stmt = $pdo->query("SELECT * FROM sensor_data ORDER BY created_at DESC LIMIT 1");
$data = $stmt->fetch(PDO::FETCH_ASSOC);

// Assign values with fallback
$reading = isset($data['reading']) ? $data['reading'] : "N/A";
$temp = isset($data['temperature']) ? $data['temperature'] : "N/A";
$hum = isset($data['humidity']) ? $data['humidity'] : "N/A";
$hi = isset($data['heatIndex']) ? $data['heatIndex'] : "N/A";
$time = isset($data['created_at']) ? $data['created_at'] : "N/A";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>DHT Sensor Latest Reading</title>
  <meta http-equiv="refresh" content="5"> <!-- Refresh every 5 seconds -->
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 40px;
      background-color: #f4f4f4;
      color: #333;
    }
    h2 {
      margin-bottom: 20px;
    }
    .reading {
      font-size: 20px;
      margin: 10px 0;
    }
    .timestamp {
      font-size: 14px;
      color: #666;
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <h2>DHT Sensor - Latest Reading</h2>
  <div class="reading"><strong>Reading #:</strong> <?= htmlspecialchars($reading) ?></div>
  <div class="reading"><strong>Temperature:</strong> <?= htmlspecialchars($temp) ?> °C</div>
  <div class="reading"><strong>Humidity:</strong> <?= htmlspecialchars($hum) ?> %</div>
  <div class="reading"><strong>Heat Index:</strong> <?= htmlspecialchars($hi) ?> °C</div>
  <div class="timestamp">Last updated: <?= htmlspecialchars($time) ?></div>
</body>
</html>
