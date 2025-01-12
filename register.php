<?php
// Tambahkan header CORS
header('Access-Control-Allow-Origin: http://localhost:5173'); // Sesuaikan dengan alamat React Anda
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Koneksi ke database
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari body request
    $input = json_decode(file_get_contents('php://input'), true);

    $name = $input['name'] ?? '';
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';

    // Validasi input
    if (empty($name) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Nama, email, dan password harus diisi!']);
        exit;
    }

    // Hash password untuk keamanan
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Cek apakah email sudah ada
    $checkEmailStmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
    $checkEmailStmt->bind_param('s', $email);
    $checkEmailStmt->execute();
    $checkResult = $checkEmailStmt->get_result();

    if ($checkResult->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email sudah terdaftar!']);
        exit;
    }

    // Masukkan data pengguna baru ke database
    $stmt = $conn->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $name, $email, $hashedPassword);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Registrasi berhasil!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat registrasi.']);
    }

    $stmt->close();
    exit;
}

// Jika metode HTTP tidak sesuai
echo json_encode([
    'success' => false,
    'message' => 'Metode HTTP tidak didukung.'
]);
exit;
