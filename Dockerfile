# Sử dụng ảnh PHP 8.2 chính thức
FROM richarvey/nginx-php-fpm:3.1.6

# Copy toàn bộ code vào trong container
COPY . /var/www/html

# Cấu hình môi trường
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1
ENV APP_ENV production
ENV APP_DEBUG true
ENV LOG_CHANNEL stderr
ENV COMPOSER_ALLOW_SUPERUSER 1

# 👇 THÊM ĐOẠN NÀY: Cài Node.js và NPM
RUN apk add --no-cache nodejs npm

# 👇 THÊM ĐOẠN NÀY: Cài thư viện và Build Assets ngay khi tạo Image
WORKDIR /var/www/html
RUN composer install --no-dev
RUN npm install
RUN npm run build

# Lệnh chạy cuối cùng (Chỉ cần clear cache và start server)
CMD ["/bin/sh", "-c", "php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan migrate --force && /start.sh"]