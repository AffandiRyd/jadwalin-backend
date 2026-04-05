<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit(); }

include 'koneksi.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // FIX: Nembak ke tabel 'pengumuman' urut dari tanggal terbaru
    $result = $conn->query("SELECT * FROM pengumuman ORDER BY tanggal DESC");
    $data = [];
    if ($result) {
        while($row = $result->fetch_assoc()) { $data[] = $row; }
    }
    echo(json_encode(["success" => true, "data" => $data]));
} 
elseif ($method === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    
    $tanggal = $conn->real_escape_string($input['tanggal']);
    $judul = $conn->real_escape_string($input['judul']);
    $isi = $conn->real_escape_string($input['isi']);
    
    // FIX: INSERT ke tabel 'pengumuman'
    if ($conn->query("INSERT INTO pengumuman (tanggal, judul, isi) VALUES ('$tanggal', '$judul', '$isi')")) {
        echo(json_encode(["success" => true, "message" => "Info berhasil ditambahkan!"]));
    } else { 
        echo(json_encode(["success" => false, "message" => "Gagal menambahkan: " . $conn->error])); 
    }
} 
elseif ($method === 'PUT') {
    $input = json_decode(file_get_contents("php://input"), true);
    $id_pengumuman = $_GET['id'];
    
    $tanggal = $conn->real_escape_string($input['tanggal']);
    $judul = $conn->real_escape_string($input['judul']);
    $isi = $conn->real_escape_string($input['isi']);
    
    // FIX: UPDATE tabel 'pengumuman' berdasarkan 'id_pengumuman'
    if ($conn->query("UPDATE pengumuman SET tanggal='$tanggal', judul='$judul', isi='$isi' WHERE id_pengumuman='$id_pengumuman'")) {
        echo(json_encode(["success" => true, "message" => "Info diupdate!"]));
    } else { 
        echo(json_encode(["success" => false, "message" => "Gagal update: " . $conn->error])); 
    }
} 
elseif ($method === 'DELETE') {
    $id_pengumuman = $_GET['id'];
    
    // FIX: DELETE dari tabel 'pengumuman'
    if ($conn->query("DELETE FROM pengumuman WHERE id_pengumuman='$id_pengumuman'")) {
        echo(json_encode(["success" => true, "message" => "Info dihapus!"]));
    } else { 
        echo(json_encode(["success" => false, "message" => "Gagal menghapus: " . $conn->error])); 
    }
}
$conn->close();
?>