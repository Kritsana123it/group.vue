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
$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

// ตรวจสอบว่ากรอกครบไหม
if (empty($name) || empty($email) || empty($password)) {
    echo json_encode([
        'success' => false,
        'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน'
    ]);
    exit;
}

// ตรวจสอบรูปแบบอีเมล
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'รูปแบบอีเมลไม่ถูกต้อง'
    ]);
    exit;
}

// ตรวจสอบความยาวรหัสผ่าน
if (strlen($password) < 6) {
    echo json_encode([
        'success' => false,
        'message' => 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร'
    ]);
    exit;
}

// ตรวจสอบอีเมลซ้ำ
$checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$checkStmt->execute([$email]);

if ($checkStmt->fetch()) {
    echo json_encode([
        'success' => false,
        'message' => 'อีเมลนี้ถูกใช้งานแล้ว'
    ]);
    exit;
}

// เข้ารหัสรหัสผ่าน
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// บันทึกข้อมูล User ใหม่
$stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, 'customer', NOW())");

if ($stmt->execute([$name, $email, $hashedPassword])) {
    echo json_encode([
        'success' => true,
        'message' => 'สมัครสมาชิกสำเร็จ',
        'user_id' => $pdo->lastInsertId()
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาดในการสมัครสมาชิก'
    ]);
}
?>