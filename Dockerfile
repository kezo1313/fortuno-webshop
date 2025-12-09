FROM dockware/shopware:6.7.3.1
# 2. Switch to root to perform copy operations (if needed)
USER root

# 3. Copy your custom APPS from Git into the container
# Use --chown to ensure the web server (www-data) can read/write them
COPY --chown=www-data:www-data ./apps /var/www/html/custom/apps

# 4. Copy theme configuration if you have it in git
# COPY --chown=www-data:www-data ./theme /var/www/html/public/theme

# 5. Switch back to the standard user (usually www-data or 1000)
# Check your specific base image documentation, but usually:
USER www-data
