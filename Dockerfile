FROM php:5.6-cli

RUN apt-get update && apt-get install -y php-soap zlib1g-dev &&\
 docker-php-ext-install zip &&\
 curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer &&\
 composer global require "hirak/prestissimo:^0.3"

WORKDIR /app
