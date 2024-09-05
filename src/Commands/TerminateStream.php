<?php

namespace Actengage\CaseyJones\Commands;

use Illuminate\Console\Command;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\InteractsWithTime;

class TerminateStream extends Command
{
    use InteractsWithTime;

    protected $signature = 'casey:restart';

    protected $description = 'Restart all Casey Jones listeners.';

    protected Connection $connection;

    public function handle()
    {
        Cache::forever('casey:restart', $this->currentTime());

        $this->info('Broadcasting Casey Jones restart signal.');
    }
}