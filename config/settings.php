<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {
    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => ($_ENV['ENVIRONMENT'] ?? '') !== 'production',
                'logError'            => false,
                'logErrorDetails'     => false,
                'logger' => [
                    'name' => 'slim-app',
                    'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                    'level' => Logger::DEBUG,
                ],
                'database' => [
                    'dsn' => $_ENV['DATABASE_DSN'],
                    'username' => $_ENV['DATABASE_USERNAME'],
                    'password' => $_ENV['DATABASE_PASSWORD'],
                ],
                's3' => [
                    'region' => $_ENV['S3_BUCKET_REGION'],
                    'bucket' => $_ENV['S3_BUCKET_NAME'],
                    'key' => $_ENV['S3_ACCESS_KEY_ID'] ?? null,
                    'secret' => $_ENV['S3_SECRET_ACCESS_KEY'] ?? null,
                ],
            ]);
        }
    ]);
};
