<?php

namespace PersistentSeeders;

use Illuminate\Support\ServiceProvider;

class PersistentSeedersServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/config/persistent_seeders.php', 'persistent_seeders');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/config/persistent_seeders.php' => config_path('persistent_seeders.php'),
        ], 'persistent-seeder-config');

        $this->publishesMigrations([
            __DIR__ . '/migrations' => database_path('migrations'),
        ], 'persistent-seeder-migrations');
    }
}
