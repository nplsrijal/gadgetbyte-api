#!/bin/bash

echo "Step 1: Running setup.sh script..."
./setup.sh
echo "Done..."

echo "Step 2: Installing Composer dependencies..."
composer install --ignore-platform-req=ext-rdkafka
echo "Done..."

echo "Step 3: Generating Laravel application key..."
php artisan key:generate
echo "Done..."

echo "Step 4: Stopping and removing existing Docker containers and volumes..."
docker compose down -v
echo "Done..."

echo "Step 5: Building and starting Docker containers..."
docker-compose up -d --build
echo "Done..."

echo "Step 6: Installing Composer dependencies inside gbn-app container..."
docker exec gbn-app composer update
echo "Done..."

echo "Step 7: Generating application key inside gbn-app container..."
docker exec gbn-app php artisan key:generate
echo "Done..."

echo "Step 8: Clearing compiled classes inside gbn-app container..."
docker exec gbn-app php artisan clear-compiled
echo "Done..."

echo "Step 9: Restarting gbn-app container..."
docker restart gbn-app
echo "Done..."

echo "Step 10: Running database migrations and seeding data inside gbn-app container..."
docker exec gbn-app php artisan migrate:fresh --seed
echo "Done..."

echo "Step 11: Generating Swagger documentation..."
docker exec gbn-app php artisan l5-swagger:generate
echo "Done..."

echo "Step 12: Generating Passport keys..."
docker exec gbn-app php artisan gbnssport:keys
echo "Done..."

echo "Step 13: Restarting Kafka worker container..."
docker restart gbn-kafka-worker
echo "Done..."

echo "Step 14: Restarting Queue worker container..."
docker restart gbn-queue-worker
echo "Done..."

echo "Setup completed successfully!"
