FROM php:7.3-apache

# Env variables
ENV ACCEPT_EULA=y

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends install apt-utils libxml2-dev gnupg apt-transport-https \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

#Install ODBC Driver
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - \
    && curl https://packages.microsoft.com/config/debian/9/prod.list > /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update

# Install sqlsrv
RUN apt-get update
RUN apt-get install -y wget
RUN wget http://ftp.br.debian.org/debian/pool/main/g/glibc/multiarch-support_2.24-11+deb9u4_amd64.deb && \
    dpkg -i multiarch-support_2.24-11+deb9u4_amd64.deb
RUN apt-get -y install msodbcsql17 unixodbc-dev
RUN pecl install sqlsrv pdo_sqlsrv
RUN docker-php-ext-enable sqlsrv pdo_sqlsrv

# Install webapp extension
RUN docker-php-ext-install pdo pdo_mysql
RUN apt-get update \
    && apt-get install -y libzip-dev \
    && apt-get install -y zlib1g-dev \
    && apt-get install -y libxml2-dev \
    && apt-get install -y libpng-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install zip \
    && docker-php-ext-install soap \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install gd

RUN a2enmod rewrite
RUN a2enmod headers