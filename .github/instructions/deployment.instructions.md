# Deployment Guidelines

## Environment Setup

### Production Environment Requirements
- **PHP 8.1+** with required extensions
- **MySQL 8.0+** or MariaDB 10.6+
- **Node.js 18+** for frontend build
- **Web server** (Apache/Nginx)
- **SSL certificate** for HTTPS
- **Process manager** (PM2/Supervisor)

### Environment Variables
```bash
# .env.production
APP_ENV=production
APP_DEBUG=false
APP_URL=https://studynotes.com

# Database
DB_HOST=localhost
DB_DATABASE=studynotes_prod
DB_USERNAME=studynotes_user
DB_PASSWORD=secure_password_here

# API Keys
GEMINI_API_KEY=your_gemini_api_key_here

# Security
SESSION_SECURE=true
SESSION_HTTPONLY=true
SESSION_SAMESITE=Strict

# Cache
CACHE_DRIVER=file
CACHE_TTL=3600

# Email (if implemented)
MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=noreply@studynotes.com
MAIL_PASSWORD=app_password_here
```

## Build Process

### Frontend Build Script
```json
{
  "scripts": {
    "build": "npm run build:css && npm run build:js",
    "build:css": "tailwindcss -i ./src/index.css -o ./public/css/app.css --minify",
    "build:js": "react-scripts build",
    "build:production": "NODE_ENV=production npm run build",
    "analyze": "npm run build && npx webpack-bundle-analyzer build/static/js/*.js"
  }
}
```

### PHP Optimization
```php
// config/production.php
<?php

// Enable OPcache
ini_set('opcache.enable', 1);
ini_set('opcache.memory_consumption', 128);
ini_set('opcache.max_accelerated_files', 10000);
ini_set('opcache.revalidate_freq', 0);
ini_set('opcache.validate_timestamps', 0);

// Session configuration
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', 1);

// Error handling
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/studynotes/php_errors.log');
```

## Server Configuration

### Nginx Configuration
```nginx
server {
    listen 443 ssl http2;
    server_name studynotes.com www.studynotes.com;
    root /var/www/studynotes/public;
    index index.php index.html;

    # SSL Configuration
    ssl_certificate /etc/ssl/certs/studynotes.crt;
    ssl_certificate_key /etc/ssl/private/studynotes.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    # Gzip Compression
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml;

    # Static file caching
    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # PHP handling
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # React Router support
    location / {
        try_files $uri $uri/ /index.html;
    }

    # Deny access to sensitive files
    location ~ /\. {
        deny all;
    }
    
    location ~ /(vendor|config|SQL) {
        deny all;
    }
}

# Redirect HTTP to HTTPS
server {
    listen 80;
    server_name studynotes.com www.studynotes.com;
    return 301 https://$server_name$request_uri;
}
```

### Apache Configuration
```apache
<VirtualHost *:443>
    ServerName studynotes.com
    ServerAlias www.studynotes.com
    DocumentRoot /var/www/studynotes/public
    
    # SSL Configuration
    SSLEngine on
    SSLCertificateFile /etc/ssl/certs/studynotes.crt
    SSLCertificateKeyFile /etc/ssl/private/studynotes.key
    
    # Security Headers
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
    
    # Compression
    LoadModule deflate_module modules/mod_deflate.so
    <Location />
        SetOutputFilter DEFLATE
        SetEnvIfNoCase Request_URI \
            \.(?:gif|jpe?g|png)$ no-gzip dont-vary
    </Location>
    
    # Caching
    <FilesMatch "\.(css|js|png|jpg|jpeg|gif|ico|svg)$">
        ExpiresActive On
        ExpiresDefault "access plus 1 year"
    </FilesMatch>
    
    # Directory permissions
    <Directory /var/www/studynotes/public>
        Options -Indexes
        AllowOverride All
        Require all granted
    </Directory>
    
    # Deny access to sensitive directories
    <Directory /var/www/studynotes/config>
        Require all denied
    </Directory>
    
    <Directory /var/www/studynotes/vendor>
        Require all denied
    </Directory>
</VirtualHost>
```

## Database Configuration

### Production MySQL Settings
```sql
-- my.cnf optimizations
[mysqld]
# InnoDB settings
innodb_buffer_pool_size = 2G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 1
innodb_file_per_table = 1

# Query cache
query_cache_type = 1
query_cache_size = 256M

# Connection settings
max_connections = 500
max_connect_errors = 100000

# Binary logging
log-bin = mysql-bin
expire_logs_days = 7
max_binlog_size = 100M

# Slow query log
slow_query_log = 1
long_query_time = 2
log_queries_not_using_indexes = 1
```

### Database Security
```sql
-- Create dedicated user for application
CREATE USER 'studynotes_app'@'localhost' IDENTIFIED BY 'secure_password';

-- Grant minimal required permissions
GRANT SELECT, INSERT, UPDATE, DELETE ON studynotes.* TO 'studynotes_app'@'localhost';

-- Remove test databases and users
DROP DATABASE IF EXISTS test;
DELETE FROM mysql.user WHERE User='';
DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');

-- Flush privileges
FLUSH PRIVILEGES;
```

## Monitoring and Logging

