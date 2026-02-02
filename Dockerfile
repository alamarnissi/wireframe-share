FROM php:8.2-apache

RUN a2enmod rewrite headers

# Install build tools + sqlite headers, then compile pdo_sqlite
RUN apt-get update && apt-get install -y --no-install-recommends \
    $PHPIZE_DEPS \
    libsqlite3-dev \
  && docker-php-ext-install pdo_sqlite \
  && rm -rf /var/lib/apt/lists/*

COPY . /var/www/html

RUN mkdir -p /var/www/html/uploads /var/www/html/data \
  && chown -R www-data:www-data /var/www/html/uploads /var/www/html/data \
  && chmod -R 775 /var/www/html/uploads /var/www/html/data