FROM php:7.1-fpm

ARG user=symfony

# Create user with home to work with permissions on files
RUN adduser --gecos "" --disabled-password $user

RUN apt-get clean && apt-get update

RUN apt-get install -y \
        libmcrypt-dev \
        libxml2-dev \
        zlib1g-dev \
        libicu-dev \
        g++ \
        libssl-dev \
        nano \
        sudo \
        git

RUN docker-php-ext-install -j$(nproc) \
        iconv \
        opcache \
        dom \
        fileinfo \
        gettext \
        intl \
        json \
        mcrypt \
        pcntl \
        phar \
        simplexml \
        zip

RUN pecl channel-update pecl
RUN pecl install apcu-5.1.7 && docker-php-ext-enable apcu

COPY conf/php.ini /usr/local/etc/php/php.ini
COPY conf/www.conf.default /usr/local/etc/php-fpm.d/www.conf.default

ADD https://getcomposer.org/composer.phar /usr/local/bin/composer
RUN chown $user:$user /usr/local/bin/composer && chmod 775 /usr/local/bin/composer

ADD https://symfony.com/installer /usr/local/bin/symfony
RUN chown $user:$user /usr/local/bin/symfony && chmod 775 /usr/local/bin/symfony


# Set password and add sudo group to new user
RUN echo "$user:$user" | chpasswd
RUN echo "root:root" | chpasswd
RUN usermod -a -G sudo $user

RUN echo "$user ALL=(ALL) NOPASSWD:ALL" > /etc/sudoers

USER $user

RUN echo "alias sf='php bin/console'\n"  >> /home/$user/.bashrc

WORKDIR /usr/share/nginx/html
