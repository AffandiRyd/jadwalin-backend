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
    $result = $conn->query("SELECT * FROM guru ORDER BY nama_guru ASC");
    $data = [];
    while($row = $result->fetch_assoc()) { $data[] = $row; }
    echo(json_encode(["success" => true, "data" => $data]));
} 
elseif ($method === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    $nama_guru = $conn->real_escape_string($input['nama_guru']);
    if ($conn->query("INSERT INTO guru (nama_guru) VALUES ('$nama_guru')")) {
        echo(json_encode(["success" => true, "message" => "Guru berhasil ditambahkan!"]));
    } else { echo(json_encode(["success" => false, "message" => "Gagal menambahkan."])); }
} 
elseif ($method === 'PUT') {
    $input = json_decode(file_get_contents("php://input"), true);
    $id_guru = $_GET['id'];
    $nama_guru = $conn->real_escape_string($input['nama_guru']);
    if ($conn->query("UPDATE guru SET nama_guru='$nama_guru' WHERE id_guru='$id_guru'")) {
        echo(json_encode(["success" => true, "message" => "Data guru diupdate!"]));
    } else { echo(json_encode(["success" => false, "message" => "Gagal update."])); }
} 
elseif ($method === 'DELETE') {
    $id_guru = $_GET['id'];
    if ($conn->query("DELETE FROM guru WHERE id_guru='$id_guru'")) {
        echo(json_encode(["success" => true, "message" => "Guru dihapus!"]));
    } else { echo(json_encode(["success" => false, "message" => "Gagal menghapus."])); }
}
$conn->close();
?>