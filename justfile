dockerComposeTest := "docker compose -f docker-compose.phpcli.yml"

test:
    {{ dockerComposeTest }} build
    {{ dockerComposeTest }} run php-cli /var/www/vendor/bin/phpunit -c /var/www/phpunit.xml

lint:
    {{ dockerComposeTest }} build
    {{ dockerComposeTest }} run php-cli /var/www/vendor/bin/phpmd /var/www/src text /var/www/phpmd.xml
    {{ dockerComposeTest }} run php-cli /var/www/vendor/bin/phpcs --standard=/var/www/phpcs.xml /var/www/src

build:
    {{ dockerComposeTest }} build