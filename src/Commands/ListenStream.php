<?php

namespace Actengage\CaseyJones\Commands;

use Illuminate\Console\Command;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Facades\Redis;
use React\EventLoop\Loop;
use React\Promise\Promise;
use React\Promise\PromiseInterface;

class ListenStream extends Command
{
    protected $signature = 'casey:listen
        {app : The name of the Casey Jones application}
        {--connection=default : The Redis connection}
        {--interval=1 : The timer interval (in seconds)}';

    protected $description = 'Listen to a Redis Stream.';

    protected Connection $connection;

    protected string $lastId = '0-0';

    public function handle()
    {
        $loop = Loop::get();
        
        $this->connection = Redis::connection($this->option('connection'));

        Loop::addTimer($this->option('interval'), fn () => $this->loop());

        // $this->info('Starting Listener...');

        $loop->run();
    }

    protected function loop(): PromiseInterface
    {
        return $this->read()
            ->then(function(array $messages) {
                foreach($messages as $key => $message) {
                    $this->info(\json_encode($message));
                }
            })
            ->finally(fn () => Loop::addTimer(1, fn () => $this->loop()));
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
}