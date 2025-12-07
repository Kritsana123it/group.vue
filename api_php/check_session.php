<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include 'condb.php';

// ✅ ดึง token จาก header
$headers = getallheaders();
$token = null;

if (isset($headers['Authorization'])) {
    $authHeader = $headers['Authorization'];
    if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
        $token = $matches[1];
    }
}

if (!$token) {
    echo json_encode([
        "success" => false,
        "message" => "ไม่พบ token"
    ]);
    exit();
}

try {
    // ✅ ตรวจสอบ token ในฐานข้อมูล
    $stmt = $conn->prepare("SELECT id, username FROM admin WHERE token = ? AND token_expires > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

   if (res.data.success) {
  localStorage.setItem("token", res.data.token); // เพิ่ม token
  localStorage.setItem("username", this.username);
  this.$router.push("/");
}

    } else {
        echo json_encode([
            "success" => false,
            "message" => "Token หมดอายุหรือไม่ถูกต้อง"
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}
?>