#!/bin/bash

# Exit on error
set -e

echo "Starting Shopware Startup Script..."

# Compile Storefront (now that DB is available)
if [ -f "./bin/build-storefront.sh" ]; then
    echo "Compiling Storefront/Theme..."
    export CI=1
    export PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true
    ./bin/build-storefront.sh
else
    echo "Warning: ./bin/build-storefront.sh not found."
fi

# Start the actual application (Supervisord)
echo "Starting Supervisord..."
exec /usr/bin/supervisord -c /etc/supervisord.conf
