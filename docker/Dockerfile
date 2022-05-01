FROM php:8.0-cli

RUN apt-get update && apt-get install -y \
  --no-install-recommends git zip unzip \
  zlib1g-dev libzip-dev

RUN docker-php-ext-install zip

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /tmp

COPY install-composer.sh /tmp/install-composer.sh
RUN chmod +x /tmp/install-composer.sh
RUN /tmp/install-composer.sh
RUN rm /tmp/install-composer.sh

RUN curl -sL https://deb.nodesource.com/setup_14.x | bash -
RUN apt-get install -y \
  --no-install-recommends nodejs \
  && rm -rf /var/lib/apt/lists/*

CMD ["bash"]
