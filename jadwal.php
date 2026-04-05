<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit(); }

include 'koneksi.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Tangkap id_kelas dari URL (dikirim dari React)
    $id_kelas = isset($_GET['id_kelas']) ? $conn->real_escape_string($_GET['id_kelas']) : '';

    $query = "SELECT j.id_jadwal, j.hari, j.jam_mulai, j.jam_selesai, j.ruangan, j.id_kelas, j.id_guru, j.id_mapel,
                     k.nama_kelas, g.nama_guru, m.nama_mapel 
              FROM jadwal j 
              LEFT JOIN kelas k ON j.id_kelas = k.id_kelas 
              LEFT JOIN guru g ON j.id_guru = g.id_guru 
              LEFT JOIN mapel m ON j.id_mapel = m.id_mapel";
              
    // MAGIC FILTERNYA DI SINI: Kalau ada id_kelas, tampilin yang cocok aja
    if (!empty($id_kelas)) {
        $query .= " WHERE j.id_kelas = '$id_kelas'";
    }

    // Biar urut dari jam pertama
    $query .= " ORDER BY j.jam_mulai ASC";

    $result = $conn->query($query);
    $data = [];
    if($result) {
        while($row = $result->fetch_assoc()) { $data[] = $row; }
    }
    echo(json_encode(["success" => true, "data" => $data]));
} 
// ... kodingan atas tetep sama ...

elseif ($method === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    
    // Tangkap data
    $id_kelas = isset($input['id_kelas']) ? $conn->real_escape_string($input['id_kelas']) : '';
    $id_mapel = isset($input['id_mapel']) ? $conn->real_escape_string($input['id_mapel']) : '';
    $id_guru  = isset($input['id_guru']) ? $conn->real_escape_string($input['id_guru']) : '';
    $hari     = isset($input['hari']) ? $conn->real_escape_string($input['hari']) : '';
    $jam_mulai = isset($input['jam_mulai']) ? $conn->real_escape_string($input['jam_mulai']) : '';
    $jam_selesai = isset($input['jam_selesai']) ? $conn->real_escape_string($input['jam_selesai']) : '';
    $ruangan  = isset($input['ruangan']) ? $conn->real_escape_string($input['ruangan']) : '';
    
    // --- TAMBAHAN SATPAM (VALIDASI) DI SINI ---
    // Cek kalau id_kelas, id_mapel, atau id_guru kosong, langsung tolak!
    if (empty($id_kelas) || empty($id_mapel) || empty($id_guru)) {
        echo(json_encode([
            "success" => false, 
            "message" => "Gagal: id_kelas, id_mapel, dan id_guru tidak boleh kosong! Pastikan dropdown di React sudah dipilih."
        ]));
        exit; // Hentikan proses PHP sampai sini, jangan lanjut ke query
    }
    // ------------------------------------------

    $query = "INSERT INTO jadwal (id_kelas, id_mapel, id_guru, hari, jam_mulai, jam_selesai, ruangan) 
              VALUES ('$id_kelas', '$id_mapel', '$id_guru', '$hari', '$jam_mulai', '$jam_selesai', '$ruangan')";
              
    if ($conn->query($query)) {
        echo(json_encode(["success" => true, "message" => "Jadwal berhasil ditambahkan!"]));
    } else { 
        echo(json_encode(["success" => false, "message" => "Gagal menambahkan: " . $conn->error])); 
    }
}
elseif ($method === 'PUT') {
    $input = json_decode(file_get_contents("php://input"), true);
    $id_jadwal = $_GET['id'];
    
    // FIX: Tahan banting juga buat fitur Edit
    $id_mapel = isset($input['id_mapel']) ? $conn->real_escape_string($input['id_mapel']) : '';
    $id_guru  = isset($input['id_guru']) ? $conn->real_escape_string($input['id_guru']) : '';
    $hari     = isset($input['hari']) ? $conn->real_escape_string($input['hari']) : '';
    $jam_mulai = isset($input['jam_mulai']) ? $conn->real_escape_string($input['jam_mulai']) : '';
    $jam_selesai = isset($input['jam_selesai']) ? $conn->real_escape_string($input['jam_selesai']) : '';
    $ruangan  = isset($input['ruangan']) ? $conn->real_escape_string($input['ruangan']) : '';
    
    $query = "UPDATE jadwal SET id_mapel='$id_mapel', id_guru='$id_guru', hari='$hari', 
              jam_mulai='$jam_mulai', jam_selesai='$jam_selesai', ruangan='$ruangan' 
              WHERE id_jadwal='$id_jadwal'";
              
    if ($conn->query($query)) {
        echo(json_encode(["success" => true, "message" => "Jadwal berhasil diupdate!"]));
    } else { 
        echo(json_encode(["success" => false, "message" => "Gagal update: " . $conn->error])); 
    }
}

// ... kodingan DELETE ke bawah tetep sama ...
elseif ($method === 'DELETE') {
    $id_jadwal = $_GET['id'];
    if ($conn->query("DELETE FROM jadwal WHERE id_jadwal='$id_jadwal'")) {
        echo(json_encode(["success" => true, "message" => "Jadwal dihapus!"]));
    } else { 
        echo(json_encode(["success" => false, "message" => "Gagal menghapus: " . $conn->error])); 
    }
}
$conn->close();
?>