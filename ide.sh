#!/usr/bin/env bash
composer dumpautoload --optimize

php artisan config:clear
php artisan clear-compiled
php artisan ide-helper:generate
php artisan ide-helper:meta
php artisan ide-helper:models -R -W
php artisan queue:restart
php artisan view:clear
php artisan cache:clear
composer format
