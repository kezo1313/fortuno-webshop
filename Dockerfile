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

# Copy core from build stage
COPY --from=build --chown=82:82 /var/www/html /var/www/html

# Copy local project files (apps, config, etc.)
COPY --chown=82:82 ./apps /var/www/html/custom/apps
COPY --chown=82:82 ./.env /var/www/html/.env
COPY --chown=82:82 ./plugins /var/www/html/custom/plugins
COPY --chown=82:82 ./files /var/www/html/files
COPY --chown=82:82 ./theme /var/www/html/public/theme
COPY --chown=82:82 ./media /var/www/html/public/media
COPY --chown=82:82 ./thumbnail /var/www/html/public/thumbnail
COPY --chown=82:82 ./sitemap /var/www/html/public/sitemap
COPY --chown=82:82 ./config/packages /var/www/html/config/packages

# Ensure final permissions


# Switch to official user 82 (www-data)
USER 82

EXPOSE 8000