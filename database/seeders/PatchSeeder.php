<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class PatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $filePath = 'seeders/pgsql/news.sql';
        $path = database_path($filePath);
        $sql = file_get_contents($path);
        DB::unprepared($sql);
        $this->command->info('added!');

    }
}
