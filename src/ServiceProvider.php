<?php

namespace Actengage\CaseyJones;

use Actengage\CaseyJones\Console\ListenStream;
use Actengage\CaseyJones\Console\MakeListener;
use Actengage\CaseyJones\Console\TerminateStream;
use Actengage\CaseyJones\Redis\Stream;
use Actengage\CaseyJones\Services\MessageGears;
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
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/casey.php', 'casey'
        );

        // Register the API Client.

        $this->app->singleton(Client::class, function() {
            return new Client(config('casey.api_key'));
        });

        $this->app->alias(Client::class, 'casey.client');

        // Register the Redis Stream client.
        
        $this->app->singleton(Stream::class, function() {
            return new Stream(
                Redis::connection(config('casey.redis.connection'))
            );
        });

        $this->app->alias(Stream::class, 'casey.stream');

        // Register the MessageGears service provider.

        $this->app->singleton(MessageGears::class, function() {
            return new MessageGears();
        });

        $this->app->alias(MessageGears::class, 'casey.mg');
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
            __DIR__.'/../database/migrations' => database_path('migrations')
        ], 'casey-migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ListenStream::class,
                MakeListener::class,
                TerminateStream::class
            ]);
        }
    }
    
}
