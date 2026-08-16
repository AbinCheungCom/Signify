#!/bin/sh
set -e

# 等待数据库就绪（DB_WAIT=0 可跳过）
if [ "${DB_WAIT:-1}" != "0" ]; then
  echo "waiting for database ${DB_HOST:-db}:${DB_PORT:-3306} ..."
  i=0
  until php -r "new PDO('mysql:host='.getenv('DB_HOST').';port='.(getenv('DB_PORT') ?: 3306), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));" >/dev/null 2>&1; do
    i=$((i+1))
    if [ "$i" -ge 30 ]; then
      echo "database not reachable, giving up" >&2
      exit 1
    fi
    sleep 2
  done
fi

# 首次启动初始化：.env / APP_KEY / 迁移 / 存储链接
MARKER=/var/www/storage/app/.docker-init
if [ ! -f "$MARKER" ]; then
  [ -f /var/www/.env ] || cp /var/www/.env.example /var/www/.env
  php artisan key:generate --force --ansi
  php artisan migrate --force
  php artisan storage:link || true
  touch "$MARKER"
fi

exec php-fpm
