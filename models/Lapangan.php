<?php
class Lapangan {
    private $conn;
    private $table_name = "lapangan";

    public $id;
    public $nama;
    public $jenis;
    public $deskripsi;
    public $harga_per_jam;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    // READ - Ambil semua lapangan
    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt  = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // READ ONE - Ambil satu lapangan by id
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt  = $this->conn->prepare($query);
        $stmt->execute([$this->id]);
        return $stmt;
    }

    // CREATE - Tambah lapangan baru
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  (nama, jenis, deskripsi, harga_per_jam, status)
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        if ($stmt->execute([
            $this->nama,
            $this->jenis,
            $this->deskripsi,
            $this->harga_per_jam,
            $this->status ?? 'tersedia'
        ])) {
            return true;
        }
        return false;
    }

    // UPDATE - Ubah data lapangan
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET nama = ?, jenis = ?, deskripsi = ?, harga_per_jam = ?, status = ?
                  WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt->execute([
            $this->nama,
            $this->jenis,
            $this->deskripsi,
            $this->harga_per_jam,
            $this->status,
            $this->id
        ])) {
            return true;
        }
        return false;
    }

    // DELETE - Hapus lapangan
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt  = $this->conn->prepare($query);
        if ($stmt->execute([$this->id])) {
            return true;
        }
        return false;
    }
}
?>
