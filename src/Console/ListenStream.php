<?php

namespace Actengage\CaseyJones\Console;

use Actengage\CaseyJones\Events\StreamEventReceived;
use Actengage\CaseyJones\Redis\Streamable;
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
     * @throws InvalidArgumentException
     */
    public function handle()
    {
        $token = $this->stringOption('token') ?? $this->hashedApiKeyConfig();

        if (! $token) {
            throw new InvalidArgumentException('Invalid personal access token');
        }

        $this->comment('Starting Casey Jones server...');

        $loop = Loop::get();

        $connectionName = $this->stringOption('connection') ?? $this->configString('casey.redis.connection');

        $connection = Redis::connection($connectionName);

        $this->ensureRestartCommandIsRespected($loop);

        $lastId = '0-0';

        $loop->addTimer(
            (float) ($this->option('interval') ?? 0), fn () => $this->loop(
                connection: $connection,
                lastId: $lastId,
                loop: $loop,
                token: $token,
            )
        );

        if (($timeout = $this->option('timeout')) !== null) {
            $loop->addTimer((float) $timeout, fn () => $loop->stop());
        }

        $loop->run();

        return null;
    }

    /**
     * Perform the loop action.
     *
     * @return PromiseInterface<void>
     */
    protected function loop(Connection $connection, string &$lastId, LoopInterface $loop, string $token): PromiseInterface
    {
        return $this->read($connection, $lastId, $token)
            ->then(function (array $messages) use ($token): void {
                foreach ($messages as $key => $message) {
                    $key = (string) $key;

                    if (is_array($message)) {
                        $payload = Arr::get($message, 'payload');

                        if (is_string($payload)) {
                            event(new StreamEventReceived(
                                token: $token,
                                key: $key,
                                payload: $this->unserializePayload($payload, $key)
                            ));
                        }
                    }

                    $this->newLine();
                    $this->line('Message Received: '.$key);
                    $this->newLine();
                    $this->info(json_encode($message, JSON_THROW_ON_ERROR));
                }
            })
            ->finally(function () use ($connection, &$lastId, $loop, $token): void {
                $loop->addTimer(1, fn () => $this->loop(
                    connection: $connection,
                    lastId: $lastId,
                    loop: $loop,
                    token: $token
                ));
            });
    }

    /**
     * Asynchronously read the Redis event.
     *
     * @return PromiseInterface<array<int|string, mixed>>
     */
    protected function read(Connection $connection, string &$lastId, string $token): PromiseInterface
    {
        $promise = new Promise(function (callable $resolve) use ($connection, $lastId, $token): void {
            $resolve($connection->xread([
                $token => $lastId,
            ], -1));
        });

        return $promise->then(function ($groups) use (&$lastId): array {
            if (! is_array($groups)) {
                return [];
            }

            $messages = array_shift($groups);

            if (is_array($messages)) {
                $lastId = (string) array_key_last($messages);

                return $messages;
            }

            return [];
        });
    }

    /**
     * Unserialize a Redis stream payload into a streamable event.
     */
    protected function unserializePayload(string $payload, string $key): Streamable
    {
        $event = unserialize($payload);

        if (! $event instanceof Streamable) {
            throw new InvalidArgumentException('The Redis stream payload is not a streamable event.');
        }

        $event->key = $key;

        return $event;
    }

    /**
     * Check to see whether the restart signal has been sent.
     */
    protected function ensureRestartCommandIsRespected(LoopInterface $loop): void
    {
        $lastRestart = Cache::get('casey:restart');

        $loop->addPeriodicTimer((float) ($this->option('poll') ?? 5), function () use ($lastRestart, $loop) {
            if ($lastRestart === Cache::get('casey:restart')) {
                return;
            }

            $this->newLine();
            $this->comment('Restart signal received. Shutting down...');

            $loop->stop();
        });
    }

    /**
     * Get the sha256 hash of the api key from the config.
     */
    protected function hashedApiKeyConfig(): ?string
    {
        $key = $this->configString('casey.api_key');

        if (! $key) {
            return null;
        }

        [, $key] = explode('|', $key);

        return hash('sha256', $key);
    }

    /**
     * Get the value of a console option as a string.
     */
    protected function stringOption(string $key): ?string
    {
        $value = $this->option($key);

        return is_string($value) ? $value : null;
    }

    /**
     * Get a configuration value as a string.
     */
    protected function configString(string $key): ?string
    {
        $value = config($key);

        return is_string($value) ? $value : null;
    }

    /**
     * Get the console command arguments.
     *
     * @return array<int, array{0: string, 1: int, 2: string, 3?: mixed}>
     */
    #[\Override]
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get the console command options.
     *
     * @return array<int, array{0: string, 1: string|null, 2: int, 3: string}>
     */
    #[\Override]
    protected function getOptions()
    {
        return [
            ['token', 't', InputOption::VALUE_OPTIONAL, 'The sha256 hashed personal access token for Casey Jones app'],
            ['connection', 'c', InputOption::VALUE_OPTIONAL, 'The name of the Redis connection to use'],
            ['interval', 'i', InputOption::VALUE_OPTIONAL, 'The interval (in seconds) used to poll for new events'],
            ['poll', null, InputOption::VALUE_OPTIONAL, 'The interval (in seconds) used to check for the restart signal'],
            ['timeout', null, InputOption::VALUE_OPTIONAL, 'Stop the listener after this many seconds'],
        ];
    }
}
