FROM php:8.2-apache

# Install PostgreSQL extensions
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pgsql pdo_pgsql

WORKDIR /var/www/html
COPY ./src /var/www/html
# Set proper permissions for web root
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80