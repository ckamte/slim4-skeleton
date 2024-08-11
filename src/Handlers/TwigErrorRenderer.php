<?php

declare(strict_types=1);

namespace App\Handlers;

use Slim\Error\AbstractErrorRenderer;
use Throwable;

class TwigErrorRenderer extends AbstractErrorRenderer
{
    public function __invoke(Throwable $exception, bool $displayErrorDetails): string
    {
        $html = '';
        if ($displayErrorDetails) {
            $html = '<p>The application could not run because of the following error:</p>';
            $html .= '<h2>Details</h2>';
            $html .= $this->renderExceptionFragment($exception);
        }
        return $html;
    }

    private function renderExceptionFragment(Throwable $exception): string
    {
        $html = sprintf('<div><strong>Type:</strong> %s</div>', get_class($exception));

        $code = $exception->getCode();

        $html .= sprintf('<div><strong>Code:</strong> %s</div>', $code);

        $html .= sprintf('<div><strong>Message:</strong> %s</div>', htmlentities($exception->getMessage()));

        $html .= sprintf('<div><strong>File:</strong> %s</div>', $exception->getFile());

        $html .= sprintf('<div><strong>Line:</strong> %s</div>', $exception->getLine());

        $html .= '<h2>Trace</h2>';
        $html .= sprintf('<pre>%s</pre>', htmlentities($exception->getTraceAsString()));

        return $html;
    }
}
