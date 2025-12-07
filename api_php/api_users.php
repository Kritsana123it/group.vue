<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'condb.php';

$method = $_SERVER['REQUEST_METHOD'];

// =========================
// =========================
// 📌 GET — ดึงข้อมูล (กรองตาม role ได้)
// =========================
if ($method === 'GET') {

    $role = $_GET['role'] ?? '';

    if (!empty($role)) {
        $stmt = $conn->prepare(
            "SELECT * FROM users WHERE role = :role"
        );
        $stmt->bindParam(":role", $role);
    } else {
        $stmt = $conn->prepare(
            "SELECT * FROM users"
        );
    }

    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "data" => $data
    ]);
    exit;


}

// =========================
// 📌 POST — add / update / delete
// =========================
if ($method === 'POST') {
    $action = $_POST['action'] ?? '';

    // =========================
    // ➕ ADD USER
    // =========================
    if ($action === 'add') {

        if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['password'])) {
            echo json_encode(["success" => false, "message" => "กรุณากรอกข้อมูลให้ครบ"]);
            exit;
        }

        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role'] ?? 'customer';

        // upload image
        $image = null;
        if (!empty($_FILES['image']['name'])) {
            $uploadDir = "uploads/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $fileName = "user_" . time() . "_" . uniqid() . "." . $ext;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName)) {
                $image = $fileName;
            }
        }

        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, image) 
                                VALUES (:name, :email, :password, :role, :image)");

        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $password);
        $stmt->bindParam(":role", $role);
        $stmt->bindParam(":image", $image);

        $stmt->execute();

        echo json_encode(["success" => true, "message" => "เพิ่มผู้ใช้สำเร็จ"]);
        exit;
    }

    // =========================
    // ✏️ UPDATE USER
    // =========================
    if ($action === 'update') {

        if (empty($_POST['id'])) {
            echo json_encode(["success" => false, "message" => "ไม่มี id"]);
            exit;
        }

        $id = $_POST['id'];
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $role = $_POST['role'] ?? 'customer';

        // หา user ก่อน
        $old = $conn->prepare("SELECT image FROM users WHERE id = :id LIMIT 1");
        $old->bindParam(":id", $id);
        $old->execute();
        $oldData = $old->fetch(PDO::FETCH_ASSOC);

        $image = $oldData['image'] ?? null;

        // ถ้ามีรูปใหม่
        if (!empty($_FILES['image']['name'])) {
            $uploadDir = "uploads/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $fileName = "user_" . time() . "_" . uniqid() . "." . $ext;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName)) {
                // ลบรูปเก่า
                if (!empty($image) && file_exists($uploadDir . $image)) {
                    unlink($uploadDir . $image);
                }
                $image = $fileName;
            }
        }

        $stmt = $conn->prepare("UPDATE users 
                                SET name = :name, email = :email, role = :role, image = :image
                                WHERE id = :id");

        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":role", $role);
        $stmt->bindParam(":image", $image);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        echo json_encode(["success" => true, "message" => "อัปเดตข้อมูลสำเร็จ"]);
        exit;
    }

    // =========================
    // 🗑️ DELETE USER
    // =========================
    if ($action === 'delete') {

        if (empty($_POST['id'])) {
            echo json_encode(["success" => false, "message" => "ไม่มี id"]);
            exit;
        }

        $id = $_POST['id'];

        // ลบรูปด้วย
        $old = $conn->prepare("SELECT image FROM users WHERE id = :id LIMIT 1");
        $old->bindParam(":id", $id);
        $old->execute();
        $oldData = $old->fetch(PDO::FETCH_ASSOC);

        if (!empty($oldData['image'])) {
            $path = "uploads/" . $oldData['image'];
            if (file_exists($path)) unlink($path);
        }

        $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        echo json_encode(["success" => true, "message" => "ลบผู้ใช้สำเร็จ"]);
        exit;
    }

    echo json_encode(["success" => false, "message" => "action ไม่ถูกต้อง"]);
}
?>
