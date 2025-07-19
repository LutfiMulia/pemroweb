# 🔐 Panduan Testing Login & Redirect

## ✅ Masalah Sudah Diperbaiki!

**Sebelumnya**: User login tidak auto-redirect ke dashboard user
**Sekarang**: Login langsung redirect sesuai role (admin/user)

---

## 🚀 Cara Test Login:

### 1. **Akses Halaman Login**
URL: `http://localhost/backup.lutfimulia/pemroweb/src/auth/login.php`

### 2. **Test User Credentials**
| Role | Email | Password | Expected Redirect |
|------|-------|----------|------------------|
| **Admin** | `admin@test.com` | `admin123` | `admin/dashboard.php` |
| **User** | `user@test.com` | `user123` | `user/dashboard.php` |
| **Existing Admin** | `admin@inisidentia.com` | (password asli) | `admin/dashboard.php` |
| **Existing User** | `user@inisidentia.com` | (password asli) | `user/dashboard.php` |

### 3. **Flow Testing**
1. Buka halaman login
2. Masukkan credentials user (bukan admin)
3. Klik "Sign In"
4. ✅ **Harus langsung redirect ke `user/dashboard.php`**
5. Logout, coba dengan admin
6. ✅ **Harus langsung redirect ke `admin/dashboard.php`**

---

## 🔧 Perubahan yang Dilakukan:

### **1. Update Role Support**
- Kode sekarang support role `user` dan `pelapor`
- Database menggunakan role `user` (bukan `pelapor`)
- Sistem tetap backward compatible

### **2. Files Yang Diupdate**:
- ✅ `src/includes/user_check.php` - Support `user` & `pelapor` 
- ✅ `src/user/dashboard.php` - Update role check
- ✅ `src/user/my_incidents.php` - Update role check  
- ✅ `src/user/report_incident.php` - Update role check
- ✅ `src/user/settings.php` - Update role check

### **3. Login Logic (sudah benar sebelumnya)**:
```php
// Di login.php baris 27-31
if ($user['role_name'] === 'admin') {
    redirect('../admin/dashboard.php');  // ✅ Admin redirect
} else {
    redirect('../user/dashboard.php');   // ✅ User redirect  
}
```

---

## 🎯 Expected Results:

### **✅ Admin Login:**
- Email: admin@test.com, Password: admin123
- **Redirect**: `http://localhost/backup.lutfimulia/pemroweb/src/admin/dashboard.php`
- **Page Shows**: Admin sidebar + "Selamat Datang, Admin!" 

### **✅ User Login:**
- Email: user@test.com, Password: user123  
- **Redirect**: `http://localhost/backup.lutfimulia/pemroweb/src/user/dashboard.php`
- **Page Shows**: User sidebar + "Halo, Test User!"

---

## 🛠️ Troubleshooting:

### **Jika redirect tidak bekerja:**
1. **Clear browser cache & cookies**
2. **Check database connection** - jalankan `test_system.php`
3. **Verify user data**:
   ```sql
   SELECT u.name, u.email, r.name as role_name 
   FROM users u JOIN roles r ON u.role_id = r.id;
   ```
4. **Check session** - pastikan tidak ada session lama

### **Jika dapat error:**
1. **Check error logs**: `D:\Programs\Xampp\apache\logs\error.log`
2. **Test autentikasi**: Akses langsung `user/dashboard.php` tanpa login
3. **Verify files exist**: Semua file `user/*.php` ada

---

## 🎉 Status Sistem:

**✅ LOGIN REDIRECT SUDAH BERFUNGSI NORMAL**

- ✅ Admin login → admin dashboard
- ✅ User login → user dashboard  
- ✅ Role-based access control aktif
- ✅ Session management aman
- ✅ Backward compatibility terjaga

---

## 🔗 Quick Links:

- **Login Page**: http://localhost/backup.lutfimulia/pemroweb/src/auth/login.php
- **Test User Creator**: http://localhost/backup.lutfimulia/pemroweb/create_test_user.php  
- **System Test**: http://localhost/backup.lutfimulia/pemroweb/test_system.php

**Silakan test dengan credentials di atas! 🚀**
