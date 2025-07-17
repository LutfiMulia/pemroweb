# Deployment Documentation

## Production Deployment

### System Requirements

- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **PHP**: 7.4+ (Recommended: 8.0+)
- **Database**: MySQL 5.7+ or MariaDB 10.3+
- **Storage**: Minimum 1GB disk space
- **Memory**: Minimum 512MB RAM
- **SSL Certificate**: Required for production

### Pre-Deployment Checklist

- [ ] Database credentials configured
- [ ] PHP extensions installed (mysqli, pdo, mbstring, json)
- [ ] Web server configured
- [ ] SSL certificate installed
- [ ] File permissions set correctly
- [ ] Error reporting configured
- [ ] Backup strategy implemented

## Server Configuration

### Apache Configuration

Create `.htaccess` file in the root directory:

```apache
# Security Headers
Header always set X-Frame-Options "SAMEORIGIN"
Header always set X-XSS-Protection "1; mode=block"
Header always set X-Content-Type-Options "nosniff"
Header always set Referrer-Policy "strict-origin-when-cross-origin"
Header always set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' cdn.jsdelivr.net cdn.tailwindcss.com; style-src 'self' 'unsafe-inline' cdn.jsdelivr.net fonts.googleapis.com; font-src 'self' fonts.gstatic.com; img-src 'self' data:;"

# URL Rewriting
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php/$1 [QSA,L]

# Prevent access to sensitive files
<Files ~ "^\.">
    Order allow,deny
    Deny from all
</Files>

<Files ~ "\.md$">
    Order allow,deny
    Deny from all
</Files>

<Files ~ "\.sql$">
    Order allow,deny
    Deny from all
</Files>

# Force HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### Nginx Configuration

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    
    root /var/www/html/insidentia;
    index index.php index.html;
    
    # SSL Configuration
    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    
    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' cdn.jsdelivr.net cdn.tailwindcss.com; style-src 'self' 'unsafe-inline' cdn.jsdelivr.net fonts.googleapis.com; font-src 'self' fonts.gstatic.com; img-src 'self' data:;" always;
    
    # PHP-FPM Configuration
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }
    
    # Deny access to sensitive files
    location ~ /\. {
        deny all;
    }
    
    location ~ \.(md|sql|txt)$ {
        deny all;
    }
    
    # Assets optimization
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

## Database Setup for Production

### 1. Create Production Database

```sql
CREATE DATABASE insidentia_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Create Database User

```sql
CREATE USER 'insidentia_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT SELECT, INSERT, UPDATE, DELETE ON insidentia_prod.* TO 'insidentia_user'@'localhost';
FLUSH PRIVILEGES;
```

### 3. Import Database Schema

```bash
mysql -u insidentia_user -p insidentia_prod < database/schema.sql
```

## Configuration Files

### Production config.php

```php
<?php
// Production Database Configuration
$host = "localhost";
$dbname = "insidentia_prod";
$user = "insidentia_user";
$pass = "strong_password_here";

// Security Settings
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/php/insidentia_errors.log');

// Session Configuration
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Service temporarily unavailable");
}

// Set charset
$conn->set_charset("utf8mb4");
?>
```

## File Permissions

Set appropriate file permissions:

```bash
# Set directory permissions
find /var/www/html/insidentia -type d -exec chmod 755 {} \;

# Set file permissions
find /var/www/html/insidentia -type f -exec chmod 644 {} \;

# Set executable permissions for PHP files
find /var/www/html/insidentia -name "*.php" -exec chmod 644 {} \;

# Set ownership
chown -R www-data:www-data /var/www/html/insidentia

# Secure sensitive directories
chmod 700 /var/www/html/insidentia/docs
chmod 700 /var/www/html/insidentia/database
```

## SSL Certificate Setup

### Using Let's Encrypt (Recommended)

```bash
# Install Certbot
sudo apt update
sudo apt install certbot python3-certbot-apache

# Obtain SSL certificate
sudo certbot --apache -d yourdomain.com -d www.yourdomain.com

# Set up auto-renewal
sudo crontab -e
# Add this line:
0 12 * * * /usr/bin/certbot renew --quiet
```

## Monitoring & Logging

### Error Logging

Create log directory and configure logging:

