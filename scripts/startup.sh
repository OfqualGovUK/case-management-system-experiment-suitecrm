#!/bin/sh
FILE1=/var/www/html/public/legacy/Api/V8/OAuth2/private.key
FILE2=/var/www/html/public/legacy/Api/V8/OAuth2/public.key  
ENVFILE=/var/www/html/suiteenv.env
echo "Starting SuiteCRM Bootstrap..." > /dev/stdout 2>&1
if [ -z "$APP_SECRET" ] && [ -n "$SUITECRM_DATABASE_HOST" ]; then
    echo "Starting SuiteCRM Initial Setup..." > /dev/stdout 2>&1
    openssl genrsa -out $FILE1 2048 > /dev/stdout 2>&1
    openssl rsa -in $FILE1 -pubout -out $FILE2 > /dev/stdout 2>&1
    
    cd /var/www/html/
    /usr/local/bin/php /var/www/html/bin/console suitecrm:app:install -W true -u $SUITECRM_USERNAME -p $SUITECRM_PASSWORD -U $SUITECRM_DATABASE_USER -P $SUITECRM_DATABASE_PASSWORD -H $SUITECRM_DATABASE_HOST -N $SUITECRM_DATABASE_NAME -S http://localhost:8080/ > /dev/stdout 2>&1
    chmod 660 $FILE1 $FILE2
    chmod +x bin/console
    
    if [ -z "$DEBUG" ] && [ -f ".env.local" ]; then
             rm .env.local
             touch .env.local
        fi   
    

    if [ -f "/var/www/html/pushsecrets.sh" ]; then
        /var/www/html/pushsecrets.sh > /dev/stdout 2>&1
        if [ -z "$DEBUG" ]; then
            rm /var/www/html/pushsecrets.sh
        fi
    fi    
    echo "SuiteCRM Initial Setup Complete." > /dev/stdout 2>&1
fi
if [ -n "$OAUTH_PRIVKEY" ]; then
    echo "Setting OAuth2 Keys from Environment Variables..." > /dev/stdout 2>&1
    echo "$OAUTH_PRIVKEY" > $FILE1
    echo "$OAUTH_PUBKEY" > $FILE2
    chmod 660 $FILE1 $FILE2
fi 
if [ ! -d "/persisted/uploads" ]; then
    mkdir -p /persisted/uploads
fi
if [ -z "$DEBUG" ]; then
    mv /var/www/html/public/legacy/config-interim.php /var/www/html/public/legacy/config.php
fi

# run the cache:clear twice becuase there is an interdependency issue.
/usr/local/bin/php /var/www/html/bin/console cache:clear
/usr/local/bin/php /var/www/html/bin/console scrm:quick-repair-and-rebuild
/usr/local/bin/php /var/www/html/bin/console cache:clear