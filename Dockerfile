# Stage 1: Build Stage
FROM ghcr.io/shopware/shopware-cli:latest-php-8.3 AS build

WORKDIR /var/www/html

# Create the project with the latest stable production template
RUN composer create-project shopware/production . --no-interaction --no-scripts --no-install && \
    composer config -g allow-plugins true && \
    composer require shopware/docker --no-update && \
    composer install --no-interaction --no-scripts --no-progress --no-dev

# Stage 2: Final Production Stage
FROM ghcr.io/shopware/docker-base:8.3-nginx

# Standard Shopware Docker user is 82 (www-data)
USER root

WORKDIR /var/www/html

# Install Bash (requested by user) and Node.js/NPM (required for theme compilation)
# We also upgrade npm to latest to fix the "ruleset" bug present in some Alpine versions
RUN apk add --no-cache bash nodejs npm && npm install -g npm@latest

# Copy core from build stage
COPY --from=build --chown=82:82 /var/www/html /var/www/html

# Copy local project files (apps, config, etc.)
COPY --chown=82:82 ./apps /var/www/html/custom/apps
COPY --chown=82:82 ./.env /var/www/html/.env
COPY --chown=82:82 ./plugins /var/www/html/custom/plugins
COPY --chown=82:82 ./config/packages /var/www/html/config/packages
COPY --chown=82:82 ./sw-domain-hash.html /var/www/html/public/sw-domain-hash.html

# Ensure final permissions


# Install assets during build to prevent 404s
RUN php bin/console assets:install



# Fix permissions for cache and public folders (since asset install ran as root)
# Fix permissions:
# 1. Delete build-time cache (it's owned by root from assets:install, and we don't need it)
RUN rm -rf /var/www/html/var/cache/*

# 2. Create required folders
RUN mkdir -p /var/www/html/public/media \
    /var/www/html/public/thumbnail \
    /var/www/html/public/theme \
    /var/www/html/public/sitemap \
    /var/www/html/files

# 3. Set ownership for public, files, AND var (so cache can be created)
RUN chown -R 82:82 /var/www/html/public /var/www/html/files /var/www/html/var

# Switch to official user 82 (www-data)
USER 82

# Copy and setup startup script
COPY --chown=82:82 start.sh /var/www/html/start.sh
RUN chmod +x /var/www/html/start.sh

EXPOSE 8000

ENTRYPOINT ["/var/www/html/start.sh"]
