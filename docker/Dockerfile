FROM debian:11.2-slim

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y --no-install-recommends \
    apt-utils \
    wget \
    ca-certificates \
    apt-transport-https \
    software-properties-common \
    gnupg2 \
    openssl \
    libssl-dev \
    git \
    procps

RUN echo "deb https://packages.sury.org/php/ bullseye main" | tee /etc/apt/sources.list.d/sury-php.list

RUN wget -qO - https://packages.sury.org/php/apt.gpg | apt-key add -

RUN apt-get update && apt-get install -y \
    php8.1-fpm \
    php8.1-swoole \
    php8.1-dom \
    php8.1-pgsql \
    php8.1-mbstring

RUN mkdir -p /run/php

COPY ./php/www.conf /etc/php/8.1/fpm/pool.d/www.conf

# RUN cd /tmp && git clone https://github.com/openswoole/swoole-src.git && \
#   cd swoole-src && \
#   git checkout v4.8.1 && \
#   phpize  && \
#   ./configure --enable-openssl --enable-swoole-curl --enable-http2 --enable-mysqlnd && \
#   make && make install

# RUN apt-get update && apt-get install vim -y && \
#     apt-get install openssl -y && \
#     apt-get install libssl-dev -y && \
#     apt-get install wget -y && \
#     apt-get install git -y && \
#     apt-get install procps -y && \
#     apt-get install htop -y
#
# RUN cd /tmp && git clone https://github.com/openswoole/swoole-src.git && \
#     cd swoole-src && \
#     git checkout v4.8.1 && \
#     phpize  && \
#     ./configure --enable-openssl --enable-swoole-curl --enable-http2 --enable-mysqlnd && \
#     make && make install
#
# RUN touch /usr/local/etc/php/conf.d/openswoole.ini && \
#     echo 'extension=openswoole.so' > /usr/local/etc/php/conf.d/zzz_openswoole.ini
#
# RUN wget -O /usr/local/bin/dumb-init https://github.com/Yelp/dumb-init/releases/download/v1.2.2/dumb-init_1.2.2_amd64
# RUN chmod +x /usr/local/bin/dumb-init
#
# RUN apt-get autoremove -y && rm -rf /var/lib/apt/lists/*
#
# ENTRYPOINT ["/usr/local/bin/dumb-init", "--", "php"]

# WORKDIR /var/www
#
# USER "${USER_ID}:${GROUP_ID}"

# CMD ["php-fpm"]

EXPOSE 9081
EXPOSE 9101

CMD ["php-fpm8.1", "-F", "-R"]
# ENTRYPOINT ["php8.1", "-r", "sleep(300);"]