```bash
sudo mkdir -p /var/log/php
sudo touch /var/log/php/insidentia_errors.log
sudo chown www-data:www-data /var/log/php/insidentia_errors.log
sudo chmod 644 /var/log/php/insidentia_errors.log
```

### Access Monitoring

Monitor important files and directories:

```bash
# Monitor login attempts
tail -f /var/log/apache2/access.log | grep "login.php"

# Monitor error logs
tail -f /var/log/php/insidentia_errors.log

# Monitor system resources
htop
```

## Backup Strategy

### Database Backup

Create automated database backup:

```bash
#!/bin/bash
# /etc/cron.daily/insidentia-backup

DB_NAME="insidentia_prod"
DB_USER="insidentia_user"
DB_PASS="strong_password_here"
BACKUP_DIR="/var/backups/insidentia"
DATE=$(date +%Y%m%d_%H%M%S)

# Create backup directory
mkdir -p $BACKUP_DIR

# Database backup
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_backup_$DATE.sql

# Compress backup
gzip $BACKUP_DIR/db_backup_$DATE.sql

# Keep only last 7 days
find $BACKUP_DIR -name "*.sql.gz" -mtime +7 -delete

# File backup
tar -czf $BACKUP_DIR/files_backup_$DATE.tar.gz /var/www/html/insidentia

# Keep only last 7 days
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete
```

### Make backup script executable

```bash
sudo chmod +x /etc/cron.daily/insidentia-backup
```

## Security Hardening

### 1. Update System Regularly

```bash
sudo apt update && sudo apt upgrade -y
```

### 2. Configure Firewall

```bash
sudo ufw enable
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw deny from <suspicious_ip>
```

### 3. Fail2Ban Configuration

```bash
# Install Fail2Ban
sudo apt install fail2ban

# Configure for Apache
sudo nano /etc/fail2ban/jail.local
```

Add this configuration:

```ini
[apache-auth]
enabled = true
port = http,https
filter = apache-auth
logpath = /var/log/apache2/error.log
maxretry = 3
bantime = 3600
findtime = 600

[apache-badbots]
enabled = true
port = http,https
filter = apache-badbots
logpath = /var/log/apache2/access.log
maxretry = 2
bantime = 86400
```

## Performance Optimization

### 1. Enable PHP OPcache

```php
; php.ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=60
opcache.fast_shutdown=1
```

### 2. Database Optimization

```sql
-- Optimize tables regularly
OPTIMIZE TABLE users, reports, categories, roles, password_resets;

-- Add indexes for better performance
CREATE INDEX idx_reports_created_at ON reports(created_at);
CREATE INDEX idx_users_email ON users(email);
```

### 3. Enable Gzip Compression

```apache
# .htaccess
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>
```

## Health Checks

### Create health check endpoint

```php
<?php
// health.php
header('Content-Type: application/json');

$health = [
    'status' => 'healthy',
    'timestamp' => date('Y-m-d H:i:s'),
    'version' => '1.0.0'
];

// Check database connection
try {
    require_once 'src/includes/config.php';
    $conn->ping();
    $health['database'] = 'connected';
} catch (Exception $e) {
    $health['status'] = 'unhealthy';
    $health['database'] = 'disconnected';
}

// Check disk space
$diskFree = disk_free_space('/');
$diskTotal = disk_total_space('/');
$diskUsage = (1 - ($diskFree / $diskTotal)) * 100;

$health['disk_usage'] = round($diskUsage, 2) . '%';

if ($diskUsage > 90) {
    $health['status'] = 'warning';
}

echo json_encode($health, JSON_PRETTY_PRINT);
?>
```

## Troubleshooting

### Common Issues

1. **Database Connection Failed**
   - Check credentials in config.php
   - Verify MySQL service is running
   - Check firewall settings

2. **File Permission Errors**
   - Verify www-data ownership
   - Check directory permissions (755)
   - Check file permissions (644)

3. **SSL Certificate Issues**
   - Verify certificate path
   - Check certificate expiration
   - Validate certificate chain

4. **Performance Issues**
   - Enable OPcache
   - Optimize database queries
   - Monitor server resources

### Log Analysis

```bash
# Check Apache error logs
sudo tail -f /var/log/apache2/error.log

# Check PHP error logs
sudo tail -f /var/log/php/insidentia_errors.log

# Check system logs
sudo journalctl -f -u apache2
```
