<?php

declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use App\Persistence\DatabaseAdapterInterface;
use App\Persistence\Pdo\PdoWrapper;
use App\Persistence\PdoDatabaseAdapter;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

use function DI\autowire;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        DatabaseAdapterInterface::class => autowire(PdoDatabaseAdapter::class),
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
        PdoWrapper::class => function (SettingsInterface $settings) {
            $config = $settings->get('database');
            return new PdoWrapper(
                $config['dsn'],
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]
            );
        },
    ]);
};
