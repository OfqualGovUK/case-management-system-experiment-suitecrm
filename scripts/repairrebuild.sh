#!/bin/sh

/var/www/html/bin/console scrm:quick-repair-and-rebuild
cp /var/www/html/public/legacy/config-interim.php /var/www/html/public/legacy/config.php