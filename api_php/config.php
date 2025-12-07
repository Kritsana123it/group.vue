<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=UTF-8');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // ใส่รหัสผ่าน MySQL ถ้ามี
define('DB_NAME', 'shop');

// Create connection
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die(json_encode([
            'success' => false,
            'message' => 'Database connection failed'
        ]));
    }
    
    $conn->set_charset('utf8mb4');
    return $conn;
}
?>