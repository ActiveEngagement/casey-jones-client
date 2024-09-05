<?php

namespace Actengage\CaseyJones;

use Actengage\CaseyJones\Commands\ListenStream;
use Illuminate\Foundation\Console\AboutCommand;
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

        $this->app->singleton(Client::class, function() {
            return new Client(config('casey.api_key'));
        });

        $this->app->alias(Client::class, 'casey.client');
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
        ]);

        if ($this->app->runningInConsole()) {

            $this->commands([
                ListenStream::class
            ]);
        }
    }
}
