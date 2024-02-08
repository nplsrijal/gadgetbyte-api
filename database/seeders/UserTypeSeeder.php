<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('user_types')->insert(
            [
            'name' => 'SuperAdmin',
            ]
        );

        DB::table('user_types')->insert(
            [
            'name' => 'Admin',
            ]
        );

       
        DB::table('user_types')->insert(
            [
            'name' => 'Data Entry Officer',
            ]
        );

       
    }
}
