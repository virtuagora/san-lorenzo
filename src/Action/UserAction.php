<?php

namespace App\Action;

use App\Util\Exception\AppException;
use App\Util\Exception\UnauthorizedException;

class UserAction
{
    protected $userResource;
    protected $representation;
    protected $helper;
    protected $authorization;
    protected $db;
    protected $view;
    protected $recaptcha;
    protected $router;
    protected $pagination;

    public function __construct(
        $userResource, $representation, $helper, $authorization,
        $db, $view, $recaptcha, $router, $pagination
    ) {
        $this->userResource = $userResource;
        $this->representation = $representation;
        $this->helper = $helper;
        $this->authorization = $authorization;
        $this->db = $db;
        $this->view = $view;
        $this->recaptcha = $recaptcha;
        $this->router = $router;
        $this->pagination = $pagination;
    }

    public function get($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        $pagParams = $this->pagination->getParams($request, [
            'dni_state' => [
                'type' => 'integer',
                'minimum' => 1,
            ],
            'roles' => [
                'type' => 'string',
            ],
            's' => [
                'type' => 'string',
            ],
        ]);
        $resultados = $this->userResource->retrieve($pagParams);
        $resultados->setUri($request->getUri());
        if ($this->authorization->checkPermission($subject, 'admin')) {
            $resultados->makeVisible(['dni']);
        }
        return $this->pagination->renderResponse($response, $resultados);
    }

    public function getAdmins($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $pagParams = $this->pagination->getParams($request, [
            'email' => [
                'type' => 'string',
            ],
        ]);
        $resultados = $this->userResource->retrieveAdmins($pagParams);
        // $resultados->setUri($request->getUri());
        // if ($this->authorization->checkPermission($subject, 'coordin')) {
        //     $resultados->makeVisible([
        //         'dni',
        //     ]);
        // }
        // return $this->pagination->renderResponse($response, $resultados);
        return $response->withJSON($resultados->toArray());
    }

     public function getPendingUsers($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        // $pagParams = $this->pagination->getParams($request, [
        //     // 'email' => [
        //     //     'type' => 'string',
        //     // ],
        // ]);
        $resultados = $this->userResource->retrievePendingUsers();
        return $response->withJSON($resultados->toArray());
    }

     public function getPendingVoters($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $resultados = $this->userResource->retrievePendingVoters();
        return $response->withJSON($resultados->toArray());
    }

    public function getCandidatos($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $pagParams = $this->pagination->getParams($request, [
            'email' => [
                'type' => 'string',
            ],
        ]);
        $resultados = $this->userResource->retrieveCandidatos($pagParams);
        // $resultados->setUri($request->getUri());
        // if ($this->authorization->checkPermission($subject, 'coordin')) {
        //     $resultados->makeVisible([
        //         'dni',
        //     ]);
        // }
        // return $this->pagination->renderResponse($response, $resultados);
        return $response->withJSON($resultados->toArray());
    }

    // GET /user/{usr}
    public function getOne($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        $usuario = $this->helper->getEntityFromId(
            'App:User', 'usr', $params
        );
        if ($this->authorization->checkPermission($subject, 'admin')) {
            $usuario->addVisible(['dni']);
        }
        return $response->withJSON($usuario->subject->roles);
    }

    public function post($request, $response, $params)
    {
        $user = $this->userResource->createOne(null, $request->getParsedBody());
        return $this->representation->returnMessage($request, $response, [
            'message' => 'El usuario se registro correctamente',
            'status' => 200,
            // 'user' => $user->toArray(),
            // 'template' => 'base/confirmSignUp.twig',
        ]);
    }

    public function newPendingUser($request, $response, $params)
    {
        $data = $request->getParsedBody();
        if (!isset($data['recaptcha'])) {
            throw new AppException('No se recibio el captcha');
        }
        $gRecaptchaResponse = $data['recaptcha'];
        unset($data['recaptcha']);
        $captchaResp = $this->recaptcha->verify($gRecaptchaResponse);
        if (!$captchaResp->isSuccess()) {
            throw new AppException('Verificación de CAPTCHA inválido');
        }
        $this->userResource->createPendingUser(null, $data);
        return $this->representation->returnMessage($request, $response, [
            'message' => 'Registro pendiente realizado con áxito!',
            'status' => 200,
            // 'template' => 'base/emailSended.twig',
        ]);
    }

