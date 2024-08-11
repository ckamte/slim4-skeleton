<?php declare(strict_types=1);

use DI\ContainerBuilder;

// Instantiate PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();

// Get app default configurations from env file
$config = include __DIR__ . '/../config/containers/defaults.php';
$config($containerBuilder);

// Get and set application settings
$settings = include __DIR__ . '/../config/containers/settings.php';
$settings($containerBuilder);

// Create container for twig
$view = include __DIR__ . '/../config/containers/view.php';
$view($containerBuilder);

// Create container for logger
$logger = include __DIR__ . '/../config/containers/logger.php';
$logger($containerBuilder);

// Build container
return $containerBuilder->build();