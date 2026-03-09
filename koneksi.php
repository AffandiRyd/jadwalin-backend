<?php
// === KONFIGURASI CORS (Wajib buat ReactJS) ===
// Ini biar browser ngizinin React ngambil data dari PHP ini
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Browser biasanya ngirim request OPTIONS dulu sebelum POST/PUT/DELETE
// Kita harus kasih respon OK (200) biar request aslinya bisa jalan
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// === KONFIGURASI DATABASE ===
$host = "sql206.infinityfree.com"; // Ganti sesuai Host Name
$user = "if0_41346207";      // Ganti sesuai User Name
$pass = "darderdorrpl1";          // Ganti sesuai Password
$db   = "if0_41346207_XXX"; // Ganti sesuai DB Name

// Bikin koneksi ke MySQL
$conn = new mysqli($host, $user, $pass, $db);

// Cek kalau koneksinya gagal
if ($conn->connect_error) {
    // Balikin error dalam format JSON biar gampang dibaca React
    echo json_encode([
        "success" => false, 
        "message" => "Koneksi database gagal: " . $conn->connect_error
    ]);
    exit();
}
?>