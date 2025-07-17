# Database Documentation

## Database Structure

Insidentia menggunakan database MySQL dengan nama `inisidentia_db`. Database ini menyimpan semua data yang berkaitan dengan sistem pelaporan dan manajemen pengguna.

### Tables Overview

| Table | Description |
|-------|-------------|
| `users` | Menyimpan informasi pengguna sistem |
| `roles` | Menyimpan peran/role pengguna |
| `password_resets` | Menyimpan token reset password |
| `reports` | Menyimpan data laporan |
| `categories` | Menyimpan kategori laporan |

### Users Table

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);
```

### Roles Table

```sql
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Password Resets Table

```sql
CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_email (email)
);
```

### Reports Table

```sql
CREATE TABLE reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(255),
    status ENUM('pending', 'processing', 'completed', 'rejected') DEFAULT 'pending',
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);
```

### Categories Table

```sql
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    color VARCHAR(7) DEFAULT '#007bff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Database Setup

### 1. Create Database

```sql
CREATE DATABASE inisidentia_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Create Tables

Execute the SQL scripts in the following order:

1. Create roles table
2. Create users table
3. Create categories table
4. Create reports table
5. Create password_resets table

### 3. Insert Default Data

```sql
-- Insert default roles
INSERT INTO roles (name, description) VALUES
('admin', 'Administrator with full access'),
('user', 'Regular user with limited access');

-- Insert default categories
INSERT INTO categories (name, description, color) VALUES
('Infrastruktur', 'Laporan terkait infrastruktur', '#dc3545'),
('Kebersihan', 'Laporan terkait kebersihan', '#28a745'),
('Keamanan', 'Laporan terkait keamanan', '#ffc107'),
('Pelayanan', 'Laporan terkait pelayanan', '#17a2b8');

-- Insert default admin user
INSERT INTO users (name, email, password, role_id) VALUES
('Administrator', 'admin@insidentia.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);
```

## Database Backup & Restore

### Backup Database

```bash
mysqldump -u root -p inisidentia_db > backup_insidentia_$(date +%Y%m%d_%H%M%S).sql
```

### Restore Database

```bash
mysql -u root -p inisidentia_db < backup_insidentia_YYYYMMDD_HHMMSS.sql
```

## Performance Optimization

### Indexes

The following indexes are recommended for optimal performance:

```sql
-- Users table indexes
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role_id ON users(role_id);
CREATE INDEX idx_users_status ON users(status);

-- Reports table indexes
CREATE INDEX idx_reports_user_id ON reports(user_id);
CREATE INDEX idx_reports_category_id ON reports(category_id);
CREATE INDEX idx_reports_status ON reports(status);
CREATE INDEX idx_reports_created_at ON reports(created_at);

-- Password resets table indexes
CREATE INDEX idx_password_resets_token ON password_resets(token);
CREATE INDEX idx_password_resets_expires_at ON password_resets(expires_at);
```

## Security Considerations

1. **Password Hashing**: All passwords are hashed using PHP's `password_hash()` function
2. **Token Security**: Password reset tokens are cryptographically secure random strings
3. **Token Expiration**: Password reset tokens expire after 1 hour
4. **Input Sanitization**: All user inputs are sanitized before database insertion
5. **Prepared Statements**: All database queries use prepared statements to prevent SQL injection

## Maintenance

### Regular Maintenance Tasks

1. **Clean expired password reset tokens**:
   ```sql
   DELETE FROM password_resets WHERE expires_at < NOW();
   ```

2. **Archive old reports** (if needed):
   ```sql
   -- Move reports older than 1 year to archive table
   CREATE TABLE reports_archive AS SELECT * FROM reports WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);
   DELETE FROM reports WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);
   ```

3. **Update statistics**:
   ```sql
   OPTIMIZE TABLE users, reports, categories, roles, password_resets;
   ```