### Application Logging
```php
// Logger configuration
class Logger 
{
    private const LOG_LEVELS = [
        'ERROR' => 1,
        'WARN' => 2,
        'INFO' => 3,
        'DEBUG' => 4
    ];
    
    private string $logPath;
    private int $maxLevel;
    
    public function __construct(string $logPath = '/var/log/studynotes/', int $maxLevel = 3) 
    {
        $this->logPath = rtrim($logPath, '/') . '/';
        $this->maxLevel = $maxLevel;
    }
    
    public function error(string $message, array $context = []): void 
    {
        $this->log('ERROR', $message, $context);
    }
    
    public function info(string $message, array $context = []): void 
    {
        $this->log('INFO', $message, $context);
    }
    
    private function log(string $level, string $message, array $context): void 
    {
        if (self::LOG_LEVELS[$level] > $this->maxLevel) {
            return;
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = empty($context) ? '' : ' ' . json_encode($context);
        $logEntry = "[{$timestamp}] {$level}: {$message}{$contextStr}\n";
        
        $filename = $this->logPath . strtolower($level) . '_' . date('Y-m-d') . '.log';
        file_put_contents($filename, $logEntry, FILE_APPEND | LOCK_EX);
    }
}
```

### System Monitoring Script
```bash
#!/bin/bash
# monitoring.sh - System health check script

LOG_FILE="/var/log/studynotes/monitoring.log"
ALERT_EMAIL="admin@studynotes.com"

# Check disk space
DISK_USAGE=$(df -h / | awk 'NR==2 {print $5}' | sed 's/%//')
if [ $DISK_USAGE -gt 80 ]; then
    echo "$(date): WARNING - Disk usage is ${DISK_USAGE}%" >> $LOG_FILE
    echo "Disk usage warning: ${DISK_USAGE}%" | mail -s "StudyNotes Alert" $ALERT_EMAIL
fi

# Check MySQL connection
mysql -u studynotes_app -p$DB_PASSWORD -e "SELECT 1" studynotes > /dev/null 2>&1
if [ $? -ne 0 ]; then
    echo "$(date): ERROR - MySQL connection failed" >> $LOG_FILE
    echo "MySQL connection failed" | mail -s "StudyNotes Critical Alert" $ALERT_EMAIL
fi

# Check web server response
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://studynotes.com)
if [ $HTTP_STATUS -ne 200 ]; then
    echo "$(date): ERROR - Web server returned HTTP $HTTP_STATUS" >> $LOG_FILE
    echo "Web server error: HTTP $HTTP_STATUS" | mail -s "StudyNotes Critical Alert" $ALERT_EMAIL
fi

# Check log file sizes
find /var/log/studynotes/ -name "*.log" -size +100M -exec echo "$(date): WARNING - Large log file: {}" \; >> $LOG_FILE
```

## Backup Strategy

### Automated Backup Script
```bash
#!/bin/bash
# backup.sh - Automated backup script

BACKUP_DIR="/backups/studynotes"
DATE=$(date +%Y%m%d_%H%M%S)
RETENTION_DAYS=30

# Create backup directory
mkdir -p $BACKUP_DIR

# Database backup
mysqldump \
    --single-transaction \
    --routines \
    --triggers \
    --user=studynotes_backup \
    --password=$DB_BACKUP_PASSWORD \
    studynotes | gzip > $BACKUP_DIR/database_$DATE.sql.gz

# File backup
tar -czf $BACKUP_DIR/files_$DATE.tar.gz \
    /var/www/studynotes \
    --exclude=/var/www/studynotes/node_modules \
    --exclude=/var/www/studynotes/vendor

# Upload to cloud storage (optional)
aws s3 cp $BACKUP_DIR/database_$DATE.sql.gz s3://studynotes-backups/
aws s3 cp $BACKUP_DIR/files_$DATE.tar.gz s3://studynotes-backups/

# Clean old backups
find $BACKUP_DIR -name "*.gz" -mtime +$RETENTION_DAYS -delete

echo "$(date): Backup completed successfully" >> /var/log/studynotes/backup.log
```

## Security Hardening

### SSL/TLS Configuration
```bash
# Generate strong SSL certificate
openssl req -x509 -nodes -days 365 -newkey rsa:4096 \
    -keyout /etc/ssl/private/studynotes.key \
    -out /etc/ssl/certs/studynotes.crt

# Set proper permissions
chmod 600 /etc/ssl/private/studynotes.key
chmod 644 /etc/ssl/certs/studynotes.crt

# Test SSL configuration
openssl s_client -connect studynotes.com:443 -servername studynotes.com
```

### Firewall Configuration
```bash
# UFW firewall rules
ufw default deny incoming
ufw default allow outgoing
ufw allow ssh
ufw allow 80/tcp
ufw allow 443/tcp
ufw allow from 127.0.0.1 to any port 3306  # MySQL local only
ufw enable

# Fail2ban for brute force protection
apt install fail2ban
cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local

# Custom jail for StudyNotes
cat > /etc/fail2ban/jail.d/studynotes.conf << EOF
[studynotes-auth]
enabled = true
port = http,https
filter = studynotes-auth
logpath = /var/log/studynotes/auth.log
maxretry = 5
bantime = 3600
EOF
```

### Regular Security Updates
```bash
#!/bin/bash
# security-updates.sh - Automated security updates

# System packages
apt update && apt upgrade -y

# PHP packages
composer update --no-dev --optimize-autoloader

# Node.js packages
npm audit fix --production

# Log update completion
echo "$(date): Security updates completed" >> /var/log/studynotes/updates.log
```
