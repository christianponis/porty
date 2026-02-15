FROM php:8.3-fpm

# Installa dipendenze di sistema
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev \
    libzip-dev nginx supervisor && \
    docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Installa Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Installa Node.js 20 LTS
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs

WORKDIR /var/www/html

# Copia file applicazione
COPY . .

# Installa dipendenze PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Installa dipendenze Node e build assets
RUN npm install && npm run build

# Permessi storage e cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copia config Nginx
COPY docker/nginx.conf /etc/nginx/sites-available/default

# Copia config Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
