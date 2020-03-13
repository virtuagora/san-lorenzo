<?php
namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AuthenticationMiddleware
{
    protected $session;

    public function __construct($session)
    {
        $this->session = $session;
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $subject = $this->session->authenticate($request);
        $request = $request->withAttribute('subject', $subject);
        return $next($request, $response);
    }
}
