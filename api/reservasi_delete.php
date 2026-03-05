<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
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

if (!empty($data->id)) {
    $reservasi->id = $data->id;
    if ($reservasi->delete()) {
        http_response_code(200);
        echo json_encode(array("message" => "Reservasi berhasil dihapus."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Gagal menghapus reservasi."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "ID reservasi wajib diisi."));
}
?>
