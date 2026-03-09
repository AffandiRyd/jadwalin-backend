<?php
// Izinkan CORS biar React (frontend) bisa ngakses API ini tanpa diblokir browser
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight request dari browser (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Konfigurasi Database (Udah disesuaikan sama database lu: db_jadwalin)
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "db_jadwalin";

$conn = new mysqli($host, $user, $pass, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    echo(json_encode(["success" => false, "message" => "Koneksi database gagal: " . $conn->connect_error]));
    exit();
}

// Ambil method HTTP yang dikirim React (GET, POST, PUT, DELETE)
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // --- READ: Ambil semua data kelas buat ditampilin di React ---
        $sql = "SELECT * FROM kelas ORDER BY tingkat ASC, nama_kelas ASC";
        $result = $conn->query($sql);
        
        $data = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        echo(json_encode(["success" => true, "data" => $data]));
        break;

    case 'POST':
        // --- CREATE: Tambah kelas baru dari form React ---
        $input = json_decode(file_get_contents("php://input"), true);
        $tingkat = $input['tingkat'] ?? '';
        $nama_kelas = $input['nama_kelas'] ?? '';

        if (empty($tingkat) || empty($nama_kelas)) {
            echo(json_encode(["success" => false, "message" => "Tingkat dan nama kelas harus diisi!"]));
            exit();
        }

        $stmt = $conn->prepare("INSERT INTO kelas (tingkat, nama_kelas) VALUES (?, ?)");
        $stmt->bind_param("ss", $tingkat, $nama_kelas);
        
        if ($stmt->execute()) {
            echo(json_encode(["success" => true, "message" => "Kelas baru berhasil ditambahkan!"]));
        } else {
            echo(json_encode(["success" => false, "message" => "Gagal menambahkan kelas."]));
        }
        $stmt->close();
        break;

    case 'PUT':
        // --- UPDATE: Edit data kelas ---
        $id_kelas = $_GET['id'] ?? '';
        $input = json_decode(file_get_contents("php://input"), true);
        $tingkat = $input['tingkat'] ?? '';
        $nama_kelas = $input['nama_kelas'] ?? '';

        if (empty($id_kelas) || empty($tingkat) || empty($nama_kelas)) {
            echo(json_encode(["success" => false, "message" => "Data tidak lengkap!"]));
            exit();
        }

        $stmt = $conn->prepare("UPDATE kelas SET tingkat = ?, nama_kelas = ? WHERE id_kelas = ?");
        $stmt->bind_param("ssi", $tingkat, $nama_kelas, $id_kelas);
        
        if ($stmt->execute()) {
            echo(json_encode(["success" => true, "message" => "Data kelas berhasil diupdate!"]));
        } else {
            echo(json_encode(["success" => false, "message" => "Gagal mengupdate kelas."]));
        }
        $stmt->close();
        break;

    case 'DELETE':
        // --- DELETE: Hapus kelas ---
        $id_kelas = $_GET['id'] ?? '';

        if (empty($id_kelas)) {
            echo(json_encode(["success" => false, "message" => "ID Kelas tidak ditemukan!"]));
            exit();
        }

        $stmt = $conn->prepare("DELETE FROM kelas WHERE id_kelas = ?");
        $stmt->bind_param("i", $id_kelas);
        
        if ($stmt->execute()) {
            echo(json_encode(["success" => true, "message" => "Kelas berhasil dihapus!"]));
        } else {
            echo(json_encode(["success" => false, "message" => "Gagal menghapus kelas."]));
        }
        $stmt->close();
        break;

    default:
        echo(json_encode(["success" => false, "message" => "Method HTTP tidak valid"]));
        break;
}

$conn->close();
?>