## 🛡️ Inisidentia – Sistem Pelaporan Insiden

Deskripsi Singkat:
Inisidentia adalah aplikasi web untuk mengelola pelaporan dan penanganan insiden, dilengkapi dengan fitur CRUD dan pelaporan. Aplikasi ini dibangun menggunakan:
1. Frontend: HTML, CSS (Bootstrap)
2. Backend: PHP Native (versi ≥ 7.4)
3. Database: MySQL (versi ≥ 5.7)

## 📁 Struktur Folder

Insidentia/
├── src/
│   ├── admin/                  # Modul untuk administrator
│   │   ├── categories.php
│   │   ├── dashboard.php
│   │   ├── incidents.php
│   │   ├── locations.php
│   │   ├── priorities.php
│   │   ├── reports.php
│   │   ├── settings.php
│   │   ├── statuses.php
│   │   └── users.php
│   │
│   ├── auth/                   # Modul autentikasi pengguna
│   │   ├── login.php
│   │   ├── logout.php
│   │   └── register.php
│   │
│   ├── includes/               # File konfigurasi & utilitas
│   │   ├── config.php
│   │   ├── functions.php
│   │   ├── header.php
│   │   ├── footer.php
│   │   └── auth_check.php
│   │
│   ├── user/                   # Modul untuk pengguna pelapor
│   │   ├── dashboard.php
│   │   ├── report_incident.php
│   │   ├── my_incidents.php
│   │   └── settings.php
│   │
│   ├── assets/                 # Aset statis (CSS, JS, gambar)
│   │   ├── css/
│   │   │   └── style.css
│   │   ├── js/
│   │   │   └── script.js
│   │   └── img/
│   │       ├── gambarlogin.png
│   │       ├── logo.png
│   │       └── welcome.jpg
│   │
│   └── index.php               # Halaman utama aplikasi
│
├── sql/                        # Skrip SQL untuk database
│   ├── schema.sql
│   └── data.sql
│
└── README.md                   # Dokumentasi utama proyek

## 💾 Struktur Database
Tabel-tabel utama:

roles: Daftar peran pengguna.
users: Data pengguna.
incident_categories: Kategori insiden.
incident_priorities: Prioritas insiden.
incident_statuses: Status insiden.
departments: Lokasi/departemen insiden.
incidents: Data laporan insiden.
incident_logs: Riwayat update insiden.
activity_logs: Log aktivitas pengguna.
attachments: File pendukung insiden.
settings: Konfigurasi sistem.

# Incident Management System – ER Diagram

Proyek ini merupakan bagian dari pengembangan sistem manajemen insiden (Incident Management System) yang bertujuan untuk mencatat, mengelola, dan memantau laporan insiden dari pengguna dalam suatu organisasi. Desain ER Diagram ini dirancang untuk mendukung berbagai fitur seperti pelaporan insiden, log aktivitas, lampiran, hingga pengelolaan status dan prioritas insiden.

## 📌 Struktur Entitas dan Relasi

Diagram ini terdiri dari beberapa tabel utama dengan relasi yang jelas antar entitas. Berikut penjelasan dari masing-masing entitas dan fungsinya:

### 🔐 `roles`
Menyimpan data peran pengguna dalam sistem (misal: Admin, Staff, User).

| Kolom        | Tipe     | Keterangan     |
|--------------|----------|----------------|
| id           | int      | Primary Key    |
| name         | varchar  | Nama peran     |

### 👤 `users`
Berisi data pengguna sistem.

| Kolom        | Tipe     | Keterangan               |
|--------------|----------|--------------------------|
| id           | int      | Primary Key              |
| name         | varchar  | Nama pengguna            |
| email        | varchar  | Unique, Email pengguna   |
| password     | varchar  | Kata sandi terenkripsi   |
| role_id      | int      | Foreign Key → roles.id   |
| status       | enum     | Status aktif/inaktif     |
| created_at   | datetime | Tanggal dibuat           |

### 🧭 `incident_categories`, `incident_priorities`, `incident_statuses`
Masing-masing menyimpan kategori, prioritas, dan status insiden.

| Kolom        | Tipe     | Keterangan        |
|--------------|----------|-------------------|
| id           | int      | Primary Key       |
| name         | varchar  | Nama              |
| description  | text     | Penjelasan detail |

### 🏢 `departments`
Mewakili lokasi atau unit kerja terkait insiden.

| Kolom        | Tipe     | Keterangan        |
|--------------|----------|-------------------|
| id           | int      | Primary Key       |
| name         | varchar  | Nama departemen   |
| details      | text     | Informasi detail  |

### 📄 `incidents`
Menyimpan laporan insiden.

| Kolom          | Tipe     | Keterangan                        |
|----------------|----------|-----------------------------------|
| id             | int      | Primary Key                       |
| title          | varchar  | Judul insiden                     |
| description    | text     | Deskripsi lengkap                 |
| reported_by    | int      | FK → users.id (pelapor)           |
| handled_by     | int      | FK → users.id (penangan)          |
| category_id    | int      | FK → incident_categories.id       |
| priority_id    | int      | FK → incident_priorities.id       |
| status_id      | int      | FK → incident_statuses.id         |
| department_id  | int      | FK → departments.id               |
| reported_at    | datetime | Tanggal laporan dibuat            |
| updated_at     | datetime | Tanggal terakhir diperbarui       |

