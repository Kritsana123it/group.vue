<?php
include 'condb.php';
header('Content-Type: application/json; charset=utf-8');

// ========================================
// GET - ดึงประวัติการสั่งอาหารของลูกค้า
// ========================================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user_id = $_GET['user_id'] ?? null;

    if (!$user_id) {
        echo json_encode(["success" => false, "message" => "ไม่พบ user_id"]);
        exit;
    }

    try {
        // ดึงคำสั่งซื้อทั้งหมดของลูกค้า
        $stmt = $conn->prepare("
            SELECT id as order_id, table_no, total_price, order_date, 
                   COALESCE(status, 'pending') as status 
            FROM orders 
            WHERE user_id = ? 
            ORDER BY order_date DESC
        ");
        $stmt->execute([$user_id]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ดึงรายการสินค้าในแต่ละคำสั่งซื้อ
        foreach ($orders as &$order) {
            $stmtItems = $conn->prepare("
                SELECT oi.id, p.product_name, oi.quantity, oi.price 
                FROM order_items oi
                JOIN products p ON oi.product_id = p.product_id
                WHERE oi.order_id = ?
            ");
            $stmtItems->execute([$order['order_id']]);
            $order['items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
            
            // เปลี่ยนชื่อคีย์ให้ตรงกับ Vue
            $order['total'] = $order['total_price'];
            $order['order_id'] = $order['order_id']; // id ถูกแปลงเป็น order_id แล้วจาก SQL
            unset($order['total_price']);
        }

        echo json_encode([
            "success" => true, 
            "orders" => $orders
        ]);

    } catch (Exception $e) {
        echo json_encode([
            "success" => false, 
            "message" => $e->getMessage()
        ]);
    }
    exit;
}

// ========================================
// POST - บันทึกคำสั่งซื้อใหม่
// ========================================
$data = json_decode(file_get_contents("php://input"), true);
$table = $data['table_no'] ?? null;
$items = $data['items'] ?? [];
$total = $data['total'] ?? 0;
$user_id = $data['user_id'] ?? null;

try {
    $conn->beginTransaction();

    $stmt = $conn->prepare("INSERT INTO orders (user_id, table_no, total_price, order_date, status) VALUES (?, ?, ?, NOW(), 'pending')");
    $stmt->execute([$user_id, $table, $total]);
    $order_id = $conn->lastInsertId();

    foreach ($items as $item) {
        if (!isset($item['product_id'], $item['price'], $item['quantity'])) {
            echo json_encode(["success" => false, "message" => "ข้อมูลสินค้าไม่ครบ"]);
            exit;
        }

        $quantity = $item['quantity'];
        $price = $item['price'];
        $subtotal = $quantity * $price;

        $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)");
        $stmtItem->execute([$order_id, $item['product_id'], $quantity, $price, $subtotal]);
    }

    $conn->commit();
    echo json_encode(["success" => true, "message" => "บันทึกคำสั่งซื้อเรียบร้อย"]);
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>