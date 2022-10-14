FROM php:8.1-apache
 
RUN a2enmod rewrite
 
RUN apt-get update \
  && apt-get install -y libzip-dev git wget unzip --no-install-recommends \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
 
RUN docker-php-ext-install pdo zip;
# Install PostgreSQL driver
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pgsql pdo_pgsql

# instalando Symfony cli
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt install -y symfony-cli

# Install composer
RUN curl -sSL https://getcomposer.org/installer | php \
    && chmod +x composer.phar \
    && mv composer.phar /usr/local/bin/composer
    
COPY docker/apache.conf /etc/apache2/sites-enabled/000-default.conf
COPY . /var/www 
WORKDIR /var/www
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"] 