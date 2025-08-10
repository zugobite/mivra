#!/usr/bin/env bash
set -euo pipefail

APP_NAME="mivra"
REMOTE_DIR="/var/www/${APP_NAME}"

if [ "$(id -u)" -ne 0 ]; then
  echo "Please run as root (sudo)." >&2
  exit 1
fi

mkdir -p "${REMOTE_DIR}"
rsync -a --delete --exclude '.git' --exclude '__MACOSX' --exclude 'deploy/*.sh' --exclude 'deploy/*.conf' --exclude '*.zip' ./ "${REMOTE_DIR}/"

chown -R www-data:www-data "${REMOTE_DIR}"
find "${REMOTE_DIR}" -type f -exec chmod 0644 {} \;
find "${REMOTE_DIR}" -type d -exec chmod 0755 {} \;

# Ensure storage/logs exists if used
mkdir -p "${REMOTE_DIR}/storage/logs"
chown -R www-data:www-data "${REMOTE_DIR}/storage"

systemctl reload nginx
PHPV=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')
systemctl reload php${PHPV}-fpm || systemctl restart php${PHPV}-fpm

echo "Deploy complete."