FROM debian:10

ARG VHOST_TYPE
ARG SSL_CRT
ARG SSL_KEY

RUN apt-get update -y
RUN apt-get install apache2 -y
RUN apt-get install git -y
RUN apt-get install rsync -y

#Install PHP
RUN apt-get install wget -y
RUN apt -y install lsb-release apt-transport-https ca-certificates
RUN wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
RUN echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/php.list
RUN apt-get update -y
RUN apt -y install php7.4 libapache2-mod-php7.4 php7.4-cli php7.4-common php7.4-dom php7.4-gd php7.4-intl php7.4-memcached php7.4-mysql php7.4-curl php7.4-mbstring php7.4-zip

#Ajout des certificats
COPY certs/${SSL_CRT} /etc/ssl
COPY certs/${SSL_KEY} /etc/ssl
RUN chown www-data:www-data /etc/ssl/${SSL_CRT} && chown www-data:www-data /etc/ssl/${SSL_KEY}
RUN chmod 750 /etc/ssl/${SSL_CRT} && chmod 750 /etc/ssl/${SSL_KEY}

#Ajout des vhosts
COPY vhosts/${VHOST_TYPE}/math.conf /etc/apache2/sites-available

COPY conf/apache2.conf /etc/apache2/apache2.conf
RUN rm -rf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite
RUN a2enmod headers
RUN a2enmod ssl
RUN a2dissite 000-default
RUN a2ensite math

#Installation de composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '906a84df04cea2aa72f40b5f787e49f22d4c2f19492ac310e8cba5b96ac8b64115ac402c8cd292b8a03482574915d1a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

#Installation de drush
RUN wget -O drush.phar https://github.com/drush-ops/drush-launcher/releases/latest/download/drush.phar
RUN mv drush.phar /usr/local/bin/drush
RUN chmod +x /usr/local/bin/drush

#Gestion des actions au start
COPY sh/container-up.sh /tmp
RUN sed -i 's/\r$//' /tmp/container-up.sh  && \
        chmod +x /tmp/container-up.sh
RUN chmod +x /tmp/container-up.sh
CMD sh /tmp/container-up.sh
