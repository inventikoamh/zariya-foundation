<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class HeartbeatCommand extends Command
{
    protected $signature = 'app:heartbeat';

    protected $description = 'Write a heartbeat log entry to verify scheduler is running';

    public function handle(): int
    {
        Log::info('[Scheduler] Heartbeat tick at '.now()->toDateTimeString());
        $this->info('Heartbeat written to log.');
        return self::SUCCESS;
    }
}




