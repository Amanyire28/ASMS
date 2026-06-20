# ASMS Production Deployment Guide

## Requirements
- PHP 8.1+ with extensions: mbstring, xml, curl, zip, gd, bcmath, tokenizer, pdo_mysql
- MySQL 8.0+
- Nginx
- Composer
- Node.js 18+ & npm (only needed if rebuilding assets)
- Supervisor (for queue worker)

---

## First-time Server Setup

### 1. Clone the repo
```bash
cd /var/www
git clone https://github.com/Amanyire28/ASMS.git
cd ASMS
```

### 2. Install PHP dependencies
```bash
composer install --optimize-autoloader --no-dev
```

### 3. Configure environment
```bash
cp .env.example .env
nano .env   # fill in DB credentials, APP_URL, mail settings
php artisan key:generate
```

### 4. Database
```bash
php artisan migrate --force
php artisan db:seed --force   # only if you want default data
```

### 5. Storage & permissions
```bash
php artisan storage:link
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 6. Cache for production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 7. Nginx
```bash
sudo cp nginx/asms.conf /etc/nginx/sites-available/asms
sudo ln -s /etc/nginx/sites-available/asms /etc/nginx/sites-enabled/
# Edit the file and replace yourdomain.com with your actual domain
sudo nginx -t && sudo systemctl reload nginx
```

### 8. SSL
```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

### 9. Queue worker (Supervisor)
```bash
sudo cp supervisor/asms-worker.conf /etc/supervisor/conf.d/
# Edit the file and update the path if different from /var/www/ASMS
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start asms-worker:*
```

---

## Subsequent Deployments

Just run the deploy script:
```bash
bash deploy.sh
```

---

## Environment Variables Reference

| Key | Description |
|-----|-------------|
| `APP_URL` | Full URL including https:// |
| `APP_DEBUG` | Must be `false` in production |
| `DB_DATABASE` | Your MySQL database name |
| `DB_USERNAME` | MySQL user |
| `DB_PASSWORD` | MySQL password |
| `QUEUE_CONNECTION` | Keep as `database` |
| `MAIL_*` | Your SMTP mail provider settings |

---

## Troubleshooting

**500 error after deploy:**
```bash
php artisan config:clear && php artisan cache:clear
tail -50 storage/logs/laravel.log
```

**Queue jobs not processing:**
```bash
sudo supervisorctl status asms-worker:*
sudo supervisorctl restart asms-worker:*
```

**Storage files not accessible:**
```bash
php artisan storage:link
```

**Mass PDF download times out:**
- Ensure `fastcgi_read_timeout 300;` is in your Nginx config
- Ensure Supervisor worker timeout is 300: `--timeout=300`
