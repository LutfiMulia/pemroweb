# Installation Guide

## Prerequisites

### System Requirements

- **Operating System**: Windows 10/11, macOS 10.14+, Ubuntu 18.04+
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **PHP**: 7.4+ (Recommended: 8.0+)
- **Database**: MySQL 5.7+ or MariaDB 10.3+
- **Storage**: Minimum 500MB available space
- **Memory**: Minimum 256MB RAM

### Required PHP Extensions

- mysqli or pdo_mysql
- mbstring
- json
- session
- hash
- openssl

## Installation Methods

### Method 1: XAMPP Installation (Recommended for Development)

#### Step 1: Download and Install XAMPP

1. Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Install XAMPP in your preferred directory (e.g., `C:\xampp`)
3. Start Apache and MySQL services from XAMPP Control Panel

#### Step 2: Download Insidentia

1. Download the latest release from GitHub
2. Extract the files to `C:\xampp\htdocs\insidentia`
3. Or clone the repository:
   ```bash
   git clone https://github.com/yourusername/insidentia.git C:\xampp\htdocs\insidentia
   ```

#### Step 3: Database Setup

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Create a new database named `inisidentia_db`
3. Import the database schema:
   - Click on `inisidentia_db` database
   - Go to "Import" tab
   - Select `database/schema.sql` file
   - Click "Go"

#### Step 4: Configuration

1. Navigate to `src/includes/config.php`
2. Update database credentials if needed:
   ```php
   $host = "localhost";
   $dbname = "inisidentia_db";
   $user = "root";
   $pass = ""; // Usually empty for XAMPP
   ```

#### Step 5: Create Required Tables

1. Open browser and go to: `http://localhost/insidentia/src/auth/setup_database.php`
2. This will create the `password_resets` table needed for forgot password functionality
3. Follow the on-screen instructions

#### Step 6: Access the Application

1. Open browser and navigate to: `http://localhost/insidentia/src/auth/login.php`
2. Use default admin credentials:
   - Email: `admin@insidentia.local`
   - Password: `password`

### Method 2: Manual Installation

#### Step 1: Prepare Environment

1. Install Apache/Nginx web server
2. Install PHP with required extensions
3. Install MySQL or MariaDB
4. Configure virtual host (optional)

#### Step 2: Download and Extract

```bash
# Download the latest release
wget https://github.com/yourusername/insidentia/archive/main.zip

# Extract files
unzip main.zip
mv insidentia-main /var/www/html/insidentia

# Set permissions
sudo chown -R www-data:www-data /var/www/html/insidentia
sudo chmod -R 755 /var/www/html/insidentia
```

#### Step 3: Database Setup

```bash
# Login to MySQL
mysql -u root -p

# Create database
CREATE DATABASE inisidentia_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Create user (optional)
CREATE USER 'insidentia_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON inisidentia_db.* TO 'insidentia_user'@'localhost';
FLUSH PRIVILEGES;

# Exit MySQL
exit;

# Import schema
mysql -u root -p inisidentia_db < /var/www/html/insidentia/database/schema.sql
```

#### Step 4: Configure Application

1. Copy configuration file:
   ```bash
   cp src/includes/config.example.php src/includes/config.php
   ```

2. Edit configuration:
   ```php
   <?php
   $host = "localhost";
   $dbname = "inisidentia_db";
   $user = "insidentia_user";
   $pass = "your_password";
   
   $conn = new mysqli($host, $user, $pass, $dbname);
   
   if ($conn->connect_error) {
       die("Connection failed: " . $conn->connect_error);
   }
   ?>
   ```

#### Step 5: Set Up Web Server

**Apache Configuration:**

Create virtual host file `/etc/apache2/sites-available/insidentia.conf`:

```apache
<VirtualHost *:80>
    ServerName insidentia.local
    DocumentRoot /var/www/html/insidentia
    
    <Directory /var/www/html/insidentia>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/insidentia_error.log
    CustomLog ${APACHE_LOG_DIR}/insidentia_access.log combined
</VirtualHost>
```

Enable the site:
```bash
sudo a2ensite insidentia.conf
sudo a2enmod rewrite
sudo systemctl reload apache2
```

**Nginx Configuration:**

Create `/etc/nginx/sites-available/insidentia`:

```nginx
server {
    listen 80;
    server_name insidentia.local;
    root /var/www/html/insidentia;
    
    index index.php index.html;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.ht {
        deny all;
    }
}
```

Enable the site:
```bash
sudo ln -s /etc/nginx/sites-available/insidentia /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Method 3: Docker Installation

#### Step 1: Install Docker

Download and install Docker from [https://www.docker.com/](https://www.docker.com/)

#### Step 2: Create Docker Compose File

Create `docker-compose.yml`:

```yaml
version: '3.8'

