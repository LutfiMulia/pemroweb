# Setup Instructions for Forgot Password Functionality

## Files Created
1. `forgot-password.php` - Form untuk request reset password
2. `reset-password.php` - Form untuk set password baru
3. `init_password_resets.php` - Script untuk membuat database table
4. `create_password_resets_table.sql` - SQL script untuk membuat table

## Database Setup

### Option 1: Using phpMyAdmin
1. Buka phpMyAdmin (http://localhost/phpmyadmin)
2. Pilih database `inisidentia_db`
3. Jalankan SQL query berikut:

```sql
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_email (email)
);

CREATE INDEX idx_token ON password_resets(token);
CREATE INDEX idx_expires_at ON password_resets(expires_at);
```

### Option 2: Using MySQL Command Line
1. Buka command prompt
2. Jalankan: `mysql -u root -p`
3. Masukkan password MySQL (biasanya kosong untuk XAMPP)
4. Jalankan: `USE inisidentia_db;`
5. Copy-paste SQL dari Option 1

## How to Use

1. **Login Page**: Link "Forgot your password?" sudah diupdate ke `forgot-password.php`
2. **Forgot Password**: User masukkan email, sistem generate token dan tampilkan link reset
3. **Reset Password**: User klik link dengan token, masukkan password baru
4. **Token Expiry**: Token berlaku 1 jam, otomatis dihapus setelah password direset

## Security Features

- Token unik dan random (64 karakter hex)
- Token expire dalam 1 jam
- Token dihapus setelah digunakan
- Password di-hash menggunakan PHP password_hash()
- Validasi email exists dan akun aktif

## Email Integration (Optional)

Untuk implementasi email yang sesungguhnya, bisa menggunakan:
- PHPMailer
- SwiftMailer
- Atau mail() function PHP

Ganti bagian dalam `forgot-password.php` yang menampilkan link dengan kode untuk mengirim email.

## Testing

1. Pastikan XAMPP Apache dan MySQL running
2. Buka: `http://localhost/insidentia/src/auth/login.php`
3. Klik "Forgot your password?"
4. Masukkan email yang ada di database
5. Klik link yang muncul
6. Set password baru
7. Login dengan password baru
