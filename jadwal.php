<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit(); }

require 'koneksi.php';

$method = $_SERVER['REQUEST_METHOD'];
$id_jadwal = isset($_GET['id']) ? $_GET['id'] : null;
$data = json_decode(file_get_contents("php://input"));

// CARA BARU YANG AMPUH:
// Ambil id_kelas langsung dari data POST/PUT (body) ATAU dari URL GET/DELETE
$id_kelas_user = isset($data->id_kelas) ? $data->id_kelas : (isset($_GET['id_kelas']) ? $_GET['id_kelas'] : null);

switch($method) {
    case 'GET':
        $sql = "SELECT j.*, k.nama_kelas, m.nama_mapel, g.nama_guru 
                FROM jadwal j
                LEFT JOIN kelas k ON j.id_kelas = k.id_kelas
                LEFT JOIN mapel m ON j.id_mapel = m.id_mapel
                LEFT JOIN guru g ON j.id_guru = g.id_guru";
        
        if ($id_kelas_user) {
            $sql .= " WHERE j.id_kelas = " . $conn->real_escape_string($id_kelas_user);
        }
        
        $sql .= " ORDER BY j.jam_mulai ASC";

        $result = $conn->query($sql);
        $jadwal = [];
        if ($result) {
            while($row = $result->fetch_assoc()) {
                $jadwal[] = $row;
            }
        }
        echo(json_encode(["success" => true, "data" => $jadwal]));
        break;

    case 'POST':
        if (!$id_kelas_user) {
            echo(json_encode(["success" => false, "message" => "Akses Ditolak! Cuma pengurus kelas yang bisa nambah jadwal."]));
            exit();
        }

        $id_mapel = $conn->real_escape_string($data->id_mapel);
        $id_guru = $conn->real_escape_string($data->id_guru);
        $hari = $conn->real_escape_string($data->hari);
        $jam_mulai = $conn->real_escape_string($data->jam_mulai);
        $jam_selesai = $conn->real_escape_string($data->jam_selesai);
        $ruangan = $conn->real_escape_string($data->ruangan);
        
        $sql = "INSERT INTO jadwal (id_kelas, id_mapel, id_guru, hari, jam_mulai, jam_selesai, ruangan) 
                VALUES ('$id_kelas_user', '$id_mapel', '$id_guru', '$hari', '$jam_mulai', '$jam_selesai', '$ruangan')";
        
        if($conn->query($sql)) {
            echo(json_encode(["success" => true, "message" => "Jadwal kelas berhasil ditambah!"]));
        } else {
            echo(json_encode(["success" => false, "message" => "Gagal: " . $conn->error]));
        }
        break;

    case 'PUT':
        if($id_jadwal && $id_kelas_user) {
            $id_mapel = $conn->real_escape_string($data->id_mapel);
            $id_guru = $conn->real_escape_string($data->id_guru);
            $hari = $conn->real_escape_string($data->hari);
            $jam_mulai = $conn->real_escape_string($data->jam_mulai);
            $jam_selesai = $conn->real_escape_string($data->jam_selesai);
            $ruangan = $conn->real_escape_string($data->ruangan);
            
            $sql = "UPDATE jadwal SET 
                    id_mapel='$id_mapel', id_guru='$id_guru', hari='$hari', 
                    jam_mulai='$jam_mulai', jam_selesai='$jam_selesai', ruangan='$ruangan' 
                    WHERE id_jadwal=$id_jadwal AND id_kelas=$id_kelas_user";
            
            if($conn->query($sql)) {
                echo(json_encode(["success" => true, "message" => "Jadwal berhasil diupdate!"]));
            } else {
                echo(json_encode(["success" => false, "message" => "Gagal: " . $conn->error]));
            }
        } else {
            echo(json_encode(["success" => false, "message" => "Akses Ditolak atau ID Jadwal tidak valid."]));
        }
        break;

    case 'DELETE':
        if($id_jadwal && $id_kelas_user) {
            $sql = "DELETE FROM jadwal WHERE id_jadwal=$id_jadwal AND id_kelas=$id_kelas_user";
            if($conn->query($sql)) {
                echo(json_encode(["success" => true, "message" => "Jadwal berhasil dihapus!"]));
            } else {
                echo(json_encode(["success" => false, "message" => "Gagal: " . $conn->error]));
            }
        } else {
            echo(json_encode(["success" => false, "message" => "Akses Ditolak atau ID Jadwal tidak valid."]));
        }
        break;
}
?>