    public function showCompleteSignUp($request, $response, $params)
    {
        // Get the token from the querystring
        $token = $request->getQueryParam('token');
        // Check if there is a pending user with this token.
        $pending = $this->db->query('App:PendingUser')
            ->where('token', $token)
            ->first();
        // If it is null (no pending user), return to the home
        if (is_null($pending)) {
            return $response->withRedirect('/');
        }
        // If not, complete registry!
        // $neighbourhoods = $this->db->query('App:Neighbourhood', ['district'])->get();
        $neighbourhoods = $this->db->query('App:Neighbourhood', ['district'])->get();
        // $districts = $this->db->query('App:District')->get();
        return $this->view->render($response, 'sl/completar-registro.twig', [
            'activation_key' => $token,
            // 'districts' => $districts->toArray(),
            'neighbourhoods' => $neighbourhoods->toArray(),
        ]);
    }

    // public function postPublicProfile($request, $response, $params)
    // {
    //     $subject = $request->getAttribute('subject');
    //     $user = $this->helper->getEntityFromId(
    //         'App:User', 'usr', $params
    //     );
    //     if (!$this->authorization->checkPermission($subject, 'updUsrProfile', $user)) {
    //         throw new UnauthorizedException();
    //     }
    //     $user = $this->userResource->updatePublicProfile($subject, $user, $request->getParsedBody());
    //     return $this->representation->returnMessage($request, $response, [
    //         'message' => 'Profile updated succefully',
    //         'status' => 200,
    //         'user' => $user->toArray(),
    //     ]);
    // }

    // public function postPendingEmail($request, $response, $params)
    // {
    //     $subject = $request->getAttribute('subject');
    //     $user = $this->helper->getEntityFromId(
    //         'App:User', 'usr', $params
    //     );
    //     if (!$this->authorization->checkPermission($subject, 'updUsrProfile', $user)) {
    //         throw new UnauthorizedException();
    //     }
    //     $this->userResource->updatePendingEmail($subject, $user, $request->getParsedBody());
    //     return $this->representation->returnMessage($request, $response, [
    //         'message' => 'Se envió un correo con los pasos para actulizar dirección',
    //         'status' => 200,
    //     ]);
    // }

    // public function updateEmail($request, $response, $params)
    // {
    //     $user = $this->helper->getEntityFromId('App:User', 'usr', $params);
    //     $data = [
    //         'token' => $this->helper->getSanitizedStr('tkn', $params),
    //     ];
    //     $this->userResource->updateEmail($user, $data);
    //     return $this->representation->returnMessage($request, $response, [
    //         'message' => 'El email ha sido actualizado',
    //         'status' => 200,
    //     ]);
    // }

    public function runRequestPasswordReset($request, $response, $params)
    {
        $this->userResource->resetPassword($request->getParsedBody());
        return $this->representation->returnMessage($request, $response, [
            'message' => 'Se envió un correo con los pasos para recuperar clave',
            'status' => 200,
        ]);
    }

    public function showCompletePasswordRestore($request, $response, $params)
    {
        $usr = $params['usr'];
        $tkn = $params['tkn'];
        $pending = $this->db->query('App:User')
            ->where('token', $tkn)
            ->first();
        if (is_null($pending)) {
            return $response->withRedirect('/');
        }
        return $this->view->render($response, 'sl/completar-password.twig', [
            'activation_key' => $tkn,
            'user' => $usr,
        ]);
    }

    public function runPasswordRestore($request, $response, $params)
    {
        $user = $this->helper->getEntityFromId(
            'App:User', 'usr', $params
        );
        $this->userResource->restorePassword($user, $request->getParsedBody());
        return $this->representation->returnMessage($request, $response, [
            'message' => 'Clave reiniciada',
            'status' => 200,
        ]);
    }

