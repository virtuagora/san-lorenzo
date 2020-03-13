<?php
$container = $app->getContainer();

$container['db'] = function ($c) {
    $settings = $c->get('settings')['eloquent'];
    return new App\Service\EloquentService($settings);
};
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['monolog'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(
        new Monolog\Handler\StreamHandler(
            $settings['path'],
            Monolog\Logger::DEBUG
        )
    );
    return $logger;
};
$container['mailer'] = function ($c) {
    $settings = $c->get('settings')['swiftmailer'];
    $mailer = new App\Service\SwiftMailerService(
        $settings['transport'],
        $settings['options']
    );
    return $mailer;
};
$container['filesystem'] = function ($c) {
    $settings = $c->get('settings')['filesystem'];
    $adapter = new \ReflectionClass($settings['adapter']);
    return new League\Flysystem\Filesystem($adapter->newInstanceArgs($settings['args']));
};
$container['store'] = function ($c) {
    return new SlimSession\Helper;
};
$container['validation'] = function ($c) {
    return new Augusthur\JsonRespector\ValidatorService();
};

$container['session'] = function ($c) {
    return new App\Service\PHPSessionService($c['store']);
};
$container['identity'] = function ($c) {
    return new App\Service\IdentityService([
        'local' => new App\Util\IdentityProvider\LocalIdentityProvider($c['db'], $c['validation']),
        // 'facebook' => new App\Util\IdentityProvider\FacebookIdentityProvider($c['db'], $c['facebook']),
    ], $c['db']);
};
$container['representation'] = function ($c) {
    return new App\Service\RepresentationService([
        'html' => new App\Representation\HTMLRepresentation($c['view']),
        'json' => new App\Representation\JSONRepresentation(),
    ]);
};
$container['facebook'] = function ($c) {
    $settings = $c->get('settings')['facebook'];
    return new \Facebook\Facebook($settings);
};
$container['recaptcha'] = function ($c) {
    $secret = $c->get('settings')['recaptcha']['secret_key'];
    return new \ReCaptcha\ReCaptcha($secret);
};
$container['authorization'] = function ($c) {
    return new App\Service\AuthorizationService($c['db'], $c['logger']);
};
$container['helper'] = function ($c) {
    return new App\Service\HelperService(
        $c['db'],
        $c['router'],
        $c['request'],
        $c['logger']
    );
};
$container['view'] = function ($c) {
    $settings = $c->get('settings')['twig'];
    $view = new Slim\Views\Twig($settings['path'], $settings['options']);
    $view->addExtension(new App\Util\TwigExtension(
        $c['router'],
        $c['request'],
        $c['options'],
        $c['logger'],
        $c['helper'],
        $c['store']
        //$c['request']->getUri()
    ));
    $view->addExtension(new Twig_Extension_Debug());
    return $view;
};
$container['options'] = function ($c) {
    return new App\Service\OptionsService($c['db']);
};
$container['image'] = function ($c) {
    return new Intervention\Image\ImageManager(['driver' => 'imagick']);
};
$container['pagination'] = function ($c) {
    return new App\Service\PaginationService($c->get('validation'));
};
$container['ts'] = function ($c) {
    return new App\Service\TimestampService(
        $c->get('settings')['mode'] != 'pro',
        $c->get('logger')
    );
};
$container['resources'] = function ($c) {
    return new App\Service\ResourcesService($c);
};
$container['projectApiAction'] = function ($c) {
    return new App\Action\ProjectApiAction($c);
};
$container['projectWebAction'] = function ($c) {
    return new App\Action\ProjectWebAction($c);
};
$container['miscAction'] = function ($c) {
    return new App\Action\MiscAction($c);
};
$container['votingWebAction'] = function ($c) {
    return new App\Action\VotingWebAction($c);
};
$container['auditWebAction'] = function ($c) {
    return new App\Action\AuditWebAction($c);
};

