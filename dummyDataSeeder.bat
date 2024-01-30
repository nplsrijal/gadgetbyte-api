@echo off
echo Running Dummy Data seeders...

php artisan db:seed --class=TestNameCategorySeeder
php artisan db:seed --class=TestNameSeeder
php artisan db:seed --class=TestNamePriceSeeder
php artisan db:seed --class=CommunitySeeder
php artisan db:seed --class=SchemeSeeder
php artisan db:seed --class=PatientSeeder

echo All Dummy Data Seeders completed.