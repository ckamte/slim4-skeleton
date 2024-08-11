<?php declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HomeController extends Controller
{
    public function index(Request $request, Response $response): Response
    {
        $this->logger('info', 'Test log');
        return $this->render($response, 'home.twig');
    }
}
