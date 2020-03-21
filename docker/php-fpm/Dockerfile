FROM php:7.4-fpm-alpine

RUN apk --update --no-cache add bash \
   && apk add wget \
   && apk add git \
   && apk add php7 \
   && apk add php7-json \
   && apk add php7-phar \
   && apk add php7-mbstring \
   && apk add php7-iconv \
   && apk add php7-ctype \
   && apk add php7-curl \
   && apk add php7-openssl \
   && apk add php7-xml \
   && apk add php7-simplexml \
   && apk add php7-dom \
   && apk add php7-session \
   && apk add php7-xmlwriter \
   && apk add php7-tokenizer \
   && apk add php7-pdo \
   && apk add php7-pdo_mysql \
   && apk add php7-posix \
   && apk add php7-intl \
   && apk add php7-opcache  \
   && apk add make \
   && apk add $PHPIZE_DEPS \
   && pecl install xdebug

ADD xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-enable xdebug

RUN mkdir -p /var/app
RUN adduser --disabled-password app && chown -R app:app /var/app

# Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Symfony installer
RUN wget https://get.symfony.com/cli/installer -O - | bash
RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

USER app
WORKDIR /var/app

EXPOSE 9000

RUN echo "alias ll='ls -l'" >> ~/.bashrc
RUN echo "alias la='ls -la'" >> ~/.bashrc
