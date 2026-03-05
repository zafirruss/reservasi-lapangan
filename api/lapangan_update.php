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
include_once '../models/Lapangan.php';

$database = new Database();
$db       = $database->getConnection();
$lapangan = new Lapangan($db);

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->id) && !empty($data->nama) && !empty($data->jenis) && !empty($data->harga_per_jam)) {
    $lapangan->id            = $data->id;
    $lapangan->nama          = $data->nama;
    $lapangan->jenis         = $data->jenis;
    $lapangan->deskripsi     = $data->deskripsi ?? '';
    $lapangan->harga_per_jam = $data->harga_per_jam;
    $lapangan->status        = $data->status ?? 'tersedia';

    if ($lapangan->update()) {
        http_response_code(200);
        echo json_encode(array("message" => "Lapangan berhasil diperbarui."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Gagal memperbarui lapangan."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Data tidak lengkap. Wajib: id, nama, jenis, harga_per_jam."));
}
?>
