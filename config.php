<?php
$host = 'localhost';
$user = 'root'; // Ganti sesuai dengan username database
$password = ''; // Ganti sesuai dengan password database
$dbname = 'api_kekeringan'; // Ganti sesuai dengan nama database

$conn = new mysqli($host, $user, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die('Koneksi database gagal: ' . $conn->connect_error);
}
?>
