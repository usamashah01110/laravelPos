FROM php:8.2-apache

RUN apt-get update && apt-get install -y wget
RUN curl -sS https://getcomposer.org/installer | php -- --version=2.5.1 --install-dir=/usr/bin/ --filename=composer

## Update apache conf to point to application public directory
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf


# https://github.com/mlocati/docker-php-extension-installer extension list
RUN apt-get update && apt-get install -y  \
    libfreetype6-dev \
    libjpeg-dev \
    libpng-dev \
    libwebp-dev \
    libgmp-dev \
    libzip-dev \
    zip unzip jq htop \
    --no-install-recommends \
    && docker-php-ext-install pdo_mysql gmp zip bcmath \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-configure pcntl --enable-pcntl \
    && docker-php-ext-install pcntl \
    && apt-get autoclean -y \
    && rm -rf /var/lib/apt/lists/*



## Update uploads config
RUN echo "file_uploads = On\n" \
         "memory_limit = 1024M\n" \
         "upload_max_filesize = 16M\n" \
         "post_max_size = 64M\n" \
         "max_execution_time = 2400\n" \
         > /usr/local/etc/php/conf.d/uploads.ini

## Enable headers module
RUN a2enmod rewrite headers

WORKDIR /var/www/html/

#copy files
COPY . /var/www/html/

RUN mkdir -p /etc/pki/tls/certs/

RUN mkdir -p /var/app  && ln -s /var/www/html /var/app/current
#fix folder permissions
RUN bash docker/php/fix_folder_permission.sh

RUN composer install

#hack to open the the bash shell from docker desktop when click on cli button
RUN ln -sf /bin/bash /bin/sh
