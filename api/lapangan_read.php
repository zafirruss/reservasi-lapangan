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
include_once '../models/Lapangan.php';

$database = new Database();
$db       = $database->getConnection();
$lapangan = new Lapangan($db);

// Jika ada ?id=x ambil satu data saja
if (isset($_GET['id'])) {
    $lapangan->id = (int)$_GET['id'];
    $stmt = $lapangan->readOne();
} else {
    $stmt = $lapangan->read();
}

$num = $stmt->rowCount();

if ($num > 0) {
    $data = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($data, array(
            "id"           => $row['id'],
            "nama"         => $row['nama'],
            "jenis"        => $row['jenis'],
            "deskripsi"    => $row['deskripsi'],
            "harga_per_jam"=> $row['harga_per_jam'],
            "status"       => $row['status']
        ));
    }
    http_response_code(200);
    echo json_encode(isset($_GET['id']) ? $data[0] : $data);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Data lapangan tidak ditemukan."));
}
?>
