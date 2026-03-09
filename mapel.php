<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit(); }

$conn = new mysqli("localhost", "root", "", "db_jadwalin");
if ($conn->connect_error) { die(json_encode(["success" => false, "message" => "Koneksi gagal"])); }

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $result = $conn->query("SELECT * FROM mapel ORDER BY nama_mapel ASC");
    $data = [];
    while($row = $result->fetch_assoc()) { $data[] = $row; }
    echo(json_encode(["success" => true, "data" => $data]));
} 
elseif ($method === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    $nama_mapel = $conn->real_escape_string($input['nama_mapel']);
    if ($conn->query("INSERT INTO mapel (nama_mapel) VALUES ('$nama_mapel')")) {
        echo(json_encode(["success" => true, "message" => "Mapel berhasil ditambahkan!"]));
    } else { echo(json_encode(["success" => false, "message" => "Gagal menambahkan."])); }
} 
elseif ($method === 'PUT') {
    $input = json_decode(file_get_contents("php://input"), true);
    $id_mapel = $_GET['id'];
    $nama_mapel = $conn->real_escape_string($input['nama_mapel']);
    if ($conn->query("UPDATE mapel SET nama_mapel='$nama_mapel' WHERE id_mapel='$id_mapel'")) {
        echo(json_encode(["success" => true, "message" => "Data mapel diupdate!"]));
    } else { echo(json_encode(["success" => false, "message" => "Gagal update."])); }
} 
elseif ($method === 'DELETE') {
    $id_mapel = $_GET['id'];
    if ($conn->query("DELETE FROM mapel WHERE id_mapel='$id_mapel'")) {
        echo(json_encode(["success" => true, "message" => "Mapel dihapus!"]));
    } else { echo(json_encode(["success" => false, "message" => "Gagal menghapus."])); }
}
$conn->close();
?>