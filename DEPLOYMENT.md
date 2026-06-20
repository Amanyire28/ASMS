# ASMS Deployment — File Manager / Shared Hosting

## What to zip

Create a zip of the project folder but **exclude** these:
- `node_modules/`
- `.git/`
- `.env`
- `storage/logs/*.log`

Include everything else — especially `vendor/` (PHP dependencies are already in there).

---

## Folder structure on your hosting

Most shared hosts (cPanel) have this layout:

```
/home/youraccount/
    public_html/        ← this is your web root (www)
    asms/               ← put all Laravel files HERE (not in public_html)
```

So you will have:
```
/home/youraccount/asms/          ← all Laravel files (app, config, routes, vendor...)
/home/youraccount/public_html/   ← only contents of Laravel's /public folder
```

---

## Step-by-step

### 1. Upload & extract
- Open File Manager in cPanel
- Go to `/home/youraccount/` (one level above `public_html`)
- Upload your zip here and extract it
- You should now have `/home/youraccount/asms/`

### 2. Move public files
- Copy everything **inside** `asms/public/` into `public_html/`
- That includes: `index.php`, `.htaccess`, `css/`, `js/`, `mix-manifest.json`, `favicon.ico`

### 3. Fix index.php paths
- Open `public_html/index.php` in the file manager editor
- Find these two lines near the top:

```php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
```

- Change both to point to your asms folder:

```php
require __DIR__.'/../asms/vendor/autoload.php';
$app = require_once __DIR__.'/../asms/bootstrap/app.php';
```

### 4. Create .env
- In `/home/youraccount/asms/` create a new file called `.env`
- Paste this content and fill in your values:

```env
APP_NAME="Administrative Student Management System"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=465
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Administrative Student Management System"
```

> Note: `QUEUE_CONNECTION=sync` — shared hosting has no queue worker,
> so jobs run immediately in the request instead.

### 5. Generate app key
- In cPanel go to **Terminal** (if available) and run:
  ```bash
  cd ~/asms && php artisan key:generate
  ```
- If no terminal: use this PHP script trick — create a file `public_html/genkey.php`:
  ```php
  <?php
  require '../asms/vendor/autoload.php';
  $app = require '../asms/bootstrap/app.php';
  $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
  echo base64_encode(random_bytes(32));
  ```
  Visit `https://yourdomain.com/genkey.php`, copy the output, paste it into `.env` as:
  `APP_KEY=base64:PASTE_HERE`
  Then **delete genkey.php immediately**.

### 6. Run migrations
- In cPanel Terminal:
  ```bash
  cd ~/asms && php artisan migrate --force
  ```
- If no terminal: create `public_html/migrate.php`:
  ```php
  <?php
  require '../asms/vendor/autoload.php';
  $app = require '../asms/bootstrap/app.php';
  $kernel = $app->make('Illuminate\Contracts\Console\Kernel');
  $kernel->call('migrate', ['--force' => true]);
  echo $kernel->output();
  ```
  Visit `https://yourdomain.com/migrate.php`
  Then **delete migrate.php immediately**.

### 7. Fix storage permissions & symlink
- In cPanel Terminal:
  ```bash
  cd ~/asms
  chmod -R 775 storage bootstrap/cache
  php artisan storage:link
  ```
- If no terminal, create `public_html/setup.php`:
  ```php
  <?php
  require '../asms/vendor/autoload.php';
  $app = require '../asms/bootstrap/app.php';
  $kernel = $app->make('Illuminate\Contracts\Console\Kernel');
  $kernel->call('storage:link');
  echo "Done: " . $kernel->output();
  ```
  Visit it, then **delete it**.

  Then in File Manager set permissions `775` on these folders:
  - `asms/storage/` (recursive)
  - `asms/bootstrap/cache/` (recursive)

### 8. Cache config & routes
- In Terminal:
  ```bash
  cd ~/asms
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  ```

---

## After everything is up

### Check it works
- Visit `https://yourdomain.com` — you should see the login page
- Try logging in
- Check `asms/storage/logs/laravel.log` if anything is broken

### Common fixes

**Blank page / 500 error:**
Set `APP_DEBUG=true` temporarily, refresh, read the error, then set it back to `false`.

**"No application encryption key" error:**
You missed step 5 — generate and set `APP_KEY`.

**Images/uploads not showing:**
Re-run `php artisan storage:link` or manually create a symlink in `public_html/storage` pointing to `asms/storage/app/public`.

**"Class not found" errors:**
```bash
cd ~/asms && composer dump-autoload
```

---

## PHP version requirement

Make sure your hosting uses **PHP 8.0 or higher**.
In cPanel → MultiPHP Manager — set your domain to PHP 8.1.

Also in cPanel → MultiPHP INI Editor, set:
```
max_execution_time = 300
memory_limit = 256M
upload_max_filesize = 20M
post_max_size = 20M
```
(needed for the mass PDF report download)
