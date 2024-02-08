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
        DB::table('usertype')->insert(
            [
            'typename' => 'SuperAdmin',
            ]
        );

        DB::table('usertype')->insert(
            [
            'typename' => 'Admin',
            ]
        );

       
        DB::table('usertype')->insert(
            [
            'typename' => 'Data Entry Officer',
            ]
        );

       
    }
}
