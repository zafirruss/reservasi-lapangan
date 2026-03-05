<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["message" => "Method tidak diizinkan."]);
    exit;
}

include_once '../config/Database.php';
include_once '../models/Reservasi.php';

$database  = new Database();
$db        = $database->getConnection();
$reservasi = new Reservasi($db);

// Ambil by ID
if (isset($_GET['id'])) {
    $reservasi->id = (int)$_GET['id'];
    $stmt = $reservasi->readOne();
// Ambil by user_id
} elseif (isset($_GET['user_id'])) {
    $reservasi->user_id = (int)$_GET['user_id'];
    $stmt = $reservasi->readByUser();
// Ambil semua
} else {
    $stmt = $reservasi->read();
}

$num = $stmt->rowCount();

if ($num > 0) {
    $data = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($data, array(
            "id"             => $row['id'],
            "user_id"        => $row['user_id'],
            "nama_user"      => $row['nama_user'] ?? null,
            "email"          => $row['email'] ?? null,
            "lapangan_id"    => $row['lapangan_id'],
            "nama_lapangan"  => $row['nama_lapangan'] ?? null,
            "jenis"          => $row['jenis'] ?? null,
            "tanggal"        => $row['tanggal'],
            "jam_mulai"      => $row['jam_mulai'],
            "jam_selesai"    => $row['jam_selesai'],
            "durasi"         => $row['durasi'],
            "total_harga"    => $row['total_harga'],
            "status"         => $row['status'],
            "catatan"        => $row['catatan'],
            "created_at"     => $row['created_at']
        ));
    }
    http_response_code(200);
    echo json_encode(isset($_GET['id']) ? $data[0] : $data);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Data reservasi tidak ditemukan."));
}
?>
