<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

// ========================================
// การเชื่อมต่อฐานข้อมูล
// ========================================
$host = 'localhost';
$dbname = 'shop';
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
    $action = $_GET['action'] ?? 'list';
    
    // 👤 Customer History - ดึงประวัติการจองของลูกค้าแต่ละคน
    if ($action === 'customer_history') {
        $user_id = $_GET['user_id'] ?? null;

        if (!$user_id) {
            echo json_encode([
                'success' => false, 
                'message' => 'ไม่พบ user_id'
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }

        try {
            $stmt = $pdo->prepare("
                SELECT 
                    booking_id,
                    zone as table_number,
                    guests,
                    time as booking_time,
                    booking_date,
                    status,
                    customer_name,
                    phone,
                    created_at
                FROM bookings 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC
            ");
            
            $stmt->execute([':user_id' => $user_id]);
            $bookings = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true, 
                'bookings' => $bookings
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
    
    // 📊 Dashboard - ดึงข้อมูลสถิติสำหรับแดชบอร์ด
    elseif ($action === 'dashboard') {
        try {
            $stmt = $pdo->query("
                SELECT COUNT(*) as total 
                FROM bookings 
                WHERE DATE(booking_date) = CURDATE()
            ");
            $result = $stmt->fetch();
            $todayBookings = $result ? (int)$result['total'] : 0;
            
            $stmt = $pdo->query("
                SELECT COUNT(*) as total 
                FROM bookings 
                WHERE status = 'รอยืนยัน'
            ");
            $result = $stmt->fetch();
            $pendingBookings = $result ? (int)$result['total'] : 0;
            
            $stmt = $pdo->query("
                SELECT * FROM bookings 
                ORDER BY created_at DESC 
                LIMIT 20
            ");
            $recentBookings = $stmt->fetchAll();
            
            $stmt = $pdo->query("
                SELECT zone, COUNT(*) as booking_count 
                FROM bookings 
                WHERE booking_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                GROUP BY zone 
                ORDER BY booking_count DESC 
                LIMIT 5
            ");
            $popularZones = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'todayBookings' => $todayBookings,
                'pendingBookings' => $pendingBookings,
                'recentBookings' => $recentBookings,
                'popularTables' => $popularZones
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
    
    // 📋 List - ดึงรายการจองทั้งหมด (สำหรับ Admin)
    elseif ($action === 'list') {
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
    
    // 🔍 Check Availability - เช็คความว่างของโซนตามเวลา
    elseif ($action === 'check_availability') {
        try {
            $stmt = $pdo->prepare("
                SELECT zone, time, COUNT(*) as booked_count
                FROM bookings 
                WHERE status IN ('ยืนยันแล้ว', 'รอยืนยัน')
                AND booking_date >= CURDATE()
                GROUP BY zone, time
            ");
            $stmt->execute();
            $results = $stmt->fetchAll();
            
            $availability = [];
            foreach ($results as $row) {
                $key = $row['zone'] . '_' . $row['time'];
                $availability[$key] = (int)$row['booked_count'];
            }
            
            echo json_encode([
                'success' => true, 
                'data' => $availability
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

    // 📝 Add - เพิ่มการจองใหม่
    if ($action === 'add') {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO bookings (user_id, zone, guests, time, customer_name, phone, booking_date, status, created_at) 
                VALUES (:user_id, :zone, :guests, :time, :customer_name, :phone, :booking_date, 'รอยืนยัน', NOW())
            ");
            
            $stmt->execute([
                ':user_id' => $data['user_id'] ?? null,
                ':zone' => $data['zone'] ?? '',
                ':guests' => $data['guests'] ?? 0,
                ':time' => $data['time'] ?? '',
                ':customer_name' => $data['name'] ?? '',
                ':phone' => $data['phone'] ?? '',
                ':booking_date' => $data['booking_date'] ?? date('Y-m-d')
            ]);

            echo json_encode([
                'success' => true, 
                'message' => 'จองสำเร็จ',
                'booking_id' => $pdo->lastInsertId()
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

    // ✏️ Update - แก้ไขข้อมูลการจอง
    elseif ($action === 'update') {
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

else {
    echo json_encode([
        'success' => false, 
        'error' => 'Method not allowed'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}
?>