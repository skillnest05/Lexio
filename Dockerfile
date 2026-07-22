# ─────────────────────────────────────────────
#  Dockerfile for Lexio (PHP + Apache)
#  Works on Railway, Render, and any Docker host
# ─────────────────────────────────────────────

FROM php:8.2-apache

# Install required PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy all project files
COPY . .

# Remove local .env if accidentally copied — Railway injects env vars
# (Safe to leave; if .env exists, config.php reads it anyway)

# Fix permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Apache listens on PORT env var (Railway sets this dynamically)
# We replace the default 80 with $PORT via entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
