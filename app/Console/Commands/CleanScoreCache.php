<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CleanScoreCache extends Command
{
    protected $signature = 'score:clean-cache';

    public function handle()
    {
        Cache::tags(['scores'])->flush();
        $this->info('Score cache cleaned successfully');
    }
} 