FROM php:8.1-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install mysqli pdo pdo_mysql zip exif pcntl bcmath

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first (for better caching)
COPY composer.json composer.lock* ./

# Install PHP dependencies (Stripe, PHPMailer, etc.)
RUN if [ -f "composer.json" ]; then \
        composer install --no-interaction --optimize-autoloader --no-dev; \
    fi

# Copy all application files
COPY . .

# Create .env file from example if it doesn't exist
RUN if [ ! -f .env ] && [ -f .env.example ]; then \
        cp .env.example .env; \
    fi

# Ensure stripe-config.php uses environment variables
RUN if [ -f stripe-config.php ]; then \
        sed -i 's|define('\''STRIPE_PUBLISHABLE_KEY'\'',.*|define('\''STRIPE_PUBLISHABLE_KEY'\'', getenv('\''STRIPE_PUBLISHABLE_KEY'\''));|g' stripe-config.php; \
        sed -i 's|define('\''STRIPE_SECRET_KEY'\'',.*|define('\''STRIPE_SECRET_KEY'\'', getenv('\''STRIPE_SECRET_KEY'\''));|g' stripe-config.php; \
    fi

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/vendor 2>/dev/null || true \
    && chmod 644 /var/www/html/.env 2>/dev/null || true

# Expose Apache port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]