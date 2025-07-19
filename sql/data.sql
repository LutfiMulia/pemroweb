-- Create Database
CREATE DATABASE IF NOT EXISTS inisidentia_db;
USE inisidentia_db;

-- ========================================
-- SCHEMA CREATION
-- ========================================

-- 1. Roles (Peran pengguna: admin dan user)
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

-- 2. Users (Pengguna aplikasi)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- 3. Categories (Kategori Insiden)
CREATE TABLE incident_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT
);

-- 4. Priorities (Prioritas Insiden)
CREATE TABLE incident_priorities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT
);

-- 5. Statuses (Status Insiden)
CREATE TABLE incident_statuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT
);

-- 6. Departments / Locations
CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    details TEXT
);

-- 7. Incidents (Laporan Insiden)
CREATE TABLE incidents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    reported_by INT NOT NULL,
    handled_by INT,
    category_id INT,
    priority_id INT,
    status_id INT,
    department_id INT,
    reported_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (reported_by) REFERENCES users(id),
    FOREIGN KEY (handled_by) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES incident_categories(id),
    FOREIGN KEY (priority_id) REFERENCES incident_priorities(id),
    FOREIGN KEY (status_id) REFERENCES incident_statuses(id),
    FOREIGN KEY (department_id) REFERENCES departments(id)
);

-- 8. Logs (Histori dan Komentar untuk Insiden)
CREATE TABLE incident_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    incident_id INT NOT NULL,
    user_id INT NOT NULL,
    status_id INT,
    comment TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (incident_id) REFERENCES incidents(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (status_id) REFERENCES incident_statuses(id)
);

-- 9. Activity Logs (Log Aktivitas Pengguna)
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(100),
    detail TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- 10. Attachments (File pendukung insiden)
CREATE TABLE attachments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    incident_id INT NOT NULL,
    file_name VARCHAR(255),
    file_path VARCHAR(255),
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (incident_id) REFERENCES incidents(id)
);

-- 11. Settings
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL,
    setting_value TEXT NOT NULL
);

-- ========================================
-- INITIAL DATA INSERTION
-- ========================================

-- Isi awal untuk roles
INSERT INTO roles (name) VALUES 
('admin'),
('user');

-- Admin user default (password: admin123, sudah di-hash)
INSERT INTO users (name, email, password, role_id, status) VALUES 
('Administrator', 'admin@insidentia.com', '$2a$12$VuaveKtvlN/.CSG2UIUeR.l.PPZQWlBDajpMTb7zIC4FFd5gQNc0u', 1, 'aktif');

-- Demo user (password: user123, sudah di-hash)
INSERT INTO users (name, email, password, role_id, status) VALUES 
('user', 'user@insidentia.com', '$2a$12$8TknJ9Fsg7M02J6pT4hF3.0NGr2Cmn2eD7u.thFWY/TQzTB8Kcuu6', 2, 'aktif');

-- Kategori insiden
INSERT INTO incident_categories (name, description) VALUES
('Kerusakan Hardware', 'Masalah fisik pada perangkat keras'),
('Bug Aplikasi', 'Kesalahan logika pada aplikasi'),
('Gangguan Jaringan', 'Koneksi tidak stabil atau putus'),
('Masalah Keamanan', 'Pelanggaran keamanan atau potensi ancaman');

-- Prioritas insiden
INSERT INTO incident_priorities (name, description) VALUES
('Darurat', 'Butuh penanganan segera'),
('Tinggi', 'Prioritas utama, tapi tidak darurat'),
('Sedang', 'Prioritas sedang'),
('Rendah', 'Tidak mendesak');

-- Status insiden
INSERT INTO incident_statuses (name, description) VALUES
('Dilaporkan', 'Insiden baru dilaporkan'),
('Dalam Peninjauan', 'Sedang ditinjau oleh tim'),
('Dalam Proses', 'Sedang ditangani'),
('Menunggu Informasi', 'Menunggu info tambahan dari pelapor'),
('Selesai', 'Sudah diselesaikan'),
('Ditolak', 'Tidak dapat ditindaklanjuti');

-- Lokasi / Departemen
INSERT INTO departments (name, details) VALUES
('Lantai 1', 'Ruang kerja umum lantai 1'),
('Gedung B', 'Divisi IT & Pengembangan'),
('Departemen IT', 'Departemen Teknologi Informasi'),
('Produksi', 'Divisi produksi utama');