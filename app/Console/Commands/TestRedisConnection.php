<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class TestRedisConnection extends Command
{
    protected $signature = 'redis:test';
    protected $description = 'Test Redis connection and cache functionality';

    public function handle()
    {
        $this->info('Iniziando test Redis...');

        try {
            // Test 1: Cache base
            $this->info('1. Testing basic cache...');
            Cache::put('test_key', 'Test Value', 60);
            $value = Cache::get('test_key');
            $this->info("Cache test: " . ($value === 'Test Value' ? '✓ OK' : '✗ FAILED'));

            // Test 2: Cache con tags
            $this->info('2. Testing cache tags...');
            try {
                Cache::tags(['test_tag'])->put('tagged_key', 'Tagged Value', 60);
                $taggedValue = Cache::tags(['test_tag'])->get('tagged_key');
                $this->info("Cache tags test: " . ($taggedValue === 'Tagged Value' ? '✓ OK' : '✗ FAILED'));
            } catch (\Exception $e) {
                $this->error("Cache tags test failed: " . $e->getMessage());
            }

            // Test 3: Cache remember
            $this->info('3. Testing cache remember...');
            $remembered = Cache::remember('remember_key', 60, function () {
                return 'Remembered Value';
            });
            $this->info("Cache remember test: " . ($remembered === 'Remembered Value' ? '✓ OK' : '✗ FAILED'));

            // Test 4: Verifica ScoreController cache
            $this->info('4. Testing ScoreController cache...');
            $cacheKey = 'scores:distribution:' . md5(serialize([]));
            $exists = Cache::has($cacheKey);
            $this->info("Score cache exists: " . ($exists ? 'Yes' : 'No'));

            // Test 5: Redis info
            $this->info('5. Redis server info:');
            $info = Redis::connection('cache')->info();
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Used Memory', $info['used_memory_human'] ?? 'N/A'],
                    ['Connected Clients', $info['connected_clients'] ?? 'N/A'],
                    ['Redis Version', $info['redis_version'] ?? 'N/A'],
                ]
            );

        } catch (\Exception $e) {
            $this->error('Redis test failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
    }
} 