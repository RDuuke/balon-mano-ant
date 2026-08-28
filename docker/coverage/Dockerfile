FROM wordpress:cli-2.11.0-php8.3@sha256:8620869ecf48e0eda305b4ee466990fa318664c638b861ebd5ce443d9904abde

USER root

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS linux-headers \
    && pecl install pcov-1.0.12 \
    && docker-php-ext-enable pcov \
    && apk del .build-deps \
    && rm -rf /tmp/pear

