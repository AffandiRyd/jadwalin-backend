<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "db_jadwalin"; // Pastiin nama DB lu bener ini

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>