-- =============================================
-- DATABASE: reservasi_lapangan
-- =============================================

CREATE DATABASE IF NOT EXISTS reservasi_lapangan;
USE reservasi_lapangan;

-- Tabel users (untuk user biasa dan admin)
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    no_hp VARCHAR(15),
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel lapangan
CREATE TABLE lapangan (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    jenis VARCHAR(50) NOT NULL,
    deskripsi TEXT,
    harga_per_jam DECIMAL(10,2) NOT NULL,
    status ENUM('tersedia', 'tidak_tersedia') DEFAULT 'tersedia',
    gambar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel reservasi
CREATE TABLE reservasi (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    user_id INTEGER NOT NULL,
    lapangan_id INTEGER NOT NULL,
    tanggal DATE NOT NULL,
    jam_mulai TIME NOT NULL,
    jam_selesai TIME NOT NULL,
    durasi INTEGER NOT NULL COMMENT 'dalam jam',
    total_harga DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'konfirmasi', 'selesai', 'batal') DEFAULT 'pending',
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (lapangan_id) REFERENCES lapangan(id) ON DELETE CASCADE
);

-- =============================================
-- DATA AWAL
-- =============================================

-- Admin default (password: admin123)
INSERT INTO users (nama, email, password, no_hp, role) VALUES
('Administrator', 'admin@reservasi.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567890', 'admin');

-- Sample user (password: user123)
INSERT INTO users (nama, email, password, no_hp, role) VALUES
('Budi Santoso', 'budi@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '082345678901', 'user'),
('Siti Rahayu', 'siti@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '083456789012', 'user');

-- Data lapangan
INSERT INTO lapangan (nama, jenis, deskripsi, harga_per_jam, status) VALUES
('Lapangan Futsal A', 'Futsal', 'Lapangan futsal premium dengan rumput sintetis berkualitas tinggi, dilengkapi pencahayaan LED modern dan tribun penonton.', 150000, 'tersedia'),
('Lapangan Futsal B', 'Futsal', 'Lapangan futsal standar dengan fasilitas lengkap, cocok untuk latihan dan pertandingan kasual.', 100000, 'tersedia'),
('Lapangan Badminton 1', 'Badminton', 'Lapangan badminton indoor ber-AC dengan lantai kayu parket berkualitas internasional.', 75000, 'tersedia'),
('Lapangan Badminton 2', 'Badminton', 'Lapangan badminton indoor standar dengan pencahayaan optimal untuk permainan nyaman.', 60000, 'tersedia'),
('Lapangan Basket', 'Basket', 'Lapangan basket full-court dengan lantai karet premium, ring standar NBA, dan pencahayaan stadium.', 200000, 'tersedia'),
('Lapangan Tenis', 'Tenis', 'Lapangan tenis outdoor dengan permukaan hard court, dilengkapi tribun dan area istirahat.', 120000, 'tersedia');

-- Sample reservasi
INSERT INTO reservasi (user_id, lapangan_id, tanggal, jam_mulai, jam_selesai, durasi, total_harga, status, catatan) VALUES
(2, 1, '2025-02-27', '08:00:00', '10:00:00', 2, 300000, 'konfirmasi', 'Untuk latihan tim'),
(2, 3, '2025-02-28', '14:00:00', '16:00:00', 2, 150000, 'pending', NULL),
(3, 2, '2025-02-27', '16:00:00', '18:00:00', 2, 200000, 'konfirmasi', 'Tournament kecil'),
(3, 5, '2025-03-01', '09:00:00', '11:00:00', 2, 400000, 'pending', NULL);
