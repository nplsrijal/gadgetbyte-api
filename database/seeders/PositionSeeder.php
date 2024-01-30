<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions =[
            [1, 'DOC','DOCTOR',1]
        ];

        foreach ($positions as $position) {
            DB::table('positions')->insert([
                'id' => $position[0],
                'code' => $position[1],
                'name' => $position[2]
            ]);
        }
    }
}
