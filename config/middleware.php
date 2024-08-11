<?php declare(strict_types=1);

use Slim\App;
use Slim\Views\TwigMiddleware;
use App\Middleware\SessionMiddleware;

// slim middleware use FILO (first in last out)
return function (App $app) {
    // Add Body Parsing Middleware
    $app->addBodyParsingMiddleware();

    // Add Routing Middleware
    $app->addRoutingMiddleware();

    // Add twig middleware for view
    $app->add(TwigMiddleware::createFromContainer($app));

    // Add php session middleware
    $app->add(SessionMiddleware::class);
};