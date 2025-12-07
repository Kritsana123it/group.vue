<?php
// ตั้งค่า CORS และ JSON
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=UTF-8');

// จัดการ OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// เชื่อมต่อ Database
$host = 'localhost';
$dbname = 'shop';
$username = 'root';
$password = ''; // ใส่รหัสผ่าน MySQL ถ้ามี

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'ไม่สามารถเชื่อมต่อฐานข้อมูลได้'
    ]);
    exit;
}

// รับข้อมูลจาก Vue
$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

// ตรวจสอบว่ากรอกครบไหม
if (empty($email) || empty($password)) {
    echo json_encode([
        'success' => false,
        'message' => 'กรุณากรอกอีเมลและรหัสผ่าน'
    ]);
    exit;
}

// ค้นหา User จาก Database
$stmt = $pdo->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// ตรวจสอบว่ามี User นี้ไหม
if (!$user) {
    echo json_encode([
        'success' => false,
        'message' => 'ไม่พบอีเมลนี้ในระบบ'
    ]);
    exit;
}

// ตรวจสอบรหัสผ่าน
if (password_verify($password, $user['password'])) {
    // Login สำเร็จ - อัปเดตเวลา login
    $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $updateStmt->execute([$user['id']]);
    
    echo json_encode([
        'success' => true,
        'message' => 'เข้าสู่ระบบสำเร็จ',
        'user' => [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ]
    ]);
} else {
    // รหัสผ่านผิด
    echo json_encode([
        'success' => false,
        'message' => 'รหัสผ่านไม่ถูกต้อง'
    ]);
}
?>