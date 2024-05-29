<?php

namespace PersistentSeeders\Tests;

use Illuminate\Support\ServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use PersistentSeeders\PersistentSeedersServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/../src/migrations');
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }

    /**
     * @param $app
     *
     * @return class-string<ServiceProvider>[]
     */
    protected function getPackageProviders($app): array
    {
        return [
            PersistentSeedersServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('database.default', 'testing');
    }
}
