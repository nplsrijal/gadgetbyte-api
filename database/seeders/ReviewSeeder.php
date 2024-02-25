<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data =[
            [1, 'Point'],
            [2, 'Star'],
            [3, 'Percent'],
            [4, 'Summary'],
        ];

        foreach ($data as $key) {
            DB::table('reviews')->insert([
                'id' => $key[0],
                'name' => $key[1]
            ]);
        }
    }
}
