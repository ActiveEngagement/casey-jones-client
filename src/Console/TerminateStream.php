<?php

namespace Actengage\CaseyJones\Console;

use Illuminate\Console\Command;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\InteractsWithTime;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'casey:restart')]
class TerminateStream extends Command
{
    use InteractsWithTime;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $command = 'casey:restart';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restart all Casey Jones listeners.';

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        Cache::forever('casey:restart', $this->currentTime());

        $this->info('Broadcasting Casey Jones restart signal.');
    }
}