    public function postPassword($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        $user = $this->helper->getEntityFromId(
            'App:User', 'usr', $params
        );
        if (!$this->authorization->checkPermission($subject, 'updatePassword', $user)) {
            throw new UnauthorizedException();
        }
        $this->userResource->updatePassword($user, $request->getParsedBody());
        return $this->representation->returnMessage($request, $response, [
            'message' => 'Clave actualizada',
            'status' => 200,
        ]);
    }

    // public function putClave($request, $response, $params)
    // {
    //     $subject = $request->getAttribute('subject');
    //     $user = $this->helper->getEntityFromId(
    //         'App:User', 'usr', $params
    //     );
    //     if (!$this->authorization->checkPermission($subject, 'updUsrPas', $user)) {
    //         throw new UnauthorizedException();
    //     }
    //     $this->userResource->updateEmail($user, $request->getParsedBody());
    //     return $this->representation->returnMessage($request, $response, [
    //         'message' => 'Clave actualizada',
    //         'status' => 200,
    //     ]);
    // }

    // public function postProfile($request, $response, $params)
    // {
    //     $subject = $request->getAttribute('subject');
    //     $user = $this->helper->getEntityFromId(
    //         'App:User', 'usr', $params
    //     );
    //     if (!$this->authorization->checkPermission($subject, 'updUsrProfile', $user)) {
    //         throw new UnauthorizedException();
    //     }
    //     $user = $this->userResource->updateProfile($subject, $user, $request->getParsedBody());
    //     return $this->representation->returnMessage($request, $response, [
    //         'message' => 'Profile updated succefully',
    //         'status' => 200,
    //         'user' => $user->toArray(),
    //     ]);
    // }

    // public function postDni($request, $response, $params)
    // {
    //     $subject = $request->getAttribute('subject');
    //     $user = $this->helper->getEntityFromId(
    //         'App:User', 'usr', $params
    //     );
    //     if (!$this->authorization->checkPermission($subject, 'updUsrDni', $user)) {
    //         throw new UnauthorizedException();
    //     }
    //     $atributos = $request->getParsedBody();
    //     if (empty($request->getUploadedFiles()['archivo'])) {
    //         throw new AppException('No se envió un archivo');
    //     }
    //     $archivo = $request->getUploadedFiles()['archivo'];
    //     $this->userResource->updateDni($subject, $user, $atributos, $archivo);
    //     return $response->withRedirect($request->getHeaderLine('HTTP_REFERER'));
    //     // return $this->representation->returnMessage($request, $response, [
    //     //     'message' => 'DNI loaded succefully',
    //     //     'status' => 200,
    //     // ]);
    // }

    // public function getDniFile($request, $response, $params)
    // {
    //     $subject = $request->getAttribute('subject');
    //     $usuario = $this->helper->getEntityFromId(
    //         'App:User', 'usr', $params
    //     );
    //     if (!$this->authorization->checkPermission($subject, 'coordin', $usuario)) {
    //         throw new UnauthorizedException();
    //     }
    //     $fileData = $this->userResource->getDniFile($usuario);
    //     return $response->withBody(new Stream($fileData['strm']))
    //         ->withHeader('Content-Type', $fileData['mime']);
    // }

    public function postRole($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $this->userResource->updateRoles($subject, $request->getParsedBody());
        return $this->representation->returnMessage($request, $response, [
            'message' => 'Roles actualizados',
            'status' => 200,
        ]);
    }

    public function runNewAdmin($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $this->userResource->updateRoles($subject, $request->getParsedBody());
        return $this->representation->returnMessage($request, $response, [
            'message' => '¡Nuevo administrador agregado!',
            'status' => 200,
        ]);
    }

    public function runResendPending($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $this->userResource->resendPending($subject, $request->getParsedBody());
        return $this->representation->returnMessage($request, $response, [
            'message' => '¡Usuario pendiente fue avisado!',
            'status' => 200,
        ]);
    }

     public function runSendPendingVoter($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $this->userResource->sendPendingVoter($subject, $request->getParsedBody());
        return $this->representation->returnMessage($request, $response, [
            'message' => '¡Votante pendiente fue avisado!',
            'status' => 200,
        ]);
    }
}
