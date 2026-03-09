<?php
// Header CORS ini wajib ada biar browser nggak nge-blok PUT & DELETE
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight request (OPTIONS) buat menghindari CORS error
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit(); }

require 'koneksi.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? $_GET['id'] : null;
$data = json_decode(file_get_contents("php://input"));

switch($method) {
    case 'GET':
        $sql = "SELECT * FROM agenda ORDER BY tanggal ASC";
        $result = $conn->query($sql);
        $agenda = [];
        if($result) {
            while($row = $result->fetch_assoc()) {
                $agenda[] = $row;
            }
        }
        echo(json_encode(["success" => true, "data" => $agenda]));
        break;

    case 'POST':
        $judul = $conn->real_escape_string($data->judul);
        $isi = $conn->real_escape_string($data->isi);
        $tanggal = $conn->real_escape_string($data->tanggal);
        
        $sql = "INSERT INTO agenda (judul, isi, tanggal) VALUES ('$judul', '$isi', '$tanggal')";
        if($conn->query($sql)) {
            echo(json_encode(["success" => true, "message" => "Agenda berhasil ditambah!"]));
        } else {
            echo(json_encode(["success" => false, "message" => "Gagal: " . $conn->error]));
        }
        break;

    case 'PUT':
        if($id) {
            $judul = $conn->real_escape_string($data->judul);
            $isi = $conn->real_escape_string($data->isi);
            $tanggal = $conn->real_escape_string($data->tanggal);
            
            // PERBAIKAN: Balikin pakai id, bukan id_agenda
            $sql = "UPDATE agenda SET judul='$judul', isi='$isi', tanggal='$tanggal' WHERE id='$id'";
            if($conn->query($sql)) {
                echo(json_encode(["success" => true, "message" => "Agenda berhasil diedit!"]));
            } else {
                echo(json_encode(["success" => false, "message" => "Gagal: " . $conn->error]));
            }
        } else {
            echo(json_encode(["success" => false, "message" => "ID tidak ditemukan untuk diedit!"]));
        }
        break;

    case 'DELETE':
        if($id) {
            // PERBAIKAN: Balikin pakai id, bukan id_agenda
            $sql = "DELETE FROM agenda WHERE id='$id'";
            if($conn->query($sql)) {
                echo(json_encode(["success" => true, "message" => "Agenda berhasil dihapus!"]));
            } else {
                echo(json_encode(["success" => false, "message" => "Gagal: " . $conn->error]));
            }
        } else {
            echo(json_encode(["success" => false, "message" => "ID tidak ditemukan untuk dihapus!"]));
        }
        break;
}
?>