# Panduan Keamanan Insidentia

## 🔐 Sistem Autentikasi

Sistem ini telah dikonfigurasi dengan keamanan berlapis untuk memastikan semua halaman memerlukan login:

### File Autentikasi Utama

1. **`src/includes/auth_check.php`** - Cek autentikasi dasar
   - Memverifikasi session user_id dan role
   - Regenerasi session ID setiap 5 menit
   - Auto-redirect ke login jika tidak terautentikasi

2. **`src/includes/admin_check.php`** - Cek khusus admin
   - Include auth_check.php otomatis  
   - Memverifikasi role = 'admin'
   - Log unauthorized access attempts
   - Redirect sesuai role atau logout jika role tidak dikenal

3. **`src/includes/user_check.php`** - Cek khusus user/pelapor
   - Include auth_check.php otomatis
   - Memverifikasi role = 'pelapor'
   - Log unauthorized access attempts
   - Redirect sesuai role atau logout jika role tidak dikenal

### Implementasi di File

#### File Admin (semua menggunakan admin_check.php):
- `src/admin/dashboard.php`
- `src/admin/users.php` 
- `src/admin/categories.php`
- `src/admin/priorities.php`
- `src/admin/statuses.php`
- `src/admin/locations.php`
- `src/admin/incidents.php`
- `src/admin/reports.php`
- `src/admin/settings.php`

#### File User (semua menggunakan user_check.php):
- `src/user/dashboard.php`
- `src/user/my_incidents.php`
- `src/user/report_incident.php`
- `src/user/settings.php`

### Flow Autentikasi

1. **Akses halaman apa saja** → Cek auth_check/admin_check/user_check
2. **Jika tidak login** → Redirect ke `auth/login.php`
3. **Jika login tapi role salah** → Redirect ke halaman yang sesuai atau logout
4. **Jika semua OK** → Tampilkan halaman

## 🛡️ Fitur Keamanan

### Session Management
- **Regenerasi Session ID**: Otomatis setiap 5 menit
- **Session Timeout**: Implementasi manual logout jika diperlukan
- **Secure Session Handling**: Validasi ganda user_id dan role

### Access Control
- **Role-based Access**: Admin vs User/Pelapor
- **Automatic Redirection**: Sesuai role pengguna
- **Logging**: Semua percobaan akses tidak sah dicatat

### Password Security
- **Hashing**: bcrypt/password_hash() untuk password
- **Verification**: password_verify() saat login
- **Reset Password**: Implementasi aman dengan token

### File Protection
- **`.htaccess`** di direktori includes: Blokir akses langsung
- **Debug Protection**: Hanya localhost yang bisa akses `/debug/`
- **Root .htaccess**: Security headers dan proteksi file sensitif

## 📁 Struktur Keamanan

```
src/
├── includes/
│   ├── .htaccess                 # Proteksi akses langsung
│   ├── auth_check.php           # Autentikasi dasar
│   ├── admin_check.php          # Cek role admin
│   ├── user_check.php           # Cek role user
│   └── config.php               # Konfigurasi database
├── admin/                       # Semua file gunakan admin_check.php
├── user/                        # Semua file gunakan user_check.php  
├── auth/                        # File login/register/logout
├── debug/                       # Hanya localhost (ada .htaccess)
└── index.php                    # Auto-redirect berdasarkan role
```

## 🚀 Cara Kerja

### 1. Akses Pertama Kali
```
User mengakses: domain.com/pemroweb/
↓
.htaccess root: redirect ke src/
↓  
src/index.php: cek session
├── Ada session + role admin → admin/dashboard.php
├── Ada session + role pelapor → user/dashboard.php  
└── Tidak ada session → auth/login.php
```

### 2. Akses Halaman Admin
```
User akses: admin/categories.php
↓
admin_check.php dipanggil
├── auth_check.php: cek login
├── cek role === 'admin'
├── ✅ OK → tampilkan halaman
└── ❌ Tidak → redirect/logout + log
```

### 3. Akses Halaman User
```
User akses: user/dashboard.php
↓
user_check.php dipanggil  
├── auth_check.php: cek login
├── cek role === 'pelapor'
├── ✅ OK → tampilkan halaman
└── ❌ Tidak → redirect/logout + log
```

## 🔧 Konfigurasi Tambahan

### Untuk Production:
1. **Aktifkan HTTPS** di `.htaccess` root
2. **Set secure cookie settings** di PHP
3. **Update Content Security Policy**
4. **Monitor error logs** untuk percobaan akses ilegal

### Environment Variables:
```php
// Dalam config.php untuk production
if ($_SERVER['HTTP_HOST'] !== 'localhost') {
    ini_set('session.cookie_secure', 1);
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_samesite', 'Strict');
}
```

## 📊 Monitoring

### Log Files
- PHP error logs: Periksa percobaan akses yang gagal
- Access logs: Monitor pola akses yang mencurigakan

### Security Headers (ada di .htaccess root):
- X-XSS-Protection
- X-Content-Type-Options  
- X-Frame-Options
- Content-Security-Policy (opsional)

## ⚠️ Troubleshooting

### Jika redirect loop:
1. Pastikan session_start() tidak error
2. Cek database koneksi
3. Verifikasi data di tabel users dan roles

### Jika akses ditolak:
1. Periksa role di database
2. Clear browser session/cookies
3. Cek file auth_check.php untuk error

### Performance:
- Session regeneration setiap 5 menit cukup aman
- Jika perlu, sesuaikan interval di auth_check.php
- Monitor database queries untuk optimisasi

---

**✅ SISTEM KEAMANAN SUDAH AKTIF**
Semua halaman sekarang memerlukan login dengan role yang sesuai!
