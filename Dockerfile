FROM php:8.2-cli

# 1. Instalar utilidades básicas del sistema y librerías necesarias
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    curl \
    && docker-php-ext-install zip pdo pdo_mysql

# 2. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Instalar Node.js (versión 20) y npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 4. Instalar Angular CLI globalmente (para que puedas usar 'ng')
RUN npm install -g @angular/cli

# 5. Definir carpeta de trabajo
WORKDIR /var/www/html