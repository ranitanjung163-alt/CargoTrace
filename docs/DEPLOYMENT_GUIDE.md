# CargoTrace – Deployment & Production Guide

This guide describes how to deploy the CargoTrace Global Shipment Visibility Platform to a production VPS, cloud server (AWS, DigitalOcean, Hetzner, etc.), or shared hosting environment.

---

## 1. System Requirements

- **PHP:** 8.2+ (PHP 8.2.12 or newer tested)
- **PHP Extensions:** `pdo_mysql`, `curl`, `mbstring`, `openssl`, `zip`, `fileinfo`, `bcmath`, `tokenizer`, `xml`
- **Database:** MySQL 8.0+ or MariaDB 10.4+
- **Web Server:** Nginx or Apache with `mod_rewrite` enabled
- **SSL Certificate:** HTTPS required for external APIs & geocoding security

---

## 2. Environment Configuration (.env)

Create a production `.env` file on the server:

```env
APP_NAME=CargoTrace
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_APP_KEY
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cargo_trace_prod
DB_USERNAME=your_db_user
DB_PASSWORD=your_strong_password

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true

CACHE_STORE=database
QUEUE_CONNECTION=database

CARGOTRACE_DELAY_THRESHOLD_HOURS=12
CARGOTRACE_MAIN_EXCEPTION=WEATHER_DELAY
CARGOTRACE_MAIN_KPI=on_time_delivery_rate
CARGOTRACE_NOMINATIM_USER_AGENT="CargoTrace-GlobalTracking/1.0 (production-deployment)"
WEATHER_API_URL=https://api.open-meteo.com/v1/forecast
WEATHER_API_KEY=
```

---

## 3. Deployment Steps

```bash
# 1. Clone repository
git clone https://github.com/your-username/cargo_trace.git /var/www/cargotrace
cd /var/www/cargotrace

# 2. Install production PHP dependencies (no dev packages)
composer install --no-dev --optimize-autoloader

# 3. Generate secure application key
php artisan key:generate

# 4. Run database migrations safely
php artisan migrate --force

# 5. Seed initial demo data (optional for demo deployment)
php artisan db:seed --force

# 6. Optimize and cache configurations, routes, and views
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Create storage symlink
php artisan storage:link

# 8. Set correct file permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

---

## 4. Web Server Configuration

### Nginx Virtual Host (`/etc/nginx/sites-available/cargotrace`)
```nginx
server {
    listen 80;
    server_name cargotrace.yourdomain.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name cargotrace.yourdomain.com;
    root /var/www/cargotrace/public;

    ssl_certificate /etc/letsencrypt/live/cargotrace.yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/cargotrace.yourdomain.com/privkey.pem;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 5. Security & Pre-Presentation Verification Checklist

- [x] `APP_DEBUG=false` in production.
- [x] Database password and API tokens not committed to GitHub.
- [x] All 20 automated tests passing (`php artisan test`).
- [x] CSRF protection enabled on all web forms.
- [x] Server-side authorization active across all controllers and API routes.
- [x] Rate limiting active on authentication and API endpoints.
