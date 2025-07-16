-- Isi awal untuk roles
INSERT INTO roles (name) VALUES 
('admin'),
('pelapor'),
('penangan');

-- Admin user default (password: admin123, sudah di-hash)
INSERT INTO users (name, email, password, role_id, status) VALUES 
('Administrator', 'admin@inisidentia.local', '$2y$10$v1OZDezJvuTZTqn/yVOf1OLHt6vAbT44Gv1BGIM3h4vQ9Qbgyu09K', 1, 'aktif');

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

