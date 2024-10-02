<?php

namespace Actengage\CaseyJones\Console;

use Actengage\CaseyJones\Events\StreamEventReceived;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use InvalidArgumentException;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\Promise\Promise;
use React\Promise\PromiseInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'casey:listen')]
class ListenStream extends Command implements PromptsForMissingInput
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $command = 'casey:listen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen to the Casey Jones event stream.';

    /**
     * Execute the console command.
     *
     * @return bool|null
     *
     * @throws \InvalidArgumentException
     */
    public function handle()
    {
        if(!$token = $this->option('token') ?? $this->hashedApiKeyConfig()) {
            throw new InvalidArgumentException('Invalid personal access token');
        }

        $this->comment('Starting Casey Jones server...');

        $loop = Loop::get();

        $connection = Redis::connection(
            $this->option('connection') ?? config('casey.redis.connection')
        );

        $this->ensureRestartCommandIsRespected($loop);

        $lastId = '0-0';

        $loop->addTimer(
            $this->option('interval'), fn () => $this->loop(
                connection: $connection,
                lastId: $lastId,
                loop: $loop,
                token: $token,
            )
        );

        $loop->run();
    }

    /**
     * Perform the loop action.
     *
     * @param Connection $connection
     * @param string $lastId
     * @param LoopInterface $loop
     * @param string $token
     * @return PromiseInterface
     */
    protected function loop(Connection $connection, string &$lastId, LoopInterface $loop, string $token): PromiseInterface
    {
        return $this->read($connection, $lastId, $token)
            ->then(function(array $messages) use ($token) {
                foreach($messages as $key => $message) {
                    if($payload = Arr::get($message, 'payload')) {
                        event(new StreamEventReceived(
                            token: $token,
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
            ->finally(fn() => $loop->addTimer(1, fn () => $this->loop(
                connection: $connection,
                lastId: $lastId,
                loop: $loop,
                token: $token
            )));
    }

    /**
     * Asynchronously read the Redis event.
     *
     * @param Connection $connection
     * @param string $token
     * @return PromiseInterface
     */
    protected function read(Connection $connection, string &$lastId, string $token): PromiseInterface
    {
        return (new Promise(function($resolve, $reject) use ($connection, $lastId, $token) {
            $resolve($connection->xread([
                $token => $lastId
            ], null));
        }))->then(function(array $groups) use (&$lastId) {
            if(is_array($messages = array_shift($groups))) {
                $lastId = array_key_last($messages);

                return $messages;
            }

            return [];
        });
    }

    /**
     * Check to see whether the restart signal has been sent.
     * 
     * @return void
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

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * 
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['token', 't', InputOption::VALUE_OPTIONAL, 'The sha256 hashed personal access token for Casey Jones app'],
            ['connection', 'c', InputOption::VALUE_OPTIONAL, 'The name of the Redis connection to use'],
            ['interval', 'i', InputOption::VALUE_OPTIONAL, 'The interval (in seconds) used to poll for new events'],
        ];
    }
}