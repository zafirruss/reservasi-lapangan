<?php
// File: config/database.php
// Deskripsi: Konfigurasi dan koneksi ke database MySQL.

// Pengaturan koneksi database
$host = 'localhost';        // Host database (biasanya localhost)
$username = 'root';         // Username database
$password = '';             // Password database (kosongkan jika tidak ada)
$database = 'reservasi_lapangan'; // Nama database

// Membuat koneksi ke database menggunakan MySQLi
$conn = new mysqli($host, $username, $password, $database);

// Memeriksa status koneksi
if ($conn->connect_error) {
    // Jika koneksi gagal, hentikan eksekusi dan tampilkan pesan error
    die("Koneksi ke database gagal: " . $conn->connect_error);
}

// Mengatur character set ke utf8mb4 untuk mendukung karakter yang lebih luas
$conn->set_charset("utf8mb4");

// Opsi untuk melaporkan error MySQLi sebagai exception
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Koneksi berhasil. Variabel $conn akan digunakan di file lain untuk berinteraksi dengan database.
?>
