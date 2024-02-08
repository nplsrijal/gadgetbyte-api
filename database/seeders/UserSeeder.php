<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('users')->insert([
            'firstname' => 'Srijal',
            'lastname' => 'Nepal',
            'email' => 'srijal@gmail.com',
            'username' => 'srijal',
            'user_type_id' => '1',
            'is_active' => '1',
            'password'=>bcrypt('12345678')           
        ]);
    }
}
