FROM php:7.4-fpm

WORKDIR /var/www/html

ADD https://raw.githubusercontent.com/mlocati/docker-php-extension-installer/master/install-php-extensions /usr/local/bin/

RUN apt-get update && apt-get install -y git zip unzip \
    && apt-get install -y libcurl4-openssl-dev pkg-config libssl-dev \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm composer-setup.php \
    && docker-php-ext-install opcache \
    && pecl install mongodb apcu && docker-php-ext-enable mongodb apcu opcache

# RUN chmod uga+x /usr/local/bin/install-php-extensions && sync && \
#    install-php-extensions pdo gd zip exif

# RUN docker-php-ext-install exif
# RUN pecl install mongodb && docker-php-ext-enable mongodb bz2 sodium zip