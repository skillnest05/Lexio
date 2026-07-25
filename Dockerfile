FROM php:8.2-apache

# Install system dependencies + PHP extensions
RUN apt-get update && apt-get install -y \
        libcurl4-openssl-dev \
        libssl-dev \
    && docker-php-ext-install pdo pdo_mysql mysqli curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ── Force ONLY mpm_prefork ───────────────────────────────
# Wipe every mpm_* symlink then re-link only prefork.
# mod_php is not thread-safe → requires prefork, not event/worker.
RUN rm -f /etc/apache2/mods-enabled/mpm_event.load \
          /etc/apache2/mods-enabled/mpm_event.conf \
          /etc/apache2/mods-enabled/mpm_worker.load \
          /etc/apache2/mods-enabled/mpm_worker.conf \
          /etc/apache2/mods-enabled/mpm_prefork.load \
          /etc/apache2/mods-enabled/mpm_prefork.conf \
    && ln -sf /etc/apache2/mods-available/mpm_prefork.load \
              /etc/apache2/mods-enabled/mpm_prefork.load \
    && ln -sf /etc/apache2/mods-available/mpm_prefork.conf \
              /etc/apache2/mods-enabled/mpm_prefork.conf \
    && a2enmod rewrite headers

# ── Apache global config ─────────────────────────────────
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf

# ── Copy project files ───────────────────────────────────
COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# ── Startup script ───────────────────────────────────────
# Railway injects $PORT at runtime. Use | as sed delimiter to avoid
# escaping the * in <VirtualHost *:80> — the primary cause of PORT
# binding failures with the previous s/.../ form.
RUN cat > /start.sh << 'EOF'
#!/bin/bash
set -e

APP_PORT="${PORT:-80}"
echo "[Lexio] Starting Apache on port $APP_PORT"

# Swap the listen port in ports.conf
sed -i "s|Listen 80|Listen ${APP_PORT}|g" /etc/apache2/ports.conf

# Swap the VirtualHost port (| delimiter safely handles the * character)
sed -i "s|<VirtualHost \*:80>|<VirtualHost *:${APP_PORT}>|g" \
    /etc/apache2/sites-enabled/000-default.conf

exec apache2-foreground
EOF

RUN chmod +x /start.sh

EXPOSE 80

CMD ["bash", "/start.sh"]
