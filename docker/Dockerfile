# Use build argument for dynamic PHP version
ARG PHP_VERSION=8.4
FROM php:${PHP_VERSION}-cli-alpine

# Set working directory
WORKDIR /app

RUN apk add --no-cache \
    bash \
    nano \
    less \
    curl \
    linux-headers \
    oniguruma-dev \
    libxml2-dev \
    icu-dev \
    $PHPIZE_DEPS \
    && rm -rf /var/cache/apk/*

# Install PHP extensions commonly needed for development
RUN docker-php-ext-install \
        bcmath \
        exif \
        intl \
        mbstring

# Install and configure Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Install Composer (latest version)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


ARG USER_ID=1000
ARG GROUP_ID=1000

# Create a non-root user for security
RUN addgroup -g $GROUP_ID -S developer && \
    adduser -u $USER_ID -S developer -G developer -h /home/developer

RUN chown -R $USER_ID:$GROUP_ID /home/developer /app

ENV COMPOSER_HOME=/home/developer/.composer
ENV COMPOSER_CACHE_DIR=/home/developer/.cache/composer
ENV PATH="/home/developer/.composer/vendor/bin:$PATH"
ENV WP_TESTS_BASE_DIR=/tmp/wordpress-tests-suite

RUN mkdir -p $COMPOSER_HOME $COMPOSER_CACHE_DIR && \
    chown -R $USER_ID:$GROUP_ID $COMPOSER_HOME $COMPOSER_CACHE_DIR

RUN mkdir -p $WP_TESTS_BASE_DIR && \
    chown -R $USER_ID:$GROUP_ID $WP_TESTS_BASE_DIR

RUN mkdir -p /app/logs /tmp/.phpunit.cache/code-coverage /tmp/.phpstan \
 && chown -R $USER_ID:$GROUP_ID /app/logs /tmp/.phpunit.cache/ /tmp/.phpunit.cache/code-coverage /tmp/.phpstan

# Switch to non-root user
USER developer

# Default command
CMD ["php", "-a"]