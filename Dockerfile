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

# 5. Copy configuration files
# We copy the whole packages directory to ensure all configs (like trusted_env.yaml) are present
COPY --chown=www-data:www-data ./config/packages /var/www/html/config/packages

# ---------------------------------------------------------------------
# FINAL SETUP
# ---------------------------------------------------------------------

# Do NOT switch to www-data here. Dockware's entrypoint needs to run as root
# to start services and set up the environment. It will handle permissions itself.