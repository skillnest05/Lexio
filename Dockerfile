FROM php:8.2-apache

# Install system dependencies + PHP extensions
RUN apt-get update && apt-get install -y \
        libcurl4-openssl-dev \
        libssl-dev \
    && docker-php-ext-install pdo pdo_mysql mysqli curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ── Step 1: Enable the modules we need ──────────────────
RUN a2enmod rewrite headers

# ── Step 2: MPM cleanup — separate RUN layer AFTER a2enmod ──
# Runs after a2enmod so it cannot be undone by module activation.
RUN rm -f /etc/apache2/mods-enabled/mpm_event.load \
          /etc/apache2/mods-enabled/mpm_event.conf \
          /etc/apache2/mods-enabled/mpm_worker.load \
          /etc/apache2/mods-enabled/mpm_worker.conf

# ── Step 3: Verify — fails the build if event.load still exists ──
RUN if [ -f /etc/apache2/mods-enabled/mpm_event.load ]; then \
        echo "ERROR: mpm_event.load still present after removal!"; \
        exit 1; \
    fi && echo "MPM check passed — only prefork is enabled"

# ── Apache global config ─────────────────────────────────
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf

# ── Copy project files ───────────────────────────────────
COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# ── Startup script ───────────────────────────────────────
RUN cat > /start.sh << 'EOF'
#!/bin/bash
set -e

# Belt-and-suspenders: remove MPM conflicts at RUNTIME too.
# Catches any case where the overlay filesystem re-exposes a symlink
# from the base image layer after the build-time rm.
rm -f /etc/apache2/mods-enabled/mpm_event.load \
      /etc/apache2/mods-enabled/mpm_event.conf \
      /etc/apache2/mods-enabled/mpm_worker.load \
      /etc/apache2/mods-enabled/mpm_worker.conf

# Debug: show exactly which MPM files are present before starting
echo "[Lexio] Active MPM files:"
ls /etc/apache2/mods-enabled/mpm_* 2>/dev/null && true

APP_PORT="${PORT:-80}"
echo "[Lexio] Starting Apache on port $APP_PORT"

sed -i "s|Listen 80|Listen ${APP_PORT}|g" /etc/apache2/ports.conf
sed -i "s|<VirtualHost \*:80>|<VirtualHost *:${APP_PORT}>|g" \
    /etc/apache2/sites-enabled/000-default.conf

exec apache2-foreground
EOF

RUN chmod +x /start.sh

EXPOSE 80

CMD ["bash", "/start.sh"]
