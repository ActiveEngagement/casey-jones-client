<?php

namespace Actengage\CaseyJones;

use Actengage\CaseyJones\Console\ListenStream;
use Actengage\CaseyJones\Console\MakeListener;
use Actengage\CaseyJones\Console\TerminateStream;
use Actengage\CaseyJones\Events\Dispatcher;
use Actengage\CaseyJones\Redis\Stream;
use Actengage\CaseyJones\Services\MessageGears;
use Illuminate\Contracts\Queue\Factory as QueueFactoryContract;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    #[\Override]
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/casey.php', 'casey'
        );

        // Register the API Client.

        $this->app->singleton(Client::class, fn () => new Client($this->configString('casey.api_key')));

        $this->app->alias(Client::class, 'casey.client');

        // Register the Redis Stream client.

        $this->app->singleton(Stream::class, fn () => new Stream(
            Redis::connection($this->configString('casey.redis.connection'))
        ));

        $this->app->alias(Stream::class, 'casey.stream');

        // Register the MessageGears service provider.

        $this->app->singleton(MessageGears::class, fn () => new MessageGears);

        $this->app->alias(MessageGears::class, 'casey.mg');

        // Register the event listener

        $this->app->singleton(Dispatcher::class, fn (Application $app) => (new Dispatcher($app))->setQueueResolver(fn () => app(QueueFactoryContract::class)));

        $this->app->alias(Dispatcher::class, 'casey.events');
    }

    /**
     * Boot any application services.
     *
     * @return void
     */
    public function boot()
    {
        AboutCommand::add('Casey Jones', fn () => ['Version' => 'v1.0.0']);

        $this->publishes([
            __DIR__.'/../config/casey.php' => config_path('casey.php'),
        ], 'casey-config');

        $this->publishesMigrations([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'casey-migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ListenStream::class,
                MakeListener::class,
                TerminateStream::class,
            ]);
        }
    }

    /**
     * Get a configuration value as a string.
     */
    protected function configString(string $key): ?string
    {
        $value = config($key);

        return is_string($value) ? $value : null;
    }
}
