<?php
// Tambahkan header CORS di bagian atas file
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

    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';

    // Validasi input
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email dan password harus diisi!']);
        exit;
    }

    // Cek apakah email ada di database
    $stmt = $conn->prepare('SELECT id, password FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Generate token unik
            $token = bin2hex(random_bytes(16)); // 16 byte = 32 karakter hex

            echo json_encode([
                'success' => true,
                'message' => 'Login berhasil!',
                'data' => ['token' => $token]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Password salah!']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Email tidak ditemukan!']);
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
