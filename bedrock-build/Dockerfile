FROM php:8.1-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    libssl-dev \
    nodejs \
    npm \
    libmemcached-dev \
    python3 \
    python3-pip \
    python3-bs4 \
    python3-requests \
    python3-pandas \
    python3-lxml \
    python3-html5lib \
    netcat-openbsd \
    && docker-php-ext-install pdo_mysql mysqli zip exif pcntl bcmath gd intl soap \
    && pecl install memcached \
    && docker-php-ext-enable memcached

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install WP-CLI
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp

# Enable Apache modules
RUN a2enmod rewrite

# Copy Apache configuration
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Set working directory
WORKDIR /srv/bedrock

# Install Yarn
RUN npm install -g yarn

# Copy entrypoint script
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
