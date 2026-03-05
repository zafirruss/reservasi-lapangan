<?php
class Reservasi {
    private $conn;
    private $table_name = "reservasi";

    public $id;
    public $user_id;
    public $lapangan_id;
    public $tanggal;
    public $jam_mulai;
    public $jam_selesai;
    public $durasi;
    public $total_harga;
    public $status;
    public $catatan;

    public function __construct($db) {
        $this->conn = $db;
    }

    // READ - Ambil semua reservasi beserta nama user & lapangan
    public function read() {
        $query = "SELECT r.*, u.nama as nama_user, u.email,
                         l.nama as nama_lapangan, l.jenis
                  FROM " . $this->table_name . " r
                  JOIN users u ON r.user_id = u.id
                  JOIN lapangan l ON r.lapangan_id = l.id
                  ORDER BY r.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // READ ONE - Ambil satu reservasi by id
    public function readOne() {
        $query = "SELECT r.*, u.nama as nama_user, u.email, u.no_hp,
                         l.nama as nama_lapangan, l.jenis, l.harga_per_jam
                  FROM " . $this->table_name . " r
                  JOIN users u ON r.user_id = u.id
                  JOIN lapangan l ON r.lapangan_id = l.id
                  WHERE r.id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->id]);
        return $stmt;
    }

    // READ BY USER - Ambil reservasi milik user tertentu
    public function readByUser() {
        $query = "SELECT r.*, l.nama as nama_lapangan, l.jenis
                  FROM " . $this->table_name . " r
                  JOIN lapangan l ON r.lapangan_id = l.id
                  WHERE r.user_id = ?
                  ORDER BY r.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->user_id]);
        return $stmt;
    }

    // CREATE - Buat reservasi baru
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  (user_id, lapangan_id, tanggal, jam_mulai, jam_selesai, durasi, total_harga, catatan)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        if ($stmt->execute([
            $this->user_id,
            $this->lapangan_id,
            $this->tanggal,
            $this->jam_mulai,
            $this->jam_selesai,
            $this->durasi,
            $this->total_harga,
            $this->catatan ?? null
        ])) {
            return true;
        }
        return false;
    }

    // UPDATE STATUS - Ubah status reservasi (admin)
    public function updateStatus() {
        $query = "UPDATE " . $this->table_name . " SET status = ? WHERE id = ?";
        $stmt  = $this->conn->prepare($query);
        if ($stmt->execute([$this->status, $this->id])) {
            return true;
        }
        return false;
    }

    // DELETE - Hapus reservasi
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
