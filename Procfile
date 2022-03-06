release: php artisan migrate --force && php artisan optimize:clear && php artisan optimize
web: vendor/bin/heroku-php-apache2 public/
worker: php artisan queue:work
