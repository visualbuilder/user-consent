#!/bin/bash
#For developer to recopy from source
php artisan vendor:publish --tag=filament-user-consent-config --force
php artisan vendor:publish --tag=filament-user-consent-migrations --force
php artisan vendor:publish --tag=filament-user-consent-translations --force
