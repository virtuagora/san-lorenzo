<?php
namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class UglyMiddleware
{
    protected $view;

    public function __construct($view)
    {
        $this->view = $view;
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $this->view->getEnvironment()->addGlobal(
            'subject',
            $request->getAttribute('subject')
        );
        return $next($request, $response);
    }
}
