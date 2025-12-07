<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include 'condb.php';

$headers = getallheaders();
$token = null;

if (isset($headers['Authorization'])) {
    $authHeader = $headers['Authorization'];
    if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
        $token = $matches[1];
    }
}

if ($token) {
    try {
        // ✅ ลบ token ออกจากฐานข้อมูล
        $stmt = $conn->prepare("UPDATE admin SET token = NULL, token_expires = NULL WHERE token = ?");
        $stmt->execute([$token]);
    } catch (PDOException $e) {
        // Silent fail
    }
}

echo json_encode([
    "success" => true,
    "message" => "Logged out successfully"
]);
?>