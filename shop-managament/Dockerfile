FROM php:8.4-cli


RUN apt-get update  && apt-get install -y\
    git \
    curl \
    unzip \
    zip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libsodium-dev \
    libpq-dev \
    default-mysql-client \
    default-libmysqlclient-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_pgsql pdo_mysql mbstring exif pcntl bcmath gd zip sodium

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN curl -SL http://deb.nodesource.com/setup_18.x | bash && \
    apt-get update && apt-get install -y nodejs

WORKDIR /var/www/html

COPY . .

EXPOSE 8000
RUN composer install
RUN npm install
RUN npm run build

CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000