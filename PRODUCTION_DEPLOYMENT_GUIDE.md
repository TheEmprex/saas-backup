# 🚀 SaaS Platform Production Deployment Guide

This comprehensive guide will walk you through deploying your SaaS platform to production with optimal security, performance, and reliability.

## 📋 Pre-Deployment Checklist

### ✅ Environment Configuration
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Generate new `APP_KEY` with `php artisan key:generate`
- [ ] Configure production database credentials
- [ ] Set up Redis for caching and sessions
- [ ] Configure email settings (SMTP)
- [ ] Set up file storage (S3 recommended)

### ✅ Security Configuration
- [ ] Enable HTTPS with SSL certificate
- [ ] Set `SESSION_SECURE_COOKIE=true`
- [ ] Set `SESSION_HTTP_ONLY=true`
- [ ] Configure `SESSION_DOMAIN` for your domain
- [ ] Set up CORS policies
- [ ] Enable production security middleware
- [ ] Configure rate limiting

### ✅ Performance Optimization
- [ ] Set `CACHE_DRIVER=redis`
- [ ] Set `SESSION_DRIVER=redis`
- [ ] Set `QUEUE_CONNECTION=redis`
- [ ] Configure OPcache for PHP
- [ ] Set up CDN for static assets
- [ ] Enable Gzip compression

### ✅ Database Setup
- [ ] Create production database
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Seed initial data if needed
- [ ] Set up database backups
- [ ] Configure database monitoring

### ✅ Monitoring & Logging
- [ ] Set `LOG_LEVEL=error` for production
- [ ] Set up log rotation
- [ ] Configure error tracking (Sentry)
- [ ] Set up performance monitoring
- [ ] Configure uptime monitoring

## 🛠️ Deployment Steps

### 1. Server Setup

```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y nginx mysql-server redis-server php8.2-fpm php8.2-mysql php8.2-redis php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js and npm
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs
```

### 2. Application Deployment

```bash
# Clone your repository
git clone https://github.com/yourusername/saas-platform.git /var/www/saas
cd /var/www/saas

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install and build frontend assets
npm ci --production
npm run build

# Set proper permissions
sudo chown -R www-data:www-data /var/www/saas
sudo chmod -R 755 /var/www/saas
sudo chmod -R 775 /var/www/saas/storage
sudo chmod -R 775 /var/www/saas/bootstrap/cache

# Create storage link
php artisan storage:link

# Run optimizations
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 3. Database Configuration

```bash
# Create database and user
sudo mysql
CREATE DATABASE saas_production;
CREATE USER 'saas_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON saas_production.* TO 'saas_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Run migrations
php artisan migrate --force
```

### 4. Web Server Configuration

Create Nginx configuration (`/etc/nginx/sites-available/saas`):

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/saas/public;
    index index.php;

    # SSL Configuration
    ssl_certificate /path/to/your/certificate.crt;
    ssl_certificate_key /path/to/your/private.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_prefer_server_ciphers on;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/javascript application/json;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site:
```bash
sudo ln -s /etc/nginx/sites-available/saas /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 5. Queue and Scheduler Setup

Create systemd service (`/etc/systemd/system/saas-worker.service`):

```ini
[Unit]
Description=SaaS Platform Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/saas
ExecStart=/usr/bin/php /var/www/saas/artisan queue:work --sleep=3 --tries=3
Restart=always
RestartSec=3

[Install]
WantedBy=multi-user.target
```

Enable and start the worker:
```bash
sudo systemctl enable saas-worker
sudo systemctl start saas-worker
```

Add to crontab (`crontab -e`):
```bash
* * * * * cd /var/www/saas && php artisan schedule:run >> /dev/null 2>&1
```

## 📊 Production Monitoring

### Performance Monitoring Command
```bash
# Run system health check
php artisan production:monitor

# Run with database backup
php artisan production:monitor --backup
```

### Scheduled Tasks
Add these to your Laravel scheduler (`app/Console/Kernel.php`):

