<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisListener extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:listen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen to redis event';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Redis::connection('default')->subscribe(['pa.public.patients'],function($message){
           $this->info($message);
        });
    }
}
