SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS medixa_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE medixa_db;

DROP TABLE IF EXISTS email_log;
DROP TABLE IF EXISTS obat_history;
DROP TABLE IF EXISTS penerima_donasi;
DROP TABLE IF EXISTS donasi;
DROP TABLE IF EXISTS bmi_history;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(50),
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'pengguna',
    aktif TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    wallpaper VARCHAR(50) DEFAULT 'gradient-1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE bmi_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    berat REAL,
    tinggi REAL,
    bmi REAL,
    kategori VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE donasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_donatur VARCHAR(255),
    email_donatur VARCHAR(255),
    jumlah REAL,
    pesan TEXT,
    status VARCHAR(20) DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE penerima_donasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    nama VARCHAR(255),
    penyakit VARCHAR(255),
    deskripsi TEXT,
    target REAL DEFAULT 0,
    terkumpul REAL DEFAULT 0,
    foto VARCHAR(255),
    status VARCHAR(20) DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE obat_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_obat VARCHAR(255) NOT NULL,
    kategori VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE email_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    to_email VARCHAR(255) NOT NULL,
    to_name VARCHAR(255),
    subject VARCHAR(255),
    type VARCHAR(50) DEFAULT 'donasi',
    status VARCHAR(20) DEFAULT 'sent',
    error_msg TEXT,
    donasi_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO users (id, nama, email, phone, password, role, aktif, created_at, wallpaper) VALUES (1, 'Admin Medixa', 'admin@medixa.id', '081234567890', '$2y$10$nRf1vRUeXJtpRAtPb1Vp5.NiUBv.FO9vVM1OWsuA.SSqp9J8YDcIu', 'admin', 1, '2026-05-03 04:46:08', 'gradient-1');
INSERT INTO users (id, nama, email, phone, password, role, aktif, created_at, wallpaper) VALUES (2, 'Ahmad Afilin', 'ahmadafilinbuss@gmail.com', '085732302325', '$2y$10$sgH8lawB2.gbvneE9ElHHOvtG9iZ3nz7.8ifyrIjodQKL/NpAxuuW', 'pengguna', 1, '2026-05-03 05:03:27', 'gradient-1');
INSERT INTO users (id, nama, email, phone, password, role, aktif, created_at, wallpaper) VALUES (3, 'Admin 1', 'admin1@medixa.id', '081234567890', '$2y$10$CRrV3T6LfuB.r7HnGcndKOiIAoAVzVoLp0tBVDvDWvZTzSBOphiZC', 'admin', 1, '2026-05-03 05:56:13', 'gradient-1');
INSERT INTO users (id, nama, email, phone, password, role, aktif, created_at, wallpaper) VALUES (4, 'Admin 2', 'admin2@medixa.id', '081234567890', '$2y$10$7BOS4x0YYbzCTRY1UYO.5eeLeWM03yhK4qyJ/ohLl4zODLlXarkLC', 'admin', 1, '2026-05-03 05:56:13', 'gradient-1');
INSERT INTO users (id, nama, email, phone, password, role, aktif, created_at, wallpaper) VALUES (5, 'Admin 3', 'admin3@medixa.id', '081234567890', '$2y$10$vBWesR138/uXwgBsoMdN.uQJHW56JeuYJqBskQFvz3HfwGqWa/0Km', 'admin', 1, '2026-05-03 05:56:14', 'gradient-1');
INSERT INTO users (id, nama, email, phone, password, role, aktif, created_at, wallpaper) VALUES (6, 'Admin 4', 'admin4@medixa.id', '081234567890', '$2y$10$nlYI2xyccv6GCNRivIB4VexivIVlm1VNTg/5veBI39PzNCug/AK7S', 'admin', 1, '2026-05-03 05:56:14', 'gradient-1');
INSERT INTO users (id, nama, email, phone, password, role, aktif, created_at, wallpaper) VALUES (7, 'Galih', 'galih123@gmail.com', '0867362937', '$2y$10$f9wbp6GiefUAwL6/IhQwoeLEkEFpzNB7woYmp77giD7W4xskPzH.C', 'pengguna', 1, '2026-06-23 01:10:18', 'gradient-1');
INSERT INTO users (id, nama, email, phone, password, role, aktif, created_at, wallpaper) VALUES (8, 'Roma', 'roma67@gmail.com', '085728462842', '$2y$10$yoiqUMcCO6nXVLSm3KpSCOr.y5GCgQNqyw4I4QNsh4T/zN3Ua0tke', 'donatur', 1, '2026-06-23 01:38:51', 'gradient-1');


INSERT INTO bmi_history (id, user_id, berat, tinggi, bmi, kategori, created_at) VALUES (1, 3, 52.0, 175.0, 17.0, 'Kekurangan Berat Badan', '2026-05-03 06:21:30');

INSERT INTO donasi (id, nama_donatur, email_donatur, jumlah, pesan, status, created_at) VALUES (1, 'Test Donatur', 'test@example.com', 25000.0, 'Semoga lekas sembuh', 'sukses', '2026-05-03 06:19:21');

INSERT INTO email_log (id, to_email, to_name, subject, type, status, error_msg, donasi_id, created_at) VALUES (1, 'test@example.com', 'Test Donatur', 'Konfirmasi Donasi MEDIXA – MDX-000001', 'donasi', 'failed', 'mail() returned false', 1, '2026-05-03 06:19:21');

ALTER TABLE users AUTO_INCREMENT = 9;
ALTER TABLE bmi_history AUTO_INCREMENT = 2;
ALTER TABLE donasi AUTO_INCREMENT = 2;
ALTER TABLE penerima_donasi AUTO_INCREMENT = 1;
ALTER TABLE obat_history AUTO_INCREMENT = 1;
ALTER TABLE email_log AUTO_INCREMENT = 2;

SET FOREIGN_KEY_CHECKS = 1;