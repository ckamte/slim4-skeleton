<?php declare(strict_types=1);

use Slim\Factory\AppFactory;
use App\Handlers\TwigErrorHandler;

require __DIR__ . '/../vendor/autoload.php';

// Create and Set container
$container = include __DIR__ . '/../config/containers.php';
$settings = $container->get('settings');
AppFactory::setContainer($container);

// Create app
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
