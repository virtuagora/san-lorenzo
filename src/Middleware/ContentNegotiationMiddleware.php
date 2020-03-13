<?php
namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ContentNegotiationMiddleware
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        //TODO hacer más dinámica la negociación
        if (substr($request->getHeaderLine('Accept'), 0, 16) == 'application/json') {
            $neg = ['content' => 'json'];
        } else {
            $neg = ['content' => 'html'];
        }
        $request = $request->withAttribute('negotiation', $neg);
        return $next($request, $response);
    }
}