$container['adminAction'] = function ($c) {
    return new App\Action\AdminAction(
        $c['options'],
        $c['representation'],
        $c['helper'],
        $c['authorization'],
        $c['db'],
        $c['filesystem'],
        $c['pagination'],
        $c['view']
    );
};
$container['userPanelAction'] = function ($c) {
    return new App\Action\UserPanelAction(
        $c['options'],
        $c['representation'],
        $c['helper'],
        $c['authorization'],
        $c['db'],
        $c['filesystem'],
        $c['pagination'],
        $c['view']
    );
};
$container['ballotAction'] = function ($c) {
    $resource = new App\Resource\BallotResource($c);
    return new App\Action\BallotAction(
        $resource,
        $c['options'],
        $c['representation'],
        $c['helper'],
        $c['authorization'],
        $c['db'],
        $c['filesystem'],
        $c['pagination'],
        $c['view'],
        $c['logger']
    );
};
$container['sessionAction'] = function ($c) {
    return new App\Action\SessionAction(
        $c['session'],
        $c['identity'],
        $c['view'],
        $c['db'],
        $c->get('settings')['recaptcha']['public_key']
    );
};
$container['pagesAction'] = function ($c) {
    return new App\Action\PagesAction(
        $c['options'],
        $c['session'],
        $c['identity'],
        $c['view'],
        $c['db']);
};
$container['voteAction'] = function ($c) {
    return new App\Action\VoteAction(
        $c['options'],
        $c['representation'],
        $c['helper'],
        $c['authorization'],
        $c['db'],
        $c['filesystem'],
        $c['pagination'],
        $c['view'],
        $c['logger']
    );
};
$container['userAction'] = function ($c) {
    $resource = new App\Resource\UserResource($c);
    return new App\Action\UserAction(
        $resource,
        $c['representation'],
        $c['helper'],
        $c['authorization'],
        $c['db'],
        $c['view'],
        $c['recaptcha'],
        $c['router'],
        $c['pagination']
    );
};
$container['projectAction'] = function ($c) {
    $resource = new App\Resource\ProjectResource($c);
    return new App\Action\ProjectAction(
        $resource,
        $c['representation'],
        $c['helper'],
        $c['authorization'],
        $c['pagination'],
        $c['view'],
        $c['filesystem']
    );
};
$container['citizenAction'] = function ($c) {
    $resource = new App\Resource\CitizenResource($c);
    return new App\Action\CitizenAction(
        $resource,
        $c['representation'],
        $c['helper'],
        $c['authorization'],
        $c['pagination'],
        $c['view'],
        $c['filesystem']
    );
};
// $container['groupAction'] = function ($c) {
//     $resource = new App\Resource\GroupResource($c);
//     return new App\Action\GroupAction(
//         $resource,
//         $c['representation'],
//         $c['helper'],
//         $c['authorization']
//     );
// };

$container['errorHandler'] = function ($c) {
    return new App\Service\ErrorHandlerService(
        [
            '\Respect\Validation\Exceptions\NestedValidationException' => function ($r, $e) {
                return [
                    'message' => 'Datos ingresados inválidos',
                    'status' => 400,
                    'errors' => $e->getMessages(),
                ];
            },
            '\Illuminate\Database\Eloquent\ModelNotFoundException' => function ($r, $e) {
                return [
                    'message' => 'Recurso no encontrado',
                    'status' => 400,
                ];
            },
            '\App\Util\Exception\AppException' => function ($r, $e) {
                return [
                    'message' => $e->getMessage(),
                    'status' => $e->getCode(),
                ];
            },
            '\App\Util\Exception\SystemException' => function ($r, $e) {
                return [
                    'mensaje' => 'Error crítico del sistema',
                    'status' => 500,
                ];
            },
        ],
        $c->get('logger'),
        $c->get('representation')
    );
};

// $container['errorHandler'] = function ($c) {
//     return function ($req, $res, $e) use ($c) {
//         $error = [
//             'message' => 'Internal error'
//         ];
//         $code = 500;
//         $c->get('logger')->info($e->getMessage());

//         $c->get('logger')->info($req);

//         if ($e instanceof App\Util\Exception\AppException) {
//             $code = $e->getCode();
//             $error['message'] = $e->getMessage();
//         } elseif ($e instanceof App\Util\Exception\SystemException) {
//             $error['message'] = 'Error en el sistema. Por favor contacte un administrador';
//         // } elseif ($e instanceof Facebook\Exceptions\FacebookResponseException) {
//         //     $error['message'] = 'En este momento no podemos comunicarnos con Facebook';
//         // } elseif ($e instanceof Facebook\Exceptions\FacebookSDKException) {
//         //     $error['message'] = 'Facebook no nos permite acceder a tu cuenta en este momento';
//         } elseif ($e instanceof Illuminate\Database\Eloquent\ModelNotFoundException) {
//             $code = 404;
//             $error['message'] = 'Recurso no encontrado.';
//         }
//         $options = ['template' => 'error.html.twig'];

//         return $res->withJSON($error)->withStatus($code);

//         /*return $c->get('representation')->convert(
//             $error, $req, $res->withStatus($code), $options
//         );*/
//     };
// };
