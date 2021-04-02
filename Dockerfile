FROM php:8.0-cli

WORKDIR /opt

RUN apt-get update \
  && apt-get install -y libkrb5-dev libc-client-dev \
  && docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
  && docker-php-ext-install imap \
  && docker-php-ext-enable imap
