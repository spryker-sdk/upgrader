ARG SPRYKER_PARENT_IMAGE=spryker/php:8.2

FROM ${SPRYKER_PARENT_IMAGE} AS application-production-dependencies

USER root

RUN apk update \
    && apk add --no-cache \
    curl \
    git \
    rsync

RUN git config --add --system safe.directory /project

########################################
# New Relic Extension
# It's already in the core image.
########################################

RUN curl -L https://download.newrelic.com/php_agent/release/newrelic-php5-10.11.0.3-linux.tar.gz | tar -C /tmp -zx && \
      export NR_INSTALL_USE_CP_NOT_LN=1 && \
      export NR_INSTALL_SILENT=1 && \
      /tmp/newrelic-php5-*/newrelic-install install && \
      rm -rf /tmp/newrelic-php5-* /tmp/nrinstall*

COPY infrastructure/newrelic/newrelic.ini  /usr/local/etc/php/conf.d/newrelic.ini

ARG SPRYKER_COMPOSER_MODE

FROM application-production-dependencies AS application-production-codebase

RUN chown spryker:spryker ${srcRoot}

USER spryker

ENV APP_ENV=sprykerci
ENV NRIA_ENABLE_PROCESS_METRICS=true

COPY --chown=spryker:spryker app ${srcRoot}/app
COPY --chown=spryker:spryker bin ${srcRoot}/bin
COPY --chown=spryker:spryker config ${srcRoot}/config
COPY --chown=spryker:spryker src ${srcRoot}/src
COPY --chown=spryker:spryker .env ${srcRoot}/.env
COPY --chown=spryker:spryker .env.sprykerci ${srcRoot}/.env.prod
COPY --chown=spryker:spryker composer.json phpstan-bootstrap.php ${srcRoot}/
COPY --chown=spryker:spryker infrastructure/newrelic/entrypoint.sh  ${srcRoot}/entrypoint.sh
RUN chmod +x ${srcRoot}/entrypoint.sh

WORKDIR ${srcRoot}

RUN composer install --no-scripts --no-interaction --optimize-autoloader -vvv --no-dev
RUN bin/upgrader cache:clear --no-debug

ENTRYPOINT ["/data/entrypoint.sh"]
