# Base image PHP dengan Apache
FROM php:8.2-apache

# Instal ekstensi mysqli untuk koneksi database MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Ubah port default Apache ke 8000 (sesuai spesifikasi soal UTS)
RUN sed -i 's/80/8000/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# Tentukan direktori kerja di dalam container
WORKDIR /var/www/html

# Salin source code aplikasi ke dalam container
COPY app/ /var/www/html/

# Buat folder logs untuk volume mount
RUN mkdir -p /var/www/logs && chown -R www-data:www-data /var/www/html /var/www/logs

# Buka port 8000
EXPOSE 8000

# Perintah default Apache berjalan di latar depan
CMD ["apache2-foreground"]
