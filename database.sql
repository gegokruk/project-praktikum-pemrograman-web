CREATE DATABASE IF NOT EXISTS db_lembur;
USE db_lembur;

-- 1. Tabel users (Menyimpan data akun)
CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('manager', 'karyawan') NOT NULL DEFAULT 'karyawan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Tabel slot_lembur (Jadwal lembur yang dibuat oleh Manager)
CREATE TABLE slot_lembur (
    id_slot INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    jam_mulai TIME NOT NULL,
    jam_selesai TIME NOT NULL,
    kuota INT NOT NULL,
    status ENUM('buka', 'penuh', 'tutup') NOT NULL DEFAULT 'buka',
    dibuat_oleh INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (dibuat_oleh) REFERENCES users(id_user) ON DELETE CASCADE
);

-- 3. Tabel pilihan_lembur
CREATE TABLE pilihan_lembur (
    id_pilihan INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    id_slot INT NOT NULL,
    status ENUM('aktif', 'dibatalkan', 'dipindahkan') NOT NULL DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unik_user_slot (id_user, id_slot),
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_slot) REFERENCES slot_lembur(id_slot) ON DELETE CASCADE
);


-- 4. DATA AWAL
-- Password untuk semua akun : password (hash bcrypt)
INSERT INTO users (nama, email, password, role) VALUES
('Pak Hendra (Manager)', 'manager@kantor.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager'),
('Budi Santoso', 'budi@kantor.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'karyawan'),
('Siti Aminah', 'siti@kantor.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'karyawan'),
('Andi Pratama', 'andi@kantor.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'karyawan');

INSERT INTO slot_lembur (tanggal, jam_mulai, jam_selesai, kuota, status, dibuat_oleh) VALUES
('2026-11-25', '17:00:00', '20:00:00', 5, 'buka', 1),
('2026-11-26', '17:00:00', '21:00:00', 3, 'buka', 1),
('2026-11-27', '17:00:00', '19:30:00', 2, 'penuh', 1),
('2026-11-30', '18:00:00', '21:00:00', 4, 'buka', 1);

INSERT INTO pilihan_lembur (id_user, id_slot, status) VALUES
(2, 1, 'aktif'),
(3, 1, 'aktif'),
(2, 3, 'aktif'),
(4, 3, 'aktif');