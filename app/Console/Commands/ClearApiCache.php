<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearApiCache extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'api:clear-cache';

    /**
     * The console command description.
     */
    protected $description = 'Clear API cache';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Clearing API cache...');
        
        // Очищаем кэш организаций
        Cache::forget('organizations_building_1');
        Cache::forget('organizations_building_2');
        Cache::forget('organizations_building_3');
        Cache::forget('organizations_building_4');
        
        // Очищаем общий кэш
        Cache::flush();
        
        $this->info('API cache cleared successfully!');
        
        return Command::SUCCESS;
    }
} 