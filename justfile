dockerComposeTest := "docker compose -f docker-compose.phpunit.yml"

test:
    {{dockerComposeTest}} run php-fpm /var/www/vendor/bin/phpunit -c /var/www/phpunit.xml

lint:
    {{dockerComposeTest}} run php-fpm /var/www/vendor/bin/phpmd /var/www/src text /var/www/phpmd.xml
    {{dockerComposeTest}} run php-fpm /var/www/vendor/bin/phpcs --standard=/var/www/phpcs.xml /var/www/src

build:
    {{dockerComposeTest}} build