```php
protected function schedule(Schedule $schedule)
{
    // System monitoring every 5 minutes
    $schedule->command('production:monitor')
             ->everyFiveMinutes()
             ->withoutOverlapping();

    // Daily backup at 2 AM
    $schedule->command('production:monitor --backup')
             ->dailyAt('02:00')
             ->withoutOverlapping();

    // Clean up old logs weekly
    $schedule->command('log:clear --days=30')
             ->weekly();
}
```

## 🔒 Security Hardening

### 1. Enable Production Security Middleware
Add to routes that need extra protection:
```php
Route::middleware(['production.security'])->group(function () {
    // Your protected routes
});
```

### 2. Configure Fail2Ban (Optional)
```bash
sudo apt install fail2ban

# Create custom filter for Laravel
sudo nano /etc/fail2ban/filter.d/laravel.conf
```

### 3. Database Security
```sql
-- Remove test databases
DROP DATABASE IF EXISTS test;

-- Secure MySQL installation
mysql_secure_installation
```

## 📈 Performance Tuning

### PHP Configuration (`/etc/php/8.2/fpm/php.ini`)
```ini
memory_limit = 512M
max_execution_time = 300
upload_max_filesize = 20M
post_max_size = 20M
max_input_vars = 3000

# OPcache settings
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=12
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60
opcache.validate_timestamps=0
```

### Redis Configuration (`/etc/redis/redis.conf`)
```
maxmemory 256mb
maxmemory-policy allkeys-lru
save 900 1
save 300 10
save 60 10000
```

## 🔄 Backup Strategy

### Automated Backups
The system includes automated backup functionality:

1. **Database backups**: Daily at 2 AM
2. **File backups**: Weekly for storage directory
3. **Configuration backups**: Before each deployment

### Manual Backup Commands
```bash
# Create database backup
php artisan backup:database

# Restore from backup
php artisan backup:restore backup_file.sql.gz

# List available backups
php artisan backup:list
```

## 🚨 Monitoring & Alerts

### Health Check Endpoint
The application includes a health check endpoint at `/health` that monitors:
- Database connectivity
- Cache performance
- Storage availability
- Queue status

### Log Monitoring
Monitor these log files:
- `storage/logs/laravel.log` - Application logs
- `/var/log/nginx/error.log` - Web server errors
- `/var/log/php8.2-fpm.log` - PHP-FPM errors

## 🔧 Maintenance

### Regular Tasks
- **Daily**: Check system health, review error logs
- **Weekly**: Update packages, clean up logs
- **Monthly**: Review performance metrics, optimize database
- **Quarterly**: Security audit, dependency updates

### Update Procedure
1. Create backup
2. Put application in maintenance mode: `php artisan down`
3. Pull latest code: `git pull origin main`
4. Update dependencies: `composer install --no-dev`
5. Run migrations: `php artisan migrate --force`
6. Clear caches: `php artisan optimize:clear && php artisan optimize`
7. Bring application online: `php artisan up`

## 📞 Troubleshooting

### Common Issues

**500 Error**
- Check `storage/logs/laravel.log`
- Verify file permissions
- Check database connectivity

**High Memory Usage**
- Enable OPcache
- Optimize database queries
- Check for memory leaks in code

**Session Issues**
- Verify Redis connection
- Check session configuration
- Clear session storage

### Emergency Contacts
- System Administrator: admin@yourdomain.com
- Database Administrator: dba@yourdomain.com
- Security Team: security@yourdomain.com

---

## 🎉 Deployment Complete!

Your SaaS platform is now production-ready with:
- ✅ Optimized performance
- ✅ Enhanced security
- ✅ Automated monitoring
- ✅ Reliable backups
- ✅ Comprehensive logging

**Next Steps:**
1. Perform load testing
2. Set up monitoring dashboards
3. Configure alerting systems
4. Document operational procedures
5. Train your team on production operations

For support and updates, refer to the project documentation or contact the development team.
