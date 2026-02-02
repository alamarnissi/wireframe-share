FROM php:8.2-apache

# Enable useful Apache modules (optional but nice)
RUN a2enmod rewrite headers

# PDO SQLite + fileinfo extensions
RUN docker-php-ext-install pdo pdo_sqlite \
  && docker-php-ext-enable pdo_sqlite \
  && docker-php-ext-install fileinfo

# Copy app into Apache web root
COPY . /var/www/html

# Create writable dirs and set permissions
RUN mkdir -p /var/www/html/uploads /var/www/html/data \
  && chown -R www-data:www-data /var/www/html/uploads /var/www/html/data \
  && chmod -R 775 /var/www/html/uploads /var/www/html/data

EXPOSE 80
