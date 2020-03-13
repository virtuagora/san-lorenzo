<?php

namespace App\Action;

use App\Util\Exception\AppException;
use App\Util\Exception\UnauthorizedException;
use App\Util\Utils;

class ProjectAction
{
    protected $projectResource;
    protected $representation;
    protected $helper;
    protected $authorization;
    protected $pagination;
    protected $view;
    protected $filesystem;

    public function __construct($projectResource, $representation, $helper, $authorization, $pagination, $view, $filesystem)
    {
        $this->projectResource = $projectResource;
        $this->representation = $representation;
        $this->helper = $helper;
        $this->authorization = $authorization;
        $this->pagination = $pagination;
        $this->view = $view;
        $this->filesystem = $filesystem;
    }

    public function show($request, $response, $params)
    {
        $proId = $this->helper->getSanitizedStr('id', $params);
        $pro = $this->projectResource->retrieveOne($proId);
        return $this->view->render($response, 'sl/project/overview.twig', [
            'project' => $pro,
        ]);
    }

    // GET /proyecto/{pro}
    // public function getOne($request, $response, $params)
    // {
    //     $subject = $request->getAttribute('subject');
    //     $proyecto = $this->helper->getEntityFromId(
    //         'App:Project', 'pro', $params, ['category']
    //     );
    //     if ($this->authorization->checkPermission($subject, 'retProFull', $proyecto)) {
    //         $proyecto->addVisible(['notes']);
    //     }
    //     return $response->withJSON($proyecto->toArray());
    // }

    public function get($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        $pagParams = $this->pagination->getParams($request, [
            'district' => [
                'type' => 'integer',
                'minimum' => 1,
            ],
            'type' => [
                'type' => 'string',
                'enum' => ['institucional', 'comunitario'],
            ],
            's' => [
                'type' => 'string',
            ],
            'random' => [
                'type' => 'boolean',
            ],
            'selected' => [
                'type' => 'boolean'
            ],
            'feasible' => [
                'type' => 'boolean'
            ],
            'author' => [
                'type' => 'integer',
                'minimum' => 1
            ]
        ]);
        if (isset($pagParams['random']) && $pagParams['random']) {
            $resultados = $this->projectResource->retrieveRandom($pagParams);
        } else {
            $resultados = $this->projectResource->retrieve($pagParams);
            $resultados->setUri($request->getUri());
        }
        if ($this->authorization->checkPermission($subject, 'admin')) {
            $resultados->makeVisible(['author_email', 'author_dni', 'author_phone']);
        }
        if (isset($pagParams['random']) && $pagParams['random']) {
            return $response->withJSON($resultados->toArray());
        } else {
            return $this->pagination->renderResponse($response, $resultados);
        }
    }

    public function getCatalog($request, $response, $params)
    {
        // $subject = $request->getAttribute('subject');
        // $pagParams = $this->pagination->getParams($request, [
        //     'loc' => [
        //         'type' => 'integer',
        //         'minimum' => 1,
        //     ],
        //     'dep' => [
        //         'type' => 'integer',
        //         'minimum' => 1,
        //     ],
        //     'reg' => [
        //         'type' => 'integer',
        //         'minimum' => 1,
        //     ],
        //     'cat' => [
        //         'type' => 'integer',
        //         'minimum' => 1,
        //     ],
        //     's' => [
        //         'type' => 'string',
        //     ],
        // ]);
        // $resultados =    arams);
        // $resultados;
        // $resultados->setUri($request->getUri());
        // if ($this->authorization->checkPermission($subject, 'retDni')) {
        //     $resultados->makeVisible([
        //         'notes',
        //     ]);
        // }

        // return $this->pagination->renderResponse($response, $resultados);
        //return $response->withJSON($orgList->toArray());
    }

    // public function getComments($request, $response, $params)
    // {
    //     $proId = $this->helper->getSanitizedId('pro', $params);
    //     $pagParams = $this->pagination->getParams($request, [
    //         'usr' => [
    //             'type' => 'integer',
    //             'minimum' => 1,
    //         ],
    //     ]);
    //     $resultados = $this->projectResource->retrieveComments($proId, $pagParams);
    //     $resultados->setUri($request->getUri());
    //     return $this->pagination->renderResponse($response, $resultados);
    // }