### 🧾 `incident_logs`
Mencatat riwayat perubahan status insiden.

| Kolom        | Tipe     | Keterangan                     |
|--------------|----------|--------------------------------|
| id           | int      | Primary Key                    |
| incident_id  | int      | FK → incidents.id              |
| user_id      | int      | FK → users.id                  |
| status_id    | int      | FK → incident_statuses.id      |
| comment      | text     | Catatan/komentar perubahan     |
| created_at   | datetime | Waktu log dibuat               |

### 📎 `attachments`
Berisi file yang dilampirkan pada insiden.

| Kolom        | Tipe     | Keterangan                     |
|--------------|----------|--------------------------------|
| id           | int      | Primary Key                    |
| incident_id  | int      | FK → incidents.id              |
| file_name    | varchar  | Nama file                      |
| file_path    | varchar  | Path penyimpanan file          |
| uploaded_at  | datetime | Waktu file diunggah            |

### 📚 `activity_logs`
Mencatat semua aktivitas pengguna dalam sistem.

| Kolom        | Tipe     | Keterangan              |
|--------------|----------|-------------------------|
| id           | int      | Primary Key             |
| user_id      | int      | FK → users.id           |
| action       | varchar  | Jenis aksi dilakukan    |
| detail       | text     | Penjelasan aksi         |
| created_at   | datetime | Waktu aksi dicatat      |

### ⚙️ `settings`
Menyimpan konfigurasi umum sistem.

| Kolom          | Tipe     | Keterangan          |
|----------------|----------|---------------------|
| id             | int      | Primary Key         |
| setting_key    | varchar  | Unique Key Setting  |
| setting_value  | text     | Nilai setting       |

---

## 🧩 Tools yang Digunakan

- [dbdiagram.io](https://dbdiagram.io) — Untuk memvisualisasikan ER Diagram dalam format DBML.
- MySQL / MariaDB — Rencana implementasi ke basis data.
- SQL DDL / DBML — Dua format dokumentasi diagram.

---

## 🔄 Relasi Antar Tabel (ERD)

Beberapa contoh relasi utama:
- `users` ↔ `incidents`: Satu pengguna bisa melaporkan atau menangani banyak insiden.
- `incidents` ↔ `incident_logs`: Setiap insiden bisa memiliki banyak log perubahan.
- `incidents` ↔ `attachments`: Setiap insiden bisa memiliki banyak lampiran.
- `users` ↔ `activity_logs`: Setiap pengguna bisa mencatat berbagai aktivitas.

---

## 🔐 Fitur Autentikasi
1. Login menggunakan username dan password.
2. Session PHP untuk proteksi halaman.
3. Logout akan menghancurkan session.
4. Proteksi halaman CRUD dan laporan berdasarkan peran pengguna.

## 🔧 Modul CRUD Admin:
Modul-modul ini memungkinkan administrator untuk mengelola data master dan insiden dalam sistem:

1. Manajemen Pengguna (admin/users.php): Tambah, Edit, Hapus, Lihat daftar pengguna.
2. Manajemen Kategori Insiden (admin/categories.php): Tambah, Edit, Hapus, Lihat daftar kategori.
3. Manajemen Prioritas (admin/priorities.php): Tambah, Edit, Hapus, Lihat daftar prioritas.
4. Manajemen Status (admin/statuses.php): Tambah, Edit, Hapus, Lihat daftar status.
5. Manajemen Lokasi/Departemen (admin/locations.php): Tambah, Edit, Hapus, Lihat daftar lokasi/departemen.
6. Manajemen Insiden (admin/incidents.php): Melihat semua insiden, mengubah status insiden.

## Setiap modul CRUD memiliki halaman untuk:

1. Tambah data baru
2. Edit data yang sudah ada
3. Hapus data
4. Melihat daftar data dalam format tabel (dilengkapi dengan Bootstrap dan potensi paginasi).

## ⚙️ Instalasi
1. Prasyarat
   - PHP 7.4+
   - MySQL 5.7+
   - Web server (Apache via XAMPP, WAMP, Laragon)

2. Clone Proyek
   - git clone https://github.com/username/inisidentia.git

3. Setup Database
   - Buat database inisidentia_db
   - Import sql/schema.sql
   - (Opsional) Import sql/data.sql untuk data awal

4. Konfigurasi Koneksi
Edit src/includes/config.php:
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'inisidentia_db');

5. Jalankan Aplikasi
   - Pindahkan folder ke htdocs/ atau www/
   - Akses di browser: http://localhost/Insidentia/src/

## 📘 Panduan Penggunaan
    - Login : Gunakan akun dari data.sql atau buat akun baru
    - Dashboard : Admin dan pengguna diarahkan ke dashboard masing-masing
    - Admin : Akses manajemen pengguna, insiden, dan laporan, lihat grafik & statistik insiden
    - Pengguna : Laporkan insiden, Lihat riwayat laporan, Kelola akun pribadi
    - Logout : Gunakan tombol logout untuk keluar dari sistem dengan aman

## 📣 Penutup
Inisidentia adalah solusi pelaporan insiden yang efisien, terorganisir, dan mudah digunakan. Didesain untuk skalabilitas dan kemudahan integrasi ke dalam berbagai organisasi atau instansi.






