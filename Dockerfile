FROM php:8.1-apache

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite

RUN docker-php-ext-install pdo pdo_mysql

RUN apt-get update -y && \
    apt-get install -y libpng-dev \
        zip \
        unzip \
        git \

        # needed for amqp
        librabbitmq-dev \
        libssl-dev \

        # needed for gd
        libfreetype6-dev \
        libjpeg62-turbo-dev
RUN rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install gd
RUN docker-php-ext-install exif
#    docker-php-ext-enable zip

# GD
RUN docker-php-ext-configure gd --with-freetype=/usr --with-jpeg=/usr \
    && docker-php-ext-install -j "$(nproc)" gd

RUN php -r 'var_dump(gd_info());'
