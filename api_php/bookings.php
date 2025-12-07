<?php
// ปิด error แสดงใน output แต่ยังบันทึก log
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=utf-8");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

// ========================================
// การเชื่อมต่อฐานข้อมูล
// ========================================
$host = 'localhost';
$dbname = 'mk_shop';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo json_encode([
        'success' => false, 
        'error' => 'Database connection failed',
        'message' => $e->getMessage()
    ]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

// ========================================
// GET - ดึงข้อมูล
// ========================================
if ($method === 'GET') {
    // ✅ รองรับทั้ง GET /bookings.php และ GET /bookings.php?action=list
    try {
        $stmt = $pdo->query("SELECT * FROM bookings ORDER BY created_at DESC");
        $bookings = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true, 
            'data' => $bookings
        ], JSON_UNESCAPED_UNICODE);
        exit();
        
    } catch(PDOException $e) {
        echo json_encode([
            'success' => false, 
            'error' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }
}

// ========================================
// POST - เพิ่ม, แก้ไข, ลบ
// ========================================
elseif ($method === 'POST') {
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);
    
    if (!$data || !isset($data['action'])) {
        echo json_encode([
            'success' => false, 
            'error' => 'Invalid request - missing action'
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $action = $data['action'];

    // ✏️ Update - แก้ไขข้อมูลการจอง
    if ($action === 'update') {
        try {
            $stmt = $pdo->prepare("
                UPDATE bookings 
                SET zone = :zone, 
                    guests = :guests, 
                    time = :time, 
                    customer_name = :customer_name, 
                    phone = :phone, 
                    booking_date = :booking_date, 
                    status = :status
                WHERE booking_id = :booking_id
            ");
            
            $stmt->execute([
                ':zone' => $data['zone'] ?? '',
                ':guests' => $data['guests'] ?? 0,
                ':time' => $data['time'] ?? '',
                ':customer_name' => $data['customer_name'] ?? '',
                ':phone' => $data['phone'] ?? '',
                ':booking_date' => $data['booking_date'] ?? date('Y-m-d'),
                ':status' => $data['status'] ?? 'รอยืนยัน',
                ':booking_id' => $data['booking_id'] ?? 0
            ]);

            echo json_encode([
                'success' => true, 
                'message' => 'อัพเดทสำเร็จ'
            ], JSON_UNESCAPED_UNICODE);
            exit();
            
        } catch(PDOException $e) {
            echo json_encode([
                'success' => false, 
                'error' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }
    }

    // ⚡ Update Status - เปลี่ยนสถานะแบบเร็ว
    elseif ($action === 'update_status') {
        try {
            $stmt = $pdo->prepare("
                UPDATE bookings 
                SET status = :status 
                WHERE booking_id = :booking_id
            ");
            
            $stmt->execute([
                ':status' => $data['status'] ?? '',
                ':booking_id' => $data['booking_id'] ?? 0
            ]);

            echo json_encode([
                'success' => true, 
                'message' => 'เปลี่ยนสถานะสำเร็จ'
            ], JSON_UNESCAPED_UNICODE);
            exit();
            
        } catch(PDOException $e) {
            echo json_encode([
                'success' => false, 
                'error' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }
    }

    // 🗑️ Delete - ลบการจอง
    elseif ($action === 'delete') {
        try {
            $stmt = $pdo->prepare("DELETE FROM bookings WHERE booking_id = :booking_id");
            $stmt->execute([':booking_id' => $data['booking_id'] ?? 0]);

            echo json_encode([
                'success' => true, 
                'message' => 'ลบสำเร็จ'
            ], JSON_UNESCAPED_UNICODE);
            exit();
            
        } catch(PDOException $e) {
            echo json_encode([
                'success' => false, 
                'error' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }
    }

    else {
        echo json_encode([
            'success' => false, 
            'error' => 'Invalid action'
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }
}

// ========================================
// Method ไม่รองรับ
// ========================================
else {
    echo json_encode([
        'success' => false, 
        'error' => 'Method not allowed'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}
?>