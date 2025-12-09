# 1. Use your preferred Dockware image
FROM dockware/shopware:6.7.3.1


# 2. Switch to root to ensure we have permission to write anywhere
USER root

# ---------------------------------------------------------------------
# COPY APPLICATION CODE
# ---------------------------------------------------------------------

# 3. Copy Custom Apps
COPY --chown=www-data:www-data ./apps /var/www/html/custom/apps

# 4. Copy the .env file
# Warning: Ensure your .env does not contain secrets if you push this image publicly!
COPY --chown=www-data:www-data ./.env /var/www/html/.env

# 5. Copy framework.yaml
# We copy it specifically to the target location
COPY --chown=www-data:www-data ./config/packages/framework.yaml /var/www/html/config/packages/framework.yaml

# ---------------------------------------------------------------------
# FINAL SETUP
# ---------------------------------------------------------------------

# 6. Switch back to www-data so the container runs safely
USER www-data