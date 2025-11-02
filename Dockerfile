FROM php:8.2-apache

# Install system dependencies and MQTT client
RUN apt-get update && apt-get install -y \
    mosquitto-clients \
    && rm -rf /var/lib/apt/lists/*

# Install mysqli extension
RUN docker-php-ext-install mysqli

# Enable Apache mod_rewrite for clean URLs (optional)
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Create logs and cache directories with proper permissions
RUN mkdir -p /var/www/html/logs /var/www/html/cache && \
    chown -R www-data:www-data /var/www/html/logs /var/www/html/cache && \
    chmod 755 /var/www/html/logs /var/www/html/cache

# Expose port 80
EXPOSE 80
