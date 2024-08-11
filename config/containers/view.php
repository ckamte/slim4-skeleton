<?php declare(strict_types=1);

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Slim\Views\Twig;
use Twig\Extension\DebugExtension;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions(
        [
        'view' => function (ContainerInterface $c) {
            $settings = $c->get('settings');
            $cacheDir = __DIR__ . $settings['cache_dir'] . '/twig';

            // Create twig view
            $twig = Twig::create(
                __DIR__ . $settings['view_dir'], [
                    'cache' => $settings['app_env'] === 'production' ? $cacheDir : false,
                    'debug' => $settings['app_debug'],
                    'auto_reload' => true,
                ]
            );

            if ($settings['app_debug'] === true) {
                $twig->addExtension(new DebugExtension());
            }

            // Add settings variable to view
            $twig->getEnvironment()->addGlobal(
                'app', [
                    'base' => $settings['full_path'],
                    'name' => $settings['app_name'],
                ]
            );

            return $twig;
        }
        ]
    );
};
