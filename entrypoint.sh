#!/bin/bash

echo "🌐 Đang chờ database tại $DB_HOST:$DB_PORT..."

# Chờ DB sẵn sàng
until nc -z $DB_HOST $DB_PORT; do
  echo "⏳ DB chưa sẵn sàng. Đợi 2s..."
  sleep 2
done

echo "✅ DB đã sẵn sàng. Tiến hành migrate và seed."

# Migrate và seed
php artisan migrate --force
php artisan db:seed --force

# Khởi chạy Laravel
echo "🚀 Đang khởi chạy Laravel tại http://0.0.0.0:8080"
php artisan serve --host=0.0.0.0 --port=8080
