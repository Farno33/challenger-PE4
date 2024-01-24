FROM php:apache
RUN DEBIAN_FRONTEND=noninteractive apt update &&\
    apt install -y libzip-dev libc-client-dev libkrb5-dev libfreetype6-dev libjpeg62-turbo-dev libpng-dev zlib1g-dev git mariadb-client &&\
    rm -r /var/lib/apt/lists/* &&\
    a2enmod rewrite
RUN docker-php-ext-install mysqli pdo pdo_mysql zip &&\
    docker-php-ext-configure imap --with-kerberos --with-imap-ssl &&\
    docker-php-ext-install imap &&\
    docker-php-ext-configure gd \
      --with-freetype \
      --with-jpeg &&\
    docker-php-ext-install -j$(nproc) gd

# On met les MàJ sur un autre layer un peu plus tardif que les install puisqu'elles bougerons plus souvent
RUN DEBIAN_FRONTEND=noninteractive apt update &&\
    apt upgrade -y &&\
    apt autoremove -y &&\
    rm -r /var/lib/apt/lists/*

COPY customEntryPoint.sh /.
RUN chmod a+rx /customEntryPoint.sh
ENV BRANCH Release
EXPOSE 80
ENTRYPOINT [ "/customEntryPoint.sh" ]
