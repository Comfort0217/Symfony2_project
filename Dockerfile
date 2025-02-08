FROM --platform=linux/amd64 php:5.6-apache

# タイムゾーンを設定する環境変数を定義
ENV TZ=Asia/Tokyo

# Debian Stretchのアーカイブリポジトリを使用し、stretch-updatesを削除
RUN sed -i 's/deb.debian.org/archive.debian.org/g' /etc/apt/sources.list && \
    sed -i 's|security.debian.org|archive.debian.org|g' /etc/apt/sources.list && \
    sed -i '/stretch-updates/d' /etc/apt/sources.list && \
    echo 'Acquire::Check-Valid-Until "false";' > /etc/apt/apt.conf.d/99no-check-valid-until

# 必要なパッケージをインストール
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    tzdata && \
    rm -rf /var/lib/apt/lists/*

# PHP拡張モジュールをインストール
RUN docker-php-ext-install pdo pdo_mysql mbstring zip pcntl

# PHPのタイムゾーンを設定
RUN echo "date.timezone = ${TZ}" > /usr/local/etc/php/conf.d/timezone.ini

# Apacheのmod_rewriteを有効化
RUN a2enmod rewrite

# Apacheの仮想ホスト設定をコピー
COPY apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Apacheの仮想ホストを有効化
RUN a2ensite 000-default

# Composerをインストール（バージョン1系を指定）
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --1

# league/csvのインストール
RUN composer require league/csv

# 作業ディレクトリを設定
WORKDIR /var/www/html

# ポート80を公開
EXPOSE 80