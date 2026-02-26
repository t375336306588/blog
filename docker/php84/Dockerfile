FROM php:8.4-fpm

ENV TZ=Europe/Minsk

RUN apt update && \
    apt -y --no-install-recommends install libonig-dev libxml2-dev zlib1g-dev libzip-dev libcurl4-openssl-dev && \
    docker-php-ext-install mysqli bcmath mbstring xml ctype zip pdo_mysql curl filter

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    mv composer.phar /usr/local/bin/composer