<?php declare(strict_types=1);

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions(
        [
        'settings' => function (ContainerInterface $c) {
            // Settings from env file
            $cfg = $c->get('defaults');

            if ($cfg['APP_DEBUG'] === 'true') {
                // Report all PHP errors
                error_reporting(E_ALL);
            } else {
                // Report all errors except E_NOTICE
                error_reporting(E_ALL & ~E_NOTICE);
            }

            $var_dir = '/../../var';
            $full_url = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://" . $_SERVER['HTTP_HOST'] . $cfg['BASE_PATH'];
            return [
                // app details
                'session_name' => 'SLIM_FRAMEWORK',
                'base_path' => $cfg['BASE_PATH'],
                'full_path' => (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://" . $_SERVER['HTTP_HOST'] . $cfg['BASE_PATH'],
                'app_version' => $cfg['APP_VER'],
                'app_name' => $cfg['APP_NAME'],
                
                // debug settings
                'app_env' => $cfg['APP_ENV'],

                // always debug on development mode
                'app_debug' => $cfg['APP_ENV'] != 'production' || !strcasecmp($cfg['APP_DEBUG'], 'true'),
                
                // app settings
                'time_zone' => $db_settings['time_zone'] ?? $cfg['TIME_ZONE'],
    
                // logging
                'max_log_count' => intval($db_settings['max_log_count'] ?? $cfg['MAX_LOG_COUNT']),

                // directories
                'view_dir' => '/../../views',
                'cache_dir' => $var_dir . '/caches',
                'log_dir' => $var_dir . '/logs',
                'tmp_dir' => $var_dir . '/temp',
                'upload_dir' => $var_dir . '/uploads',
                'backup_dir' => $var_dir . '/backups',

                // database settings
                'database' => [
                    'host' => $cfg['DB_HOST'] ?? '',
                    'user' => $cfg['DB_USERNAME'] ?? '',
                    'pass' => $cfg['DB_PASSWORD'] ?? '',
                    'name' => $cfg['DB_DATABASE'] ?? '',
                    'port' => $cfg['DB_PORT'] ?? 3306
                ]
            ];
        }
        ]
    );
};
