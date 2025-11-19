#!/bin/bash

FILE1=/bitnami/suitecrm/public/legacy/Api/V8/OAuth2/private.key     
GENPRIV="FALSE"
if [ ! -f $FILE1 ]; then
  openssl genrsa -out $FILE1 2048
  echo "Generating Private Key"
  GENPRIV="TRUE"
fi

# generate the public key if it doesnt exist OR if the private key has just been generated.
FILE2=/bitnami/suitecrm/public/legacy/Api/V8/OAuth2/public.key
if [[ ! -f $FILE2 || $GENPRIV == "TRUE" ]] ; then
  openssl rsa -in $FILE1 -pubout -out $FILE2
  echo "Generating Public Key"
fi

if [ !  $(stat -c %a $FILE1) != 660 ]; then
    chmod 660 $FILE1 $FILE2
    echo "Resetting Key Permissions"
fi

cd /opt/bitnami/suitecrm

php bin/console cache:clear
php bin/console scrm:quick-repair-and-rebuild

# reset permissions again as we are root at this stage

find . -type d -not -perm 2755 -exec chmod 2755 {} \;
find . -type f -not -perm 0644 -exec chmod 0644 {} \;
find . ! -user daemon -exec chown daemon:daemon {} \;
chmod +x bin/console