    public function post($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'createProject')) {
            throw new UnauthorizedException();
        }
        $project = $this->projectResource->createOne($subject, $request->getParsedBody());
        return $this->representation->returnMessage($request, $response, [
            'message' => 'Proyecto creado exitosamente',
            'status' => 200,
            'project' => $project->toArray(),
        ]);
    }

    public function patch($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        $project = $this->helper->getEntityFromId(
            'App:Project', 'pro', $params
        );
        if (!$this->authorization->checkPermission($subject, 'updateProject', $project)) {
            throw new UnauthorizedException();
        }
        $data = $request->getParsedBody();
        $project = $this->projectResource->updateOne($subject, $project, $data);
        return $this->representation->returnMessage($request, $response, [
            'message' => 'Información del proyecto actualizada exitosamente',
            'status' => 200,
            'project' => $project->toArray(),
        ]);
    }

    public function putNotes($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        $project = $this->helper->getEntityFromId(
            'App:Project', 'pro', $params
        );
        if (!$this->authorization->checkPermission($subject, 'coordin')) {
            throw new UnauthorizedException();
        }
        $notes = $this->helper->getSanitizedStr('notes', $request->getParsedBody());
        $project->notes = $notes;
        $project->save();
        return $this->representation->returnMessage($request, $response, [
            'message' => 'Notas del proyecto actualizada exitosamente',
            'status' => 200,
        ]);
    }

    public function delete($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        $project = $this->helper->getEntityFromId(
            'App:Project', 'pro', $params, ['group']
        );
        if (!$this->authorization->checkPermission($subject, 'delPro', $project)) {
            throw new UnauthorizedException();
        }
        $this->projectResource->delete($subject, $project);
        return $this->representation->returnMessage($request, $response, [
            'message' => 'Proyecto y equipo eliminados exitosamente',
            'status' => 200,
        ]);
    }

    public function runUploadImage($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        if (empty($request->getUploadedFiles()['archivo'])) {
            throw new AppException('No se envió un archivo');
        }
        $proId = $this->helper->getSanitizedStr('pro', $params);
        $project = $this->projectResource->retrieveOne($proId);
        $archivo = $request->getUploadedFiles()['archivo'];
        $this->projectResource->updatePicture($subject, $project, $archivo);
        return $response->withRedirect($request->getHeaderLine('HTTP_REFERER'));
    }

    public function getImage($request, $response, $params)
    {
        $path = 'project/' . $this->helper->getSanitizedStr('slug', $params) . '.jpg';
        if (!$this->filesystem->has($path)) {
            throw new AppException(
                'No se encuentra el recurso',
                404
            );
        }
        return $response
            ->withBody(new \Slim\Http\Stream($this->filesystem->readStream($path)))
            ->withHeader('Content-Type', $this->filesystem->getMimetype($path));
    }

    public function runSeleccionar($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $project = $this->helper->getEntityFromId(
            'App:Project', 'pro', $params
        );
        $project->selected = true;
        $project->save();
        return $response->withRedirect($request->getHeaderLine('HTTP_REFERER'));
    }

     public function runQuitar($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $project = $this->helper->getEntityFromId(
            'App:Project', 'pro', $params
        );
        $project->selected = false;
        $project->save();
        return $response->withRedirect($request->getHeaderLine('HTTP_REFERER'));
    }

    // public function postVote($request, $response, $params)
    // {
    //     $subject = $request->getAttribute('subject');
    //     if ($subject->getType() == 'Annonymous') {
    //         throw new UnauthorizedException();
    //     }
    //     $project = $this->helper->getEntityFromId(
    //         'App:Project', 'pro', $params
    //     );
    //     $vote = $this->projectResource->vote($subject, $project);
    //     return $this->representation->returnMessage($request, $response, [
    //         'message' => $vote ? '¡Proyecto bancado!' : 'Proyecto ya no bancado.',
    //         'vote' => $vote,
    //         'status' => 200,
    //     ]);
    // }

    // public function postCoordin($request, $response, $params)
    // {
    //     $subject = $request->getAttribute('subject');
    //     if (!$this->authorization->checkPermission($subject, 'coordin')) {
    //         throw new UnauthorizedException();
    //     }
    //     $project = $this->helper->getEntityFromId(
    //         'App:Project', 'pro', $params
    //     );
    //     $user = $this->helper->getUserFromSubject($subject);
    //     $project->coordin_id = $user->id;
    //     $project->save();
    //     return $this->representation->returnMessage($request, $response, [
    //         'message' => 'Proyecto asignado',
    //         'status' => 200,
    //     ]);
    // }

    // public function deleteCoordin($request, $response, $params)
    // {
    //     $subject = $request->getAttribute('subject');
    //     if (!$this->authorization->checkPermission($subject, 'coordin')) {
    //         throw new UnauthorizedException();
    //     }
    //     $project = $this->helper->getEntityFromId(
    //         'App:Project', 'pro', $params
    //     );
    //     $project->coordin_id = null;
    //     $project->save();
    //     return $this->representation->returnMessage($request, $response, [
    //         'message' => 'Coordinador desvinculado',
    //         'status' => 200,
    //     ]);
    // }

    // public function postReview($request, $response, $params)
    // {
    //     $subject = $request->getAttribute('subject');
    //     $project = $this->helper->getEntityFromId(
    //         'App:Project', 'pro', $params, ['group']
    //     );
    //     if (!$this->authorization->checkPermission($subject, 'coordin')) {
    //         throw new UnauthorizedException();
    //     }
    //     $this->projectResource->updateReview(
    //         $subject, $project, $request->getParsedBody()
    //     );
    //     return $this->representation->returnMessage($request, $response, [
    //         'message' => 'Evaluación del proyecto actualizada',
    //         'status' => 200,
    //     ]);
    // }

    // public function postComment($request, $response, $params)
    // {
    //     $subject = $request->getAttribute('subject');
    //     if ($subject->getType() != 'User') {
    //         throw new UnauthorizedException();
    //     }
    //     $project = $this->helper->getEntityFromId(
    //         'App:Project', 'pro', $params
    //     );
    //     $comment = $this->projectResource->createComment(
    //         $subject, $project, $request->getParsedBody()
    //     );
    //     return $this->representation->returnMessage($request, $response, [
    //         'message' => 'Comentario realizado',
    //         'comment' => $comment,
    //         'status' => 200,
    //     ]);
    // }

    // public function postReply($request, $response, $params)
    // {
    //     $subject = $request->getAttribute('subject');
    //     if ($subject->getType() != 'User') {
    //         throw new UnauthorizedException();
    //     }
    //     $comment = $this->helper->getEntityFromId(
    //         'App:Comment', 'com', $params
    //     );
    //     $reply = $this->projectResource->createReply(
    //         $subject, $comment, $request->getParsedBody()
    //     );
    //     return $this->representation->returnMessage($request, $response, [
    //         'message' => 'Respuesta realizada',
    //         'reply' => $reply,
    //         'status' => 200,
    //     ]);
    // }

    // public function postCommentVote($request, $response, $params)
    // {
    //     $subject = $request->getAttribute('subject');
    //     if ($subject->getType() != 'User') {
    //         throw new UnauthorizedException();
    //     }
    //     $comment = $this->helper->getEntityFromId(
    //         'App:Comment', 'com', $params
    //     );
    //     $votes = $this->projectResource->voteComment(
    //         $subject, $comment, $request->getParsedBody()
    //     );
    //     return $this->representation->returnMessage($request, $response, [
    //         'message' => 'Comentario votado',
    //         'status' => 200,
    //         'votes' => $votes,
    //     ]);
    // }

    // public function showProject($request, $response, $params)
    // {
    //     $proyecto = $this->helper->getEntityFromId(
    //         'App:Project', 'pro', $params
    //     );
    //     $proyecto->addVisible(['goals', 'schedule', 'budget', 'category_id']);
    //     // return $response->withJSON($proyecto->toArray());
    //     return $this->view->render($response, 'ingenia/project/showProject.twig', [
    //         'project' => $proyecto,
    //     ]);
    // }
}
