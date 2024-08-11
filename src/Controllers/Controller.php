<?php declare(strict_types=1);

namespace App\Controllers;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Psr\Http\Message\ResponseInterface as Response;

class Controller
{
    private Container $_container;

    public function __construct(Container $container)
    {
        $this->_container = $container;
    }

    /**
     * Get app setting
     *
     * @param string $key Setting key
     * 
     * @return mixed
     * @throws DependencyException
     * @throws NotFoundException
     */
    protected function settings(string $key): mixed
    {
        $appSettings = $this->_container->get('settings');
        return $appSettings[$key];
    }

    /**
     * Render to view
     *
     * @param Response   $response Slim response
     * @param string     $template Template name
     * @param array|null $args     Template data
     * 
     * @return object
     * @throws DependencyException
     * @throws NotFoundException
     */
    protected function render(Response $response, string $template, array $args = null): object
    {
        $view = $this->_container->get('view');

        if ($args === null) {
            return $view->render($response, $template);
        }

        return $view->render($response, $template, $args);
    }

    /**
     * Redirect to another url
     *
     * @param Response    $response Slim response
     * @param string|null $url      Url to redirect
     *
     * @return object
     * @throws DependencyException
     * @throws NotFoundException
     */
    protected function redirect(Response $response, ?string $url = null): object
    {
        return $response
            ->withHeader('Location', $this->settings('base_path') . '/' . $url)
            ->withStatus(302);
    }

    /**
     * Json encoded response
     *
     * @param Response     $response Slim response
     * @param string|array $data     Response data
     * 
     * @return object
     */
    protected function jsonResponse(Response $response, string|array $data): object
    {
        header('Content-type: application/json');
        $response->getBody()->write(json_encode($data));
        return $response;
    }

    /**
     * Logging to log file
     *
     * @param string $level   Log level
     * @param string $message Log message
     * 
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    protected function logger(string $level, string $message): void
    {
        $logger = $this->_container->get('logger');
        $logger->$level($message);
    }
}
