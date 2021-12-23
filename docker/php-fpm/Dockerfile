FROM php:8.1-fpm-alpine

RUN apk --update --no-cache add bash \
    && apk add wget \
    && apk add git \
    && apk add php8-phar \
    && apk add php8-openssl \
    && apk add php8-xml \
    && apk add php8-pdo \
    && apk add php8-pdo_mysql \
    && apk add php8-mbstring \
    && apk add php8-iconv \
    && apk add php8-ctype \
    && apk add php8-posix \
    && apk add php8-intl \
    && apk add php8-opcache \
    && apk add php8-bcmath \
    && apk add php8-gmp \
    && apk add gmp gmp-dev \
    && apk add icu-dev \
    && apk add alpine-conf \
    && apk add $PHPIZE_DEPS \
    && pecl install apcu \
    && pecl install xdebug

RUN setup-timezone -z Europe/Rome

RUN docker-php-ext-install pdo_mysql \
    && docker-php-ext-install intl \
    && docker-php-ext-configure intl \
    && docker-php-ext-enable intl \
    && docker-php-ext-install opcache \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install gmp \
    && docker-php-ext-install opcache \
    && docker-php-ext-enable apcu

RUN mkdir -p /var/app
RUN adduser --disabled-password app && chown -R app:app /var/app

# Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Symfony installer
RUN wget https://get.symfony.com/cli/installer -O - | bash
RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

COPY php-overrides.ini /usr/local/etc/php/conf.d/
COPY timezone.ini /usr/local/etc/php/conf.d/
COPY opcache.ini /usr/local/etc/php/conf.d/
COPY xdebug.ini /usr/local/etc/php/conf.d/

ARG XDEBUG_ENABLED
RUN if [ "${XDEBUG_ENABLED}" = "false" ]; then mv /usr/local/etc/php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini.off ; fi

USER app
WORKDIR /var/app

EXPOSE 9000

RUN echo "alias ll='ls -l'" >> ~/.bashrc
RUN echo "alias la='ls -la'" >> ~/.bashrc
