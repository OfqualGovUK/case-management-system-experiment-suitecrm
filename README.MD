#Ofqual

#SuiteCRM Proof of Concept

#Overview

This repository consists of container configuration and additional code for deployment, the Dockerfile is shared between the local development environment and the Ofqual dev/production environment. Env Vars are used throughout for configuration and settings. In this proof of concept SuiteCRM is mainly used via the API, with the interface being used purely for higher priority updates/fixes. All code deploys etc will be undertaken by git/Docker as such the persisted portions of the filesystem are minimal, additionally security has been enhanced beyond the default SuiteCRM, ensuring that all secrets are obtained via Environment Variables rather than directly written into the config files. All logging in SuiteCRM is updated to /dev/stdout this pushes the logs to the Docker Log stream.

#Containers Used

We have used the ServersideUp php-fpm debian/apache container currently at version 8.3 with a mysqlDB at 8.0.44

#Development

Initial development is done via local Docker containers, before being pushed to the Ofqual development pipeline, the docker-compose.yml file and any environment variables in these files are purely for examples or to set-up the local development environment

#Install

SuiteCRM has an install process on first deploy we run this process and capture the generated secrets, we additionally generate the API keys, this process also ensures that the Environment Variables are set in both the Ofqual Environment and the local dev environment ensuring that the install happens once and variables are set consistently.

#System
Startup.sh is executed as a oneshot via S6-Overlay as the Container is started

We additionally have a long running script that executes the SuiteCRM Cron jobs every minute

#Enviroment Variables

#Required
SUITECRM_DATABASE_USER - Set to the username for accessing the database
SUITECRM_DATABASE_PASSWORD - Set to the password for accessing the database
SUITECRM_DATABASE_HOST - Set to the Database host or IP
SUITECRM_DATABASE_NAME - The database name to use
SUITECRM_DATABASE_PORT_NUMBER - The port your database is listening on
SUITECRM_USERNAME - The (admin) username to create on initial install
SUITECRM_PASSWORD - The password to use for the initial user

#Required by container on first install in Ofqual Environment
VAULT_CLIENT_ID - the Container Client id to use to authenticate to Entra
AZURE_SUITECRM_KEYVAULT_NAME - The SuiteCRM Keyvault Name
AZURE_APIM_KEYVAULT_NAME - The APIM Keyvault name

#Optional
DEBUG - set this on a local install to not delete various files.
SCHEDULE_REPEAT_TIME - set the time to wait, in seconds, between scheduled job runs defaults to 60 seconds

#Recommended for Live
PHP_OPCACHE_ENABLE - set to 1 for live to enable PHP OPCACHE and extra speed.

Other Environment variables are created for you on install and put into the file suiteenv.env, this is referenced in docker-compose.yml to allow for consistent variables across deployments, empty this file to force a re-install. A deployment into Ofqual enviroment will also create Env vars for you in the KeyVault, to deploy this container into an environment that is already active, take copies of the following and set into env vars either in suiteenv.env or the Container/Keyvault:

#Container Env Vars
APP_SECRET (available in .env.local after install)
SUITECRM_UNIQUE_KEY (available in config.php after install)
SUITECRM_OAUTH_ENCRYPTION_KEY (available in config.php after install)
OAUTH_PRIVKEY (available in public/legacy/Api/V8/OAuth2/private.key after install)
OAUTH_PUBKEY (available in public/legacy/Api/V8/OAuth2/public.key after install)

#Runtime Environment Variables

#Notifications

These Environment variables provide the authentication for the notification system

notifications-client-id - The client ID
notifications-client-secret - The client secret
notifications-scope - The scope to request
notifications-token-endpoint - The endpoint to call to request a token
notifications_notification_api - The endpoint to call to send a notification
notifications-source-id The notification Source ID
notifications-source-upn - The notification Source UPN
notifications-grant-type - The notification Grant Type

#Local Development Docker-compose.yml

This file is purely for local development, you will need to edit the database parameters in teh database environment section to have sensible values (username/password), additionally once set you need to update the SUITECRM_DATABASE_USERNAME and SUITECRM_DATABASE_PASSWORD values to match. Update the SUITECRM_USER and SUITECRM_PASSWORD variables to a sensible username and password, these will be used to logon to the interface with.

The interface will be available at http://localhost:8080 or https://localhost:8443 it uses an automatic self signed certificate so your browser will throw a warning. Please visit https://serversideup.net/open-source/docker-php/docs/deployment-and-production/configuring-ssl for details on configuring an SSL certificate.