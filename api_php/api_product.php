<?php
// ✅ ปิดการแสดง error แบบ HTML
error_reporting(0);
ini_set('display_errors', 0);

// ✅ เพิ่ม CORS headers ก่อนอื่นเสมอ
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=utf-8");

// จัดการ Preflight Request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ✅ ตรวจสอบว่าไฟล์ condb.php มีอยู่หรือไม่
if (!file_exists('condb.php')) {
    echo json_encode([
        "success" => false,
        "error" => "ไม่พบไฟล์ condb.php"
    ]);
    exit();
}

// ✅ Try-catch สำหรับ include
try {
    include 'condb.php';
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => "เชื่อมต่อฐานข้อมูลไม่สำเร็จ: " . $e->getMessage()
    ]);
    exit();
}

// ✅ ตรวจสอบว่าตัวแปร $conn มีอยู่หรือไม่
if (!isset($conn)) {
    echo json_encode([
        "success" => false,
        "error" => "ไม่สามารถเชื่อมต่อฐานข้อมูลได้"
    ]);
    exit();
}

$action = $_POST['action'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action) {
    // เพิ่ม / แก้ไข / ลบ
    switch($action) {

        case 'add':
            try {
                $product_name = $_POST['product_name'] ?? '';
                $type_id = $_POST['type_id'] ?? '';
                $description = $_POST['description'] ?? '';
                $price = $_POST['price'] ?? 0;
                $stock = $_POST['stock'] ?? 0;

                // อัพโหลดไฟล์รูป
                $filename = null;
                if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                    $targetDir = "uploads/";
                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0777, true);
                    }
                    $filename = time() . '_' . basename($_FILES['image']['name']);
                    $targetFile = $targetDir . $filename;
                    move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);
                }

                $sql = "INSERT INTO products (product_name, type_id, description, price, stock, image)
                        VALUES (:product_name, :type_id, :description, :price, :stock, :image)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':product_name', $product_name);
                $stmt->bindParam(':type_id', $type_id);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':price', $price);
                $stmt->bindParam(':stock', $stock);
                $stmt->bindParam(':image', $filename);

                if ($stmt->execute()) {
                    echo json_encode(["success" => true, "message" => "เพิ่มสินค้าสำเร็จ"]);
                } else {
                    echo json_encode(["success" => false, "error" => "เพิ่มสินค้าล้มเหลว"]);
                }
            } catch (Exception $e) {
                echo json_encode(["success" => false, "error" => "เกิดข้อผิดพลาด: " . $e->getMessage()]);
            }
            break;

        case 'update':
            try {
                $product_id = $_POST['product_id'] ?? 0;
                $product_name = $_POST['product_name'] ?? '';
                $type_id = $_POST['type_id'] ?? '';
                $description = $_POST['description'] ?? '';
                $price = $_POST['price'] ?? 0;
                $stock = $_POST['stock'] ?? 0;

                // อัพโหลดไฟล์รูป
                if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                    $targetDir = "uploads/";
                    $filename = time() . '_' . basename($_FILES['image']['name']);
                    $targetFile = $targetDir . $filename;
                    move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);
                    $imageSQL = ", image = :image";
                } else {
                    $imageSQL = "";
                }

                $sql = "UPDATE products SET 
                            product_name = :product_name,
                            type_id = :type_id,
                            description = :description,
                            price = :price,
                            stock = :stock
                            $imageSQL
                        WHERE product_id = :product_id";
                $stmt = $conn->prepare($sql);

                $stmt->bindParam(':product_name', $product_name);
                $stmt->bindParam(':type_id', $type_id);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':price', $price);
                $stmt->bindParam(':stock', $stock);
                $stmt->bindParam(':product_id', $product_id);
                if (isset($filename)) $stmt->bindParam(':image', $filename);

                if ($stmt->execute()) {
                    echo json_encode(["success" => true, "message" => "แก้ไขสินค้าสำเร็จ"]);
                } else {
                    echo json_encode(["success" => false, "error" => "แก้ไขสินค้าล้มเหลว"]);
                }
            } catch (Exception $e) {
                echo json_encode(["success" => false, "error" => "เกิดข้อผิดพลาด: " . $e->getMessage()]);
            }
            break;

        case 'delete':
            try {
                $product_id = $_POST['product_id'] ?? 0;

                // ดึงชื่อไฟล์รูปจากฐานข้อมูลก่อนลบ
                $stmt = $conn->prepare("SELECT image FROM products WHERE product_id = :product_id");
                $stmt->bindParam(':product_id', $product_id);
                $stmt->execute();
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($product && !empty($product['image'])) {
                    $filePath = "uploads/" . $product['image'];
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }

                // ลบข้อมูลสินค้าออกจากฐานข้อมูล
                $stmt = $conn->prepare("DELETE FROM products WHERE product_id = :product_id");
                $stmt->bindParam(':product_id', $product_id);

                if ($stmt->execute()) {
                    echo json_encode(["success" => true, "message" => "ลบสินค้าสำเร็จ"]);
                } else {
                    echo json_encode(["success" => false, "error" => "ลบสินค้าล้มเหลว"]);
                }
            } catch (Exception $e) {
                echo json_encode(["success" => false, "error" => "เกิดข้อผิดพลาด: " . $e->getMessage()]);
            }
            break;

        default:
            echo json_encode(["success" => false, "error" => "Action ไม่ถูกต้อง"]);
            break;
    }

} else {
    // GET: ดึงข้อมูลสินค้า
    try {
        $stmt = $conn->prepare("SELECT * FROM products ORDER BY product_id DESC");
        if ($stmt->execute()) {
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(["success" => true, "data" => $products]);
        } else {
            echo json_encode(["success" => false, "data" => [], "error" => "ไม่สามารถดึงข้อมูลได้"]);
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "data" => [], "error" => "เกิดข้อผิดพลาด: " . $e->getMessage()]);
    }
}
?>