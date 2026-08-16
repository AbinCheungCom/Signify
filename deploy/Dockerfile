# Signify 运行镜像（PHP-FPM）
# 基础镜像已自带 fileinfo；此处补齐 gd(带 freetype/jpeg)、pdo_mysql、mbstring、zip、opcache
FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
        freetype-dev libjpeg-turbo-dev libpng-dev oniguruma-dev libzip-dev icu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" gd pdo_mysql mbstring zip opcache

# composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# 先拷依赖清单以利用镜像层缓存
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# 再拷代码（public/ 已包含构建好的 css/js，无需 node 阶段）
COPY . .

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY deploy/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 9000
ENTRYPOINT ["docker-entrypoint.sh"]
