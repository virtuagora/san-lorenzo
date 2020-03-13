<?php
$container = $app->getContainer();

//$app->add($container->get('csrf'));

$app->add(new App\Middleware\UglyMiddleware($container->get('view')));
$app->add(new App\Middleware\AuthenticationMiddleware($container->get('session')));
$app->add(new Slim\Middleware\Session($container->get('settings')['store']));
//$app->add(new App\Middleware\ContentNegotiationMiddleware());
