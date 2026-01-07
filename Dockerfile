# 1. Use your preferred Dockware image
FROM dockware/shopware:6.7.3.1


# 2. Switch to root to ensure we have permission to write anywhere
USER root

# ---------------------------------------------------------------------
# EXTRACT SHOPWARE (Build-time)
# ---------------------------------------------------------------------

# We extract the core files now and delete the archive so Dockware skips runtime decompression.
RUN tar -I zstd -xf /var/www/html/shopware.tar.zst -C /var/www/html && \
    rm /var/www/html/shopware.tar.zst && \
    chown -R www-data:www-data /var/www/html

# ---------------------------------------------------------------------
# COPY APPLICATION CODE & CONFIG
# ---------------------------------------------------------------------

# 3. Copy Custom Apps
COPY --chown=www-data:www-data ./apps /var/www/html/custom/apps

# 4. Copy the .env file
COPY --chown=www-data:www-data ./.env /var/www/html/.env

# 5. Copy configuration files
COPY --chown=www-data:www-data ./config/packages /var/www/html/config/packages

# ---------------------------------------------------------------------
# FINAL SETUP
# ---------------------------------------------------------------------

# Do NOT switch to www-data here. Dockware's entrypoint needs to run as root.
EXPOSE 80 443