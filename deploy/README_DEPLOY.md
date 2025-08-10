# Mivra: DigitalOcean Droplet Hosting Guide

This app is designed to run behind **Nginx + PHP-FPM** on Ubuntu 22.04/24.04.

## 1) Prepare the Droplet
```bash
ssh root@YOUR_IP
apt-get update && apt-get upgrade -y
```

## 2) Install stack and configure
Copy the project to the droplet (via `git clone` or `scp`), then run:

```bash
cd /var/www && git clone https://github.com/YOU/mivra.git
cd mivra
sudo ./deploy/setup_ubuntu.sh
```

Edit `deploy/nginx.mivra.conf` and set `server_name` to your domain or IP, then:

```bash
sudo nginx -t && sudo systemctl reload nginx
```

## 3) Environment
Create an `.env` file in the project root based on `.env.example`:

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your.domain
DB_HOST=127.0.0.1
DB_NAME=mivra
DB_USER=mivra_user
DB_PASS=secure_password
DB_PORT=3306
```

## 4) Database
Create DB and user:
```sql
CREATE DATABASE mivra CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'mivra_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON mivra.* TO 'mivra_user'@'localhost';
FLUSH PRIVILEGES;
```

## 5) SSL (Let's Encrypt)
```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d your.domain
```

## 6) Directory Permissions
Ensure web server can read the app and write to storage if used:
```bash
sudo chown -R www-data:www-data /var/www/mivra
sudo find /var/www/mivra -type f -exec chmod 0644 {} \;
sudo find /var/www/mivra -type d -exec chmod 0755 {} \;
```

## 7) Zero-downtime deploys (optional)
Use the provided `deploy_local_to_server.sh` to rsync changes from a build machine (run on the droplet after uploading code).

---

### Nginx quick check
- `root` points to `/var/www/mivra/public`
- `try_files` sends unknown routes to `index.php`
- PHP handled by `php8.3-fpm` socket (adjust if your version differs)

### Apache fallback
A `.htaccess` is included in `public/` for Apache with mod_rewrite. For Nginx you **do not** use `.htaccess`.

### Hardening tips
- Keep `APP_DEBUG=false` in production.
- Deny access to hidden files if serving via Apache.
- Ensure `public/` is the only web root; the rest should not be web-accessible.
- Set up automatic security updates: `sudo dpkg-reconfigure -plow unattended-upgrades`.
- Monitor logs: `/var/log/nginx/*.log` and `/var/log/php*.log`.