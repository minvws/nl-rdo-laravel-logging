<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Tests;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\DBAL\TimestampType;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Support\Facades\DB;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    use DatabaseMigrations;
    use DatabaseTransactions;

    public function runDatabaseMigrations(): void
    {
        if (! RefreshDatabaseState::$migrated) {
            $this->artisan('migrate:fresh', [
                '--database' => 'testing',
                '--path' => 'database/migrations',
                '--realpath' => true,
            ]);

            RefreshDatabaseState::$migrated = true;
        }
    }


    public function getPackageProviders($app): array
    {
        return [
        ];
    }

    public function defineEnvironment($app): void
    {
        tap($app->make('config'), function (Repository $config) {
            $config->set('database.default', 'testing');
            $config->set('database.connections.' . 'testing', [
                'driver' => env('DB_DRIVER'),
                'database' => env('DB_DATABASE'),
                'charset' => 'utf8',
                'prefix' => '',
                'prefix_indexes' => true,
                'search_path' => 'public',
                'sslmode' => 'prefer',
            ]);
        });
    }
}
