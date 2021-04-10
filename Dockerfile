FROM php:8.0-cli

VOLUME /opt/data

RUN apt-get update \
  && apt-get install -y libzip-dev git libkrb5-dev libc-client-dev \
  && docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
  && docker-php-ext-install imap zip \
  && docker-php-ext-enable imap

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /opt
COPY . .

RUN composer install

CMD ["php", "main.php"]
