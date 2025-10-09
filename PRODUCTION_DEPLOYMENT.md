# Production Deployment Guide - Rick's Golden Question Contest System

## 🚀 Pre-Deployment Checklist

### Required Services & Keys
- [x] MySQL Database (8.0+)
- [x] Redis Server (7.0+)
- [x] Google reCAPTCHA v3 keys (https://www.google.com/recaptcha/admin/create)
- [x] Google Maps API key (https://console.cloud.google.com/google/maps-apis)
- [x] Tremendous API key (https://www.tremendous.com)
- [x] Email SMTP credentials
- [x] SSL certificate for domain
- [x] Pusher account (for real-time features)

---

## 📦 Step 1: Server Preparation

### System Requirements
```bash
- Ubuntu 20.04+ / Debian 11+
- PHP 8.4+
- MySQL 8.0+ or MariaDB 10.6+
- Redis 7.0+
- Nginx 1.18+ or Apache 2.4+
- Node.js 20.x+
- Composer 2.x
- Supervisor (for queue workers)
```

### Install Dependencies
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.4 and extensions
sudo apt install php8.4-fpm php8.4-cli php8.4-mysql php8.4-redis \
  php8.4-mbstring php8.4-xml php8.4-curl php8.4-zip php8.4-gd \
  php8.4-bcmath php8.4-intl php8.4-opcache

# Install MySQL
sudo apt install mysql-server

# Install Redis
sudo apt install redis-server

# Install Nginx
sudo apt install nginx

# Install Node.js 20
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install nodejs

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Supervisor
sudo apt install supervisor
```

---

## 🗄️ Step 2: Database Setup

### Create Production Database
```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE trivia_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'trivia_user'@'localhost' IDENTIFIED BY 'SECURE_PASSWORD_HERE';
GRANT ALL PRIVILEGES ON trivia_production.* TO 'trivia_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Optimize MySQL Configuration
Edit `/etc/mysql/mysql.conf.d/mysqld.cnf`:
```ini
[mysqld]
# Performance optimizations
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT
max_connections = 200
query_cache_size = 0
query_cache_type = 0

# Character set
character-set-server = utf8mb4
collation-server = utf8mb4_unicode_ci
```

Restart MySQL:
```bash
sudo systemctl restart mysql
```

---

## 🔧 Step 3: Redis Configuration

Edit `/etc/redis/redis.conf`:
```ini
# Memory limit
maxmemory 512mb
maxmemory-policy allkeys-lru

# Persistence (disable for pure cache)
save ""
appendonly no

# Performance
tcp-backlog 511
timeout 0
tcp-keepalive 300
```

Restart Redis:
```bash
sudo systemctl restart redis-server
```

---

## 📁 Step 4: Deploy Application

### Clone Repository
```bash
cd /var/www
sudo git clone https://github.com/mrshanebarron/upwork-trivia.git trivia.sbarron.com
cd trivia.sbarron.com
```

### Set Permissions
```bash
sudo chown -R www-data:www-data /var/www/trivia.sbarron.com
sudo chmod -R 755 /var/www/trivia.sbarron.com
sudo chmod -R 775 /var/www/trivia.sbarron.com/storage
sudo chmod -R 775 /var/www/trivia.sbarron.com/bootstrap/cache
```

### Install Dependencies
```bash
# PHP dependencies (no dev)
composer install --no-dev --optimize-autoloader

# Node dependencies
npm ci

# Build frontend assets
npm run build
```

### Environment Configuration
```bash
# Copy production environment
cp .env.production .env

# Generate application key
php artisan key:generate

# Edit .env with production values
nano .env
```

**Critical .env Settings:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://trivia.sbarron.com

DB_CONNECTION=mysql
DB_DATABASE=trivia_production
DB_USERNAME=trivia_user
DB_PASSWORD=your_secure_password

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Add all API keys (reCAPTCHA, Maps, Tremendous)
```

### Run Migrations
```bash
php artisan migrate --force
php artisan db:seed --force
```

### Create Database Indexes
```bash
php artisan migrate --path=database/migrations/2024_10_09_000000_add_performance_indexes.php --force
```

---

## ⚡ Step 5: Performance Optimization

### Optimize Application
```bash
# Run comprehensive optimization
php artisan app:optimize-performance --warm-cache

# Or manually:
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### Configure PHP OpCache
Edit `/etc/php/8.4/fpm/php.ini`:
```ini
[opcache]
opcache.enable=1
opcache.enable_cli=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0  # Disable in production
opcache.revalidate_freq=0
opcache.save_comments=1
opcache.fast_shutdown=1
```

Restart PHP-FPM:
```bash
sudo systemctl restart php8.4-fpm
```

---

## 🌐 Step 6: Web Server Configuration

### Nginx Configuration
Create `/etc/nginx/sites-available/trivia.sbarron.com`:
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name trivia.sbarron.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name trivia.sbarron.com;

    root /var/www/trivia.sbarron.com/public;
    index index.php index.html;

    # SSL certificates (Let's Encrypt)
    ssl_certificate /etc/letsencrypt/live/trivia.sbarron.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/trivia.sbarron.com/privkey.pem;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript
               application/x-javascript application/xml+rss
               application/javascript application/json;

    # Browser caching for static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|ttf|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;

        # Performance
        fastcgi_buffer_size 128k;
        fastcgi_buffers 256 16k;
        fastcgi_busy_buffers_size 256k;
        fastcgi_temp_file_write_size 256k;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/trivia.sbarron.com /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### SSL Certificate (Let's Encrypt)
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d trivia.sbarron.com
```

---

## 👷 Step 7: Queue Workers

### Configure Supervisor
Create `/etc/supervisor/conf.d/trivia-worker.conf`:
```ini
[program:trivia-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/trivia.sbarron.com/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/trivia.sbarron.com/storage/logs/worker.log
stopwaitsecs=3600
```

Start workers:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start trivia-worker:*
```

### Configure Scheduler
Add to crontab (`crontab -e`):
```bash
* * * * * cd /var/www/trivia.sbarron.com && php artisan schedule:run >> /dev/null 2>&1
```

---

## 📊 Step 8: Monitoring & Logging

### Log Rotation
Create `/etc/logrotate.d/trivia`:
```
/var/www/trivia.sbarron.com/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 0640 www-data www-data
    sharedscripts
}
```

### Health Check Endpoint
Add to `routes/web.php`:
```php
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now(),
        'redis' => Redis::ping(),
        'database' => DB::connection()->getPdo() !== null,
    ]);
});
```

---

## 🔐 Step 9: Security Hardening

### Firewall Configuration
```bash
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

### Fail2Ban (Optional)
```bash
sudo apt install fail2ban
sudo systemctl enable fail2ban
```

### File Permissions Review
```bash
# Ensure sensitive files are protected
chmod 600 /var/www/trivia.sbarron.com/.env
chmod 644 /var/www/trivia.sbarron.com/config/*.php
```

---

## 🚦 Step 10: Post-Deployment Verification

### Test Checklist
- [ ] Homepage loads (https://trivia.sbarron.com)
- [ ] Admin panel accessible (https://trivia.sbarron.com/admin)
- [ ] User registration works
- [ ] Contest submission works
- [ ] Gift card delivery job processes
- [ ] Email notifications send
- [ ] QR code scanning works
- [ ] Google Maps heatmap displays
- [ ] Cache is working (check Redis: `redis-cli MONITOR`)
- [ ] Queue workers are running (`sudo supervisorctl status`)
- [ ] SSL certificate valid
- [ ] Logs are being written

### Performance Testing
```bash
# Test response times
curl -w "@curl-format.txt" -o /dev/null -s https://trivia.sbarron.com

# Check opcache status
php -r "print_r(opcache_get_status());"

# Monitor Redis
redis-cli INFO stats
```

---

## 🔄 Deployment Updates

### Standard Deployment Process
```bash
cd /var/www/trivia.sbarron.com

# Pull latest code
git pull origin main

# Update dependencies
composer install --no-dev --optimize-autoloader
npm ci && npm run build

# Run migrations
php artisan migrate --force

# Clear and rebuild caches
php artisan app:optimize-performance --clear-cache --warm-cache

# Restart workers
sudo supervisorctl restart trivia-worker:*

# Restart PHP-FPM
sudo systemctl reload php8.4-fpm
```

---

## 🆘 Troubleshooting

### Common Issues

**Issue: 500 Server Error**
```bash
# Check logs
tail -f storage/logs/laravel.log

# Check permissions
sudo chown -R www-data:www-data storage bootstrap/cache
```

**Issue: Queue jobs not processing**
```bash
# Check worker status
sudo supervisorctl status

# Restart workers
sudo supervisorctl restart trivia-worker:*

# Check Redis connection
redis-cli ping
```

**Issue: Slow page loads**
```bash
# Check if caches are enabled
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Check opcache
php -r "print_r(opcache_get_status());"

# Monitor Redis
redis-cli --latency
```

**Issue: Database connection errors**
```bash
# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Check MySQL status
sudo systemctl status mysql
```

---

## 📈 Performance Metrics

### Expected Performance (Production)
- **Homepage Load**: < 200ms
- **Contest Submission**: < 500ms
- **Admin Dashboard**: < 1s
- **Database Queries**: < 50ms average
- **Cache Hit Rate**: > 90%
- **Queue Processing**: < 5s average

### Monitoring Tools
- Laravel Telescope (development only)
- Laravel Horizon (queue monitoring)
- Flare (error tracking)
- New Relic / DataDog (APM)

---

## 📞 Support Contacts

**For Production Issues:**
- Flare Dashboard: https://flareapp.io
- Error Logs: `storage/logs/laravel.log`
- Worker Logs: `storage/logs/worker.log`

**Shane Barron**
- Email: mrshanebarron@gmail.com
- Local deployment: http://upwork-trivia.test

---

## ✅ Success Criteria

The deployment is successful when:
1. All health checks pass
2. Contest submissions process in < 500ms
3. Gift cards deliver successfully
4. No errors in logs for 24 hours
5. Queue workers process jobs reliably
6. Cache hit rate > 90%
7. Rick can manage everything via admin panel
8. SSL certificate valid and auto-renewing
9. Backups configured and tested
10. Monitoring alerts configured

---

**Last Updated:** October 9, 2025
**Version:** 2.0.0 - Golden Question Contest System
