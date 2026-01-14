###> shopware/core ###
TRUSTED_PROXIES=**



APP_SECRET=b348599f51d293616add811f655d458f
INSTANCE_ID=844d726c515dce9883438a194bb107f9
BLUE_GREEN_DEPLOYMENT=0

###< shopware/core ###

###> symfony/messenger ###
# Choose one of the transports below
# MESSENGER_TRANSPORT_DSN=amqp://guest:guest@localhost:5672/%2f/messages
# MESSENGER_TRANSPORT_DSN=redis://localhost:6379/messages
# doctrine://default?auto_setup=0
###< symfony/messenger ###

###> symfony/mailer ###
# MAILER_DSN=null://null
###< symfony/mailer ###

###> symfony/lock ###
# Choose one of the stores below
# postgresql+advisory://db_user:db_password@localhost/db_name
LOCK_DSN=flock
###< symfony/lock ###

###> shopware/elasticsearch ###
OPENSEARCH_URL=http://localhost:9200
SHOPWARE_ES_ENABLED=0
SHOPWARE_ES_INDEXING_ENABLED=0
SHOPWARE_ES_INDEX_PREFIX=sw
SHOPWARE_ES_THROW_EXCEPTION=1
###< shopware/elasticsearch ###

###> shopware/storefront ###
PROXY_URL=https://localhost
SHOPWARE_HTTP_CACHE_ENABLED=1
SHOPWARE_HTTP_DEFAULT_TTL=7200
APP_ENV=dev
APP_URL=https://guide4youshop.vendorasuite.com
DATABASE_URL=mysql://root:root@127.0.0.1:3306/shopware
MAILER_DSN=smtp://127.0.0.1:1025

# Deployment on Coolify

When deploying to Coolify, ensure you set the following environment variables in your project settings:

*   **APP_URL**: `https://guide4youshop.vendorasuite.com`
*   **APP_ENV**: `prod`
*   **APP_SECRET**: `b348599f51d293616add811f655d458f` (or generate a new one)
*   **DATABASE_URL**: `mysql://root:root@database:3306/shopware` (If using the included database service)
    *   *Note: If you use a managed database provided by Coolify, replace this connection string with the one provided by Coolify.*
*   **INSTANCE_ID**: `844d726c515dce9883438a194bb107f9` (Recommended to keep finding logs/sessions easier)