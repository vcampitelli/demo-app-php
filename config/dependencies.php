<?php

declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use App\Persistence\DatabaseAdapterInterface;
use App\Persistence\Pdo\PdoWrapper;
use App\Persistence\PdoDatabaseAdapter;
use App\Storage\S3Storage;
use Aws\S3\S3Client;
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
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                ]
            );
        },
        S3Client::class => function (ContainerInterface $container) {
            /** @var SettingsInterface $settings */
            $settings = $container->get(SettingsInterface::class);
            $s3 = $settings->get('s3');

            $options = [
                'region' => $s3['region'],
            ];
            if ((!empty($s3['key'])) && (!empty($s3['secret']))) {
                $options['credentials'] = [
                    'key' => $s3['key'],
                    'secret' => $s3['secret'],
                ];
            }

            return new S3Client($options);
        },
        S3Storage::class => function (ContainerInterface $container) {
            /** @var SettingsInterface $settings */
            $settings = $container->get(SettingsInterface::class);

            /** @var S3Client $s3Client */
            $s3Client = $container->get(S3Client::class);

            return new S3Storage(
                $s3Client,
                $settings->get('s3')['bucket'],
            );
        },
    ]);
};
