<?php
// Header CORS wajib biar nggak diblokir React
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit(); }

require 'koneksi.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? $_GET['id'] : null;
$data = json_decode(file_get_contents("php://input"));

switch($method) {
    case 'GET':
        // Urutin berdasarkan tingkat dulu, baru nama kelas
        $sql = "SELECT * FROM kelas ORDER BY tingkat ASC, nama_kelas ASC";
        $result = $conn->query($sql);
        $kelas = [];
        if($result) {
            while($row = $result->fetch_assoc()) {
                $kelas[] = $row;
            }
        }
        echo(json_encode(["success" => true, "data" => $kelas]));
        break;

    case 'POST':
        // Nangkep data tingkat dan nama_kelas dari React
        $tingkat = $conn->real_escape_string($data->tingkat);
        $nama_kelas = $conn->real_escape_string($data->nama_kelas);
        
        // Disini fix-nya: Pastiin 'tingkat' ikut di-INSERT ke database
        $sql = "INSERT INTO kelas (tingkat, nama_kelas) VALUES ('$tingkat', '$nama_kelas')";
        
        if($conn->query($sql)) {
            echo(json_encode(["success" => true, "message" => "Kelas berhasil ditambah!"]));
        } else {
            echo(json_encode(["success" => false, "message" => "Gagal: " . $conn->error]));
        }
        break;

    case 'PUT':
        if($id) {
            $tingkat = $conn->real_escape_string($data->tingkat);
            $nama_kelas = $conn->real_escape_string($data->nama_kelas);
            
            // Disini fix-nya: Pastiin 'tingkat' ikut di-UPDATE
            $sql = "UPDATE kelas SET tingkat='$tingkat', nama_kelas='$nama_kelas' WHERE id_kelas='$id'";
            
            if($conn->query($sql)) {
                echo(json_encode(["success" => true, "message" => "Kelas berhasil diedit!"]));
            } else {
                echo(json_encode(["success" => false, "message" => "Gagal: " . $conn->error]));
            }
        } else {
            echo(json_encode(["success" => false, "message" => "ID kelas tidak ditemukan!"]));
        }
        break;

    case 'DELETE':
        if($id) {
            $sql = "DELETE FROM kelas WHERE id_kelas='$id'";
            if($conn->query($sql)) {
                echo(json_encode(["success" => true, "message" => "Kelas berhasil dihapus!"]));
            } else {
                echo(json_encode(["success" => false, "message" => "Gagal: " . $conn->error]));
            }
        } else {
            echo(json_encode(["success" => false, "message" => "ID kelas tidak ditemukan!"]));
        }
        break;
}
?>