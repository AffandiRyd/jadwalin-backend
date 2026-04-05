<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once 'koneksi.php'; // Pastikan file koneksi lu bener namanya

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        // Nampilin semua user (kecuali admin utama biar ga kehapus ga sengaja)
        $query = "SELECT u.id_user, u.username, u.nama_lengkap, u.role, u.id_kelas, k.nama_kelas 
                  FROM users u 
                  LEFT JOIN kelas k ON u.id_kelas = k.id_kelas 
                  WHERE u.role != 'admin' ORDER BY u.id_user DESC";
        $result = $conn->query($query);
        $users = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        }
        echo(json_encode(['success' => true, 'data' => $users]));
        break;

    case 'POST':
        // Nambah user baru
        $data = json_decode(file_get_contents("php://input"));
        if(!empty($data->username) && !empty($data->password) && !empty($data->nama_lengkap)) {
            $username = $conn->real_escape_string($data->username);
            
            // FIX: Hash password biar aman dan sama persis sistemnya kayak register.php
            $password = password_hash($data->password, PASSWORD_DEFAULT); 
            
            $nama_lengkap = $conn->real_escape_string($data->nama_lengkap);
            
            // FIX: Tangkap id_kelas dengan benar
            $id_kelas = (isset($data->id_kelas) && $data->id_kelas !== "") ? $conn->real_escape_string($data->id_kelas) : "NULL";
            $role = 'pengurus'; // Default role

            // Cek username udah ada blm
            $cek = $conn->query("SELECT * FROM users WHERE username='$username'");
            if($cek->num_rows > 0) {
                echo(json_encode(['success' => false, 'message' => 'Username sudah terpakai!']));
                exit;
            }

            // Insert data
            $query = "INSERT INTO users (username, password, nama_lengkap, role, id_kelas) 
                      VALUES ('$username', '$password', '$nama_lengkap', '$role', $id_kelas)";
            
            if($conn->query($query)) {
                echo(json_encode(['success' => true, 'message' => 'Akun berhasil dibuat.']));
            } else {
                echo(json_encode(['success' => false, 'message' => 'Gagal membuat akun: ' . $conn->error]));
            }
        } else {
            echo(json_encode(['success' => false, 'message' => 'Data tidak lengkap.']));
        }
        break;

    case 'PUT':
        // Update user
        $data = json_decode(file_get_contents("php://input"));
        $id = isset($_GET['id']) ? $conn->real_escape_string($_GET['id']) : die(json_encode(['success' => false, 'message' => 'ID tidak ditemukan.']));
        
        $username = $conn->real_escape_string($data->username);
        $nama_lengkap = $conn->real_escape_string($data->nama_lengkap);
        
        // FIX: Tangkap id_kelas dengan benar untuk Update
        $id_kelas = (isset($data->id_kelas) && $data->id_kelas !== "") ? $conn->real_escape_string($data->id_kelas) : "NULL";
        
        // Cek apakah username dipakai orang lain
        $cek = $conn->query("SELECT * FROM users WHERE username='$username' AND id_user != '$id'");
        if($cek->num_rows > 0) {
            echo(json_encode(['success' => false, 'message' => 'Username sudah dipakai akun lain!']));
            exit;
        }

        // Kalau password diisi, ikut diupdate. Kalau kosong, biarin password lama
        if(!empty($data->password)) {
            $password = password_hash($data->password, PASSWORD_DEFAULT); // FIX: Hashing password saat di-update
            $query = "UPDATE users SET username='$username', password='$password', nama_lengkap='$nama_lengkap', id_kelas=$id_kelas WHERE id_user='$id'";
        } else {
            $query = "UPDATE users SET username='$username', nama_lengkap='$nama_lengkap', id_kelas=$id_kelas WHERE id_user='$id'";
        }

        if($conn->query($query)) {
            echo(json_encode(['success' => true, 'message' => 'Data akun berhasil diupdate.']));
        } else {
            echo(json_encode(['success' => false, 'message' => 'Gagal update akun: ' . $conn->error]));
        }
        break;

    case 'DELETE':
        // Hapus user
        $id = isset($_GET['id']) ? $conn->real_escape_string($_GET['id']) : die(json_encode(['success' => false, 'message' => 'ID tidak ditemukan.']));
        if($conn->query("DELETE FROM users WHERE id_user='$id'")) {
            echo(json_encode(['success' => true, 'message' => 'Akun berhasil dihapus.']));
        } else {
            echo(json_encode(['success' => false, 'message' => 'Gagal menghapus akun.']));
        }
        break;
}
$conn->close();
?>