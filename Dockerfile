FROM php:8.4-apache
WORKDIR /var/www/html

# Установка расширений
RUN docker-php-ext-install pdo pdo_mysql
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Копирование файлов
COPY . /var/www/html
COPY ./public /var/www/html/public

# Включение mod_rewrite
RUN a2enmod rewrite

# Настройки Apache
RUN echo '<Directory /var/www/html/public>' >> /etc/apache2/apache2.conf && \
    echo '    AllowOverride All' >> /etc/apache2/apache2.conf && \
    echo '</Directory>' >> /etc/apache2/apache2.conf