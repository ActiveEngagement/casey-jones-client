<?php

namespace Actengage\CaseyJones\Commands;

use Actengage\CaseyJones\Events\StreamEventReceived;
use Illuminate\Console\Command;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use InvalidArgumentException;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\Promise\Promise;
use React\Promise\PromiseInterface;

class ListenStream extends Command
{
    protected $signature = 'casey:listen
        {--key= : The sha256 encoded token for the Casey Jones app}
        {--connection= : The Redis connection}
        {--interval=1 : The timer interval (in seconds)}';

    protected $description = 'Listen to a Redis stream.';

    protected Connection $connection;

    protected string $lastId = '0-0';

    protected ?string $app;

    public function handle()
    {
        $this->app = $this->option('key') ?? $this->hashedApiKeyConfig();

        if(!$this->app) {
            throw new InvalidArgumentException(
                'You must set an API Key in your .env file or pass it with the --key flag.'
            );
        }

        $this->comment('Starting Casey Jones server...');

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
                        event(new StreamEventReceived(
                            app: $this->app,
                            key: $key,
                            payload: unserialize($payload)
                        ));
                    }
                    
                    $this->newLine();
                    $this->line('Message Received: '.$key);
                    $this->newLine();
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
                $this->app => $this->lastId
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

            $this->newLine();
            $this->comment("Restart signal received. Shutting down...");

            $loop->stop();
        });
    }

    /**
     * Get the sha256 hash of the api key from the config.
     *
     * @return string|null
     */
    protected function hashedApiKeyConfig(): ?string
    {
        if(!$key = config('casey.api_key')) {
            return null;
        }

        [ , $key ] = explode('|', $key);

        return hash('sha256', $key);
    }
}