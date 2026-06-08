<?php

namespace Tests;

use Actengage\CaseyJones\ServiceProvider;
use Illuminate\Foundation\Application;
use Spatie\LaravelData\LaravelDataServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * @param  Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app)
    {
        return [
            LaravelDataServiceProvider::class,
            ServiceProvider::class,
        ];
    }

    /**
     * Define the test environment: an in-memory SQLite database shared by all
     * tests, plus a non-production MessageGears campaign id.
     *
     * @param  Application  $app
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);

        $app['config']->set('services.mg.campaign_id', 1);
    }

    /**
     * Load the package migrations for every test.
     */
    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
