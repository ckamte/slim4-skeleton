<?php declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Processor\WebProcessor;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions(
        [
        'logger' => function (ContainerInterface $c) {
            $settings = $c->get('settings');

            $app_name = $settings['app_name'];
            $log_file = __DIR__ . $settings['log_dir'] . '/info.log';
            $max_file_count = $settings['max_log_count'];
            
            $logger = new Logger($app_name);

            // set timezone
            $logger->setTimezone(new \DateTimeZone($settings['time_zone']));

            $processor = new WebProcessor();
            $logger->pushProcessor($processor);

            // logger level
            $loggerLevel = Level::Info;
            $loggerTimeFormat = 'Y-m-d H:i:s';
            $loggerFormat = "[%datetime%] %level_name% %message%\n";
            if ($settings['app_debug'] === true) {
                $loggerLevel = Level::Debug;
                $loggerFormat = "[%datetime%] %level_name% %message% %context% %extra%\n";
            }

            // rotate file on limit
            $handler = new RotatingFileHandler($log_file, $max_file_count, $loggerLevel);

            // log format
            $lineFormatter = new LineFormatter($loggerFormat, $loggerTimeFormat);
            $lineFormatter->ignoreEmptyContextAndExtra(true);

            $handler->setFormatter($lineFormatter);

            // create file daily
            $handler->setFilenameFormat('{date}-{filename}', 'Ymd');
            $logger->pushHandler($handler);

            return $logger;
        },
        ]
    );
};
