<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
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

$allowed_status = ['pending', 'konfirmasi', 'selesai', 'batal'];

if (!empty($data->id) && !empty($data->status) && in_array($data->status, $allowed_status)) {
    $reservasi->id     = $data->id;
    $reservasi->status = $data->status;

    if ($reservasi->updateStatus()) {
        http_response_code(200);
        echo json_encode(array("message" => "Status reservasi berhasil diperbarui menjadi: " . $data->status));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Gagal memperbarui status reservasi."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Data tidak valid. Wajib: id, status (pending/konfirmasi/selesai/batal)."));
}
?>
