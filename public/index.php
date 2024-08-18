<?php declare(strict_types=1);

use Slim\Factory\AppFactory;
use App\Handlers\TwigErrorHandler;

require __DIR__ . '/../vendor/autoload.php';

// Create container
$container = include __DIR__ . '/../config/containers.php';
$settings = $container->get('settings');

// Set time zone
date_default_timezone_set($settings['time_zone']);

// Create app with container
AppFactory::setContainer($container);
$app = AppFactory::create();
$app->setBasePath($settings['base_path']);

// Register middleware
$middleware = include __DIR__ . '/../config/middleware.php';
$middleware($app);

// Register routes
$route = include __DIR__ . '/../config/routes.php';
$route($app);

// Custom error handling
$errorMiddleware = $app->addErrorMiddleware($settings['app_debug'], true, true);
$errorHandler = new TwigErrorHandler($app);
$errorMiddleware->setDefaultErrorHandler($errorHandler);

// Run app
$app->run();
