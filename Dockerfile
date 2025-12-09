# Sử dụng ảnh PHP 8.2 chính thức
FROM richarvey/nginx-php-fpm:3.1.6

# Copy toàn bộ code vào trong container
COPY . /var/www/html

# Cấu hình môi trường cho Image này
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Cấu hình Laravel
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Cho phép Composer chạy dưới quyền root
ENV COMPOSER_ALLOW_SUPERUSER 1

# Cài đặt các thư viện (Chạy mỗi khi deploy)
CMD ["/bin/sh", "-c", "composer install --no-dev --working-dir=/var/www/html && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan migrate --force && /start.sh"]