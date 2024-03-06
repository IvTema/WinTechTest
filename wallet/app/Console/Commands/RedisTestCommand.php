<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class RedisTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:go';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $str = 'some_string';
        // $result = Cache::remember('my_string', 60*60, function () use ($str){  // remeberForever() if you want to store it forever
        //     return $str;
        // });

        // dd($result);


        // $str = 'some_string';
        // if (Cache::has('my_string')) {
        //     $result = Cache::get('my_string');
        // } else {
        //     Cache::put('my_string', $str);
        //     $result = Cache::get('my_string');
        // }
        // dd($result);


        // Cache::put('example', 'my_string');
        // $value = Cache::get('example');
        // Cache::put('example', $value.'-new');
        // Cache::forget('example');
        // $value2 = Cache::get('example');
        // dd($value2);
    }
}
