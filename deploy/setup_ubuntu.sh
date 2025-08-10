#!/usr/bin/env bash
set -euo pipefail

APP_NAME="mivra"
APP_DIR="/var/www/${APP_NAME}"
PHP_VERSION="$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')"

sudo apt-get update
sudo apt-get install -y nginx software-properties-common curl git unzip ufw

# Install PHP-FPM and extensions
sudo apt-get install -y php-fpm php-mysql php-xml php-mbstring php-curl php-zip

# Create app dir
sudo mkdir -p "${APP_DIR}"
sudo chown -R $USER:www-data "${APP_DIR}"
sudo chmod -R 775 "${APP_DIR}"

# Copy code (expects repo archive or rsync from local)
# rsync -a --delete ./ "${APP_DIR}/"

# Nginx
sudo cp deploy/nginx.mivra.conf /etc/nginx/sites-available/${APP_NAME}.conf
sudo ln -sf /etc/nginx/sites-available/${APP_NAME}.conf /etc/nginx/sites-enabled/${APP_NAME}.conf
sudo rm -f /etc/nginx/sites-enabled/default

# PHP ini override
if [ -f "deploy/99-mivra.ini" ]; then
  sudo cp deploy/99-mivra.ini /etc/php/${PHP_VERSION}/fpm/conf.d/99-mivra.ini
fi

# Firewall
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full' || true
echo "y" | sudo ufw enable || true

# Reload services
sudo systemctl reload php${PHP_VERSION}-fpm || sudo systemctl restart php${PHP_VERSION}-fpm
sudo nginx -t
sudo systemctl reload nginx

echo "Done. Point DNS to this droplet and set up SSL: sudo apt install -y certbot python3-certbot-nginx && sudo certbot --nginx -d your.domain"