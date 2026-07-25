FROM php:8.2-apache

# Install system dependencies + PHP extensions
RUN apt-get update && apt-get install -y \
        libcurl4-openssl-dev \
        libssl-dev \
    && docker-php-ext-install pdo pdo_mysql mysqli curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ── Fix MPM conflict ─────────────────────────────────────
# php:8.2-apache requires mpm_prefork (mod_php is not thread-safe).
# mpm_event gets auto-loaded on some builds — disable it explicitly.
RUN a2dismod mpm_event mpm_worker 2>/dev/null || true \
    && a2enmod mpm_prefork rewrite headers

# Copy project files into the web root
COPY . /var/www/html/

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Allow .htaccess overrides globally
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Railway assigns a dynamic PORT — Apache must listen on it
RUN printf '#!/bin/bash\n\
PORT=${PORT:-80}\n\
sed -i "s/Listen 80/Listen $PORT/" /etc/apache2/ports.conf\n\
sed -i "s/<VirtualHost \\*:80>/<VirtualHost *:$PORT>/" /etc/apache2/sites-enabled/000-default.conf\n\
exec apache2-foreground\n' > /start.sh \
    && chmod +x /start.sh

EXPOSE 80

CMD ["bash", "/start.sh"]
