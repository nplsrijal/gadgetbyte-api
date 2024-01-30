@echo off
echo Running seeders...

php artisan db:seed --class=PassportClientsTableSeeder


echo All Seeders completed.