<?php
header("Content-Type: application/json; charset=utf-8");

$host = 'localhost';
$dbname = 'mk_shop';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // ✅ สร้างตารางถ้ายังไม่มี
    $sql = "
    CREATE TABLE IF NOT EXISTS `bookings` (
      `booking_id` INT(11) NOT NULL AUTO_INCREMENT,
      `zone` VARCHAR(50) NOT NULL,
      `guests` INT(11) NOT NULL,
      `time` VARCHAR(10) NOT NULL,
      `customer_name` VARCHAR(100) NOT NULL,
      `phone` VARCHAR(20) NOT NULL,
      `booking_date` DATE NOT NULL,
      `status` ENUM('รอยืนยัน', 'ยืนยันแล้ว', 'ยกเลิก', 'เสร็จสิ้น') DEFAULT 'รอยืนยัน',
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`booking_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    $pdo->exec($sql);
    
    // เช็คว่าสร้างสำเร็จหรือไม่
    $stmt = $pdo->query("SHOW TABLES LIKE 'bookings'");
    $table_exists = $stmt->rowCount() > 0;
    
    // ถ้ามีแล้ว ลองใส่ข้อมูลทดสอบ
    if ($table_exists) {
        $pdo->exec("
            INSERT IGNORE INTO bookings 
            (booking_id, zone, guests, time, customer_name, phone, booking_date, status) 
            VALUES 
            (1, 'หน้าต่าง', 2, '11:00', 'ทดสอบระบบ', '0812345678', CURDATE(), 'รอยืนยัน')
        ");
    }
    
    // ดึงข้อมูลทั้งหมด
    $stmt = $pdo->query("SELECT * FROM bookings");
    $data = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'message' => 'Table created successfully!',
        'table_exists' => $table_exists,
        'data_count' => count($data),
        'data' => $data
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
?>
