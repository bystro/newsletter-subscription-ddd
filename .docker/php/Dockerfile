FROM php:7-fpm
MAINTAINER Krzysztof Kubacki
LABEL maintainer="krzysztof.kubacki@bystro.biz.pl"

RUN apt-get --assume-yes update && apt-get --assume-yes install apt-utils openssh-server curl zip unzip git
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN apt-get autoremove && apt-get clean

RUN printf '[PHP]\ndate.timezone = "Europe/Warsaw"\n' > /usr/local/etc/php/conf.d/tzone.ini
RUN printf '[PHP]\nlog_errors = On\n' > /usr/local/etc/php/conf.d/log_errors.ini
RUN printf '[PHP]\ndisplay_errors = On\n' > /usr/local/etc/php/conf.d/display_errors.ini

RUN curl -sS https://getcomposer.org/installer | php \
  	&& chmod +x composer.phar && mv composer.phar /usr/local/bin/composer
RUN composer global require hirak/prestissimo

RUN mkdir -p /var/www/html/app/data/subscription
RUN chmod g+w /var/www/html/app/data/subscription
RUN chown root:www-data /var/www/html/app/data/subscription
