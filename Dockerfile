FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        mysqli \
        gd \
        intl \
        zip \
        opcache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy project files to the container
COPY . /var/www/html

# Ensure the web server has full access to the project
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/writable \
    && chmod -R 775 /var/www/html/files \
    && chmod +x /var/www/html/docker-entrypoint.sh

# Expose port 80 for Render / web traffic
EXPOSE 80

# Set our custom entrypoint script
ENTRYPOINT ["/var/www/html/docker-entrypoint.sh"]

# Start Apache in the foreground
CMD ["apache2-foreground"]
