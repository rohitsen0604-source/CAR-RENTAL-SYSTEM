FROM php:8.2-apache

# Install MySQL extensions for PHP
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Configure PHP settings (upload limit, memory)
RUN echo "upload_max_filesize = 32M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size = 40M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/uploads.ini

# Support dynamic PORT injection from Render (defaults to 80 if PORT is not set)
ENV PORT=80
RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# Set document root and copy application files
WORKDIR /var/www/html
COPY . /var/www/html/

# Set ownership and permissions for web uploads
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    mkdir -p /var/www/html/admin/img/vehicleimages && \
    chmod -R 777 /var/www/html/admin/img/vehicleimages

EXPOSE 80 10000

CMD ["apache2-foreground"]
