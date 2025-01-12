<?php
// Tambahkan header CORS
header('Access-Control-Allow-Origin: http://localhost:5173'); // Sesuaikan dengan alamat React Anda
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS'); // Tambahkan semua metode HTTP
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Koneksi ke database
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $title = $input['title'] ?? '';
    $report = $input['report'] ?? '';

    if (empty($title) || empty($report)) {
        echo json_encode(['success' => false, 'message' => 'Title dan report harus diisi!']);
        exit;
    }

    $stmt = $conn->prepare('INSERT INTO reports (title, report) VALUES (?, ?)');
    $stmt->bind_param('ss', $title, $report);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Report berhasil ditambahkan!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat menambahkan report.']);
    }
    $stmt->close();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $conn->query('SELECT id, title, report FROM reports');
    $reports = [];

    while ($row = $result->fetch_assoc()) {
        $reports[] = $row;
    }

    echo json_encode(['success' => true, 'data' => $reports]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $_GET['id'] ?? null;
    $title = $input['title'] ?? '';
    $report = $input['report'] ?? '';

    if (!$id || empty($title) || empty($report)) {
        echo json_encode(['success' => false, 'message' => 'ID, title, dan report harus diisi!']);
        exit;
    }

    $stmt = $conn->prepare('UPDATE reports SET title = ?, report = ? WHERE id = ?');
    $stmt->bind_param('ssi', $title, $report, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Report berhasil diperbarui!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui report.']);
    }
    $stmt->close();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = $_GET['id'] ?? null;

    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'ID harus disertakan untuk menghapus report!']);
        exit;
    }

    $stmt = $conn->prepare('DELETE FROM reports WHERE id = ?');
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Report berhasil dihapus!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus report.']);
    }
    $stmt->close();
    exit;
}

echo json_encode(['success' => false, 'message' => 'Metode HTTP tidak didukung.']);
exit;
