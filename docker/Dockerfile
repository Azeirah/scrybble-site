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

RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.1/install.sh | bash \
    && chmod +x ~/.nvm/nvm.sh \
    && bash ~/.nvm/nvm.sh \
    && export NVM_DIR="$HOME/.nvm" \
    && [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"  # This loads nvm \
    && [ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion"  # This loads nvm bash_completion \
    && nvm install 16.14.2 \
    && nvm use 16.14.2

CMD ["bash"]
