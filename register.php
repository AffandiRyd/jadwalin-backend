<?php
// WAJIB ADA BIAR REACT BISA BACA API-NYA
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require 'koneksi.php';

$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($data)) {
    $username = $conn->real_escape_string($data->username);
    // Hash password biar aman, gak keliatan di database
    $password = password_hash($data->password, PASSWORD_DEFAULT);
    $nama_lengkap = $conn->real_escape_string($data->nama_lengkap);
    
    // Tentukan role. Karena yang ditambahin dari modal itu ketua kelas, kita set 'pengurus'
    $role = 'pengurus'; 
    
    // Tangkap id_kelas
    $id_kelas = isset($data->id_kelas) && $data->id_kelas !== "" ? $data->id_kelas : "NULL";

    // Cek apakah username udah dipakai
    $cek_username = $conn->query("SELECT * FROM users WHERE username='$username'");
    if ($cek_username->num_rows > 0) {
        echo(json_encode(["success" => false, "message" => "Username sudah terdaftar! Pilih yang lain."]));
        exit();
    }

    $sql = "INSERT INTO users (username, password, nama_lengkap, role, id_kelas) 
            VALUES ('$username', '$password', '$nama_lengkap', '$role', $id_kelas)";
    
    if ($conn->query($sql) === TRUE) {
        echo(json_encode(["success" => true, "message" => "Akun Pengurus berhasil dibuat!"]));
    } else {
        echo(json_encode(["success" => false, "message" => "Gagal bikin akun: " . $conn->error]));
    }
} else {
    echo(json_encode(["success" => false, "message" => "Data tidak valid."]));
}
?>