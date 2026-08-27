# syntax=docker/dockerfile:1

# The vagrant box ran php8.5 from ppa:ondrej/php. Override if that tag is not
# published for your platform: docker compose build --build-arg PHP_VERSION=8.4
ARG PHP_VERSION=8.5
ARG PHP_BASE=php:${PHP_VERSION}-fpm-bookworm

# ---------------------------------------------------------------------------
# Stage 1: the C++ event handler
#
# Built on the same base as the runtime stage on purpose - a binary compiled
# against a different debian release will not necessarily load there.
#
# It is installed to /usr/local/bin instead of eventhandler/target, because that
# path is covered by the bind mount of the project and the copy would be hidden.
# The entrypoint points the daemon_exe config row at the new location, which
# EventHandlerManager::getExecutable() already honours.
# ---------------------------------------------------------------------------
FROM ${PHP_BASE} AS eventhandler-builder

RUN apt-get update && apt-get install -y --no-install-recommends \
        cmake \
        g++ \
        make \
        libboost-thread-dev \
        libboost-regex-dev \
        libboost-filesystem-dev \
        libboost-system-dev \
        libmysql++-dev \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /src
COPY eventhandler/ ./eventhandler/
RUN cmake -S eventhandler -B build \
    && cmake --build build -j "$(nproc)" \
    && test -x eventhandler/target/etoad

# ---------------------------------------------------------------------------
# Stage 2: php-fpm
# ---------------------------------------------------------------------------
FROM ${PHP_BASE}

COPY --from=mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/

# gd, intl, zip and pdo_mysql come from composer.json and provision.sh; ctype,
# iconv, simplexml, xmlreader and mbstring are already part of the base image
RUN install-php-extensions \
        gd \
        intl \
        zip \
        pdo_mysql \
        opcache \
        redis \
        xdebug

# The runtime libraries of the event handler binary. The -dev packages are used
# rather than the versioned runtime ones (libboost-thread1.74.0 and friends) so
# that this does not have to be touched when the base image moves to a new debian
# release. Costs about 100 MB in a development image.
RUN apt-get update && apt-get install -y --no-install-recommends \
        libboost-thread-dev \
        libboost-regex-dev \
        libboost-filesystem-dev \
        libboost-system-dev \
        libmysql++-dev \
        procps \
        default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

COPY --from=eventhandler-builder /src/eventhandler/target/etoad /usr/local/bin/etoad
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

COPY docker/php/conf.d/ /usr/local/etc/php/conf.d/
COPY docker/php/entrypoint.sh /usr/local/bin/etoa-entrypoint
RUN chmod +x /usr/local/bin/etoa-entrypoint

WORKDIR /app

ENTRYPOINT ["/usr/local/bin/etoa-entrypoint"]
CMD ["php-fpm"]
