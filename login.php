<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");

// Pastikan file koneksi database lu namanya bener (koneksi.php)
include 'koneksi.php'; 

$data = json_decode(file_get_contents("php://input"));

if(isset($data->username) && isset($data->password)) {
    // Tangkap input dari React
    $username = $conn->real_escape_string($data->username);
    $password_input = $data->password;

    // Cari user di database
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // --- KODE YANG SUDAH DIPERBAIKI ---
        if($password_input === $user['password']) {
    
        // Kita tambahkan id_kelas ke dalam array data
        echo json_encode([
            "success" => true, 
            "message" => "Login berhasil",
            "data" => [
            "id_user" => $user['id_user'],
            "username" => $user['username'],
            "nama_lengkap" => $user['nama_lengkap'],
            "role" => $user['role'],
            "id_kelas" => $user['id_kelas'] // WAJIB ADA INI BIAR REACT TAU KELASNYA
        ]
        ]);
    } else {
            echo(json_encode(["success" => false, "message" => "Password salah!"]));
        }
    } else {
        echo(json_encode(["success" => false, "message" => "Username tidak ditemukan!"]));
    }
} else {
    echo(json_encode(["success" => false, "message" => "Data tidak lengkap!"]));
}
?>