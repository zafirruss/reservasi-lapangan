<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["message" => "Method tidak diizinkan."]);
    exit;
}

include_once '../config/Database.php';
include_once '../models/Reservasi.php';

$database  = new Database();
$db        = $database->getConnection();
$reservasi = new Reservasi($db);

$data = json_decode(file_get_contents("php://input"));

if (
    !empty($data->user_id) &&
    !empty($data->lapangan_id) &&
    !empty($data->tanggal) &&
    !empty($data->jam_mulai) &&
    !empty($data->durasi)
) {
    // Hitung jam selesai dan total harga
    $jam_selesai_ts = strtotime($data->jam_mulai) + ($data->durasi * 3600);
    $jam_selesai    = date('H:i:s', $jam_selesai_ts);

    // Ambil harga lapangan
    $stmt = $db->prepare("SELECT harga_per_jam FROM lapangan WHERE id = ?");
    $stmt->execute([$data->lapangan_id]);
    $lap = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$lap) {
        http_response_code(404);
        echo json_encode(array("message" => "Lapangan tidak ditemukan."));
        exit;
    }

    $total_harga = $lap['harga_per_jam'] * $data->durasi;

    $reservasi->user_id     = $data->user_id;
    $reservasi->lapangan_id = $data->lapangan_id;
    $reservasi->tanggal     = $data->tanggal;
    $reservasi->jam_mulai   = $data->jam_mulai;
    $reservasi->jam_selesai = $jam_selesai;
    $reservasi->durasi      = $data->durasi;
    $reservasi->total_harga = $total_harga;
    $reservasi->catatan     = $data->catatan ?? null;

    if ($reservasi->create()) {
        http_response_code(201);
        echo json_encode(array(
            "message"     => "Reservasi berhasil dibuat.",
            "jam_selesai" => $jam_selesai,
            "total_harga" => $total_harga
        ));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Gagal membuat reservasi."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Data tidak lengkap. Wajib: user_id, lapangan_id, tanggal, jam_mulai, durasi."));
}
?>
