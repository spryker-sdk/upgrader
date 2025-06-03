ARG SPRYKER_PARENT_IMAGE

FROM ${SPRYKER_PARENT_IMAGE} AS application-production-dependencies

USER root
RUN apk update \
    && apk add --no-cache \
    curl \
    bash \
    git \
    rsync

RUN docker-php-ext-enable xdebug

COPY --chown=spryker:spryker composer.json composer.lock ${srcRoot}/

RUN --mount=type=cache,id=composer,sharing=locked,target=/home/spryker/.composer/cache,uid=1000 \
  --mount=type=ssh,uid=1000 --mount=type=secret,id=secrets-env,uid=1000 \
    composer clear-cache && composer install --no-cache --no-scripts --no-interaction --dev -vvv

FROM application-production-dependencies AS application-production-codebase

RUN chown spryker:spryker ${srcRoot}

COPY --chown=spryker:spryker phpstan-bootstrap.php ${srcRoot}/phpstan-bootstrap.php
COPY --chown=spryker:spryker app ${srcRoot}/app
COPY --chown=spryker:spryker bin ${srcRoot}/bin
COPY --chown=spryker:spryker src ${srcRoot}/src
COPY --chown=spryker:spryker config ${srcRoot}/config
COPY --chown=spryker:spryker tests ${srcRoot}/tests
COPY --chown=spryker:spryker infrastructure ${srcRoot}/infrastructure

COPY --chown=spryker:spryker infrastructure/context/php/91-opcache-dev.ini /usr/local/etc/php/conf.d

RUN --mount=type=cache,id=composer,sharing=locked,target=/home/spryker/.composer/cache,uid=1000 \
  composer dump-autoload -o

ENV APP_ENV=dev
