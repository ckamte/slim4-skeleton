<?php declare(strict_types=1);

namespace App\Middleware;

use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class SessionMiddleware implements Middleware
{
    protected Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Set php session with session name
     * 
     * @param Request        $request PSR-7 request
     * @param RequestHandler $handler PSR-15 request handler
     * 
     * @return Response
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        // Get session name from app
        $settings = $this->container->get('settings');
        $sessionName = $settings['session_name'];
        
        // Start session
        if (session_status() === PHP_SESSION_NONE) {
            session_name($sessionName);
            session_start();
        }

        return $handler->handle($request);
    }
}
