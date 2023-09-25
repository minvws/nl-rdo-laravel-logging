<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Log\LogManager;
use MinVWS\Logging\Laravel\Loggers\SysLogger;
use Illuminate\Support\ServiceProvider;
use MinVWS\Logging\Laravel\Loggers\DbLogger;
use MinVWS\Logging\Laravel\Loggers\RabbitLogger;
use Illuminate\Foundation\Application;

class LogServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ .'/../config/logging.php',
            'logging'
        );
    }

    public function boot(): void
    {
        $this->app->singleton(LogService::class, function (Application $app) {
            $logger = new LogService([]);

            if (config('logging.dblog_enabled', false)) {
                $modelFqcn = (string)config('logging.auditlog_model');
                if (! is_a($modelFqcn, Model::class, true)) {
                    throw new \Exception("Model $modelFqcn does not inherit the eloquent model class");
                }

                $logger->addLogger(new DbLogger($modelFqcn));
            }

            if (config('logging.syslog_enabled', false)) {
                $logInterface = config('logging.syslog_channel')
                    ? $app->make(LogManager::class)->channel(config('logging.syslog_channel'))
                    : $app->make('log');
                $logger->addLogger(new SysLogger(
                    config('logging.syslog_encrypt'),
                    config('logging.syslog_pubkey') ? base64_decode(config('logging.syslog_pubkey', '')) : "",
                    config('logging.syslog_secret') ? base64_decode(config('logging.syslog_secret', '')) : "",
                    $logInterface
                ));
            }

            if (config('logging.rabbitmq_enabled', false)) {
                $logger->addLogger(new RabbitLogger(
                    config('logging.rabbitmq_additional_allowed_events', []),
                    config('rabbitevents.prefix', config('app.name', 'laravel')),
                    config('logging.rabbitmq_log_pii', false),
                ));
            }

            return $logger;
        });
    }
}
