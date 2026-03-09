<?php
// Izinkan CORS biar React nggak diblokir
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Koneksi ke database (Sesuai nama db lu: db_jadwalin)
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "db_jadwalin";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    echo(json_encode(["success" => false, "message" => "Koneksi database gagal"]));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    
    $id_user = $input['id_user'] ?? '';
    $password_baru = $input['password_baru'] ?? '';

    if (empty($id_user) || empty($password_baru)) {
        echo(json_encode(["success" => false, "message" => "Data tidak lengkap!"]));
        exit();
    }

    // Update password berdasarkan id_user
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id_user = ?");
    $stmt->bind_param("si", $password_baru, $id_user);
    
    if ($stmt->execute()) {
        echo(json_encode(["success" => true, "message" => "Password berhasil diubah!"]));
    } else {
        echo(json_encode(["success" => false, "message" => "Gagal mengubah password."]));
    }
    
    $stmt->close();
} else {
    echo(json_encode(["success" => false, "message" => "Method tidak valid"]));
}

$conn->close();
?>