services:
  web:
    image: php:8.0-apache
    ports:
      - "80:80"
    volumes:
      - ./:/var/www/html
    depends_on:
      - db
    environment:
      - DB_HOST=db
      - DB_NAME=inisidentia_db
      - DB_USER=insidentia_user
      - DB_PASS=insidentia_pass

  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root_password
      MYSQL_DATABASE: inisidentia_db
      MYSQL_USER: insidentia_user
      MYSQL_PASSWORD: insidentia_pass
    ports:
      - "3306:3306"
    volumes:
      - db_data:/var/lib/mysql
      - ./database/schema.sql:/docker-entrypoint-initdb.d/schema.sql

  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    ports:
      - "8080:80"
    environment:
      PMA_HOST: db
      PMA_USER: root
      PMA_PASSWORD: root_password

volumes:
  db_data:
```

#### Step 3: Run Docker Compose

```bash
# Start services
docker-compose up -d

# Check status
docker-compose ps

# View logs
docker-compose logs web
```

#### Step 4: Access Application

- Application: `http://localhost`
- phpMyAdmin: `http://localhost:8080`

## Post-Installation Setup

### 1. Create Admin User

If admin user doesn't exist, create one:

```sql
INSERT INTO users (name, email, password, role_id) VALUES
('Administrator', 'admin@insidentia.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);
```

### 2. Configure Email (Optional)

For forgot password functionality, configure email settings in `src/includes/config.php`:

```php
// Email configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_FROM', 'your-email@gmail.com');
define('SMTP_FROM_NAME', 'Insidentia System');
```

### 3. Set Up Cron Jobs (Optional)

For automated tasks like cleaning expired tokens:

```bash
# Edit crontab
crontab -e

# Add this line to run cleanup every hour
0 * * * * /usr/bin/php /var/www/html/insidentia/src/includes/cleanup.php
```

### 4. Security Configuration

#### Create .htaccess file:

```apache
# Disable directory browsing
Options -Indexes

# Prevent access to configuration files
<Files "config.php">
    Order allow,deny
    Deny from all
</Files>

# Prevent access to .env files
<Files ".env">
    Order allow,deny
    Deny from all
</Files>

# Enable mod_rewrite
RewriteEngine On

# Redirect HTTP to HTTPS (for production)
# RewriteCond %{HTTPS} off
# RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

## Verification

### 1. Test Database Connection

Visit: `http://localhost/insidentia/src/auth/test_forgot_password.php`

### 2. Test Login

1. Go to: `http://localhost/insidentia/src/auth/login.php`
2. Login with admin credentials
3. Verify dashboard access

### 3. Test Forgot Password

1. Go to login page
2. Click "Forgot your password?"
3. Enter admin email
4. Follow the reset link

## Troubleshooting

### Common Issues

#### 1. Database Connection Error

**Error:** `Connection failed: Access denied for user`

**Solution:**
- Check database credentials in `config.php`
- Verify MySQL service is running
- Check user permissions

#### 2. File Permission Errors

**Error:** `Permission denied`

**Solution:**
```bash
# Fix permissions
sudo chown -R www-data:www-data /var/www/html/insidentia
sudo chmod -R 755 /var/www/html/insidentia
```

#### 3. Missing PHP Extensions

**Error:** `Call to undefined function mysqli_connect()`

**Solution:**
```bash
# Ubuntu/Debian
sudo apt-get install php-mysqli php-mbstring php-json

# CentOS/RHEL
sudo yum install php-mysqli php-mbstring php-json

# Restart web server
sudo systemctl restart apache2
```

#### 4. Apache ModRewrite Not Working

**Error:** `The requested URL was not found on this server`

**Solution:**
```bash
# Enable mod_rewrite
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### 5. Session Issues

**Error:** `Session not starting`

**Solution:**
- Check PHP session configuration
- Verify session directory permissions
- Check `session.save_path` in php.ini

### Log Files

Check these log files for debugging:

- **Apache Error Log**: `/var/log/apache2/error.log`
- **PHP Error Log**: `/var/log/php/errors.log`
- **MySQL Error Log**: `/var/log/mysql/error.log`

### Getting Help

If you encounter issues:

1. Check the [troubleshooting section](#troubleshooting)
2. Review log files for error messages
3. Search existing issues on GitHub
4. Create a new issue with:
   - Error message
   - System information
   - Steps to reproduce
   - Log file excerpts

## Next Steps

After successful installation:

1. Read the [Usage Guide](usage.md)
2. Review [Database Documentation](database.md)
3. Check [Deployment Guide](deployment.md) for production setup
4. Customize the application for your needs

## Uninstallation

To remove Insidentia:

1. **Remove files:**
   ```bash
   rm -rf /var/www/html/insidentia
   ```

2. **Remove database:**
   ```sql
   DROP DATABASE inisidentia_db;
   DROP USER 'insidentia_user'@'localhost';
   ```

3. **Remove Apache/Nginx configuration:**
   ```bash
   # Apache
   sudo a2dissite insidentia.conf
   sudo rm /etc/apache2/sites-available/insidentia.conf
   sudo systemctl reload apache2
   
   # Nginx
   sudo rm /etc/nginx/sites-enabled/insidentia
   sudo rm /etc/nginx/sites-available/insidentia
   sudo systemctl reload nginx
   ```

4. **Remove cron jobs:**
   ```bash
   crontab -e
   # Remove Insidentia-related entries
   ```
