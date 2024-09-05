<?php

namespace Actengage\CaseyJones\Commands;

use Actengage\CaseyJones\Events\StreamEventReceived;
use Illuminate\Console\Command;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\Promise\Promise;
use React\Promise\PromiseInterface;

class ListenStream extends Command
{
    protected $signature = 'casey:listen
        {app : The name of the Casey Jones application}
        {--connection : The Redis connection}
        {--interval=1 : The timer interval (in seconds)}';

    protected $description = 'Listen to a Redis stream.';

    protected Connection $connection;

    protected string $lastId = '0-0';

    public function handle()
    {
        $loop = Loop::get();

        $this->connection = Redis::connection(
            $this->option('connection') ?? config('casey.redis.connection')
        );

        $this->ensureRestartCommandIsRespected($loop);

        $loop->addTimer($this->option('interval'), fn () => $this->loop($loop));

        $loop->run();
    }

    protected function loop(LoopInterface $loop): PromiseInterface
    {
        return $this->read()
            ->then(function(array $messages) {
                foreach($messages as $key => $message) {
                    if($payload = Arr::get($message, 'payload')) {
                        event(new StreamEventReceived(unserialize($payload)));
                    }
                    
                    $this->info(\json_encode($message));
                }
            })
            ->finally(function() use ($loop) {
                $loop->addTimer(1, fn () => $this->loop($loop));
            });
    }

    protected function read(): PromiseInterface
    {
        return (new Promise(function($resolve, $reject) {
            $resolve($this->connection->xread([
                $this->argument('app') => $this->lastId
            ], null));
        }))->then(function(array $groups) {
            if(is_array($messages = array_shift($groups))) {
                $this->lastId = array_key_last($messages);

                return $messages;
            }

            return [];
        });
    }

    /**
     * Check to see whether the restart signal has been sent.
     */
    protected function ensureRestartCommandIsRespected(LoopInterface $loop): void
    {
        $lastRestart = Cache::get('casey:restart');

        $loop->addPeriodicTimer(5, function () use ($lastRestart, $loop) {
            if ($lastRestart === Cache::get('casey:restart')) {
                return;
            }

            $this->warn("Restart signal received.");

            $loop->stop();
        });
    }
}