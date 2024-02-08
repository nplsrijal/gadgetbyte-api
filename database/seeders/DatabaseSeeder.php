<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

// use App\Models\Organization;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        $this->call(PassportClientsTableSeeder::class);
        $this->call(UserTypeSeeder::class);
        $this->call(UserSeeder::class);
       
        if (config('app.env') == 'local') {
           
        }

    }
}
