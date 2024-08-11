<?php declare(strict_types=1);

namespace App\Handlers;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Slim\App;
use Slim\Error\Renderers\PlainTextErrorRenderer;
use Slim\Exception\HttpException;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\ErrorHandlerInterface;
use Throwable;

class TwigErrorHandler implements ErrorHandlerInterface
{
    protected string $htmlErrorRenderer = TwigErrorRenderer::class;

    protected string $logErrorRenderer = PlainTextErrorRenderer::class;

    protected bool $displayErrorDetails = false;

    protected bool $logErrors;

    protected bool $logErrorDetails = false;

    protected ?string $method = null;

    protected Throwable $exception;

    protected int $statusCode;

    protected string $errorDescription;

    protected CallableResolverInterface $callableResolver;

    protected ResponseFactoryInterface $responseFactory;

    protected ContainerInterface $container;

    public function __construct(
        App $app
    ) {
        $this->container = $app->getContainer();
        $this->responseFactory = $app->getResponseFactory();
        $this->callableResolver = $app->getCallableResolver();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ): ResponseInterface {
        // error data
        $this->exception = $exception;
        $this->method = $request->getMethod();
        $this->statusCode = $this->determineStatusCode();
        $this->errorDescription = $this->getErrorDescription();

        $this->displayErrorDetails = $displayErrorDetails;
        $this->logErrors = $logErrors;
        $this->logErrorDetails = $logErrorDetails;

        if ($logErrors) {
            $this->writeToErrorLog();
        }

        return $this->respond();
    }

    /**
     * Get error code
     *
     * @return integer
     */
    protected function determineStatusCode(): int
    {
        if ($this->method === 'OPTIONS') {
            return 200;
        }

        if ($this->exception instanceof HttpException) {
            return $this->exception->getCode();
        }

        return 500;
    }

    /**
     * Render message as html
     *
     * @throws RuntimeException
     */
    protected function htmlRenderer(): callable
    {
        $renderer = $this->htmlErrorRenderer;

        return $this->callableResolver->resolve($renderer);
    }

    /**
     * Get error descriptions
     *
     * @return string
     */
    protected function getErrorDescription(): string
    {
        if ($this->exception instanceof HttpException) {
            return $this->exception->getDescription();
        }

        return 'Something went wrong!';
    }

    /**
     * Render error page using twig-view
     *
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function respond(): ResponseInterface
    {
        $response = $this->responseFactory->createResponse($this->statusCode);

        $view = $this->container->get('view');

        $data = [
            'code' => $this->statusCode,
            'message' => $this->errorDescription
        ];

        // if set to display details
        if ($this->displayErrorDetails) {
            $renderer = $this->htmlRenderer();
            $data['details'] = call_user_func(
                $renderer,
                $this->exception,
                $this->displayErrorDetails
            );
        }

        return $view->render($response, 'error.twig', ['error' => $data]);
    }

    /**
     * Write to the error log if $logErrors has been set to true
     */
    protected function writeToErrorLog(): void
    {
        $renderer = $this->callableResolver->resolve($this->logErrorRenderer);
        $error = $renderer($this->exception, $this->logErrorDetails);
        if (!$this->displayErrorDetails) {
            $error .= "\nTips: To display error details in HTTP response ";
            $error .= 'set "displayErrorDetails" to true in the ErrorHandler constructor.';
        }
        $this->logError($error);
    }

    /**
     * Wraps the error_log function so that this can be easily tested
     */
    protected function logError(string $error): void
    {
        try {
            $logger = $this->container->get('logger');
            $logger->error($error);
        } catch (ContainerExceptionInterface $e) {
            die($e->getMessage() . ' Check your logger setting');
        }
    }
}
