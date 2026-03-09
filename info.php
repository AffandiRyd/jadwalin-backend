<?php
require 'koneksi.php';

// Tambahin header ini biar gak kena masalah CORS dari React
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

// Handle OPTIONS request untuk CORS preflight
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? $_GET['id'] : null;
$data = json_decode(file_get_contents("php://input"));

switch($method) {
    case 'GET':
        // Narik semua data info dari database
        $sql = "SELECT * FROM pengumuman ORDER BY tanggal DESC";
        $result = $conn->query($sql);
        $info = [];
        if ($result) {
            while($row = $result->fetch_assoc()) {
                $info[] = $row;
            }
            // UBAH DISINI
            echo(json_encode(["success" => true, "data" => $info]));
        } else {
             echo(json_encode(["success" => false, "message" => "Gagal ambil data: " . $conn->error]));
        }
        break;

    case 'POST':
        // Nambah info baru
        if(isset($data->judul) && isset($data->isi) && isset($data->tanggal)){
            $judul = $conn->real_escape_string($data->judul);
            $isi = $conn->real_escape_string($data->isi);
            $tanggal = $conn->real_escape_string($data->tanggal);
            
            $sql = "INSERT INTO pengumuman (judul, isi, tanggal) VALUES ('$judul', '$isi', '$tanggal')";
            if($conn->query($sql)) {
                // UBAH DISINI
                echo(json_encode(["success" => true, "message" => "Info berhasil ditambah!"]));
            } else {
                // UBAH DISINI
                echo(json_encode(["success" => false, "message" => "Gagal: " . $conn->error]));
            }
        } else {
            echo(json_encode(["success" => false, "message" => "Data tidak lengkap!"]));
        }
        break;

    case 'PUT':
        // Ngedit info berdasarkan ID
        if($id && isset($data->judul) && isset($data->isi) && isset($data->tanggal)) {
            $judul = $conn->real_escape_string($data->judul);
            $isi = $conn->real_escape_string($data->isi);
            $tanggal = $conn->real_escape_string($data->tanggal);
            
            $sql = "UPDATE pengumuman SET judul='$judul', isi='$isi', tanggal='$tanggal' WHERE id_pengumuman=$id";
            if($conn->query($sql)) {
                // UBAH DISINI
                echo(json_encode(["success" => true, "message" => "Info berhasil diedit!"]));
            } else {
                // UBAH DISINI
                echo(json_encode(["success" => false, "message" => "Gagal: " . $conn->error]));
            }
        } else {
            echo(json_encode(["success" => false, "message" => "ID atau Data tidak lengkap!"]));
        }
        break;

    case 'DELETE':
        // Hapus info berdasarkan ID
        if($id) {
            $sql = "DELETE FROM pengumuman WHERE id_pengumuman=$id";
            if($conn->query($sql)) {
                // UBAH DISINI
                echo(json_encode(["success" => true, "message" => "Data berhasil diproses!"]));
            } else {
                // UBAH DISINI
                echo(json_encode(["success" => false, "message" => "Gagal: " . $conn->error]));
            }
        } else {
            echo(json_encode(["success" => false, "message" => "ID tidak ditemukan!"]));
        }
        break;
}
?>